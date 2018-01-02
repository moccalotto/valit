Documentation:
==============
- Finish the docs at moccalotto.github.com
- Add more examples

Tests:
======

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
    $if,            // bool, Validator, Template or Logic
    $then,          // bool, Validator, Tempalte or Logic
    $else           // bool, Validator, Tempalte or Logic
)
```
