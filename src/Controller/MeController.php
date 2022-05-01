<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeController extends AbstractController
{
     public function __construct(private Security $security)
    {
    }

    public function __invoke()
    {
        $user = $this->security->getUser();
        return $user;
    }
}