Logic:
======

Make large kahlan tests that demonstrate the awesomeness of
logic comparisons, roughly based on control-structures.php

DRYness:
========

DateCheckProvider::dt() should be moved to a trait.
DateCheckProvider::canParse() should be moved to a trait.


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
