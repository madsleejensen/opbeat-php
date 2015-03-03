# Opbeat PHP agent

Simple library for communicating with the Opbeat services. 

### Opbeat client
Instantiate a instance of the Opbeat_Client, and provide the organization_id, application_id, secret_token provided by opbeat. 

```php
$client = new Opbeat_Client($organization_id, $application_id, $secret_token);
```

### Send errors

```php
# example of logging a exception
$exception = new Exception("hello world");
$client->captureException($exception);

# example of logging a simple error message.
$client->captureMessage("This is a simple error", Opbeat_Message::LEVEL_DEBUG);

# example of logging a SQL statement.
$query_string = "SELECT * FROM users";
$engine = "MySQL";
$client->captureQuery($query_string, $engine);
```

### Handler
You can decide to use the Opbeat_Handler to register for errors and automatically forward them to the a client.

```php
$handler = new Opbeat_Handler();
$handler->addClient($client);
$handler->registerErrorHandler();
$handler->registerExceptionHandler();

// example of an uncaught exception.
function blabla() {
    throw new Exception('123213123123');
}

blabla();

```