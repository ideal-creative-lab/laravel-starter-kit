<h1 align="center">
    <a href="https://github.com/ideal-creative-lab/laravel-starter-kit#gh-dark-mode-only">
        <img src="./.github/assets/laravel-starter-kit-dark.svg" alt="Laravel Starter Kit">
    </a>
    <a href="https://github.com/ideal-creative-lab/laravel-starter-kit#gh-light-mode-only">
        <img src="./.github/assets/laravel-starter-kit-light.svg" alt="Laravel Starter Kit">
    </a>
</h1>

<p align="center">
    <i align="center">This is a starter kit for Laravel projects</i>
</p>

<h4 align="center">
    <img src="https://img.shields.io/badge/laravel-10.10-blueviolet" alt="Laravel Version">
    <img src="https://img.shields.io/badge/php-%3E=8.1-royalblue" alt="PHP Version">
    <img src="https://img.shields.io/badge/license-MIT-green" alt="License">
</h4>

## Introduction

The **Laravel Starter Kit** is a collection of tools and libraries designed to speed up the initial bootstrapping process for new Laravel projects. It includes several useful packages and tools that will help you get started quickly and easily developing web applications in Laravel.


**⚠️ Before contributing to the project, please, carefully read through this README document.**

## Included Packages

The **Laravel Starter Kit** includes the following packages and tools:

- [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper): Enhances the Laravel development experience with IDE support.
- [Laravel Lang](https://github.com/overtrue/laravel-lang): Simplifies language management in Laravel applications, ideal for multilingual projects.
- [Spatie's Ray](https://github.com/spatie/ray): A powerful real-time debugging tool that provides a new level of insight into Laravel applications.
- [Laravel Telescope](https://laravel.com/docs/10.x/telescope): A full-featured debugging and introspection tool for Laravel applications.
- [Laravel Pint](https://laravel.com/docs/10.x/pint): A opinionated PHP code style fixer for Laravel applications.
- [Pest](https://pestphp.com/): A modern and elegant testing framework for writing tests in PHP.


## ️ Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing
purposes.

### Prerequisites
To run the project, you need to install [PHP](https://www.php.net/manual/en/install.php) and dependency manager
[Composer](https://getcomposer.org) first.

### Installing
Follow these steps to install and set up the project:

1. Get the repo
    ```zsh
    git clone git@github.com:ideal-creative-lab/laravel-starter-kit.git
    cd laravel-starter-kit
    ```

2. Install PHP dependencies
    ```zsh
    composer install
    ```

3. Copy and edit the environment file
    ```zsh
    cp .env.example .env
    ```

4. Configure your database connection by editing the `.env` file using the Vim text editor
    ```zsh
    vim .env
    ```
    Make necessary changes to the environment variables and save the file.

5. Generate application key
    ```zsh
    php artisan key:generate
    ```

6. Install necessary [backend packages](https://github.com/ideal-creative-lab/laravel-starter-kit/wiki/How-to-install-backend-packages)
    ```zsh
    php artisan install:backend
    ``` 
   
7. Run migrations.
    ```zsh
    php artisan migrate
    ```

8. Install [frontend stack](https://github.com/ideal-creative-lab/laravel-starter-kit/wiki/How-to-install-frontend-components) (To avoid duplication, ensure that you run the command once with a specific stack)
    ```zsh
    php artisan install:frontend
    ```

9. (optional) If you'd like to install the [authentication component](https://github.com/ideal-creative-lab/laravel-starter-kit/wiki/How-to-install-the-authentication-component) please run following command
    ```zsh
    php artisan install:auth
    ```

10. Start the server
    ```zsh
    php artisan serve
    ```

You should see a success message with host and port of the running server, by default it's `http://127.0.0.1:8000`.

_For hot reloading you can use the Package Manager that you chose on step 8_

### CI/CD

You can use [Auto Deploy Github Action](/.github/workflows/deploy.yml) to deploy your project to [Ploi](https://ploi.io). It will be triggered on push to `main` branch, you can change it, if you want. The following steps will help you get started:

1. Add your app to the Ploi server.
2. Open your app in the control panel.
3. Press the `Repository` tab.
4. Scroll down to the `Deploy Webhook URL` section.
5. Copy the webhook URL.
6. Add the link to the github secrets of the app repository.

### Server Requirements

* **PHP** >= 8.1
* **composer**
* Ctype PHP Extension
* cURL PHP Extension
* DOM PHP Extension
* Fileinfo PHP Extension
* Filter PHP Extension
* Hash PHP Extension
* Mbstring PHP Extension
* OpenSSL PHP Extension
* PCRE PHP Extension
* PDO PHP Extension
* Session PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* **MariaDB**


### Example Nginx Configuration
```
server {
    listen 80;
    listen [::]:80;
    server_name example.com;
    root /srv/example.com/public;
 
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
 
    index index.php;
 
    charset utf-8;
 
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
 
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
 
    error_page 404 /index.php;
 
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
 
    location ~ /\.(?!well-known).* {
        deny all;
    }
} 
```

### Laravel base config setup script
You can skip install steps 1-5 by using ./setup.sh script. This script is a simple setup tool for a Laravel application. It copies the .env.example file to create an .env configuration file. It then installs Composer dependencies, generates an application key, and interacts with the user to set up a MySQL database and user. Finally, it updates the application's configuration with the provided database information and caches the configuration settings for improved performance.

## Contribute
See [CONTRIBUTING.md](CONTRIBUTING.md) for ways to get started.
