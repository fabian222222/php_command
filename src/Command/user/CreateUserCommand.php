<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\User;
use App\Form\RegistrationFormType;
// use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
// use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    public function __construct(bool $requirePassword = false)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->requirePassword = $requirePassword;

        parent::__construct();
    }

    protected function configure(): void
    {
        // $this
            // ->addArgument('email', InputeArgument::REQUIRED, "The user's email")
            // ->addArgument('password', InputeArgument::REQUIRED, "The user's password")
            // ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {   
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);

        // $user = new User();
        // $user->setEmail($input->getArgument('email'));
        // $user->setRoles("[]");
        // $user->setPassword(
        //     $userPasswordHasher->hashPassword(
        //         $user,
        //         $input->getArgument('password')
        //     )
        // );

        // $entityManager->persist($user);
        // $entityManager->flush();

        // $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
        //     (new TemplatedEmail())
        //         ->from(new Address('vincent@vincent.com', 'Vincent Bot'))
        //         ->to($user->getEmail())
        //         ->subject('Please Confirm your Email')
        //         ->htmlTemplate('registration/confirmation_email.html.twig')
        // );

        return Command::SUCCESS;
    }
}