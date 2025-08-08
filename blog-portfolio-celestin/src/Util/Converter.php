<?php

namespace App\Util;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class Converter
{
    public static function convertDate($dateToConvert)
    {
        if (null === $dateToConvert) {
            return null;
        }

        if ($dateToConvert instanceof \DateTime) {
            return $dateToConvert;
        }

        if (0 == strlen(trim($dateToConvert))) {
            return null;
        }

        // On remplace les slash par des tirets pour que la fonction strtotime reconnaisse une date au format
        // une date au format européen (d-m-Y)
        $dateToConvert = str_replace('/', '-', $dateToConvert);

        if (!strtotime($dateToConvert) && is_numeric($dateToConvert)) {
            try {
                $timestamp = Date::excelToDateTimeObject($dateToConvert)->getTimestamp();
                $convertedDate = date('d/m/Y', $timestamp);
            } catch (\Exception $e) {
                return null;
            }
        } elseif (!strtotime($dateToConvert)) {
            return null;
        } else {
            $convertedDate = date('d/m/Y', strtotime($dateToConvert));
        }

        return date_time_set(\DateTime::createFromFormat('d/m/Y', $convertedDate), 0, 0, 0);
    }

    public static function convertStringToCanonical($str)
    {
        if (null === $str) {
            return null;
        }

        $normalizeChars = [
                'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
                'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
                'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
                'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
                'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
                'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
                'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
                'ă' => 'a', 'ș' => 's', 'ț' => 't', 'Ă' => 'A', 'Ș' => 'S', 'Ț' => 'T',
                ' ' => '_', '\'' => '_', '’' => '_',
        ];

        $str = strtr($str, $normalizeChars);

        return preg_replace('/[^A-Za-z0-9 ]/', '_', $str);
    }
}
