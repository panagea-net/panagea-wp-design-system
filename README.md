# Panagea Design System on Wordpress
Implementation of Panagea designs as a plugin for wordpress.

See a summary explanation fo the approach taken by this plugin in order to implement reusable blocks across multiple corporate WP instances in the [approaches](./docs/components.md)

## Develop new components / blocks
A more or less detailed guide on how to implement new blocks: [how-to](./docs/components-how-to.md)

In order for consumer Wordpress instances to install update your new components, plugin must be [published](./docs/publish.md)

## Utils
Some custom commands and utils scripts have been developed to be able to reliably deploy to new environments or to ease common tasks. See [utils section](./docs/utils.md)

## Install panagea-core via Composer (consumer site)

Instruction on how-to install the panagea-core plugin on a consumer Worpress site cna be [found here](./docs/install-plugin.md)

## Set up development environment
In order to setup local environment for development:
1. run `composer install` to install dependencies and wp itself
1. rename `env.sample` to `.env` and adjust values in it
1. run `scripts/setup-salt.sh` from the root folder to generate salts values
1. run command to install core wordpress:
    ```
    wp -- core install \
    --url="http://localhost:8080" \
    --title="Panagea WP Design System" \
    --admin_user="admin" \
    --admin_password="admin" \
    --admin_email="you@example.com" \
    --skip-email
    ```
    (use `composer run wp -- core ...` if not installed globally )
1. ensure blocksy theme is active:
    ```
    composer wp theme activate blocksy
    composer wp plugin activate blocksy-companion
    ```
1. ensure stackable plugin is also active: `composer wp plugin activate stackable-ultimate-gutenberg-blocks`
1. ensure panagea plugin is active: `composer wp plugin activate panagea-core`
1. sync editor preview `composer wp panagea-core blocksy-defaults -- --force`
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


## ToDo
- [ ] lock down the design so content editors on the final sites can change the text of the patterns but cannot break the layout
