# Swagvalidator
Validates data against a Swagger schema

## How to Install
```
composer require --dev mlambley/swagvalidator
```

## What is Swagger?
Swagger 2.0 (aka Open API 2.0) defines the structure of your API, including end points and the structure of input and output data.
See [their website](https://swagger.io/) for more information.

## What is Swagvalidator?
If you have an existing Swagger 2.0 specification, you can use it to validate data coming in or out of your API using this tool.
This library fully takes into account the features of the [Swagger 2.0 specification](https://swagger.io/docs/specification/2-0/basic-structure/).

## Requirements
None. This is a pure PHP tool with no dependencies and will work with PHP 5.6 onwards. 

## Usage
```php
use Mlambley\Swagvalidator\Validator\Validator;
use Mlambley\Swagvalidator\Exception\ValidationException;

$response = $this->getApi("your/path");
$json = (string)$response->getBody();
$data = json_decode($json);
$schema = json_decode(file_get_contents(__DIR__ . '/swagger.json'));

try {
    (new Validator())
        ->validate($schema->paths->{"your/path"}->get->responses->{"200"}->schema, $data);
} catch (ValidationException $e) {
    //dd($e->getMessage());
}
```

## Issues?
Log a [github issue](https://github.com/mlambley/swagvalidator/issues). Your assistance is appreciated.
