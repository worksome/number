<?php

declare(strict_types=1);

namespace Worksome\Number\Providers;

use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Worksome\Number\GraphQL\Scalars\DecimalTwoType;
use Worksome\Number\GraphQL\Scalars\PercentageType;
use Worksome\Number\GraphQL\Scalars\StrictPercentageType;

class NumberServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->callAfterResolving(TypeRegistry::class, function (TypeRegistry $registry) {
            $registry->register(new DecimalTwoType());
            $registry->register(new PercentageType());
            $registry->register(new StrictPercentageType());
        });
    }
}
