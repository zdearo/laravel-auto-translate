<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MergeTranslations extends Command
{
    protected $signature = 'translations:merge {locale : The language code (e.g. pt_BR, en, es)}';

    protected $description = 'Merge translated strings from new_strings_{locale}.json to {locale}.json';

    public function handle(): int
    {
        $locale = $this->argument('locale');

        if (!preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $locale)) {
            $this->error("Invalid locale format. Please use format like 'en', 'pt_BR', 'es_ES'.");
            return 1;
        }

        $this->info("Merging translations for locale: {$locale}");

        $langPath = lang_path("{$locale}.json");
        $newStringsPath = lang_path("new_strings_{$locale}.json");

        if (!File::exists($newStringsPath)) {
            $this->error("new_strings_{$locale}.json not found.");

            if ($this->confirm("Would you like to run translations:extract to create it?")) {
                $this->call('translations:extract', ['locale' => $locale]);
            } else {
                return 1;
            }
        }

        $existingTranslations = [];
        if (File::exists($langPath)) {
            $existingTranslations = json_decode(File::get($langPath), true) ?? [];
            $this->info("Found " . count($existingTranslations) . " existing translations in {$locale}.json");
        } else {
            $this->warn("No existing {$locale}.json file found. A new one will be created.");
        }

        $newStrings = json_decode(File::get($newStringsPath), true) ?? [];
        $this->info("Found " . count($newStrings) . " strings in new_strings_{$locale}.json");

        $totalNewStrings = count($newStrings);
        $translatedCount = count(array_filter($newStrings, fn($value) => !empty($value)));
        $untranslatedCount = $totalNewStrings - $translatedCount;

        $this->info("Statistics:");
        $this->info("- Total strings: {$totalNewStrings}");
        $this->info("- Translated: {$translatedCount}");
        $this->info("- Untranslated: {$untranslatedCount}");

        $mergeOption = $this->choice(
            'How would you like to merge the translations?',
            [
                'translated' => 'Only merge strings that have translations',
                'all' => 'Merge all strings (including empty translations)',
                'interactive' => 'Interactive mode: review each untranslated string',
            ],
            'translated'
        );

        $stringsToMerge = [];

        switch ($mergeOption) {
            case 'translated':
                $stringsToMerge = array_filter($newStrings, fn($value) => !empty($value));
                break;

            case 'all':
                $stringsToMerge = $newStrings;
                break;

            case 'interactive':
                $stringsToMerge = array_filter($newStrings, fn($value) => !empty($value));

                foreach ($newStrings as $key => $value) {
                    if (empty($value)) {
                        if ($this->confirm("Translate '{$key}' now?")) {
                            $translation = $this->ask("Enter translation for '{$key}'");
                            if (!empty($translation)) {
                                $stringsToMerge[$key] = $translation;
                            }
                        }
                    }
                }
                break;
        }

        if (empty($stringsToMerge)) {
            $this->error('No strings to merge.');
            return 1;
        }

        $mergedTranslations = array_merge($existingTranslations, $stringsToMerge);
        ksort($mergedTranslations);

        // Backup the original file if it exists
        if (File::exists($langPath)) {
            $backupPath = lang_path("{$locale}_backup_" . date('Y-m-d_His') . ".json");
            File::copy($langPath, $backupPath);
            $this->info("Backup of original file created at {$backupPath}");
        }

        File::put($langPath, json_encode($mergedTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Ask if the user wants to delete the new strings file
        if ($this->confirm("Delete new_strings_{$locale}.json file?", true)) {
            File::delete($newStringsPath);
            $this->info("new_strings_{$locale}.json deleted.");
        }

        $this->info("Translations successfully merged into {$locale}.json");
        $this->info("Merged " . count($stringsToMerge) . " strings");

        return 0;
    }
}
