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

DateCheckProvider:
==================

Allow all $against values to be strings as well as DateTimeInterface objects.
Add tests for these


Tests:
======

Add tests for Str class
Add tests for Size class
Add missing checks for FileSystemChecks
Add missing checks for DateCheckProvider
Ensure checks for DateCheckProvider also validate that $against can be strings in certain cases.


FileSystemChecks:
=================

Finish tests.

[ file sizes can be given as integers (bytes) or strings such as '1024 MiB', '1 GiB', '15 kB', etc]
[ file dates can be given as strings or DateTimeInterface objects ]

fileLargerThan(123123 /* bytes */), fileSizeGreaterThan('1G' /* 1 gigabyte */)
fileSmallerThan, fileLargerThan

fileNewerThan, fileCreatedAfter
fileOlderThan, fileCreatedBefore
fileCreatedAt

fileAccessedBefore
fileAccessedAfter
fileAccessedAt

Consider support for mime types. Maybe a separate mime type checker.
