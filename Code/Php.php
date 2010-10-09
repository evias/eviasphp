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
                elseif|new|delete|try|throw|catch|finally|class|function|string|
                array|object|resource|var|bool|boolean|int|integer|float|double|
                real|string|array|global|const|static|public|private|protected|
                published|extends|switch|true|false|null|void|this|self|struct|
                char|signed|unsigned|short|long
            )(?!\w|=")/ix'
            => '<span class="keyword">$1</span>',

            // Language Addon
            // - functions, runtime constants..
            '/(?<!\w|\$|\%|\@|>)(
                __CLASS__|__DIR__|__FILE__|__LINE__|__FUNCTION__|__METHOD__|__NAMESPACE__|
                die|include|require_once|echo|include_once|return|empty|isset|print|eval|
                require|var_dump|var_export
            )(?!\w|=")/ix'
            => '<span class="library">$1</span>',

            // Language prototype (default overload)
            // - constructor, setters ..
            '/(?<!\w|\$|\%|\@|>)(
                __construct|__destruct|__call|__invoke|__set|__get|__isset
            )(?!\w|=")/ix'
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

