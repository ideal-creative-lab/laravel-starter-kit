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

        $this->installTailwindCssAndStandartJs();

        $stack = $this->choice('Choose a frontend stack to install:', [
            'None',
            'AlpineJS + HTMX',
            'LiveWire',
            'InertiaJS + Svelte',
        ], 0, 3);

        switch ($stack) {
            case 'None':
                $this->info('No additional frontend stack selected.');
                break;
            case 'AlpineJS + HTMX':
                $this->installAlpineAndHTMX();
                break;
            case 'LiveWire':
                $this->installLiveWire();
                break;
            case 'InertiaJS + Svelte':
                //TODO update stack installation
                $this->installInertiaAndSvelte();
                break;
            default:
                $this->error('Invalid choice. No additional frontend stack will be installed.');
        }

        $this->info('Frontend components installed. Please run npm run dev command to start.');
    }

    /**
     * Install Tailwind CSS, StandartJS and configure them.
     *
     * @return void
     */
    protected function installTailwindCssAndStandartJs()
    {
        $this->info('Installing Tailwind CSS dependencies...');
        $this->executeCommand('npm install -D tailwindcss postcss autoprefixer');

        $this->info('Initializing Tailwind CSS configuration...');
        $this->executeCommand('npx tailwindcss init -p');

        $this->info('Updating tailwind.config.js...');
        $this->replaceTailwindConfig();

        $this->info('Updating app.css...');

        $content = /** @lang CSS */
            <<<EOL
            @tailwind base;
            @tailwind components;
            @tailwind utilities;
            EOL;

        $this->updateAppCss($content);

        $this->info('Tailwind CSS installed and configured successfully.');

        $this->info('Installing Standart.JS dependencies...');

        $this->executeCommand('npm install standard --save-dev');

        $this->info('Standart.JS installed and configured successfully.');

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
     * Install LiveWire.
     *
     * @return void
     */
    protected function installLiveWire()
    {
        $this->info('Installing LiveWire...');

        $this->executeCommand('composer require livewire/livewire');

        $this->addLayoutScripts('@livewireScripts');

        $this->info('LiveWire installed successfully.');
    }

    /**
     * Install InertiaJS and Svelte.
     *
     * @return void
     */
    //TODO update stack installation
    protected function installInertiaAndSvelte()
    {
        $this->info('Installing InertiaJS and Svelte...');

        $this->executeCommand('composer require inertiajs/inertia-laravel');

        $this->addLayoutScripts('@inertiaHead');

        $this->executeCommand('php artisan inertia:middleware');

        $this->registerInertiaMiddleware();

        $this->executeCommand('npm install @inertiajs/svelte');

        $content =
            <<<'EOL'
            import { createInertiaApp } from '@inertiajs/svelte'

            createInertiaApp({
              resolve: name => {
                const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true })
                return pages[`./Pages/${name}.svelte`]
              },
              setup({ el, App, props }) {
                new App({ target: el, props })
              },
            })
            EOL;

        $this->updateAppJs($content);

        $this->info('InertiaJS and Svelte installed successfully. Please follow instruction to run https://inertiajs.com/client-side-setup?frontend=svelte&backend=laravel!');
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
     * Add scripts to the app layout file.
     *
     * @return void
     */
    protected function addLayoutScripts($script)
    {
        $layoutFile = resource_path('views/layouts/app.blade.php');

        if ($this->filesystem->exists($layoutFile)) {
            $content = $this->filesystem->get($layoutFile);
            if (strpos($content, $script) === false) {
                $content = str_replace('</head>', $script . "\n\t</head>", $content);
                if ($script == '@livewireScripts') {
                    $content = str_replace('</body>', $script . "\n\t</body>", $content);
                } else {
                    $content = str_replace("@yield('content')", "@inertia", $content);
                }
                $this->filesystem->put($layoutFile, $content);

                $this->info('Scripts added to the app layout.');
            } else {
                $this->info('Scripts is already present in the app layout.');
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
    protected function updateAppCss($content)
    {
        $appCssFile = resource_path('css/app.css');

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

    /**
     * Register Inertia Middleware.
     *
     * @return void
     */
    //TODO update stack installation
    protected function registerInertiaMiddleware()
    {
        $kernelFile = app_path('Http/Kernel.php');

        if (!$this->filesystem->exists($kernelFile)) {
            $this->error('Kernel file not found.');
            return;
        }

        $kernelContent = $this->filesystem->get($kernelFile);

        if (strpos($kernelContent, 'HandleInertiaRequests::class') !== false) {
            $this->info('HandleInertiaRequests middleware is already registered in Kernel.');
            return;
        }

        $kernelContent = str_replace(
            "'web' => [",
            "'web' => [\n            \\App\\Http\\Middleware\\HandleInertiaRequests::class,",
            $kernelContent
        );

        $this->filesystem->put($kernelFile, $kernelContent);

        $this->info('HandleInertiaRequests middleware registered in Kernel.');
    }

}