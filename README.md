Symblog
============

The "Symblog Application" is a simple application to show how to
develop applications following the [Symfony Best Practices][1].

Requirements
------------

Require the [usual Symfony application requirements][2].

  * PHP 7.2.9 or higher
  * Mysql 8.0.21 or higher
  * Some PHP extensions
  * Composer
  * Symfony CLI

Installation
------------

Clone the [repository][3] on your computer:

```bash
$ git clone https://github.com/mickaelbober/symblog.git symblog
```

Configuring your `.env` file:

```bash
DATABASE_URL=mysql://symblog:Azerty12@127.0.0.1:3306/symblog?serverVersion=8.0
```

Create the database:

```bash
$ cd symblog/
$ php bin/console doctrine:database:create
```

Apply migrations:

```bash
$ cd symblog/
$ php bin/console doctrine:migrations:migrate
```

Add some fixtures:

```bash
$ cd symblog/
$ php bin/console doctrine:fixture:load
```

Composer
------------

[Download Composer][4] to install the `composer` binary on your computer and install
dependencies to the `./vendor` directory.

```bash
$ cd symblog/
$ composer install
```

Symfony CLI
------------

[Download Symfony][5] to install the `symfony` binary on your computer. 

```bash
$ wget https://get.symfony.com/cli/installer -O - | bash
```

Run this command and access the application in your
browser at the given URL (<https://localhost:8000> by default):

```bash
$ cd symblog/
$ symfony server:start
```

If you don't have the Symfony binary installed, run `php -S localhost:8000 -t public/`
to use the built-in PHP web server or [configure a web server][6] like Nginx or
Apache to run the application.

[1]: https://symfony.com/doc/current/best_practices.html
[2]: https://symfony.com/doc/current/setup.html
[3]: https://github.com/mickaelbober/symblog
[4]: https://getcomposer.org/download/
[5]: https://symfony.com/download
[6]: https://symfony.com/doc/current/setup/web_server_configuration.html
