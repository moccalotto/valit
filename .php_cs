<?php

$fixers = [
    '@PSR2' => true,
    'array_element_no_space_before_comma',
    'array_element_white_space_after_comma',
    'blankline_after_open_tag',
    'declare_equal_normalize',
    'double_arrow_multiline_whitespaces',
    'duplicate_semicolon',
    'extra_empty_lines',
    'function_typehint_space',
    'hash_to_slash_comment',
    'list_commas',
    'lowercase_cast',
    'method_argument_default_value',
    'multiline_array_trailing_comma',
    'namespace_no_leading_whitespace',
    'new_with_braces',
    'no_blank_lines_after_class_opening',
    'no_empty_lines_after_phpdocs',
    'no_empty_phpdoc',
    'no_empty_statement',
    'object_operator',
    'operators_spaces',
    'phpdoc_annotation_without_dot',
    'phpdoc_indent',
    'phpdoc_inline_tag',
    'phpdoc_params',
    'phpdoc_scalar',
    'phpdoc_separation',
    'phpdoc_short_description',
    'phpdoc_single_line_var_spacing',
    'phpdoc_to_comment',
    'phpdoc_trim',
    'phpdoc_type_to_var',
    'phpdoc_types',
    'phpdoc_var_without_name',
    'remove_leading_slash_use',
    'remove_lines_between_uses',
    'return',
    'no_useless_return',
    'self_accessor',
    'short_bool_cast',
    'short_scalar_cast',
    'single_array_no_trailing_comma',
    'single_blank_line_before_namespace',
    'single_quote',
    'spaces_after_semicolon',
    'spaces_before_semicolon',
    'spaces_cast',
    'standardize_not_equal',
    'ternary_spaces',
    'trim_array_spaces',
    'unneeded_control_parentheses',
    'unused_use',
    'whitespacy_lines',
    'php4_constructor',
    'no_useless_else',
];

return Symfony\CS\Config\Config::create()
    ->fixers($fixers)
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()->in(__DIR__ . '/src')
    );
