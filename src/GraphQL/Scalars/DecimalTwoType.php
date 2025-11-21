<?php

declare(strict_types=1);

namespace Worksome\Number\GraphQL\Scalars;

final class DecimalTwoType extends DecimalType
{
    public string|null $description = <<<TXT
        The `DecimalTwo` scalar type represents a number with 2 decimal places.
        TXT;

    protected int|null $decimals = 2;
}
