<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DocumentControllerTest extends WebTestCase
{
    private static $client = null;
    public static function setUpBeforeClass()
    {
        self::$client = static::createClient();
        $crawler = self::$client->request('GET', '/login');
        //$crawler->
        $form = $crawler->selectButton('_submit')->form();

        // set some values
        $form['_username'] = 'hnguyenhuu';
        $form['_password'] = '123456';

        // submit the form
        $crawler = self::$client->submit($form);
    }

    public function testCreateDocument()
    {
        //test create folder and specify parent folder
        $repository = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('P5:Folder');
        $qb = $repository->createQueryBuilder('f')
            ->select('f.id')
            ->where('f.name = :name')
            ->setParameter('name', 'Drawing');
        $folderId = $qb->getQuery()->getSingleScalarResult();
        $crawler = self::$client->request('GET', '/add-document');
        $form = $crawler->selectButton('save')->form();
        $documentName = 'test_document_create_successful';
        // set some values
        $form['form[filename]'] = $documentName;
        $form['form[type]'] = 'PDF';
        $form['form[folder]'] = $folderId;

        // submit the form
        self::$client->submit($form);

        self::$client->request('GET', '/documents?size=1000');
        $this->assertContains(
            $documentName,
            self::$client->getResponse()->getContent()
        );
    }

    public function testRemoveDocument()
    {
        $documentRepository = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('P5:Document');
        $documentFilename = '[Derek_Chen-Becker,_Tyler_Weir,_Marius_Danciu]_The Definitive Guide to Lift A Scala-Based Web Framework(BookFi.org).pdf';
        $document = $documentRepository->findOneByFilename($documentFilename);
        self::$client->request('POST', '/remove_document/'.$document->getId());
        $crawler = self::$client->request('GET', '/documents?size=1000');
        $this->assertNotContains(
            $documentFilename,
            $crawler->html()
        );
    }
}
