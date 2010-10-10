<?php

/**
 *	Class: eVias_Bootstrap
 *	@author: Grégory Saive (saive.gregory@gmail.com)
 *  @brief:
 *      Object representing a PHP source code.
 *
 */

class eVias_Code_Php
    extends eVias_Code
{
    static public $expressions = array(
            // Comments / Strings
            // - multi line comments '/*'
            // - single line comments '//'
            // - single line comments '#'
            // - strings starting with double quote
            // - strings starting with single quote
            '/(
                \/\*.*?\*\/|
                \/\/.*?\n|
                \#.*?\n|
                (?<!\\\)&quot;.*?(?<!\\\)&quot;|
                (?<!\\\)\'(.*?)(?<!\\\)\'
            )/isex'
            => 'self::safeParse($tokens,\'$1\')',

            // Numeric values
            // - hex value
            // - decimal
            '/(?<!\w)(
                0x[\da-f]+|
                \d+
            )(?!\w)/ix'
            => '<span class="numeric">$1</span>',

            // Constants, statics name (most commonly uppercase)
            '/(?<!\w|>)(
                [A-Z_0-9]{2,}
            )(?!\w)/x'
            => '<span class="uppercased">$1</span>',

            // Keywords
            // - labels, loops, conditions
            // - declarations
            '/(?<!\w|\$|\%|\@|>)(
                and|or|xor|for|do|while|foreach|as|return|die|exit|if|then|else|
                elseif|try|throw|catch|finally|function|string|
                array|object|resource|var|bool|boolean|int|integer|float|double|
                real|string|array|global|const|static|
                published|switch|true|false|null|void|
                char|signed|unsigned|short|long
            )(?!\w|=")/ix'
            => '<span class="keyword">$1</span>',

            // Classes
            '/(
                class|this|parent|self|new|delete|extends|implements|
                public|protected|private|struct
            )(?!\w|=")/ix'
            => '<span class="classes">$1</span>',

            // Language Addon
            // - functions, runtime constants..
            '/(
                __CLASS__|__DIR__|__FILE__|__LINE__|__FUNCTION__|__METHOD__|__NAMESPACE__|
                die|include|require_once|echo|include_once|return|print|eval|
                require|var_dump|var_export
            )/ix'
            => '<span class="library">$1</span>',

            // Language prototype (default overload)
            // - constructor, setters ..
            // - type functions
            // - string functions
            // - array_map
            '/(
                __construct|__destruct|__call|__invoke|__set|__get|__isset|
                is_integer|is_numeric|is_bool|is_string|is_scalar|is_object|
                unset|isset|empty|implode|explode|strpos|str_replace|substr|
                strtr|strchr|in_array|is_null|count|array_map|array_keys|
                array_values|array_diff|array_diff_keys|array_diff_values
            )/ix'
            => '<span class="prototype">$1</span>',

            // PHP / Perl style variables: $var, %var, @var
            '/(?<!\w)(
                (\$|\%|\@)(\-&gt;|\w)+
            )(?!\w)/ix'
            => '<span class="variable">$1</span>'
    );

    static public function getExpressions()
    {
        return self::$expressions;
    }
}

