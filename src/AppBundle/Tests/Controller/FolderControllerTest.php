<?php
namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
class FolderControllerTest extends WebTestCase
{
    private  static $client = null;
    public static function  setUpBeforeClass()
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

    public function testCreateFolder()
    {
        $crawler = self::$client->request('GET', '/folders');
        $form = $crawler->selectButton('form[save]')->form();
        $folderName = 'test_folder_create_successful';
        // set some values
        $form['form[name]'] = $folderName;

        // submit the form
        self::$client->submit($form);
        $crawler = self::$client->request('GET', '/folders');
        $this->assertContains(
            $folderName,
            self::$client->getResponse()->getContent()
        );

        //test create folder and specify parent folder
        $repository = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('P5:Folder');
        $qb = $repository->createQueryBuilder('f')
            ->select('f.id')
            ->where('f.name = :name')
            ->setParameter('name', 'Drawing' );
        $parentId = $qb->getQuery()->getSingleScalarResult();

        $form = $crawler->selectButton('form[save]')->form();
        $folderName = 'test_folder_create_successful_set_parent';
        $form['form[name]'] = $folderName;
        $form['form[parent]'] = $parentId;
        self::$client->submit($form);

        $qb->select('f')->setParameter('name', $folderName);
        $folder = $qb->getQuery()->getSingleResult();
        $this->assertEquals($folderName, $folder->getName());
        $this->assertEquals($folder->getParent()->getId(), $parentId);
    }
}