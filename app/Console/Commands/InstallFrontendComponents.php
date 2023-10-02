<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class InstallFrontendComponents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:frontend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Tailwind CSS and selected frontend components.';

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
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Installing Tailwind CSS...');

        $this->installTailwindCss();

        $stack = $this->choice('Choose a frontend stack to install:', [
            'None',
            'AlpineJS + HTMX',
            'HTMX + LiveWire',
            'InertiaJS + Svelte',
        ], 0, 3);

        switch ($stack) {
            case 'None':
                $this->info('No additional frontend stack selected.');
                break;
            case 'AlpineJS + HTMX':
                $this->installAlpineAndHTMX();
                break;
            case 'HTMX + LiveWire':
                $this->installHTMXAndLiveWire();
                break;
            case 'InertiaJS + Svelte':
                //$this->installInertiaAndSvelte();
                break;
            default:
                $this->error('Invalid choice. No additional frontend stack will be installed.');
        }


        $this->info('Frontend components installed.');
    }

    /**
     * Install Tailwind CSS and configure it.
     *
     * @return void
     */
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

    /**
     * Install AlpineJS and HTMX.
     *
     * @return void
     */
    protected function installAlpineAndHTMX()
    {
        $this->info('Installing AlpineJS and HTMX...');

        $this->executeCommand('npm install -D alpinejs htmx.org');

        $content = /** @lang JavaScript */
            <<<EOL
        import Alpine from 'alpinejs'
        import Htmx from 'htmx.org'

        window.Alpine = Alpine
        window.Htmx = Htmx

        Alpine.start()
        EOL;

        $this->updateAppJs($content);

        $this->info('AlpineJS and HTMX installed successfully.');
    }

    /**
     * Install HTMX and LiveWire.
     *
     * @return void
     */
    protected function installHTMXAndLiveWire()
    {
        $this->info('Installing HTMX and LiveWire...');

        $this->executeCommand('npm install -D htmx.org');


        $content = /** @lang JavaScript */
            <<<EOL
        import Htmx from 'htmx.org'

        window.Htmx = Htmx

        EOL;

        $this->updateAppJs($content);

        $this->executeCommand('composer require livewire/livewire');

        $this->addLiveWireScripts();

        $this->info('HTMX and LiveWire installed successfully.');
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

    /**
     * Replace the content of the Tailwind CSS configuration file.
     *
     * @return void
     */
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

    /**
     * Add LiveWire scripts to the app layout file.
     *
     * @return void
     */
    protected function addLiveWireScripts()
    {
        $layoutFile = resource_path('views/layouts/app.blade.php');

        if ($this->filesystem->exists($layoutFile)) {
            $content = $this->filesystem->get($layoutFile);
            if (strpos($content, '@livewireScripts') === false) {
                $content = str_replace('</head>', "@livewireScripts\n\t</head>", $content);
                $content = str_replace('</body>', "@livewireScripts\n\t</body>", $content);

                $this->filesystem->put($layoutFile, $content);

                $this->info('LiveWire scripts added to the app layout.');
            } else {
                $this->info('LiveWire scripts is already present in the app layout.');
            }
        } else {
            $this->error('App layout file not found.');
        }
    }

    /**
     * Update the content of the app.css file with Tailwind CSS directives.
     *
     * @return void
     */
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

    /**
     * Append content to the app.js file.
     *
     * @param string $content The content to append to the app.js file.
     *
     * @return void
     */
    protected function updateAppJs($content)
    {
        $appJsFile = resource_path('js/app.js');

        $this->filesystem->append($appJsFile, $content);
    }
}
