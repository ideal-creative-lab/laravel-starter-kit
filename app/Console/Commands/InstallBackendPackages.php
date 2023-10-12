<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
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

        $this->createBaseNovaUser();
    }

    protected function createBaseNovaUser()
    {
        $data = [
            'name' => $this->ask('Name of the Nova User:'),
            'email' => $this->ask('Email address of the Nova User:'),
            'password' => Hash::make($this->secret('Password for the Nova User:')),
            'is_admin' => true
        ];

        User::updateOrCreate(
            ['email' => $data['email']],
            $data
        );

        $this->info('User created successfully!');
    }

    protected function installFilament()
    {
        $this->info("Adding Filament");
    }

    protected function installStatamic()
    {
        $this->info("Adding Statamic");
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
