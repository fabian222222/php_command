<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Compagny;
use App\Entity\Invoice;
use App\Entity\InvoiceRow;
use App\Repository\PaymentRepository;
use App\Form\CommandFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\Mailer\MailerManager;

class CommandController extends AbstractController
{
    // get all commands by a state
    #[Route('/commands/{id_state}', name: 'commands')]
    public function getAll(int $id_state, ManagerRegistry $doctrine): Response
    {

        switch ($id_state){
            case 1 :
                $state = "non traitée";
                $title = "Commandes non traitées";
                break;
            case 2 :
                $state = "traitée";
                $title = "Commandes traitées";
                break;
            case 3 :
                $state = "payée";
                $title = "Commandes payées";
                break;
            case 4 :
                $state = "retard";
                $title = "Commandes en retard";
                break;
        }

        $entityManager = $doctrine->getManager();

        $commands = $entityManager->getRepository(Command::class)->findByState($state);
        
        return $this->render('command/index.html.twig', [
            'commands' => $commands,
            'title' => $title
        ]);
    }

    //create a command
    #[Route('/command/create', name: 'command_create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($this->getUser() != null){

            $entityManager = $doctrine->getManager();

            $command = new Command();
            $invoice = new Invoice();

            $form = $this->createForm(CommandFormType::class, $command);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $company = "McDo 65 rue de la gare";
                $references = 'IZUDHGZ667D8Z9F0';
                $invoice->setReference($references);
                $invoice->setClientInformations($form->get('client_fullname')->getData());
                $invoice->setCompagnyInformations($company);

                foreach ($form->get('products') as $key => $value) {
                    $invoiceRow = new InvoiceRow();
                    $invoiceRow->setInvoice($invoice);
                    $invoiceRow->setName($form->get('products')[$key]->getData()->getName());
                    $invoiceRow->setPrice($form->get('products')[$key]->getData()->getPrice());
                    $invoice->addInvoiceRow($invoiceRow);
                }

                $command->setState("non traitée");

                $entityManager->persist($invoice);
                $entityManager->persist($command);
                $entityManager->flush();

                return $this->redirectToRoute('command_show', ['id_command' => $command->getId()]);
            }

            return $this->renderForm('command/create.html.twig',[
                'form' => $form,
            ]);

        } 
        return $this->redirectToRoute('login');
    }

    //show a command
    #[Route('/command/show/{id_command}', name: 'command_show')]
    public function show(int $id_command, ManagerRegistry $doctrine, PaymentRepository $payment_repo): Response
    {

        $entityManager = $doctrine->getManager();

        $command = $entityManager->getRepository(Command::class)->find($id_command);
        $payedPrice = $payment_repo->findBy(["id_command" => $id_command]);

        if (!$command) {
            return $this->redirectToRoute('commands',['state'=> 1]);
        }
        return $this->render('command/show_command.html.twig',[
            'command' => $command,
            'payed' => $payedPrice
        ]);
    }

    //edit a command
    #[Route('/command/{id_command}/edit', name: 'command_edit')]
    public function edit(int $id_command, Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $invoice = new Invoice();

        $command = $entityManager->getRepository(Command::class)->find($id_command);

        if (!$command) {
            return $this->redirectToRoute('commands',['state'=> 1]);
        }

        $form = $this->createForm(CommandFormType::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $company = "McDo 65 rue de la gare";
            $references = 'IZUDHGZ667D8Z9F0';
            $invoice->setReference($references);
            $invoice->setClientInformations($form->get('client_fullname')->getData());
            $invoice->setCompagnyInformations($company);

            foreach ($form->get('products') as $key => $value) {
                $invoiceRow = new InvoiceRow();
                $invoiceRow->setInvoice($invoice);
                $invoiceRow->setName($form->get('products')[$key]->getData()->getName());
                $invoiceRow->setPrice($form->get('products')[$key]->getData()->getPrice());
                $invoice->addInvoiceRow($invoiceRow);
            }

            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('command_show', ['id' => $command->getId()]);
        }

        return $this->renderForm('command/edit.html.twig', [
            'command' => $command,
            'form' => $form,
        ]);
    }

    //edit state of a command
    #[Route('/command/{id_command}/edit/state/{id_state}', name: 'command_edit_state')]
    public function editState(
        int $id_command,
        int $id_state, 
        Request $request, 
        ManagerRegistry $doctrine,
        MailerInterface $mailerInterface
        ):Response
    {

        $entityManager = $doctrine->getManager();

        $command = $entityManager->getRepository(Command::class)->find($id_command);

        switch ($id_state){
            case 2 :

                $state = "traitée";
                $mailer = new MailerManager($mailerInterface);
                $subject = "You command is now treated !";
                $content = "Thank you " . $command->getClientFullname() . " for your trust !!";
                $mailer->sendMail($subject, $content);
                break;
                
            case 3 :
                $state = "payée";
                break;
            case 4 :
                $state = "retard";
                break;
        }
        if ($command) {
            $command->setState($state);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commands', [
            "id_state" => $id_state - 1
        ]);
    }
}