<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CompagnyRepository;
use App\Form\CompanyFormType;
use Doctrine\ORM\EntityManagerInterface;

class CompanyController extends AbstractController
{

    #[Route('/company/{id}', name: 'get_company')]
    public function get_company(
        int $id,
        CompagnyRepository $companyRepo
    ):Response
    {
        $company = $companyRepo->find($id);
        return $this->render('company/info.html.twig', [
            "company" => $company
        ]);
    }

    #[Route('/company/update/{id}', name: 'update_company')]
    public function update_company(
        int $id,
        CompagnyRepository $companyRepo,
        EntityManagerInterface $em,
        Request $request
    ): Response
    {
        $company = $companyRepo->find($id);

        $form = $this->createForm(CompanyFormType::class, $company, ["attr" => ["class" => "form-group"]]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $company = $form->getData();
            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('update_company', [
                "id" => $id   
            ]);
        }

        return $this->renderForm('company/update.html.twig', [
            'form' => $form
        ]);
    }
}
