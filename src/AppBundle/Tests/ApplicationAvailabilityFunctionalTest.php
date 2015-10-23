<?php
// src/AppBundle/Tests/ApplicationAvailabilityFunctionalTest.php
namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlAnonymousProvider
     */
    public function testAnonymousPage($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider urlAuthenticateProvider
     */
    public function testAuthenticatePage($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function urlAnonymousProvider()
    {
        return array(
            array('/'),
            array('/folders'),
            array('/login'),
        );
    }

    public function urlAuthenticateProvider()
    {
        return array(
            array('/admin'),
            array('/admin/user'),
            array('/admin/delete'),
            array('/documents'),
            array('/profile'),
        );
    }
}