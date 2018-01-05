# Valit

[![Build Status](https://travis-ci.org/moccalotto/valit.svg)](https://travis-ci.org/moccalotto/valit)

Validate variables using a fluent syntax.

## Installation

Execute the following composer command in your terminal:

```bash
composer require moccalotto/valit
```

## Usage

```php
Ensure::that($age)
    ->isNumeric()
    ->isGreaterThanOrEqual(18)
    ->isLowerThanOrEqual(75);
```

The example above uses the `Valit\Ensure` facade to validate a variable.
If any of the assertions fail, a `Valit\Exceptions\InvalidValueException`
is thrown.

### Validating values
You can determine if a variable passes all your criteria by using the
`valid` method.

Conversely you can use the `invalid` method to check if one or more
checks did not pass.

```php
use Valit\Check;

$age = 42; // the variable to validate

$validation = Check::that($age)
    ->isInt()                   // Success
    ->isGreaterThanOrEqual(42)  // Success
    ->isLessThan(100);          // Success

$validation->throwExceptionIfNotSuccessful();

// no exception cast, we continue
```

See also:
* [Using the »Ensure« facade](examples/01-intro.php)
* [Status Messages](examples/02-status-messages.php)
* [Error Messags](examples/03-error-messages.php)
* [InvalidValueException](examples/10-invalid-value-exceptions.php)


### Validating containers

You can easily test an entire array, for instance posted fields or a json response,
in a structured and well defined way like the example below:

```php
$checks = Check::that($input)->contains([
    'name'      => 'string & shorterThan(100)',
    'email'     => 'email & shorterThan(255)',
    'address'   => 'string',
    'age'       => 'naturalNumber & greaterThanOrEqual(18) & lowerThanOrEqual(100)',

    'orderLines'                => 'conventionalArray',
    'orderLines/*'              => 'associative',
    'orderLines/*/productId'    => 'uuid',
    'orderLines/*/count'        => 'integer & greaterThan(0)',
    'orderLines/*/comments'     => 'optional & string & shorterThan(1024)',
]);
```

As you can see, check for nested data via the `/` character.

You can get the error messages for a single field like so:

```php
// get the errors associated with the top level field 'age'.
$errors = $checks->errorMessagesByPath('age');

// get the errors for the productId of the first orderLine.
$errors = $checks->errorMessagesByPath('orderLines/0/productId');

// get the error associated with the second orderLine
$errors = $checks->errorMessagesByPath('orderLines/1');
```
