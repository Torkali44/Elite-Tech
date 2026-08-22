<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Translate text from Arabic to English.
     */
    public static function translateToEnglish(?string $text): ?string
    {
        if (empty(trim($text))) {
            return null;
        }

        try {
            $tr = new GoogleTranslate('en', 'ar');
            return $tr->translate($text);
        } catch (\Exception $e) {
            Log::error('Translation failed: ' . $e->getMessage());
            return null;
        }
    }
}
