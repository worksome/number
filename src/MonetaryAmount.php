<?php

declare(strict_types=1);

namespace Worksome\Number;

use Worksome\Number\Exceptions\MonetaryAmountDecimalCountException;

class MonetaryAmount extends Number
{
    protected int|null $decimals = 2;

    final protected function validate(): void
    {
        if ($this->decimals !== 2) {
            throw new MonetaryAmountDecimalCountException();
        }
    }
}
