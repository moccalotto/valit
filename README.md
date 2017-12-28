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

### Facades

The `Check` and `Ensure` classes are the a so-called facade classes.
In short they make it easier for you to use the Valit library.

```php
use Valit\Check;
use Valit\Ensure;
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

$validation = Check::that($x)
    ->isInt()                   // Success
    ->isGreaterThanOrEqual(42)  // Success
    ->isLessThan(100);          // Success

if (!$validation->success()) {
    throw new RuntimeException('Validation Failed');
}

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

/*
Array
(
    [0] => Your Email Address must end with the string ".co.uk"
)
*/

// Notice it says "Your Email Address" rather than "value".
```

### Ensuring
If you want to assert that all checks must pass, you can
use the `Valit\Ensure` facade.

If a single check fails, we throw a
`Valit\Exceptions\InvalidValueException` that contains the
error message for that check.


```php
$email = 'Doctor.Hansen@Example.com';

try {
    Ensure::that($x)
        ->as('Email')
        ->isEmail()             // Success
        ->isLowercase()         // Throws InvalidValueException
        ->endsWith('.co.uk');   // Not run
} catch (InvalidValueException $e) {
    var_dump($e->getMessage());
    /*
        string(42) "Email must be a syntax-valid email address"
     */
}
```

### Ensuring all checks
If you want to assert that all checks pass, and you want
info about all tests, you can use the Check facade in
combination with the `orThrowException` method.

The thrown `InvalidValueException` will contain a list of
all the error messages. These can be accessed via the
`errorMessages` method like so:

```php
$age = '42.3';

try {
    Check::that($age)
        ->as('age')
        ->isNaturalNumber()     // Fail
        ->isGreaterThan(18)     // Success
        ->isLowerThan(30)       // Fail
        ->orThrowException();
} catch (InvalidValueException $e) {
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


### Checking arrays and containers

You can easily test an entire array, for instance posted fields or a json response,
in a structured and well defined way like the example below:

```php
$checks = Check::that($input)->contains([
    'name'      => 'required & string & shorterThan(100)',
    'email'     => 'required & email & shorterThan(255)',
    'address'   => ['required', 'string'],
    'age'       => ['greaterThanOrEqual' => [18], 'lowerThan(70)'],

    'orderLines'                => 'required & conventionalArray',
    'orderLines/*'              => 'required & associative',
    'orderLines/*/productId'    => 'required & uuid',
    'orderLines/*/count'        => 'required & integer & greaterThan(0)',
    'orderLines/*/comments'     => 'string & shorterThan(1024)',
]);

if ($checks->hasErrors()) {
    print_r($checks->errors());
    /*
        Array
        (
            [age] => Array
                (
                    [0] => Field must be less than 70
                )

            [orderLines/1] => Array
                (
                    [0] => Field must be an associative array
                )

            [orderLines/0/productId] => Array
                (
                    [0] => Field must be a valid UUID
                )

        )
     */
}
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

### Array assertions

As with single variable tests, you can assert that an array must pass
a set of filters via the `Ensure` facade like so:

```php
// Throw an exception if $responseData does not adhere to all the criteria:
Ensure::that($responseData)->contains([
    'statusCode' => 'required & integer & greaterThanOrEqual(0) & lowerThanOrEqual(1000)',
    'message' => 'required & string',
    'payload' => 'required & isArray',
    'payload/paymentAddress/name' => 'required & string & shorterThan(100)',
    'payload/paymentAddress/email' => 'required & email & shorterThan(255)',
    'payload/paymentAddress/address' => 'required & string & shorterThan(255)',
    'payload/paymentAddress/country' => 'required & string & isUpperCase & hasLength(2)'
    'payload/billingAddress/name' => 'required & string & shorterThan(100)',
    'payload/billingAddress/email' => 'required & email & shorterThan(255)',
    'payload/billingAddress/address' => 'required & string & shorterThan(255)',
    'payload/billingAddress/country' => 'required & string & isUpperCase & hasLength(2)'
]);
```


### Throwing detailed exceptions

If you use `Check` instead of `Ensure`, and the use the `orThrowException` method,
you get an exception message that contains all the errors on all the fields.

```php
Check::that([
    'a' => 1234,
    'b' => [
        'c' => 'g',
        'd' => 'h',
    ],

])->contains([
    'a' => 'required & isString & longerThan(100)',
    'b' => 'required & isArray',
    'b/c' => 'required & isInt & greaterThan(10)',
    'b/d' => 'required & isString',
    'b/e' => 'required',
    'c' => 'required & isString & longerThan(100)',
])->orThrowException();

/* Throws InvalidContainerException.
    InvalidContainerException::getMessage() would return:
<<<EOT
Container did not pass validation:
   a must have the type "string"
   a must be a string that is longer than 100
   b/c must have the type "integer"
   b/c must be greater than 10
   b/e is required
   c is required
EOT;
*/
}
```
