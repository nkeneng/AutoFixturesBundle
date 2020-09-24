<?php


namespace Steven\AutoFixturesBundle\Command;


use Steven\AutoFixturesBundle\Services\FixturablesClasses;
use Steven\AutoFixturesBundle\Services\FixtureCreator;
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
     * @var FixtureCreator
     */
    private $creator;

    public function __construct(string $name = null,FixtureCreator $creator)
    {
        parent::__construct($name);
        $this->creator = $creator;
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
        $this->creator->createEntities();
        dump($this->creator->classes);
        $this->io->success('hello world');
        return 0;
    }
}
