<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<h1 align="center">
    <a href="https://github.com/ideal-creative-lab/laravel-starter-kit">
        Laravel Starter Kit
    </a>
</h1>

<p align="center">
    <i align="center">This is a starter kit for Laravel projects</i>
</p>

<h4 align="center">
    <img src="https://img.shields.io/badge/release-v1.0.0-blue" alt="Project Version">
    <img src="https://img.shields.io/badge/laravel-10.8-blueviolet" alt="Laravel Version">
    <img src="https://img.shields.io/badge/php-%3E=8.1-royalblue" alt="PHP Version">
    <img src="https://img.shields.io/badge/platform-*nix-lightgrey" alt="Platform">
    <img src="https://img.shields.io/badge/license-proprietary-green" alt="License">
</h4>

## Introduction

**Laravel Starter Kit** is a collection of tools and libraries to make developing your Laravel projects easier. This set includes several useful packages and tools that will help you quickly get started and easily develop web applications in Laravel.


**⚠️ Before contributing to the project, please, carefully read through this README document.**

## Included Packages

Laravel Starter Kit includes the following packages and tools:

- [Laravel IDE Helper](https://github.com/barryvdh/laravel-ide-helper): Enhances your Laravel development experience with IDE support.

- [Laravel Lang](https://github.com/overtrue/laravel-lang): Simplifies language management in your Laravel applications, ideal for multilingual projects.

- [Spatie's Ray](https://github.com/spatie/ray): A powerful real-time debugging tool that provides a new level of insight into your application.

- [Laravel Telescope](https://laravel.com/docs/10.x/telescope): A full-featured debugging and introspection tool for your Laravel applications.

- [Laravel Pint](https://laravel.com/docs/10.x/pint): A opinionated PHP code style fixer for your application.

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
