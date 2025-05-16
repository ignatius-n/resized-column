<?php

namespace Asmit\ResizedColumn;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ResizedColumnServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('asmit-resized-column')
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('resized-column', __DIR__.'/../resources/dist/js/resized-column.js'),
        ], 'asmit/resized-column');
    }
}
