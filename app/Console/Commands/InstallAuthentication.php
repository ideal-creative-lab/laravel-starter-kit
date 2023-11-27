<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class InstallAuthentication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:auth
                            {--halt : Publish HALT components}
                            {--tall : Publish TALL components}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install authentication components';

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
        $this->copyStubs();
        $this->copyViews();
        $this->updateRoutes();

        if ($this->option('halt')) {
            $this->info('Publishing HALT components.');
            $this->executeCommand('php artisan auth:halt');
        }

        if ($this->option('tall')) {
            $this->info('Publishing TALL components.');
            $this->executeCommand('php artisan auth:tall');
        }

        $this->createUsersTableMigration();

        $this->addDashboard();

        $this->addPreline();

        $this->info('Authentication components installed successfully. Please run command for building assets.');
    }

    /**
     * Copy stub files to their destinations.
     */
    protected function copyStubs()
    {
        $stubs = [
            'App/Http/Controllers/AuthController.stub' => app_path('Http/Controllers/AuthController.php'),
            'App/Http/Controllers/ForgotPasswordController.stub' => app_path('Http/Controllers/ForgotPasswordController.php'),
            'App/Http/Requests/LoginRequest.stub' => app_path('Http/Requests/LoginRequest.php'),
            'App/Http/Requests/PasswordResetRequest.stub' => app_path('Http/Requests/PasswordResetRequest.php'),
            'App/Http/Requests/PasswordUpdateRequest.stub' => app_path('Http/Requests/PasswordUpdateRequest.php'),
            'App/Http/Requests/RegisterRequest.stub' => app_path('Http/Requests/RegisterRequest.php'),
            'App/Mail/AccountConfirmationMail.stub' => app_path('Mail/AccountConfirmationMail.php'),
            'App/Mail/PasswordResetMail.stub' => app_path('Mail/PasswordResetMail.php'),
            'App/Models/User.stub' => app_path('Models/User.php'),
            'App/Services/AccountConfirmationService.stub' => app_path('Services/AccountConfirmationService.php'),
            'App/Services/PasswordResetService.stub' => app_path('Services/PasswordResetService.php'),
        ];

        $this->copyFiles($stubs);

        $this->info('Chore files created successfully.');
    }

    /**
     * Copy view files to their destinations.
     */
    protected function copyViews()
    {
        $views = [
            'views/emails/account_confirmation.blade.php' => base_path('/resources/views/emails/account_confirmation.blade.php'),
            'views/emails/password_reset.blade.php' => base_path('/resources/views/emails/password_reset.blade.php'),
            'views/auth/confirm.stub' => base_path('/resources/views/auth/confirm.blade.php'),
        ];

        $this->copyFiles($views);

        $this->info('Views created successfully.');
    }

    /**
     * Update the application's routes.
     */
    protected function updateRoutes()
    {
        $routesContent = $this->filesystem->get(app_path('Console/Commands/stubs/routes/web.stub'));
        $this->filesystem->append(base_path('routes/web.php'), $routesContent);

        $this->info('Authentication routes added successfully.');
    }

    /**
     * Create the users table migration file.
     */
    protected function createUsersTableMigration()
    {
        $migrationFile = database_path('migrations/' . date('Y_m_d_His', time()) . '_add_confirmation_token_to_users.php');
        $migrationContent = $this->filesystem->get(app_path('Console/Commands/stubs/database/migrations/add_confirmation_token_to_users.stub'));
        $this->filesystem->put($migrationFile, $migrationContent);

        $this->info('Users table migration created successfully.');

        $this->executeCommand('php artisan migrate');

        $this->info('Users table created successfully.');
    }

    /**
     * Add base dashboard.
     */
    public function addDashboard()
    {
        $stubPath = app_path('Console/Commands/stubs/views/admin/dashboard.stub');
        $destinationPath = resource_path('views/admin/dashboard.blade.php');
        $this->filesystem->copy($stubPath, $destinationPath);
        $this->info('Dashboard created');
    }

    public function addPreline()
    {
        $answer = $this->ask('Do you want to install PrelineUI scripts (y/n)');

        if (strtolower($answer) === 'y') {
            $this->info('PrelineUI scripts installation initiated...');

            $this->executeCommand('npm install preline');

            $this->filesystem->copy(app_path('Console/Commands/stubs/config/preline-tailwind.stub'), base_path('tailwind.config.js'));
            $this->filesystem->prepend(resource_path('js/app.js'), "import('preline')\n");

            $this->info('PrelineUI scripts installation completed.');
        } else {
            $this->info('PrelineUI scripts installation skipped.');
        }
    }


    /**
     * Copy files to new path.
     */
    protected function copyFiles($filePaths)
    {
        foreach ($filePaths as $sourcePath => $destinationPath) {
            $this->makeDirectoryIfNeeded($destinationPath);
            $this->filesystem->copy(app_path('Console/Commands/stubs/' . $sourcePath), $destinationPath);
            $this->info("$destinationPath created");
        }
    }

    /**
     * Create the directory for the file if it doesn't exist.
     *
     * @param string $path
     */
    protected function makeDirectoryIfNeeded($path)
    {
        $directory = dirname($path);
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->makeDirectory($directory, 0755, true, true);
        }
    }

    /**
     * Execute a shell command.
     *
     * @param string $command
     */
    protected function executeCommand($command)
    {
        $process = Process::fromShellCommandline($command, base_path());
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Error executing command: ' . $command);
            $this->error($process->getErrorOutput());
        }
    }
}
