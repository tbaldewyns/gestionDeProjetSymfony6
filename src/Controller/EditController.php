<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EditController extends AbstractController
{
    #[Route('/editUser/{id}', name: 'editUser')]
    public function index(User $user): Response
    {
        return $this->render('edit/editUser.html.twig', [
            'controller_name' => 'EditController',
        ]);
    }
}