<?php

namespace App\DataFixtures;

use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Products();
        $product->setName('Name');
        $product->setWeight(100);
        $product->setDescription('Description');
        $product->setCategory('Category');
        $manager->persist($product);

        $product2 = new Products();
        $product2->setName('Name2');
        $product2->setWeight(1000);
        $product2->setDescription('Description2');
        $product2->setCategory('Category2');
        $manager->persist($product2);

        $product3 = new Products();
        $product3->setName('Name3');
        $product3->setWeight(500);
        $product3->setDescription('Description3');
        $product3->setCategory('Category3');
        $manager->persist($product3);

        $manager->flush();
    }
}
