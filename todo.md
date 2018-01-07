Assertions:
===========
- Check valid Base64
- Check valid utf-7


Readme:
=======
- use http://shields.io/ for badges

Testing:
========

* Add kahlan tests for Val class
* Add kahlan tests for Size class
* Add kahlan tests for Date class
* Add kahlan tests for Value class
* Add kahlan tests that match the scenarios in the example
  files such that interested parties can use it for
  documentation.
* Consider tests for File and FileInfo classes.


Logic:
======

Consider an InvalidLogicException that somehow has
prettier error messages that we currently have for
logi scenarios.

```txt
Data validation failed:

Precisely 1 of the following scenarios must pass:
* scenario 1:
    * auth must be present
    * auth must be an array
    * auth/apitoken must be a string
    * auth/apitoken must be shorter than 255 characters
* scenario 2:
    * headers/authorization must be present
    * headers/authorization must be a string
    * headers/authorization must pass the following logic:
            Precisely 1 of the following scenarios must pass:
            * scenario 1
                * value must be a string of length 42
            * scenario 2
                * value must be a string of length 80
                * value must contain only hexidecimal characters
```

Enable booleans in logic scenarios.
A boolean is simple evaluated as `Check::that($bool)->isTrue()`

```php
// If the product type is tobacco or alcohol then the age must be at least 18
Check::ifThen(
    Check::that($productType)->isNotOneOf(['alcohol', 'tobacco']),  // can also be a boolean
    Check::that($age)->isGreaterThanOrEqual(18),                    // can also be a boolean
)

// if $if is true the $then must be true, else $else must be true
Check::ifThenElse(
    $if,            // bool, Validator, AssertionBag, Logic or Result interface
    $then,          // bool, Validator, AssertionBag, Logic or Result interface
    $else           // bool, Validator, AssertionBag, Logic or Result interface
)
```
