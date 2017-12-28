Documentation:
==============
- Consider a docs directory with a jekyll site?
- Consider rtfd
- Consider Jigsaw http://jigsaw.tighten.co/


Refactor:
=========
AssertionNormalizer => AssertionBagFactory
    normalize() => create()

Tests:
======

* Add tests for Val class
* Add tests for Size class
* Add tests for Date class
* Consider tests for File and FileInfo classes.
* use phpspec rather than kahlan for FileSystemCheckProvider.
* Use File::override instead of actually creating temp dirs and files in spec for FileSystemCheckProvider
