<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Compagny;
use App\Entity\Invoice;
use App\Entity\InvoiceRow;
use App\Form\CommandFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandController extends AbstractController
{
    // get all commands by a state
    #[Route('/commands/{id_sate}', name: 'commands')]
    public function getAll(int $id_state, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $commands = $entityManager->getRepository(Command::class)->findBy(array('state' => $id_state));

        return $this->render('commands/index.html.twig', [
            'commands' => $commands,
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

                $company = $entityManager->getRepository(Compagny::class)->find(1)->getName() + " " + $entityManager->getRepository(Compagny::class)->find(1)->getAddress();
                $references = 'IZUDHGZ667D8Z9F0';
                $invoice->setReference($references);
                $invoice->setClientInformations($form->get('client_fullname')->getData());
                $invoice->setCompagnyInformations($company);

                foreach ($form->get('products') as $key => $value) {
                    $invoiceRow = new InvoiceRow();
                    $invoiceRow->setInvoice($invoice);
                    $invoiceRow->setName($form->get('products')[$key]->getData()->getName());
                    $invoiceRow->setPrice($form->get('products')[$key]->getData()->getPrice());
                }

                $command->setState(1);
                $entityManager->persist($command);
                $entityManager->flush();

                return $this->redirectToRoute('command_show', ['id' => $command->getId()]);
            }

        }
        return $this->renderForm('form',[
            'form' => $form->createView(),
        ]);
    }

    //show a command
    #[Route('/command/show/{id}', name: 'command_show')]
    public function show(int $id_command, ManagerRegistry $doctrine): Response
    {

        $entityManager = $doctrine->getManager();

        $command = $entityManager->getRepository(Command::class)->find($id_command);

        if (!$command) {
            return $this->redirectToRoute('commands',['state'=> 1]);
        }
        return $this->render('page',[
            'command' => $command,
        ]);
    }

    //edit a command
    #[Route('/command/{id_command}/edit', name: 'command_edit')]
    public function edit(int $id_command, Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $command = $entityManager->getRepository(Command::class)->find($id_command);

        if (!$command) {
            return $this->redirectToRoute('commands',['state'=> 1]);
        }

        $form = $this->createForm(CommandFormType::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('command_show', ['id' => $command->getId()]);
        }

        return $this->render('commands/edit.html.twig', [
            'command' => $command,
            'form' => $form->createView(),
        ]);
    }

    //edit state of a command
    #[Route('/command/{id_command}/edit/state/{id_state}', name: 'command_edit_state')]
    public function editState(int $id_command,int $id_state, Request $request, ManagerRegistry $doctrine): void
    {
        $entityManager = $doctrine->getManager();

        $command = $entityManager->getRepository(Command::class)->find($id_command);

        if ($command) {
            $command->setState($id_state);
            $entityManager->flush();
        }
    }
}
