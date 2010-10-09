<?php

/**
 *	Class: eVias_Bootstrap
 *	@author: Grégory Saive (saive.gregory@gmail.com)
 *  @brief:
 *      Object representing a source code.
 *
 */

class eVias_Code
{
    protected $_code = null;
    protected $_html = null;
    static public $expressions = array();

    static public function toHtml($code, $className)
    {
        if (! class_exists($className))
            throw new eVias_Exception ("wrong class name: $className given");

        $tokens = array();

        $codeHtml = htmlspecialchars (stripslashes($code));

        // this is a work around for double backslash escaping.
        $codeHtml = str_replace (array('\\\\', PHP_EOL, ' '), array('\\\\<fix>', '<br/>', '&nbsp;'), $codeHtml);

        $languageExpressions = call_user_func(array($className, 'getExpressions'));

        // get the html code for found keywords
        $codeHtml = preg_replace(
            array_keys($languageExpressions),
            array_values($languageExpressions),
            $codeHtml
        );

        // after parsing the regular expressions, comments and string
        // will be represented by unique IDs and need to be inserted
        // in the code again. (they were place in the array $tokens with
        // the safeParse method.
        $codeHtml = str_replace( array_keys($tokens), array_values($tokens), $codeHtml );

        // remove <fix> for backslash escaping.
        // replace space by html space, replace tabs with four spaces,
        // replace PHP_EOL by html line break
        // replace spaces by html space
        $codeHtml = str_replace( array( '<fix>', "\t"), array( '', '&nbsp;&nbsp;&nbsp;&nbsp;' ), $codeHtml );

        return $codeHtml;
    }

    /**
     * replace any comment or string by a unique ID so that
     * the content of those is not processed when highlighting.
     *
     * @param array $a      array to fill with regexp match
     * @param match string  what to do with a match ? (reg exp match)
     *
     * return integer       unique ID for comment / string
     */
    private static function safeParse( &$tokens, $match )
    {
        $id = "##eVias_" . uniqid() . "##";

        if( $match{0} == '/' || $match{0} == '#' )
            $tokens[$id] = '<span class="comment">' . $match . '</span>';
        else
            $tokens[$id] = '<span class="string">' . $match . '</span>';

        return $id;
    }

}

