<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setIndent('    ')
    ->setRules(
        [
            '@PHP71Migration'                           => true,
            '@Symfony'                                  => true,
            '@Symfony:risky'                            => true,
            'align_multiline_comment'                   => true,
            'array_syntax'                              => ['syntax' => 'short'],
            'concat_space'                              => ['spacing' => 'one'],
            'declare_strict_types'                      => false,
            'dir_constant'                              => true,
            'doctrine_annotation_array_assignment'      => true,
            'doctrine_annotation_braces'                => true,
            'doctrine_annotation_indentation'           => true,
            'doctrine_annotation_spaces'                => true,
            'general_phpdoc_annotation_remove'          => ['annotations' => ['author', 'package']],
            'heredoc_to_nowdoc'                         => true,
            'indentation_type'                          => true,
            'linebreak_after_opening_tag'               => true,
            'list_syntax'                               => ['syntax' => 'short'],
            'modernize_types_casting'                   => true,
            'no_mixed_echo_print'                       => ['use' => 'echo'],
            'no_multiline_whitespace_before_semicolons' => true,
            'no_php4_constructor'                       => true,
            'no_short_echo_tag'                         => true,
            'no_unreachable_default_argument_value'     => true,
            'no_useless_else'                           => true,
            'no_useless_return'                         => true,
            'ordered_class_elements'                    => true,
            'ordered_imports'                           => true,
            'phpdoc_add_missing_param_annotation'       => ['only_untyped' => false],
            'phpdoc_order'                              => true,
            'phpdoc_types_order'                        => ['null_adjustment' => 'always_last'],
            'psr4'                                      => false,
            'semicolon_after_instruction'               => true,
            'single_line_comment_style'                 => true,
            'binary_operator_spaces'                    => [
                'align_double_arrow' => true,
                'align_equals'       => true,
            ],
        ]
    )
    ->setCacheFile(__DIR__ . '/.php_cs.cache');
