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
        $this->mailer = $mailer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setHelp('This command allows you to check all bils');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $commands = $this->command_repo->findUncheckInvoice();
        foreach ($commands as $command) {

            $email = (new Email())
            ->from('fabianzuo@gmail.com')
            ->to('fabianzuo@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML ' . $command->getClientFullname() . ' ! </p>');

            $this->mailer->send($email);

            $command->setPayCheck(1);
            $this->em->persist($command);
            $this->em->flush();

        }

        return Command::SUCCESS;
    }
}