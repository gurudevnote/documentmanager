<?php
// src/AppBundle/Tests/ApplicationAvailabilityFunctionalTest.php
namespace AppBundle\Tests;

use AppBundle\Tests\RunConsoleCommand;
use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpKernel\Kernel;

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
    public static  function setUpBeforeClass()
    {
        echo "\nLoad setup before class\n";

        self::$kernel->boot();
        self::loadFixturesData(self::$kernel);
    }
    private static function loadFixturesData(Kernel $kernel)
    {
        $application = new Application($kernel);
        // add the database:drop command to the application and run it
        $commandDrop = new DropDatabaseDoctrineCommand();
        $commandCreateDb = new CreateDatabaseDoctrineCommand();
        $commandCreateScheme = new CreateSchemaDoctrineCommand();
        $commandLoadFixtureData = new LoadDataFixturesDoctrineCommand();
        $application->add($commandDrop);
        $application->add($commandCreateDb);
        $application->add($commandCreateScheme);
        $application->add($commandLoadFixtureData);

        $input = new ArrayInput(array(
            'command' => 'doctrine:database:drop',
            '--force' => true,
        ));
        $commandDrop->run($input, new ConsoleOutput());

        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create'
        ));
        $commandCreateDb->run($input, new ConsoleOutput());

        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:create'
        ));
        $commandCreateScheme->run($input, new ConsoleOutput());

        $input = new ArrayInput(array(
            'command' => 'doctrine:fixture:load',
            //'--no-interaction' => false,
        ));
        $input->setInteractive(false);
        $commandLoadFixtureData->run($input, new ConsoleOutput());

//Another way to load fixture data
//        $loader = new DataFixturesLoader($kernel->getContainer());
//        $em = $kernel->getContainer()->get('doctrine')->getManager();
//
//        foreach ($kernel->getBundles() as $bundle) {
//            $path = $bundle->getPath().'/DataFixtures/ORM';
//
//            if (is_dir($path)) {
//                $loader->loadFromDirectory($path);
//            }
//        }
//
//        $fixtures = $loader->getFixtures();
//        if (!$fixtures) {
//            throw new InvalidArgumentException('Could not find any fixtures to load in');
//        }
//        $purger = new ORMPurger($em);
//        $executor = new ORMExecutor($em, $purger);
//        $executor->execute($fixtures, true);
    }

//    private  static $isLoadFixture = false;
//    public function setUp()
//    {
//        if(self::$isLoadFixture === false) {
//            $classes = array(
//                'AppBundle\DataFixtures\ORM\LoadBasicData',
//            );
//            $this->loadFixtures($classes);
//            self::$isLoadFixture = true;
//        }
//    }

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