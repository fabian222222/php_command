<?php

namespace App\Controller;

use App\Entity\Payment;
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
    public function create(int $id_command, ManagerRegistry $doctrine, Request $request): Response
    {

        $entityManager = $doctrine->getManager();

        $payment = new Payment();

        $form = $this->createForm(PaymentFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('command_show', ['id' => $id_command]);
        }

        return $this->render('payment/create.html.twig', [
            'id_command' => $id_command,
        ]);
    }

}
