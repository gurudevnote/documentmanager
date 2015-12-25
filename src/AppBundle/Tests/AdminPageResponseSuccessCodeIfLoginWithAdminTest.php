<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPageResponseSuccessCodeIfLoginWithAdminTest  extends WebTestCase
{
    private static $client = null;
    public static function setUpBeforeClass()
    {
        //self::$client = static::createClient(array('environment' => 'test', 'debug' => true));
        self::$client = static::createClient();
        $crawler = self::$client->request('GET', '/login');
        //$crawler->
        $form = $crawler->selectButton('_submit')->form();

        // set some values
        $form['_username'] = 'admin';
        $form['_password'] = '123456';

        // submit the form
        self::$client->submit($form);
    }

    /**
     * @dataProvider urlAuthenticateOfAdminProvider
     */
    public function testAuthenticatePageAfterLoginWithSupperAdminRole($url)
    {
        self::$client->request('GET', $url);
        //echo self::$client->getResponse()->getStatusCode();
        $this->assertTrue(self::$client->getResponse()->isSuccessful());
    }

    public function urlAuthenticateOfAdminProvider()
    {
        return array(
            array('/admin/'),
            array('/admin/user'),
            array('/admin/user/1/edit'),
        );
    }
}
