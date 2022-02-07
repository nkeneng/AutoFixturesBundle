<?php


namespace Steven\AutoFixturesBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Steven\AutoFixturesBundle\Services\FixturablesClasses;
use Steven\AutoFixturesBundle\Services\FixtureManager;
use Steven\AutoFixturesBundle\Services\Purger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateFixturesCommand extends Command
{
    protected static $defaultName = 'app:create-fixtures';
    /**
     * @var FixturablesClasses
     */
    private $classes;
    /**
     * @var FixtureManager
     */
    private $fixtureManager;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(string $name = null, FixtureManager $fixtureManager,EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->fixtureManager = $fixtureManager;
        $this->em = $em;
    }

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $purger = new Purger($this->em);
        $purger->setPurgeMode( Purger::PURGE_MODE_DELETE);
        $purger->purge();
        $this->fixtureManager->createEntities();
        $this->io->success('Fixtures loaded successfully');
        return 0;
    }
}
