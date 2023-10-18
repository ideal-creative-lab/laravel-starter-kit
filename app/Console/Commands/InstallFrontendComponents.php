<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use App\Enums\PackageManager;

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
     * The name of the package manager to use.
     *
     * @var PackageManager
     */
    protected $manager;

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
        $this->initPackageManager();
        $this->executeCommand($this->manager . ' add -D axios vite laravel-vite-plugin');

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
                $this->installInertiaAndSvelte();
                break;
            default:
                $this->error('Invalid choice. No additional frontend stack will be installed.');
        }

        $this->info('Adding CI GitHub Actions...');
        $this->executeCommand('cp ./.github/workflows/templates/build/' . $this->manager . '.yml .github/workflows/build.yml');

        $this->info('Frontend components installed.');
        $this->setPackageScripts();
    }

    /**
     * Install and initialize the package manager.
     *
     * @return void
     */
    protected function initPackageManager()
    {
        $this->manager = $this->choice(
            'Choose a package manager to install:',
            PackageManager::getValues(),
            0
        );

        $this->executeCommand($this->manager . ' init');
    }

    /**
     * Install Tailwind CSS, StandartJS and configure them.
     *
     * @return void
     */
    protected function installTailwindCssAndStandartJs()
    {
        $this->info('Installing Tailwind CSS dependencies...');
        $this->executeCommand($this->manager . ' add -D tailwindcss postcss autoprefixer');

        $this->info('Initializing Tailwind CSS configuration...');
        $runExecutablesCommand = PackageManager::getRunExecutablesCommand($this->manager);
        $this->executeCommand($runExecutablesCommand . ' tailwindcss init -p');

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

        $this->executeCommand($this->manager . ' add -D standard');

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

        $this->executeCommand($this->manager . ' add -D alpinejs htmx.org');

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
    protected function installInertiaAndSvelte()
    {
        $this->info('Installing InertiaJS and Svelte...');

        $this->executeCommand('composer require inertiajs/inertia-laravel');
        $this->info('Dependencies installed.');

        $this->addLayoutScripts('@inertiaHead');

        $this->executeCommand('php artisan inertia:middleware');
        $this->registerInertiaMiddleware();
        $this->info('Middleware configured.');

        $this->executeCommand($this->manager . ' add @inertiajs/svelte');
        $content = $this->filesystem->get(app_path('Console/Commands/stubs/inertiajs/js/app.stub'));
        $this->updateAppJs($content);
        $this->info('Scripts configured.');

        $this->updateInertiaRoutes();

        $this->createSvelteHomePage();

        $this->configureSvelteVite();

        $this->configureInertiaRoot();

        $this->info('InertiaJS and Svelte installed successfully.');
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
        $process->setTty(true)->run();

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
                if ($script == '@inertiaHead') {
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
     * Update the application's routes.
     */
    protected function updateInertiaRoutes()
    {
        $routesContent = $this->filesystem->get(app_path('Console/Commands/stubs/inertiajs/routes.stub'));
        $this->filesystem->put(base_path('routes/web.php'), $routesContent);

        $this->info('Routes added successfully.');
    }

    /**
     * Register Inertia Middleware.
     *
     * @return void
     */
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

    /**
     * Configure Inertia Root view.
     */
    protected function configureInertiaRoot()
    {
        $content = $this->filesystem->get(app_path('Http/Middleware/HandleInertiaRequests.php'));
        $content = str_replace('protected $rootView = \'app\';', 'protected $rootView = \'layouts.app\';', $content);
        $this->filesystem->put(app_path('Http/Middleware/HandleInertiaRequests.php'), $content);
        $this->info('Inertia root configured');
    }

    /**
     * Create svelte vite settings.
     */
    protected function configureSvelteVite()
    {
        $this->executeCommand($this->manager . ' add -D @sveltejs/vite-plugin-svelte');
        $this->filesystem->copy(app_path('Console/Commands/stubs/inertiajs/vite.stub'), base_path('vite.config.mjs'));
        $this->filesystem->delete(base_path('vite.config.js'));
        $this->info('Vite scripts configured');
    }

    /**
     * Create svelte home page.
     */
    protected function createSvelteHomePage()
    {
        $this->makeDirectoryIfNeeded(resource_path('js/Pages/Home.svelte'));
        $this->filesystem->copy(app_path('Console/Commands/stubs/inertiajs/js/Pages/Home.stub'), resource_path('js/Pages/Home.svelte'));
        $this->info("Homepage created.");
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
     * Set package scripts to run the server and build the app.
     * Provide instructions to add scripts to the package.json file, if the package manager is not NPM.
     *
     * @return void
     */
    protected function setPackageScripts()
    {
        if ($this->manager == PackageManager::NPM->value) {
            $this->executeCommand($this->manager . ' pkg set scripts.dev="vite"');
            $this->executeCommand($this->manager . ' pkg set scripts.build="vite build"');
            $this->info('Please run ' . $this->manager . ' run dev command to start the server.');
        } else {
            $isBun = $this->manager == PackageManager::NPM->value;

            $this->newLine(1);
            $this->comment('Add `dev` script to the package.json file to run the server:');
            $this->line($isBun ? 'bunx --bun vite' : 'vite');
            $this->newLine(1);
            $this->comment('And `build` script to build your app for production:');
            $this->line($isBun ? 'bunx --bun vite build' : 'vite build');
        }
    }
}
