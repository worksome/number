<?php

declare(strict_types=1);

namespace Worksome\Number;

class Percentage extends Number
{
    public function toString(): string
    {
        return "{$this->value}%";
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
