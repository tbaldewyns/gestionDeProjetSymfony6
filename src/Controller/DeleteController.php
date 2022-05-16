<?php

namespace App\Controller;

use App\Entity\DataType;
use App\Entity\User;
use App\Entity\Local;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeleteController extends AbstractController
{
    //Supprime l'utilisateur /{id}
    #[Route('/deleteUser/{id}', name: 'deleteUser')]
    public function deleteUser(User $user, ManagerRegistry $manager)
    {
        $manager->getManager()->remove($user);
        $manager->getManager()->flush();
         $this->addFlash("danger", "L'utilisateur à bien été supprimé");
        return $this->redirectToRoute("settings");
    }
    //Supprime le local /{id}
    #[Route('/deleteLocal/{id}', name: 'deleteLocal')]
    public function deleteLocal(Local $local, ManagerRegistry $manager)
    {
        $manager->getManager()->remove($local);
        $manager->getManager()->flush();
         $this->addFlash("danger", "Le local à bien été supprimé");
        return $this->redirectToRoute("settings");
    }
    //Supprime le type de donnée /{id}
    #[Route('/deleteDataType/{id}', name: 'deleteDataType')]
    public function deleteDataType(DataType $dataType, ManagerRegistry $manager)
    {
        $manager->getManager()->remove($dataType);
        $manager->getManager()->flush();
         $this->addFlash("danger", "Le type à bien été supprimé");
        return $this->redirectToRoute("settings");
    }
}