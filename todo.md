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

allow all $against values to be strings as well as DateTimeInterface objects.
Allows prettier string syntax.

For that purpose DateUtil::dt() should throw better
exception messages.

It currently states 'Candidate could be parsed as a datetime via the format "%s"'
but format can be NULL, so it is a stoopid message.



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



