<?php

namespace App\Command\Invoice;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandRepository;

class Expired extends Command
{
    protected static $defaultName = 'app:expired';
    public function __construct(
        EntityManagerInterface $em,
        CommandRepository $command_repo
    ){
        $this->em = $em;
        $this->command_repo = $command_repo;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setHelp('This command allows you to check all bils date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $today = date("Y-m-d H:i:s"); 

        $commands = $this->command_repo->checkExpired($today);
        foreach ($commands as $command) {
            $command->setState("retard");
            $this->em->persist($command);
            $this->em->flush();

        }

        return Command::SUCCESS;
    }
}