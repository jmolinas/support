<?php
namespace JMolinas\Support\Helpers;

class StringHelper
{
    /**
     * Convert String to UTF8
     *
     * @param string $string
     *
     * @return string
     */
    public static function stringToUtf8($string)
    {
        return preg_replace_callback("/(&#[0-9]+;)/", function($entitiy) {
            return mb_convert_encoding($entitiy[1], "UTF-8", "HTML-ENTITIES");
        }, $string);
    }
}
