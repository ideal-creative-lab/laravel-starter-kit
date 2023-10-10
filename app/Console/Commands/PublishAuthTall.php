<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishAuthTall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:auth-tall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish UI for authentication pages using TALL';

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
        $this->copyViews();

        $this->createLiveWireComponents();

        $this->createLiveWireViews();

        $this->publishRoutes();

        $this->updateMail();
    }

    /**
     * Copy views to new path.
     *
     * @param array $views
     */
    protected function copyViews()
    {
        $views = [
            'register',
            'login',
            'forgot-password',
            'reset-password',
        ];

        foreach ($views as $view) {
            $stubPath = app_path('Console/Commands/stubs/views/auth/lw-' . $view . '.stub');
            $destinationPath = resource_path('views/auth/' . $view . '.blade.php');
            $this->makeDirectoryIfNeeded($destinationPath);
            $this->filesystem->copy($stubPath, $destinationPath);
            $this->info("$destinationPath created");
        }

        $this->info("Base views created");
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
     * Publish LiveWire components.
     */
    protected function createLiveWireComponents()
    {
        $components = [
            'Login',
            'PasswordForget',
            'PasswordReset',
            'Register',
        ];

        foreach ($components as $component) {
            $stubPath = app_path('Console/Commands/stubs/App/Livewire/' . $component . '.stub');
            $destinationPath = app_path('Livewire/' . $component . '.php');
            $this->makeDirectoryIfNeeded($destinationPath);
            $this->filesystem->copy($stubPath, $destinationPath);
            $this->info("$component created");
        }

        $this->info("LiveWire components created");
    }

    /**
     * Publish LiveWire views.
     */
    protected function createLiveWireViews()
    {
        $views = [
            'login',
            'password-forget',
            'password-reset',
            'register',
        ];

        foreach ($views as $view) {
            $stubPath = app_path('Console/Commands/stubs/views/livewire/' . $view . '.stub');
            $destinationPath = resource_path('views/livewire/' . $view . '.blade.php');
            $this->makeDirectoryIfNeeded($destinationPath);
            $this->filesystem->copy($stubPath, $destinationPath);
            $this->info("$destinationPath created");
        }

        $this->info("LiveWire views created");
    }

    /**
     * Publish UI routes.
     */
    protected function publishRoutes()
    {
        $this->filesystem->copy(app_path('Console/Commands/stubs/routes/lw-web.stub'), base_path('routes/web.php'));
        $this->info("Routes updated");
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
}
