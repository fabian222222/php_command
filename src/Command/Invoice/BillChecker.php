<?php

namespace App\Command\Invoice;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use App\Service\Mailer\MailerManager;

class BillChecker extends Command
{
    protected static $defaultName = 'app:check-bill';
    public function __construct(
        EntityManagerInterface $em,
        CommandRepository $command_repo,
        MailerInterface $mailer
    ){
        $this->em = $em;
        $this->command_repo = $command_repo;
        $this->mailerInterface = $mailer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setHelp('This command allows you to check all bills');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $commands = $this->command_repo->findUncheckInvoice();
        $mailer = new MailerManager($this->mailerInterface);
        
        foreach ($commands as $command) {
            
            $subject = "You command is now payed !";
            $content = "Thank you " . $command->getClientFullname() . " for your trust !!";

            $mailer->sendMail($subject, $content);

            $command->setPayCheck(1);
            $this->em->persist($command);
            $this->em->flush();

        }

        return Command::SUCCESS;
    }
}