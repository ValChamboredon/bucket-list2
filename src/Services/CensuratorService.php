<?php
namespace App\Services;

class CensuratorService
{
    private array $badWords = [];

    public function __construct(string $badWordsFile)
    {
        if (file_exists($badWordsFile)) {
            $this->badWords = file($badWordsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
    }

    public function purify(string $text): string
    {
        foreach ($this->badWords as $word) {
            $pattern = '/' . preg_quote($word, '/') . '/i';
            $replacement = str_repeat('*', mb_strlen($word));
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }
}

