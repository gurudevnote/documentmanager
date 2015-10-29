<?php

namespace AppBundle\Tests;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

require_once __DIR__.'/../../../app/AppKernel.php';

trait RunConsoleCommand
{
    protected static $traitKernel;
    protected static $traitContainer;
    protected static $traitApplication = null;

    public static function initKernel()
    {
        self::$traitKernel = new \AppKernel('test', true);
        self::$traitKernel->boot();

        self::$traitContainer = self::$traitKernel->getContainer();
        self::$traitApplication = new Application(self::$traitKernel);
        self::$traitApplication->setAutoExit(false);
    }

    public function get($serviceId)
    {
        return self::$traitKernel->getContainer()->get($serviceId);
    }

    public static function runConsole($command, Array $options = array())
    {
        if(self::$traitApplication == null)
        {
            self::initKernel();
        }

        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return self::$traitApplication->run(new ArrayInput($options));
    }
}