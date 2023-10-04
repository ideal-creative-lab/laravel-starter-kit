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
    protected $signature = 'install:auth';

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
        $this->createUsersTableMigration();

        $this->info('Authentication components installed successfully.');
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
        ];

        $this->copyFiles($views);

        $this->info('Views created successfully.');
    }

    /**
     * Update the application's routes.
     */
    protected function updateRoutes()
    {
        $routesContent = $this->filesystem->get(base_path('stubs/routes/web.stub'));
        $this->filesystem->append(base_path('routes/web.php'), $routesContent);

        $this->info('Authentication routes added successfully.');
    }

    /**
     * Create the users table migration file.
     */
    protected function createUsersTableMigration()
    {
        $migrationFile = database_path('migrations/' . date('Y_m_d_His', time()) . '_add_confirmation_token_to_users.php');
        $migrationContent = $this->filesystem->get(base_path('/stubs/database/migrations/add_confirmation_token_to_users.stub'));
        $this->filesystem->put($migrationFile, $migrationContent);

        $this->info('Users table migration created successfully.');

        $this->executeCommand('php artisan migrate');

        $this->info('Users table created successfully.');
    }

    /**
     * Copy files to new path.
     */
    protected function copyFiles($filePaths)
    {
        foreach ($filePaths as $sourcePath => $destinationPath) {
            $this->makeDirectoryIfNeeded($destinationPath);
            $this->filesystem->copy(base_path('stubs/' . $sourcePath), $destinationPath);
            $this->info("$destinationPath is created");
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
            exit(1);
        }
    }
}