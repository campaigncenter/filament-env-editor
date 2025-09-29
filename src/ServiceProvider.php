<?php

namespace Campaigncenter\FilamentEnvEditor;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-env-editor';

    public function configurePackage(Package $package): void
    {
        $package->name('filament-env-editor')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->askToStarRepoOnGitHub('campaigncenter/filament-env-editor');
            })
            ->hasTranslations()
            ->hasViews();
    }
}
