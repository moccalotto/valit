Filters
-------

Opret en CheckBag klasse
FilterSet => CheckNormalizer, som kan lave et array om til en CheckBag
En Template indeholder en CheckBag


Results
-------

Container\ValidationResult  => Valit\Results\ResultBag (indeholder paths til de forskellige variable)
Valit\Result                => Valit\Results\SingleValidationResult


Misc
----

FileSystemCheckProvider skal implementeres, testes og autoloades.
Find andre eventuelle providers der skal have samme behandling.
