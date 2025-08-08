<?php

namespace App\Util;

class WordHelper
{
    final public const ENCODING = 'UTF-8';

    public static function formatUpper($textToFormat): array|bool|string|null
    {
        return mb_convert_case($textToFormat, MB_CASE_UPPER, self::ENCODING);
    }

    public static function formatTitle($textToFormat): array|bool|string|null
    {
        return mb_convert_case($textToFormat, MB_CASE_TITLE, self::ENCODING);
    }
}
