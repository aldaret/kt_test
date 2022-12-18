<?php

namespace App\Tests;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductsRepositoryTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testProductsInfo(): void
    {
        $product = $this->entityManager
            ->getRepository(Products::class)
            ->findOneBy(['name' => 'Name']);

        $this->assertSame(1, $product->getId());
        $this->assertSame('Name', $product->getName());
        $this->assertSame(100, $product->getWeight());
        $this->assertSame('Description', $product->getDescription());
        $this->assertSame('Category', $product->getCategory());
    }

    public function testProductsSet(): void
    {
        $products = new Products();

        $this->assertEquals('Name2', $products->setName('Name2')->getName());
        $this->assertEquals(1000, $products->setWeight(1000)->getWeight());
        $this->assertEquals('Description2', $products->setDescription('Description2')->getDescription());
        $this->assertEquals('Category2', $products->setCategory('Category2')->getCategory());
    }

    public function testProductsSaveAndRemove(): void
    {
        static::bootKernel();
        $container = static::getContainer();

        $products = new Products();
        $products->setName('Name2');
        $products->setDescription('Description2');
        $products->setCategory('Category2');
        $products->setWeight(1000);

        /** @var ProductsRepository $repository */
        $repository = $container->get(ProductsRepository::class);
        $repository->save($products, true);
        $request = $repository->findOneBy(['id' => $products->getId()]);
        $this->assertSame('Name2', $request->getName());
        $this->assertSame('Description2', $request->getDescription());
        $this->assertSame('Category2', $request->getCategory());
        $this->assertSame(1000, $request->getWeight());

        $repository->remove($request, true);
        $request = $repository->findOneBy(['id' => $products->getId()]);
        $this->assertNull($request);
    }

    public function testProductsPaginator(): void
    {
        static::bootKernel();
        $container = static::getContainer();

        $params = [
            'category' => 'Category',
            'weight' => null
        ];

        /** @var ProductsRepository $repository */
        $repository = $container->get(ProductsRepository::class);
        $request = $repository->getProductsPaginator($params)->getArrayResult()[0];

        $this->assertSame('Name', $request['name']);
        $this->assertSame('Description', $request['description']);
        $this->assertSame('Category', $request['category']);
        $this->assertSame(100, $request['weight']);

        $params = [
            'category' => null,
            'weight' => 'light'
        ];

        /** @var ProductsRepository $repository */
        $repository = $container->get(ProductsRepository::class);
        $request = $repository->getProductsPaginator($params)->getArrayResult()[0];

        $this->assertSame('Name', $request['name']);
        $this->assertSame('Description', $request['description']);
        $this->assertSame('Category', $request['category']);
        $this->assertSame(100, $request['weight']);

        $params = [
            'category' => null,
            'weight' => 'hight'
        ];

        /** @var ProductsRepository $repository */
        $repository = $container->get(ProductsRepository::class);
        $request = $repository->getProductsPaginator($params)->getArrayResult()[0];

        $this->assertSame('Name2', $request['name']);
        $this->assertSame('Description2', $request['description']);
        $this->assertSame('Category2', $request['category']);
        $this->assertSame(1000, $request['weight']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
