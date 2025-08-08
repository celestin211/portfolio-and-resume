<?php


namespace App\Service;

class LanguageProvider
{
    public const LANGUAGES = ['fr', 'en', 'es', 'de', 'pt'];

    public function getLanguages(): array
    {
        return self::LANGUAGES;
    }
}
