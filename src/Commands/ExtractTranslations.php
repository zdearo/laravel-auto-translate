<?php

namespace Zdearo\LaravelAutoTranslate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExtractTranslations extends Command
{
    protected $signature = 'translations:extract {locale : The language code (e.g. pt_BR, en, es)}';

    protected $description = 'Extract new translation strings from app directory for a specific language';

    public function handle(): int
    {
        $locale = $this->argument('locale');

        // Validate the locale format
        if (!preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $locale)) {
            $this->error("Invalid locale format. Please use format like 'en', 'pt_BR', 'es_ES'.");
            return 1;
        }

        $this->info("Extracting translations for locale: {$locale}");

        $translations = [];
        $this->extractTranslationsFromDirectory(base_path('app'), $translations);
        $this->extractTranslationsFromDirectory(base_path('resources/views'), $translations);

        $langPath = lang_path("{$locale}.json");
        $newStringsPath = lang_path("new_strings_{$locale}.json");

        // Load existing translations
        $existingTranslations = [];
        if (File::exists($langPath)) {
            $existingTranslations = json_decode(File::get($langPath), true) ?? [];
            $this->info("Found " . count($existingTranslations) . " existing translations in {$locale}.json");
        } else {
            $this->warn("No existing translations found for {$locale}. Will create a new file.");
        }

        // Filter out strings that already exist in the locale json
        $newTranslations = array_filter($translations, function ($string) use ($existingTranslations) {
            return !isset($existingTranslations[$string]);
        });

        if (empty($newTranslations)) {
            $this->info("No new strings found for {$locale}.");
            return 0;
        }

        // Create array with empty translations
        $newTranslations = array_fill_keys($newTranslations, '');
        ksort($newTranslations);

        File::put($newStringsPath, json_encode($newTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("New translations extracted to lang/new_strings_{$locale}.json");
        $this->info("Found " . count($newTranslations) . " new strings for {$locale}");

        // Ask if the user wants to merge the new strings into the existing locale file
        if ($this->confirm("Would you like to merge the new strings into the main {$locale}.json file?")) {
            $mergedTranslations = array_merge($existingTranslations, $newTranslations);
            ksort($mergedTranslations);

            File::put($langPath, json_encode($mergedTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("Merged new strings into {$locale}.json");

            // Optionally delete the new strings file after merging
            if ($this->confirm("Remove the temporary new_strings_{$locale}.json file?")) {
                File::delete($newStringsPath);
                $this->info("Temporary file removed.");
            }
        }

        return 0;
    }

    /**
     * Recursively extract translation strings from a directory.
     *
     * @param string $directory
     * @param array<string> $translations
     * @return void
     */
    protected function extractTranslationsFromDirectory(string $directory, array &$translations): void
    {
        $files = File::files($directory, true);

        foreach ($files as $file) {
            if ($file->getExtension() === 'php' || $file->getExtension() === 'blade') {
                $content = file_get_contents($file->getPathname());

                // __('string')
                preg_match_all("/__\(['\"](.+?)['\"](,|\))/", $content, $matches);

                // trans('string')
                preg_match_all("/trans\(['\"](.+?)['\"](,|\))/", $content, $transMatches);

                // @lang('string')
                preg_match_all("/@lang\(['\"](.+?)['\"]\)/", $content, $langMatches);

                // {{ __('string') }}
                preg_match_all("/\{\{\s*__\(['\"](.+?)['\"](,|\))/", $content, $bladeMatches);

                $allMatches = array_merge(
                    $matches[1],
                    $transMatches[1],
                    $langMatches[1],
                    $bladeMatches[1]
                );

                if (!empty($allMatches)) {
                    foreach ($allMatches as $match) {
                        if (trim($match) === '') {
                            continue;
                        }

                        $match = str_replace('\\\'', "'", $match);
                        $match = str_replace('\\"', '"', $match);

                        if (!in_array($match, $translations)) {
                            $translations[] = $match;
                        }
                    }
                }
            }
        }

        foreach (File::directories($directory) as $subdirectory) {
            $this->extractTranslationsFromDirectory($subdirectory, $translations);
        }
    }
}
