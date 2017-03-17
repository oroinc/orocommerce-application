OroCommerce Sample Application
==============================

What is OroCommerce?
--------------------

OroCommerce is an open-source Business to Business Commerce application built with flexibility in mind. It can be customized and extended to fit any B2B commerce needs.
You can find out more about OroCommerce at [www.orocommerce.com](https://www.orocommerce.com/).

Requirements
------------

OroCommerce is a Symfony-based application with the following requirements:

* PHP 5.6 or above
* Command line interface
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

Installation Instructions
-------------------------

This OroCommerce application uses [Composer][1] to manage its dependencies, this is the recommended way to install the application.

 - If you do not have Composer yet, download it and follow the instructions on
http://getcomposer.org/ website or simply run the following command:

```bash
curl -s https://getcomposer.org/installer | php
```

OroCommerce uses [fxpio/composer-asset-plugin][2] to manage dependencies on some third-party asset libraries. The plugin has to be installed globally (per user):
 
```bash
    composer global require "fxp/composer-asset-plugin:dev-master"
```
**Note:** This is a temporary solution.  After plugin version 1.3.0 is released, the requirement will be changed to version 1.3
([see related issue](https://github.com/fxpio/composer-asset-plugin/issues/277#issuecomment-282745055)).

- Clone https://github.com/orocommerce/orocommerce-application.git repository with

```bash
git clone --recursive https://github.com/orocommerce/orocommerce-application.git
```

- Make sure that you have [NodeJS][3] installed

- Install project dependencies with Composer. If the installation process is too slow, you can use the "--prefer-dist" option.
  Run composer installation:

```bash
php composer.phar install --prefer-dist
```

- Create a database with the name specified in the previous step (the default name is "b2b_dev").

- On some systems it might be necessary to temporarily increase memory_limit setting to 1 GB in php.ini configuration file for the duration of the installation process:

```ini
memory_limit=1024M
```

**Note:** After the installation is finished the memory_limit configuration can be changed back to the recommended value (512 MB or more).

- Install the application and create the admin user with the web installation wizard by opening install.php in the browser or running the following CLI command:

```bash
php app/console oro:install --env=prod
```

**Note:** If the installation process times out, add the `--timeout=0` argument to the oro:install command.

- Enable WebSocket messaging

```bash
php app/console clank:server --env=prod
```

- Configure crontab or scheduled tasks execution to run the command below every minute:

```bash
php app/console oro:cron --env=prod
```

- Launch the message queue processing:

```bash
php app/console oro:message-queue:consume --env=prod
```

**Note:** ``app/console`` is a path from the project root folder. Please make sure you are using the full path for crontab configuration if you are running console command from a different location.

Installation Notes
------------------

Installed PHP Accelerators must be compatible with Symfony and Doctrine (support DOCBLOCKs).

Note that the port used by the WebSocket server must be open in firewall for outgoing/incoming connections.

### MySQL Configuration

Using MySQL 5.6 on HDD is potentially risky as it can result in performance issues.

Recommended configuration for this case:

```ini
innodb_file_per_table = 0
```

Ensure that the timeout has a default value

```ini
wait_timeout = 28800
```

See [Optimizing InnoDB Disk I/O][4] for more information.

The default MySQL character set utf8 uses a maximum of three bytes per character and contains only BMP characters. The [utf8mb4][5] character set uses a maximum of four bytes per character and supports supplemental characters (e.g. emojis). It is [recommended][6] to use utf8mb4 character set in your app/config.yml:

```yaml
doctrine:
    dbal:
        connections:
            default:
                driver:       "%database_driver%"
                host:         "%database_host%"
                port:         "%database_port%"
                dbname:       "%database_name%"
                user:         "%database_user%"
                password:     "%database_password%"
                charset:      utf8mb4
                default_table_options:
                    charset: utf8mb4
                    collate: utf8mb4_unicode_ci
                    row_format: dynamic
```

Using utf8mb4 might have side effects. MySQL indexes have a default limit of 767 bytes, so any indexed fields with varchar(255) will fail when inserted, because utf8mb4 can have 4 bytes per character (255 * 4 = 1020 bytes), thus the longest data can be 191 (191 * 4 = 764 < 767). To be able to use any 4 byte charset all indexed varchars should be at most varchar(191). To overcome the index size issue the server can be configured to have large index size by enabling [sysvar_innodb_large_prefix][7]. However, innodb_large_prefix requires some additional settings to work:

- `innodb_default_row_format=DYNAMIC` (you may also enable it per connection as in the config above)
- `innodb_file_format=Barracuda`
- `innodb_file_per_table=1` (see above performance issues with this setting)

More details about this issue can be found [here][8]

### Web Server Configuration

The OroCommerce sample application is based on the Symfony standard application, so the web server configuration recommendations are the [same][9].

##Using Redis for application caching

To use Redis for application caching, follow the corresponding [configuration instructions][10]

[1]: http://getcomposer.org/
[2]: https://github.com/fxpio/composer-asset-plugin/blob/master/Resources/doc/index.md
[3]: https://github.com/joyent/node/wiki/Installing-Node.js-via-package-manager
[4]: http://dev.mysql.com/doc/refman/5.6/en/optimizing-innodb-diskio.html
[5]: https://dev.mysql.com/doc/refman/5.6/en/charset-unicode-utf8mb4.html
[6]: http://symfony.com/doc/current/doctrine.html#configuring-the-database
[7]: http://dev.mysql.com/doc/refman/5.6/en/innodb-parameters.html#sysvar_innodb_large_prefix
[8]: https://mathiasbynens.be/notes/mysql-utf8mb4#utf8-to-utf8mb4
[9]: http://symfony.com/doc/2.8/setup/web_server_configuration.html
[10]: https://github.com/orocrm/redis-config#configuration