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
use App\Repository\InvoiceRepository;
use App\Service\Reference\ReferenceGenerator;
use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\ORM\EntityManagerInterface;

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
        MailerInterface $mailerInterface,
        InvoiceRepository $invoice_repo,
        ReferenceGenerator $referenceGenerator,
        EntityManagerInterface $entityManager
        ): Response
    {
        $payment = new Payment();
        
        $form = $this->createForm(PaymentFormType::class, $payment, ["attr" => ["class" => "form-group"]]);
        $payment->setIdCommand($id_command);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager->persist($data);
            $entityManager->flush();

            $command = $command_repo->find($id_command);
            $invoice = $invoice_repo->findByReference($command->getLastInvoice());
            $reference = $command->getLastInvoice();

            $commandPrice = $command->getProducts()->getValues();
            $commandPrice = array_map(fn($value)=>$value->getPrice(), $commandPrice);
            $commandPrice = array_sum($commandPrice);

            $payedPrice = $payment_repo->findBy(["id_command" => $id_command]);
            $payments = $payedPrice;
            $payedPrice = array_map(fn($value) => $value->getAmount(), $payedPrice);
            $payedPrice = array_sum($payedPrice);
            
            $pdfOptions = new Options();

            $pdfOptions->set('defaultFont', 'Arial');
            $pdfOptions->set('isRemoteEnabled',true);   
            $dompdf = new Dompdf($pdfOptions);
            $html = $this->render('invoice/invoice_template.html.twig', [
                'invoice' => $invoice[0],
                'tot_command' => $commandPrice,
                "payments" => $payments
            ]);

            $publicDirectory = $this->getParameter('kernel.project_dir').'/public/';
            $pdfFilePath = $publicDirectory . '/pdf/' . $reference . '.pdf';

            $dompdf->load_html($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();            
            
            file_put_contents($pdfFilePath, $output);

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
