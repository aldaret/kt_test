<?php

namespace App\DataFixtures;

use App\Entity\Products;
use App\Entity\ProductsFiles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductsFilesFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $productFile = new ProductsFiles();
        $productFile->setName('File');
        $productFile->setDate(new \DateTime(date('Y-m-d', strtotime('12/17/2022'))));
        $manager->persist($productFile);

        $manager->flush();
    }
}
