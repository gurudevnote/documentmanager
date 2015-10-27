<?php
// src/AppBundle/Tests/ApplicationAvailabilityFunctionalTest.php
namespace AppBundle\Tests;

use AppBundle\Tests\RunConsoleCommand;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    //TODO: another way to load fixture data only one for run all test function on a class
    //using trait for doing that
//    use RunConsoleCommand;
//
//    public static function setUpBeforeClass()
//    {
//        echo "\n==== LOAD FIXTURE DATA ON SETUP BEFORE CLASS USE TRAI ====\n";
//        RunConsoleCommand::initKernel();
//        echo RunConsoleCommand::runConsole('doctrine:database:drop --force');
//        RunConsoleCommand::runConsole('doctrine:database:create');
//        RunConsoleCommand::runConsole('doctrine:schema:create');
//        RunConsoleCommand::runConsole('doctrine:fixture:load -n --fixtures=src/AppBundle/DataFixtures');
//    }

    private  static $isLoadFixture = false;
    public function setUp()
    {
        if(self::$isLoadFixture === false) {
            $classes = array(
                'AppBundle\DataFixtures\ORM\LoadBasicData',
            );
            $this->loadFixtures($classes);
            self::$isLoadFixture = true;
        }
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