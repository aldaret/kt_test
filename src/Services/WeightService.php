<?php

namespace App\Services;

class WeightService
{
    public function getWeight(string $weight): int
    {
        $arrayWeight = explode(' ', $weight);
        return ('kg' === $arrayWeight[1]) ? $arrayWeight[0] * 1000 : $arrayWeight[0];
    }

    public function setIterator(array $array)
    {
        foreach ($array as $product) {
            yield $product;
        }
    }
}
