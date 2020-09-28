<?php

namespace App\Specifications;

interface SpecificationInterface
{
    public function isSatisfiedBy($object): bool;
}
