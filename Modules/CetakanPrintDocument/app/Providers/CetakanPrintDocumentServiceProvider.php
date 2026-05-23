<?php

namespace Modules\CetakanPrintDocument\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class CetakanPrintDocumentServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'CetakanPrintDocument';

    protected string $nameLower = 'cetakanprintdocument';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
