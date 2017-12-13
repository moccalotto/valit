Logic:
======

OneOf succeeds of container has exactly 1 success.
AnyOf succeeds if LogicExecutor has 1 or more successes.
Not (only executes a single logic branch) succeeds if there are no successes
AllOf succeeds if successCount == branchCount

Cleanup:
========

Move Template class to a namespace.
