# Valit

[![Travis Build Status](https://img.shields.io/travis/moccalotto/valit.svg?style=flat-square)](https://travis-ci.org/moccalotto/valit)
![PHP Versions](https://img.shields.io/packagist/php-v/moccalotto/valit.svg?style=flat-square)
[![Latest Stable Version](https://img.shields.io/packagist/v/moccalotto/valit.svg?style=flat-square)](https://packagist.org/packages/moccalotto/valit)
[![License](https://img.shields.io/packagist/l/moccalotto/valit.svg?style=flat-square)](LICENSE)

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
If you don't want exceptions to be thrown, use the `Check` facade.

You can use the `throwExceptionIfNotSuccessful` method to throw an
exception if one or more assertions fail.
The advantage of this is that the exception thrown will
contain *all* the failed assertions, not just the first one.

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


### Utilities

Valit provides the `Val` facade that lets to do quick type juggling and assertions.

Below are ways of testing if a variable is [iterable](http://php.net/manual/en/language.types.iterable.php),
that is agnostic of your php version.

```php
use Valit\Util\Val;

if (!Val::is($container, 'iterable')) {
    throw new LogicException('$container should be iterable');
}
```

Or alternatively:

```php
use Valit\Util\Val;

// an InvalidArgumentException will be thrown if $container is not iterable.
Val::mustBe($container, 'iterable');
```

Or with your own custom exception
```php
use Valit\Util\Val;

$myException = throw LogicException('$container should be iterable');

// $myException will be thrown if $container is not iterable.
Val::mustBe($container, 'iterable', $myException);
```

Below are some of the type validations you can make.

| $type          | Validation                                    |
|:-------------- |:--------------------------------------------- |
| `null`         | `is_null()`                                   |
| `object`       | `is_object()`                                 |
| `int`          | `is_int()`                                    |
| `integer`      | `is_int()`                                    |
| `bool`         | `is_bool()`                                   |
| `boolean`      | `is_bool()`                                   |
| `string`       | `is_string()`                                 |
| `float`        | `is_float()`                                  |
| `double`       | `is_float()`                                  |
| `numeric`      | `is_numeric()`                                |
| `intable`      | `stringable` that can be converted to an int  |
| `nan`          | `is_nan()`                                    |
| `inf`          | `is_inf()`                                    |
| `callable`     | `is_callable()`                               |
| `iterable`     | `is_array() or is_a($value, 'Traversable')`   |
| `countable`    | `is_array() or is_a($value, 'Cointable')`     |
| `arrayable`    | `is_array() or is_a($value, 'ArrayAccess')`   |
| `container`    | `iterable`, `countable` and `arrayable`       |
| `stringable`   | scalar or object with a`__toString()` method  |
| _class name_   | `is_a()`                                      |
| _foo[]_        | array of _foo_                                |

Code examples:

```php
// single type
Val::mustBe($value, 'callable');

// multiple allowed types via the pipe character
Val::mustBe($value, 'float | int');

// check that $foo is an array of floats
// or an array of integers
Val::mustBe($value, 'float[] | int[]');

// mixing classes, interfaces and basic types.
Val::mustBe($value, 'int|DateTime|DateTimeImmutable');

// multiple types via array notation
Val::mustBe($value, ['object', 'array']);

// a strict array with 0-based numeric index
Val::mustBe($value, 'mixed[]');

// a strict array of strict arrays
Val::mustBe($value, 'mixed[][]');
```
