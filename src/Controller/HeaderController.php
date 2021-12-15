<?php

namespace App\Controller;

use App\Repository\CompagnyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HeaderController extends AbstractController
{
    public function getNavData(CompagnyRepository $compagnyRepo): Response
    {
        $user = $this->getUser();
        $compagny = $compagnyRepo->findBy(array('name'=> 'McDo'));
        return $this->render('header/header.html.twig', [
            'compagny'=> $compagny[0],
            'user'=>$user
        ]); 
    }
}
