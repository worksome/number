<?php

declare(strict_types=1);

namespace Worksome\Number\Exceptions;

use InvalidArgumentException;

class InvalidValueException extends InvalidArgumentException implements NumberException
{
}
