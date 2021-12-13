<?php

namespace App\Command\Invoice;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManagerInterface;

class BillChecker extends Command
{
    protected static $defaultName = 'app:check-bill';
    public function __construct(
        UserPasswordHasherInterface $passwordInter,
        EntityManagerInterface $em
    ){
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to check current invoice')
            ->addArgument('command_id', InputArgument::REQUIRED, 'The command that you want to check');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return Command::SUCCESS;
    }
}