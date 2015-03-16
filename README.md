# Opbeat PHP agent

Simple library for communicating with the Opbeat services.


## Installation

Use Composer to install this library:

```bash
composer require madsleejensen/opbeat-php
```

## Configuration

You are required to provide a configuration handler which implements [Illuminate/Contracts/Config/Repository](https://github.com/illuminate/contracts/blob/master/Config/Repository.php).

Pass an instance of this config handler when initializing the `Client`.

`Opbeat\Client` will require the following settings:

- `opbeat.organization_id` (string)
- `opbeat.app_id` (string)
- `opbeat.access_token` (string)

The following settings are optional and have a default value:

- `opbeat.enable_exception_handler` (boolean, default: true)
- `opbeat.enable_error_handler` (boolean, default: true)


## Enable Client

When you've set up your configuration, simply create an instance of `Opbeat\Client`. Unless you set `enable_exception_handler` or `enable_error_handler` to `false`, the client will automatically register a handler for both.

```php
$client = new \Opbeat\Client($config);
```


## Manually Catch Exception

You can also catch exceptions manually. `Opbeat\Client` exposes a `catchException` method for this:

```php
public void catchException ( Exception $exception )
```


## Contributing

If you experience any issues or have ideas for improvements, please open a pull request or an issue.

Pull requests must adhere to the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) and naming scheme of classes must adhere to [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) with `Opbeat` as namespace prefix.
