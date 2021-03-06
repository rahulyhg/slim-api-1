<?php
namespace SlimApi\Command;

use \Exception;
use SlimApi\Skeleton\SkeletonInterface;
use SlimApi\Migration\MigrationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitDbCommand extends Command
{
    public static $successMessage = "Database initiated successfully!";

    /**
     * {@inheritdoc}
     */
    public function __construct(MigrationInterface $databaseService)
    {
        parent::__construct();
        $this->databaseService = $databaseService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('init:db')
            ->setDescription('Initiates the db setup.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false === is_file('composer.json')) {
            throw new Exception('Commands must be run from root of project.');
        }

        try {
            $this->databaseService->init(getcwd());
            $output->writeln('<info>'.static::$successMessage.'</info>');
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
