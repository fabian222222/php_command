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

    public function sendMail(string $subject, string $content, string $pdf)
    {
        if (strlen($pdf) === 0){
            $email = (new Email())
            ->from('macdo@cool.com')
            ->to('macdo@cool.com')
            ->subject($subject)
            ->html($content);
        }else {
            $email = (new Email())
            ->from('macdo@cool.com')
            ->to('macdo@cool.com')
            ->subject($subject)
            ->html($content)
            ->attachFromPath($pdf);
        }

        $this->mailer->send($email);
    }
}