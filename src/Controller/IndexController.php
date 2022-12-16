<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\ProductsFiles;
use App\Form\ProductsFilesType;
use App\Repository\ProductsRepository;
use App\Services\SerializerService;
use App\Services\WeightService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'index',
        ]);
    }

    #[Route('/products', name: 'products')]
    public function products(Request $request, ProductsRepository $productsRepository, PaginatorInterface $paginator): Response
    {
        return $this->render('index/products.html.twig', [
            'title' => 'Products',
            'products' => $productsRepository->getProductsPaginator($request, $paginator),
            'categories' => $productsRepository->getCategories(),
            'route_name' => 'products',
            'category' => $request->query->get('category')
        ]);
    }
    
    #[Route('/import', name: 'import')]
    public function import(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $productsFiles = new ProductsFiles();

        $form = $this->createForm(ProductsFilesType::class, $productsFiles);
        $form->handleRequest($request);

        $arrayTwig['report'] = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($file) {

                ini_set('memory_limit', '-1');

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $prepareFilename = $safeFilename.'-'.uniqid().'.';
                $newFilename = $prepareFilename.$file->guessExtension();
                $csvFilename = $prepareFilename.'csv';

                try {
                    $content = $file->getContent();

                    $file->move(
                        $this->getParameter('xml_files'),
                        $newFilename
                    );

                    $productsFiles->setName($newFilename);
                    $productsFiles->setDate(new \DateTime(date('Y-m-d H:i:s')));

                    $em->persist($productsFiles);
                    $em->flush();

                    $arrayProducts = (new SerializerService())->getArray($content);

                    $productsAdd = 0;
                    $flushCounter = 0;

                    $reportName = $this->getParameter('upload_files') . $csvFilename;

                    $fp = fopen($this->getParameter('xml_files') . $csvFilename, 'w');
                    fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

                    $fields = ['Name', 'Description', 'Weight', 'Category'];
                    fputcsv($fp, $fields, ';');

                    foreach ($arrayProducts['product'] as $product) {
                        $products = new Products();

                        $products->setName($product['name']);
                        $products->setDescription($product['description']);
                        $products->setWeight((new WeightService())->getWeight($product['weight']));
                        $products->setCategory($product['category']);

                        $em->persist($products);
                        $productsAdd++;

                        $fields = [$product['name'], $product['description'], (new WeightService())->getWeight($product['weight']), $product['category']];
                        fputcsv($fp, $fields, ';');

                        if($flushCounter++ == 100000){
                            $em->flush();
                            $em->clear();
                            $flushCounter = 0;
                        }
                    }
                    $em->flush();
                    $em->clear();
                } catch (FileException $e) {}

                fclose($fp);

                $arrayTwig['report'] = $reportName;
            }
        }

        $arrayTwig['title'] = 'Import';
        $arrayTwig['form'] = $form;

        return $this->renderForm('index/import.html.twig', $arrayTwig);
    }
    
    #[Route('/coverage', name: 'coverage')]
    public function coverage(Request $request, ProductsRepository $productsRepository, PaginatorInterface $paginator): Response
    {
        shell_exec('cd ../;php8.0 ./vendor/bin/phpunit --coverage-html -i > tests/coverage/test.html');
        $testResult = file_get_contents($this->getParameter('test_result') . 'test.html');

        $testResult = str_replace('PHPUnit 9.5.27 by Sebastian Bergmann and contributors.

Warning:       No code coverage driver available
', '', $testResult);

        return $this->render('index/coverage.html.twig', [
            'title' => 'Coverage',
            'controller_name' => 'coverage',
            'test_result' => $testResult
        ]);
    }
}
