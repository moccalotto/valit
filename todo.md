Containers:
===========

Consider a syntax such as:

```php
Check::that($container)->contains([
    'foo' => 'isArray',
    'foo/bar' => 'isString',
    'foo/baz' => 'optional & isUuid',
]);
```

```php
Check::that($container, $value)->contains([
    'foo' => $value->isArray(),
    'foo/bar' => $value->isString(),
    'foo/baz' => $value->isOptional()->isUuid()
]);
```

Templates:
==========
Maybe merge Template and AssertionBag.
Maybe refactor Template:
    Maybe rename
    Maybe inherit from AssertionBag instead of composition

Tests:
======

Add tests for Str class
Add tests for Size class
Add tests for Date class


StatInfo:
=========

File.php should have an stat() function that can be overridden so we
do not have to monkey patch.
