# Valit

[![Build Status](https://travis-ci.org/moccalotto/valit.svg)](https://travis-ci.org/moccalotto/valit)

Validate variables using a fluent syntax.

## Installation

To add this package as a local, per-project dependency to your project, simply add a dependency on
 `moccalotto/valit` to your project's `composer.json` file like so:

```json
{
    "require": {
        "moccalotto/valit": "~0.5"
    }
}
```

Alternatively execute the following command in your shell.

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

### Facades

The `Check` and `Ensure` classes are the a so-called facade classes.
In short they make it easier for you to use the Valit library.

```php
use Moccalotto\Valit\Facades\Check;
use Moccalotto\Valit\Facades\Ensure;
```

The `Ensure` class allows you to make checks with fluent API,
throwing an exception as soon as a check doesn't pass.

The `Check` class allows you to make checks that do not throw exceptions.
This means all checks are processed and that you can get a pretty rendered
list of errors.

### Validity
You can determine if a variable passes all your criteria by using the
`valid` method.

Conversely you can use the `invalid` method to check if one or more
checks did not pass.

```php
$x = 42; // the variable to validate

$valid = Check::that($x)
    ->isInt()                   // Success
    ->isGreaterThanOrEqual(42)  // Success
    ->isLessThan(100)           // Success
    ->valid();                  // true
```

### Error Messages
If you want to know precisely which checks failed,
you can use the `errorMessages` method.

```php
$x = 42; // the variable to validate

$errors = Check::that($x)
    ->isNumeric()       // Success
    ->isFloat()         // Fail
    ->isCloseTo(40)     // Fail
    ->errorMessages();

print_r($errors);

/*
Array
(
    [0] => value must have the type "double"
    [1] => value must equal 40 with a margin of error of 1.0e-5
)
 */
```

### Aliases
If you display the error messages to the end user,
you can tell them exactly which variable failed the validation by giving the
value an alias. This is done via the `as` method. If you prefer, you can
use the `alias` method, which does exactly the same.

```php
$email = 'foo@example.com';

$errors = Check::that($email)
    ->as('Your Email Address')
    ->isEmail()             // Success
    ->endsWith('.co.uk')    // Fail
    ->errorMessages();

/* [
Array
(
    [0] => Your Email Address must end with the string ".co.uk"
)
*/

// Notice it says "Your Email Address" rather than "value".
```

### Ensuring
If you want to assert that all checks must pass, you can
use the `Moccalotto\Valit\Ensure` facade.

If a single check fails, we throw a
`Moccalotto\Valit\ValidationException` that contains the
error message for that check.


```php
$email = 'Doctor.Hansen@Example.com';

try {
    Ensure::that($x)
        ->as('Email')
        ->isEmail()             // Success
        ->isLowercase()         // Throws ValidationException
        ->endsWith('.co.uk');   // Not run
} catch (ValidationException $e) {
    var_dump($e->getMessage());
    /*
        string(42) "Email must be a syntax-valid email address"
     */
}
```

The `ValidationException` will contain a list of all the error messages,
that can be accessed via the `getErrorMessages` method like so:

```php
/*
|----------------------------------------------------------
| Ensuring all checks
|----------------------------------------------------------
|
| If you want to assert that all checks pass, and you want
| info about all tests, you can use the Check facade in
| combination with the `orThrowException` method.
|
 */

$age = '42.3';

try {
    Check::that($age)
        ->as('age')
        ->isNaturalNumber()     // Fail
        ->isGreaterThan(18)     // Success
        ->isLowerThan(30)       // Fail
        ->orThrowException();
} catch (ValidationException $e) {
    print_r($e->errorMessages());
    /*
        Array
        (
            [0] => age must be a natural number
            [1] => age must be less than 30
        )
     */
}
```
