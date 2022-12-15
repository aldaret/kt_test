<?php

namespace App\Services;

use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SerializerService
{
    public function getArray($content): array
    {
        $encoder      = [new XmlEncoder()];
        $normalizer   = [new ObjectNormalizer()];
        $serializer   = new Serializer($normalizer, $encoder);

        return $serializer->decode($content, 'xml');
    }

    public function setCsv($content)
    {
        $encoder      = [new CsvEncoder()];
        $normalizer   = [new ObjectNormalizer()];
        $serializer   = new Serializer($normalizer, $encoder);

        return $serializer->encode($content, 'scv');
    }
}
