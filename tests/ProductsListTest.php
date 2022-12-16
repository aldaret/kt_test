<?php

namespace App\Tests;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use App\Services\SerializerService;
use App\Services\WeightService;
use PHPUnit\Framework\TestCase;

class ProductsListTest extends TestCase
{
    public function testWeightService(): void
    {
        $weight = new WeightService();
        $test = $weight->getWeight('10 kg');
        $this->assertSame(10000, $test);

        $test = $weight->getWeight('300 g');
        $this->assertSame(300, $test);
    }

    public function testSerializerService(): void
    {
        $content = <<<EOF
        <products>
            <product>
                <name>in suscipit</name>
                <description>By this time she found herself at last it unfolded its arms, took the regular course.' 'What was that?' inquired Alice. 'Reeling and Writhing, of course, I meant,' the King very decidedly, and he.</description>
                <weight>30 g</weight>
                <category>et</category>
            </product>
        </products>
        EOF;

        $serializer = new SerializerService();
        $test = $serializer->getArray($content);

        $this->assertIsArray($test);
        $this->assertArrayHasKey('product', $test);
        $this->assertArrayHasKey('name', $test['product']);
        $this->assertArrayHasKey('description', $test['product']);
        $this->assertArrayHasKey('weight', $test['product']);
        $this->assertArrayHasKey('category', $test['product']);
    }
}
