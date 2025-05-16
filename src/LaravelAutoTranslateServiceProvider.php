<?php

namespace Zdearo\LaravelAutoTranslate;

use App\Console\Commands\ExtractTranslations;
use App\Console\Commands\MergeTranslations;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Zdearo\LaravelAutoTranslate\Commands\LaravelAutoTranslateCommand;

class LaravelAutoTranslateServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-auto-translate')
            ->hasConfigFile()
            ->hasCommands([
                ExtractTranslations::class,
                MergeTranslations::class
            ]);
    }
}
