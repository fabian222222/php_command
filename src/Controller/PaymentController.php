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

class PaymentController extends AbstractController
{
    //create a payment
    #[Route('/payment/create/{id_command}', name: 'payment_create')]
    public function create(
        int $id_command, 
        ManagerRegistry $doctrine, 
        Request $request,
        CommandRepository $command_repo,
        PaymentRepository $payment_repo
        ): Response
    {

        $entityManager = $doctrine->getManager();

        $payment = new Payment();
        
        $form = $this->createForm(PaymentFormType::class, $payment);
        $payment->setIdCommand($id_command);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager->persist($data);
            $entityManager->flush();

            $command = $command_repo->find($id_command);
            $commandPrice = $command->getProducts()->getValues();
            $commandPrice = array_map(fn($value)=>$value->getPrice(), $commandPrice);
            $commandPrice = array_sum($commandPrice);

            $payedPrice = $payment_repo->findBy(["id_command" => $id_command]);
            $payedPrice = array_map(fn($value) => $value->getAmount(), $payedPrice);
            $payedPrice = array_sum($payedPrice);

            if($payedPrice >= $commandPrice){
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
