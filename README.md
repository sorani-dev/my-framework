# my-framework
Simple Framework based on Grafikart's POO  by example Course

Environment modes:
For production:
```php -S localhost:8001 -d display_errors=1 -d opcache.enable_cli=1 -t public```

For PHP-DI: an errors occurs when the container is compiled. In the CompiledContainer.php
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

For development:

- on Unix-like environment: ```ENV=dev php -S localhost:8001 -d display_errors=1 -d opcache.enable_cli=1 -t public```
- on Windows: ```SET ENV=dev && php -S localhost:8001 -d display_errors=1 -d opcache.enable_cli=1 -t public```
