# Panagea Design System on Wordpress
Implementation of Panagea designs as a plugin for wordpress

## Development
In order to setup local environment for development:
1. run `composer install` to install dependencies and wp itself
1. rename `env.sample` to `.env` and adjust values in it
1. run `scripts/setup-salt.sh` from the root folder to generate salts values
1. run command to install core wordpress:
    ```
        wp core install \
        --url="http://localhost:8080" \
        --title="Panagea WP Design System" \
        --admin_user="admin" \
        --admin_password="admin" \
        --admin_email="you@example.com" \
        --skip-email
    ```
    (use `composer run wp -- core ...` if not installed globally )
1. use `php -S 127.0.0.1:8080 -t web` from root folder to start th site

### Requirements on MacOsX
```
brew install php
brew install composer
brew install mysql
```

To start mysql now and restart at login:
`brew services start mysql`

Or, if you don't want/need a background service you can just run:
`/opt/homebrew/opt/mysql/bin/mysqld_safe --datadir\=/opt/homebrew/var/mysql`

Crate a dev user for the project:
```
$> mysql -u root
mysql> create database panaga_wp_ds;
mysql> create user 'localuser'@'localhost' identified by 'localuser';
mysql> grant all on panaga_wp_ds.* to 'localuser'@'localhost';
mysql> exit

## Test new user access
$> mysql -u localuser -p
mysql> SHOW DATABASES;
```