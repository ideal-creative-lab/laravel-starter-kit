<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<h1 align="center">
    <a href="https://moneysherlock.com/#gh-light-mode-only">
        Laravel Starter Kit
    </a>
</h1>

<p align="center">
    <i align="center">This is a starter kit for Laravel projects</i>
</p>

<h4 align="center">
    <img src="https://img.shields.io/badge/release-v0.1.0-blue" alt="Project Version">
    <img src="https://img.shields.io/badge/laravel-10.10-blueviolet" alt="Laravel Version">
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

## ️Getting Started

### Installing

Follow these steps to install and set up the project:

1. Clone the repository from GitHub:
   ```
   git clone git@github.com:ideal-creative-lab/laravel-starter-kit.git
   ```

2. Navigate to the project directory:
   ```
   cd laravel-starter-kit
   ```

3. Install the project dependencies using Composer:
   ```
   composer install
   ```

4. Copy the `.env.example` file to `.env`:
   ```
   cp .env.example .env
   ```

5. Open the `.env` file in a text editor (e.g., vim) and update the necessary configuration values.

6. Generate an application key:
   ```
   php artisan key:generate
   ```

7. For using <a href='https://laravel.com/docs/10.x/telescope'>laravel telescope</a> run following command:
   ```
   php artisan telescope:install
   ```
   
8. Install necessary backend packages:
   ``` 
   php artisan install:backend
   ``` 

9. Run migration:
   ```
   php artisan migrate
   ```

10. Start the development server:
   ```
   php artisan serve
   ```

11. Install frontend stack:
   ```
   php artisan install:frontend
   ```

12. Build the project assets:
   ```
   npm run dev
   ```

You can now access the project by visiting the URL provided by the development server.

## Contribute

See [CONTRIBUTING.md](CONTRIBUTING.md) for ways to get started.
