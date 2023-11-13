<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishAuthHalt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:halt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish UI for authentication pages using HALT';

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
        $this->publishUIInterfaces();

        $this->info('Authentication UI interfaces published successfully.');
    }

    /**
     * Publishing UI interfaces from stub files.
     */
    protected function publishUIInterfaces()
    {
        $views = [
            'register',
            'login',
            'forgot-password',
            'reset-password',
        ];

        $this->copyViews($views);

        $this->publishRoutes();

        $this->addDashboard();

        $this->updateControllers();

        $this->updateMail();
    }

    /**
     * Copy views to new path.
     *
     * @param array $views
     */
    protected function copyViews($views)
    {
        foreach ($views as $view) {
            $stubPath = app_path('Console/Commands/stubs/views/auth/hx-' . $view . '.stub');
            $destinationPath = resource_path('views/auth/' . $view . '.blade.php');
            $this->makeDirectoryIfNeeded($destinationPath);
            $this->filesystem->copy($stubPath, $destinationPath);
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
     * Publish UI routes.
     */
    protected function publishRoutes()
    {
        $routes = $this->filesystem->get(app_path('Console/Commands/stubs/routes/auth.stub'));
        $baseRoutes = $this->filesystem->get(app_path('Console/Commands/stubs/routes/web-base.stub'));

        $this->filesystem->put(base_path('routes/web.php'), $baseRoutes);
        $this->filesystem->put(base_path('routes/auth.php'), $routes);
        $this->filesystem->append(base_path('routes/web.php'), "require __DIR__.'/auth.php';");

        $this->info("Routes updated");
    }

    /**
     * Update auth controller.
     */
    protected function updateControllers()
    {
        $filePaths = [
            app_path('Console/Commands/stubs/App/Http/Controllers/AuthControllerWithUiHalt.stub') => app_path('Http/Controllers/AuthController.php'),
            app_path('Console/Commands/stubs/App/Http/Controllers/ForgotPasswordControllerWithUiHalt.stub') => app_path('Http/Controllers/ForgotPasswordController.php'),
        ];

        foreach ($filePaths as $sourcePath => $destinationPath) {
            $controller = $this->filesystem->get($sourcePath);
            $this->filesystem->put($destinationPath, $controller);
        }
        $this->info("Controllers updated with UI");
    }

    /**
     * Update auth controller.
     */
    protected function updateMail()
    {
        $routes = $this->filesystem->get(app_path('Console/Commands/stubs/views/emails/password_reset_ui.blade.php'));
        $this->filesystem->put(resource_path('views/emails/password_reset.blade.php'), $routes);
        $this->info("Email template updated");
    }

    /**
     * Add base dashboard.
     */
    protected function addDashboard()
    {
        $stubPath = app_path('Console/Commands/stubs/views/admin/dashboard.stub');
        $destinationPath = resource_path('views/admin/dashboard.blade.php');
        $this->makeDirectoryIfNeeded($destinationPath);
        $this->filesystem->copy($stubPath, $destinationPath);
        $this->info("Admin dashboard created");
    }
}
