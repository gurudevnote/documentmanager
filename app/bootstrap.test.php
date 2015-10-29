<?php
require_once __DIR__ . '/bootstrap.php.cache';
require_once __DIR__ . '/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArrayInput;

use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
//use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;

echo "load fixture data at bootstrap\n";

$kernel = new AppKernel('test', true); // create a "test" kernel
$kernel->boot();

$application = new Application($kernel);

// add the database:drop command to the application and run it
$command = new DropDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:drop',
    '--force' => true,
));
$command->run($input, new ConsoleOutput());

// add the database:create command to the application and run it
$command = new CreateDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:create',
));
$command->run($input, new ConsoleOutput());

$command = new CreateSchemaDoctrineCommand();
$input = new ArrayInput(array(
    'command' => 'doctrine:schema:create'
));
$application->add($command);
$command->run($input, new ConsoleOutput());

//// Run the database migrations, with --quiet because they are quite
//// chatty on the console.
//$command = new MigrationsMigrateDoctrineCommand();
//$application->add($command);
//$input = new ArrayInput(array(
//    'command' => 'doctrine:migrations:migrate',
//    '--quiet' => true,
//    '--no-interaction' => true,
//));
//$input->setInteractive(false);
//$command->run($input, new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));

// and load the fixtures
$command = new LoadDataFixturesDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:fixtures:load',
    '--quiet',
));
$input->setInteractive(false);
$command->run($input, new ConsoleOutput());