OroCommerce Sample Application
==============================

What is OroCommerce?
--------------------

OroCommerce is an open-source Business to Business Commerce application built with flexibility in mind.
OroCommerce can be customized and extended to fit any B2B commerce needs.

## Requirements

OroCommerce is a Symfony 2 based application with the following requirements:

* PHP 5.5.9 or above
* PHP 5.5.9 or above with command line interface
* PHP Extensions
    * GD
    * Mcrypt
    * JSON
    * ctype
    * Tokenizer
    * SimpleXML
    * PCRE
    * ICU
* MySQL 5.1 or above
* PostgreSQL 9.1 or above

## Installation instructions

### Using Composer

This OroCommerce application uses both [Composer][1] and [Git Submodules][5] to manage its dependencies, this is the recommended way to install the application.

 - If you don't have Composer yet, download it and follow the instructions on
http://getcomposer.org/ or just run the following command:

```bash
curl -s https://getcomposer.org/installer | php
```

- Clone https://github.com/orocommerce/orocommerce-application.git repository with

```bash
git clone --recursive https://github.com/orocommerce/orocommerce-application.git
```

- Make sure that you have [NodeJS][2] installed

- Install project dependencies with composer. If installation process seems too slow you can use "--prefer-dist" option.
  Run composer installation:

```bash
php composer.phar install --prefer-dist
```

- Create the database with the name specified on previous step (default name is "b2b_dev").

- Install application and admin user with Installation Wizard by opening install.php in the browser or from CLI:

```bash
php app/console oro:install --env prod
```
**Note:** If the installation process times out, add the `--timeout=0` argument to the command.

- Enable WebSockets messaging

```bash
php app/console clank:server --env prod
```

- Configure crontab or scheduled tasks execution to run the command below every minute:

```bash
php app/console oro:cron --env prod
```

**Note:** ``app/console`` is a path from project root folder. Please make sure you are using full path for crontab configuration or if you running console command from other location.

## Installation notes

Installed PHP Accelerators must be compatible with Symfony and Doctrine (support DOCBLOCKs)

Note that the port used in Websocket must be open in firewall for outgoing/incoming connections

Using MySQL 5.6 on HDD is potentially risky because of performance issues

Recommended configuration for this case:

    innodb_file_per_table = 0

And ensure that timeout has default value

    wait_timeout = 28800

See [Optimizing InnoDB Disk I/O][3] for more

## PostgreSQL installation notes

You need to load `uuid-ossp` extension for proper doctrine's `guid` type handling.
Log into database and run sql query:

```
CREATE EXTENSION "uuid-ossp";
```

## Web Server Configuration

The OroCommerce sample application is based on the Symfony standard application so web server configuration recommendations are the [same][4].

[1]: http://getcomposer.org/
[2]: https://github.com/joyent/node/wiki/Installing-Node.js-via-package-manager
[3]: http://dev.mysql.com/doc/refman/5.6/en/optimizing-innodb-diskio.html
[4]: http://symfony.com/doc/2.3/cookbook/configuration/web_server_configuration.html
[5]: https://git-scm.com/book/en/v2/Git-Tools-Submodules
