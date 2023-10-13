#!/bin/bash
# Set up the .env file
cp .env.example .env

# Install Composer dependencies
composer install

# Generate the application key
php artisan key:generate

# Prompt the user for MySQL database information
read -p "Enter MySQL database name: " db_name
read -p "Enter MySQL username: " db_user
read -p "Enter MySQL password: " db_password

# Set up the MySQL database and user
mysql -u root -p$db_password -e "CREATE DATABASE $db_name;"
mysql -u root -p$db_password -e "GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'localhost' IDENTIFIED BY '$db_password';"

# Update the application's configuration
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/g" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/g" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_password/g" .env

# Cache the configuration
php artisan config:cache

