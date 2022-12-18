<?php

namespace App\Tests;

use App\Entity\ProductsFiles;
use App\Repository\ProductsFilesRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductsFilesRepositoryTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testProductsFilesInfo(): void
    {
        $productFile = $this->entityManager
            ->getRepository(ProductsFiles::class)
            ->findOneBy(['name' => 'File']);

        $this->assertSame(1, $productFile->getId());
        $this->assertSame('File', $productFile->getName());
        $this->assertSame('2022-12-17', $productFile->getDate()->format('Y-m-d'));
    }

    public function testProductsFilesSet(): void
    {
        $productFile = new ProductsFiles();

        $this->assertEquals('File2', $productFile->setName('File2')->getName());
        $this->assertEquals(
            '2022-12-18',
            $productFile->setDate(new \DateTime(date('Y-m-d', strtotime('12/18/2022'))))->getDate()->format('Y-m-d')
        );
    }

    public function testProductsFilesSaveAndRemove(): void
    {
        static::bootKernel();
        $container = static::getContainer();

        $productsFiles = new ProductsFiles();
        $productsFiles->setName('File2');
        $productsFiles->setDate(new \DateTime(date('Y-m-d', strtotime('12/19/2022'))));

        /** @var ProductsFilesRepository $repository */
        $repository = $container->get(ProductsFilesRepository::class);
        $repository->save($productsFiles, true);
        $request = $repository->findOneBy(['id' => $productsFiles->getId()]);
        $this->assertSame('File2', $request->getName());

        $repository->remove($request, true);
        $request = $repository->findOneBy(['id' => $productsFiles->getId()]);
        $this->assertNull($request);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
