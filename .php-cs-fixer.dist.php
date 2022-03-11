<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        'src',
        'tests',
    ]);

$config = new PhpCsFixer\Config();
$config->setRules([
    '@Symfony' => true,
    'align_multiline_comment' => true,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'blank_line_after_opening_tag' => true,
    'blank_line_before_statement' => [
        'statements' => ['try'],
    ],
    'class_attributes_separation' => [
        'elements' => ['method' => 'one'],
    ],
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'compact_nullable_typehint' => true,
    'concat_space' => [
        'spacing' => 'one',
    ],
    'declare_strict_types' => true,
    'doctrine_annotation_indentation' => [
        'indent_mixed_lines' => true,
    ],
    'dir_constant' => true,
    'ereg_to_preg' => true,
    'escape_implicit_backslashes' => true,
    'explicit_indirect_variable' => true,
    'linebreak_after_opening_tag' => true,
    'method_chaining_indentation' => true,
    'multiline_comment_opening_closing' => true,
    'no_homoglyph_names' => true,
    'no_null_property_initialization' => true,
    'no_superfluous_elseif' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant_public', 'constant_protected', 'constant_private',
            'property_public_static', 'property_protected_static', 'property_private_static',
            'property_public', 'property_protected', 'property_private',
            'construct', 'destruct', 'method_public_abstract_static', 'method_protected_abstract_static', 'method_private_abstract_static',
            'method_public_abstract', 'method_protected_abstract', 'method_private_abstract',
            'method_public_static', 'method_protected_static', 'method_private_static',
            'method_public', 'method_protected', 'method_private',
        ],
    ],
    'ordered_imports' => true,
    'no_superfluous_phpdoc_tags' => false,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_align' => false,
    'phpdoc_no_package' => false,
    'phpdoc_order' => true,
    'phpdoc_separation' => false,
    'phpdoc_summary' => false,
    'phpdoc_to_comment' => false,
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'none',
    ],
    'phpdoc_no_empty_return' => false,
    'yoda_style' => false,
])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
return $config;
