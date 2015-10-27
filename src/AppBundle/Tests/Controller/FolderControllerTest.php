<?php
namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
        $folderName = 'test_folder_create_sucessfull';
        // set some values
        $form['form[name]'] = $folderName;

        // submit the form
        self::$client->submit($form);
        self::$client->request('GET', '/folders');
        $this->assertContains(
            $folderName,
            self::$client->getResponse()->getContent()
        );
    }
}