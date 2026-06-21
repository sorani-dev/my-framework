# my-framework

Simple Framework based on Grafikart's POO  by example Course

## Install app

Install app with a php version < 8 (required).

Install dependencies with `composer install`

Create a Mysql database named `pratiquepoo`.

Migrate database tables and seed the database with:

- Unix-like environment: `php ./vendor/bin/phinx migrate`
- on Windows: `php .\vendor\bin\phinx migrate`

Then start server with the command below for development or production.

## Problems while running the app

For PHP-DI: an error occurs when the container is compiled. In the CompiledContainer.php
replace

```php
$object = new Sorani\\SimpleFramework\\Middleware\\CsrfMiddleware(\$this->get35(), 50, '_csrf', 'csrf');
return $object;
```

with

```php
$session = $this->get35();
$object = new Sorani\SimpleFramework\Middleware\CsrfMiddleware($session, 50, '_csrf', 'csrf');
return $object;
```

and the Notice  about the variable passed by reference should go away.

N.B.: Replace `$this->get35()` with the name of the method used in the first argument of the `Sorani\SimpleFramework\Middleware\CsrfMiddleware` function.

## Environment modes

### For production

```php -S localhost:8001 -d display_errors=1 -d opcache.enable_cli=1 -t public```

### For development

- on Unix-like environment: ```ENV=dev php -S localhost:8001 -d display_errors=1 -d opcache.enable_cli=1 -t public```
- on Windows: ```SET ENV=dev && php -S localhost:8001 -d display_errors=1 -d opcache.enable_cli=1 -t public```
