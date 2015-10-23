<?php
// src/AppBundle/Tests/ApplicationAvailabilityFunctionalTest.php
namespace AppBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    public function setUp()
    {
        $classes = array(
            'AppBundle\DataFixtures\ORM\LoadBasicData',
        );
        $this->loadFixtures($classes);
    }

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