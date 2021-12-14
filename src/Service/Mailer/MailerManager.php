<?php

namespace App\Service\Mailer;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerManager 
{
    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    public function sendMail(string $subject, string $content)
    {
        $email = (new Email())
        ->from('macdo@cool.com')
        ->to('macdo@cool.com')
        ->subject($subject)
        ->html($content);

        $this->mailer->send($email);
    }
}