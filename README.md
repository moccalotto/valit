# Valit

[![Build Status](https://travis-ci.org/moccalotto/valit.svg)](https://travis-ci.org/moccalotto/valit)

Validate variables using a fluent syntax.

Easily assert that variables have pass certain critera.

Re-use the same validators for many variables.

## Installation

To add this package as a local, per-project dependency to your project, simply add a dependency on
 `moccalotto/validation` to your project's `composer.json` file like so:

```json
{
    "require": {
        "moccalotto/validation": "~0.3"
    }
}
```

Alternatively simply call
```bash
composer require moccalotto/validation
```


## Checkers

The `Check` class is the base of the validation package.
A checker checks a variable against a set of rules and keeps an
internal record of all the checks and failures.

### Validity
You can determine if a variable passes all your criteria by using the
`valid` method.

Conversely you can use the `invalid` method to check if one or more
checks did not pass.

### Error Messages
If you want to know precisely which checks failed,

```php
$x = 42; // the variable to validate

$errors = Check::that($x)
    ->isNumeric()       // Success.
    ->isFloat()         // Fail. X is int
    ->floatEquals(40)   // Fail. X is not 40
    ->errorMessages()

/* [
'value should have type double, but has type integer',
'value should equal 40 with 5 decimals. But difference is 2',
] */
```

### Aliases
If you display the error messages to the end user,
you can tell them exactly which variable failed the validation by giving the
value an alias. This is done via the `as` method. If you prefer, you can
use the `alias` method, which does exactly the same.

```php
$x = 'foo@example.com';

$errors = Check::that($x)
    ->as('Email')           // We could have used alias('email') instead
    ->isEmail()             // Success
    ->endsWith('.co.uk')    // Fail
    ->errorMessages()

/* [
'Email must end with .co.uk, but it is string:"foo@example.com"',
] */
```
