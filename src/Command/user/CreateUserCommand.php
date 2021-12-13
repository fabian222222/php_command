<?php

namespace App\Command\User;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    public function __construct(
        UserPasswordHasherInterface $passwordInter,
        EntityManagerInterface $em
    ){
        $this->hasher = $passwordInter;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create user')
            ->addArgument('email', InputArgument::REQUIRED, 'User mail')
            ->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User;

        $user->setEmail($input->getArgument("email"));
        $user->setPassword(
        $this->hasher->hashPassword(
                $user,
                $input->getArgument('password')
            )
        );

        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;
    }
}