<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\CommandRepository;
use App\Repository\PaymentRepository;
use App\Form\PaymentFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\Mailer\MailerManager;

class PaymentController extends AbstractController
{
    //create a payment
    #[Route('/payment/create/{id_command}', name: 'payment_create')]
    public function create(
        int $id_command, 
        ManagerRegistry $doctrine, 
        Request $request,
        CommandRepository $command_repo,
        PaymentRepository $payment_repo,
        MailerInterface $mailerInterface
        ): Response
    {

        $entityManager = $doctrine->getManager();

        $payment = new Payment();
        
        $form = $this->createForm(PaymentFormType::class, $payment, ["attr" => ["class" => "form-group"]]);
        $payment->setIdCommand($id_command);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager->persist($data);
            $entityManager->flush();

            $command = $command_repo->find($id_command);
            $reference = $command->getLastInvoice();
            $commandPrice = $command->getProducts()->getValues();
            $commandPrice = array_map(fn($value)=>$value->getPrice(), $commandPrice);
            $commandPrice = array_sum($commandPrice);

            $payedPrice = $payment_repo->findBy(["id_command" => $id_command]);
            $payedPrice = array_map(fn($value) => $value->getAmount(), $payedPrice);
            $payedPrice = array_sum($payedPrice);

            if($payedPrice >= $commandPrice){

                $mailer = new MailerManager($mailerInterface);
                $subject = "You command is now payed !";
                $content = "Thank you " . $command->getClientFullname() . " for your trust !!";
                $file = "pdf/$reference.pdf";
                $mailer->sendMail($subject, $content, $file);

                return $this->redirectToRoute('command_edit_state', [
                    "id_command" => $id_command,
                    "id_state" => 3
                ]);
            }

            return $this->redirectToRoute('command_show', ['id_command' => $id_command]);
        }

        return $this->renderForm('payment/create.html.twig', [
            "form" => $form
        ]);
    }

}
