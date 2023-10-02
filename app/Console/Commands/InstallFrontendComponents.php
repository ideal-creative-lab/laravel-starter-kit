<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class InstallFrontendComponents extends Command
{
    protected $signature = 'install:frontend';

    protected $description = 'Install Tailwind CSS and selected frontend components.';

    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle()
    {
        $this->info('Installing Tailwind CSS...');

        $this->installTailwindCss();

        $this->info('Frontend components installed successfully.');
    }

    protected function installTailwindCss()
    {
        $this->info('Installing Tailwind CSS dependencies...');
        $this->executeCommand('npm install -D tailwindcss postcss autoprefixer');

        $this->info('Initializing Tailwind CSS configuration...');
        $this->executeCommand('npx tailwindcss init -p');

        $this->info('Updating tailwind.config.js...');
        $this->replaceTailwindConfig();

        $this->info('Updating app.css...');
        $this->updateAppCss();

        $this->info('Tailwind CSS installed and configured successfully.');
    }

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

    protected function replaceTailwindConfig()
    {
        $configFile = base_path('tailwind.config.js');

        $content = /** @lang JavaScript */
            <<<EOL
        module.exports = {
            content: [
                "./resources/**/*.blade.php",
                "./resources/**/*.js",
                "./resources/**/*.vue",
            ],
            theme: {
                extend: {},
            },
            plugins: [],
        }
        EOL;

        $this->filesystem->put($configFile, $content);
    }

    protected function updateAppCss()
    {
        $appCssFile = resource_path('css/app.css');

        $content = /** @lang CSS */
            <<<EOL
        @tailwind base;
        @tailwind components;
        @tailwind utilities;
        EOL;

        $this->filesystem->put($appCssFile, $content);
    }
}
