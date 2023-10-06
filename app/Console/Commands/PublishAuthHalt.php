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
    protected $signature = 'publish:auth-halt';

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
    }

    /**
     * Copy views to new path.
     *
     * @param array $views
     */
    protected function copyViews($views)
    {
        foreach ($views as $view) {
            $stubPath = app_path('Console/Commands/stubs/views/auth/' . $view . '.stub');
            $destinationPath = resource_path('views/auth/' . $view . '.blade.php');
            $this->makeDirectoryIfNeeded($destinationPath);
            $this->filesystem->copy($stubPath, $destinationPath);
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
}
