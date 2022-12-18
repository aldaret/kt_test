<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ControllersTest extends WebTestCase
{
    public function testIndexController(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Main Page');
    }

    public function testProductsController(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Products');
    }

    public function testImportController(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/import');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Import');
    }

    public function testImportSubmitController(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/import');

        $buttonCrawlerNode = $crawler->selectButton('products_files_save');
        $form = $buttonCrawlerNode->form();

        $client->submit($form);

        $content = "<?xml version=\"1.0\"?><products><product><name>in suscipit</name><description>By this time she found herself at last it unfolded its arms, took the regular course.' 'What was that?' inquired Alice. 'Reeling and Writhing, of course, I meant,' the King very decidedly, and he.</description><weight>30 g</weight><category>et</category></product></products>";

        $upl = tempnam(sys_get_temp_dir(), 'upl');

        $fp = fopen($upl, 'w');
        fputs($fp, $content);

        $file = new UploadedFile(
            $upl,
            'import_test.xml'
        );

        $memory_limit = ini_get('memory_limit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Import');
        $this->assertSame('text/xml', $file->getMimeType());
        $this->assertSame('268435456', $memory_limit);
    }

    public function testCoverageController(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/coverage');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Coverage');
    }

    public function testTestsController(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tests');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Tests');
    }
}
