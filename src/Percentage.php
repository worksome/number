<?php

declare(strict_types=1);

namespace Worksome\Number;

class Percentage extends Number
{
    public function toString(): string
    {
        $value = parent::toString();

        return "{$value}%";
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
