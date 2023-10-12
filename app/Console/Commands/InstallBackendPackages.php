<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Process\Process;

class InstallBackendPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:backend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install backend packages';

    /**
     * Filesystem instance for file operations.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $availablePackages = [
            'Without packages' => 'no_packages',
            'Laravel Request Docs' => 'addLaravelRequestDocs',
            'Laravel Sluggable Eloquent' => 'addLaravelSluggableEloquent',
            'Laravel Passport' => 'addLaravelPassport',
            'Laravel Medialibrary' => 'addLaravelMedialibrary',
        ];

        $selectedPackages = $this->choice('Select backend packages to install (separated by commas):', array_keys($availablePackages), null, $max = null, $allowMultipleSelections = true);

        $installedPackages = [];

        foreach ($selectedPackages as $selectedPackage) {
            if (isset($availablePackages[$selectedPackage]) && $availablePackages[$selectedPackage] != 'no_packages') {
                $this->{$availablePackages[$selectedPackage]}();
                $installedPackages[] = $selectedPackage;
            }
        }

        if (!empty($installedPackages)) {
            $this->info('Installed packages: ' . implode(', ', $installedPackages));
        } else {
            $this->info('No packages selected for installation.');
        }

        $this->installBackendSystem();
    }

    /**
     * Add a Laravel package.
     *
     * @param string $packageName
     * @param string $composerCommand
     * @param string $documentationUrl
     */
    protected function addPackage($packageName, $composerCommand, $documentationUrl)
    {
        $this->info("Adding $packageName...");
        $this->executeCommand($composerCommand);
        $this->info("$packageName successfully added, see documentation $documentationUrl");
    }


    /**
     * Install one of backend systems.
     */
    protected function installBackendSystem()
    {
        $availableSystems = [
            'Without CMS' => '',
            'Laravel Nova' => 'installLaravelNova',
            'Filament' => 'installFilament',
            'Statamic' => 'installStatamic',
        ];

        $selectedSystem = $this->choice('Select backend content management system to install:', array_keys($availableSystems));

        if (isset($availableSystems[$selectedSystem])) {
            $this->{$availableSystems[$selectedSystem]}();
        }
    }

    /**
     * Install Laravel Nova.
     */
    protected function installLaravelNova()
    {
        $email = $this->ask('Enter your nova licence email: ');
        $key = $this->ask('Enter your nova licence key: ');

        $this->info('License activation...');
        $this->executeCommand("composer config http-basic.nova.laravel.com $email $key");

        $this->info("Adding Laravel Nova...");
        $this->executeCommand('composer config repositories.nova \'{"type": "composer", "url": "https://nova.laravel.com"}\' --file composer.json');
        $this->executeCommand('composer require laravel/nova');

        $this->executeCommand('php artisan nova:install');

        $this->info('Database migration...');
        $this->executeCommand("php artisan migrate");

        $this->createBackendUser();

        $this->info('Nova installed successfully! You may now log in at ' . url('/nova'));
    }

    /**
     * Install Filament.
     */
    protected function installFilament()
    {
        $this->info('Requiring filament...');
        $this->executeCommand('composer require filament/filament:"^3.0-stable" -W');

        $this->info('Filament installation...');
        $this->executeCommand('php artisan filament:install --panels');

        $this->createBackendUser();

        $this->filesystem->copy(app_path('Console/Commands/stubs/routes/web-base.stub'), base_path('routes/web.php'));

        $this->info('Filament installed successfully! You may now log in at ' . url('/admin'));
    }

    /**
     * Create backend user.
     *
     * @param boolean $super
     */
    protected function createBackendUser($super = false)
    {
        $data = [
            'name' => $this->ask('Name of the Backend User:'),
            'email' => $this->ask('Email address of the Backend User:'),
            'password' => Hash::make($this->secret('Password for the Backend User:')),
        ];

        if ($super) {
            $data['super'] = $super;
        }

        User::updateOrCreate(
            ['email' => $data['email']],
            $data
        );

        $this->info('User created successfully!');
    }

    /**
     * Install Statamic.
     */
    protected function installStatamic()
    {
        $this->executeCommand('php artisan config:clear');
        $this->updateComposer();

        $this->info('Requiring Statamic...');
        $this->executeCommand('composer require statamic/cms --with-dependencies');

        $this->updateStatamicConfig();

        $this->executeCommand('php please auth:migration');
        $this->executeCommand('php artisan migrate');

        $this->createBackendUser(true);

        $this->info('Statamic installed successfully! You may now log in at ' . url('/cp'));
    }

    /**
     * Update Statamic config files.
     */
    protected function updateStatamicConfig()
    {
        $this->filesystem->copy(app_path('Console/Commands/stubs/statamic/StatamicUser.stub'), app_path('Models/User.php'));
        $this->filesystem->copy(app_path('Console/Commands/stubs/statamic/auth.stub'), config_path('auth.php'));
        $this->filesystem->copy(app_path('Console/Commands/stubs/statamic/users.stub'), config_path('statamic/users.php'));
        $this->info('Config files updated');
    }

    /**
     * Update composer for statamic.
     */
    protected function updateComposer()
    {
        $composerPath = base_path('composer.json');

        $composer = json_decode($this->filesystem->get($composerPath));

        $allowPlugins = $composer->config->{'allow-plugins'} ?? [];
        $allowPlugins->{'pixelfear/composer-dist-plugin'} = true;

        $scripts = $composer->scripts->{'post-autoload-dump'} ?? [];
        $scripts[] = '@php artisan statamic:install --ansi';

        $composer->config->{'allow-plugins'} = $allowPlugins;
        $composer->scripts->{'post-autoload-dump'} = $scripts;

        $this->filesystem->put($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info('composer.json updated');
    }

    /**
     * Adding Laravel Request Docs package.
     *
     * @return void
     */
    protected function addLaravelRequestDocs()
    {
        $this->addPackage(
            'Laravel Request Docs',
            'composer require rakutentech/laravel-request-docs',
            'https://github.com/rakutentech/laravel-request-docs'
        );
    }

    /**
     * Adding Laravel Sluggable Eloquent package.
     *
     * @return void
     */
    protected function addLaravelSluggableEloquent()
    {
        $this->addPackage(
            'Laravel Sluggable Eloquent',
            'composer require spatie/laravel-sluggable',
            'https://github.com/spatie/laravel-sluggable'
        );
    }

    /**
     * Adding Laravel Passport package.
     *
     * @return void
     */
    protected function addLaravelPassport()
    {
        $this->addPackage(
            'Laravel Passport',
            'composer require laravel/passport',
            'https://laravel.com/docs/10.x/passport'
        );
        $this->executeCommand('php artisan migrate');
        $this->executeCommand('php artisan passport:install');
    }

    /**
     * Adding Laravel Medialibrary package.
     *
     * @return void
     */
    protected function addLaravelMedialibrary()
    {
        $this->addPackage(
            'Laravel Medialibrary',
            'composer require spatie/laravel-medialibrary',
            'https://spatie.be/docs/laravel-medialibrary/v10/introduction'
        );
    }


    /**
     * Execute a shell command.
     *
     * @param string $command The shell command to execute.
     *
     * @return void
     */
    protected function executeCommand($command)
    {
        $process = Process::fromShellCommandline($command, base_path());
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Error executing command: ' . $command);
            $this->error($process->getErrorOutput());
            exit(1);
        }
    }
}
