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

Documentation:
==============
- Consider a docs directory with a jekyll site?
- Consider rtfd
- Consider Jigsaw http://jigsaw.tighten.co/


MISC:
=====
Merge Var and VarDumper

Tests:
======

* Add tests for Val class
* Add tests for Size class
* Add tests for Date class
* Consider tests for File and FileInfo classes.
* use phpspec rather than kahlan for FileSystemCheckProvider.
* Use File::override instead of actually creating temp dirs and files in spec for FileSystemCheckProvider
