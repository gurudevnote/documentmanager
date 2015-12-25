<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticatePageResponseSuccessCodeIfLoginTest  extends WebTestCase
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
        $form['_username'] = 'hnguyenhuu';
        $form['_password'] = '123456';

        // submit the form
        self::$client->submit($form);
    }

    /**
     * @dataProvider urlAuthenticateOfUserProvider
     */
    public function testAuthenticatePageAfterLoginWithUserRole($url)
    {
        self::$client->request('GET', $url);
        $this->assertTrue(self::$client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider urlAuthenticateOfAdminProvider
     */
    public function testAuthenticatePageAfterLoginWithSupperAdminRole($url)
    {
        self::$client->request('GET', $url);
        //echo self::$client->getResponse()->getStatusCode();
        $this->assertTrue(self::$client->getResponse()->isForbidden());
    }

    public function urlAuthenticateOfAdminProvider()
    {
        return array(
            array('/admin/'),
            array('/admin/user'),
            array('/admin/delete'),
        );
    }

    public function urlAuthenticateOfUserProvider()
    {
        return array(
            //array('/documents'),
            array('/profile/'),
        );
    }
}
