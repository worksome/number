<?php

declare(strict_types=1);

namespace Worksome\Number\Exceptions;

use InvalidArgumentException;

class MonetaryAmountDecimalCountException extends InvalidArgumentException implements NumberException
{
    protected $message = 'MonetaryAmount must have exactly 2 decimal places.';
}
