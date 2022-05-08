<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInfosType;
use App\Form\PasswordInfosType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EditController extends AbstractController
{
    #[Route('/editUser/{id}', name: 'editUser')]
    public function index(User $user, Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('error404');
        }

        if ($this->getUser()->getUserIdentifier() != $user->getId()) {
            return $this->redirectToRoute('error403');
        }
        $infosForm = $this->createForm(UserInfosType::class, $user);

        $infosForm->handleRequest($request);
        if ($infosForm->isSubmitted() && $infosForm->isValid()) {

            $manager->getManager()->flush();
            $this->addFlash("success", "Vos informations ont été modifié.e.s");
            return $this->redirectToRoute('editUser', [
                'id' => $user->getId()
            ]);
        }

        $passwordsForm = $this->createForm(PasswordInfosType::class, $user);

        $passwordsForm->handleRequest($request);

        if ($passwordsForm->isSubmitted() && $passwordsForm->isValid()) {

            $hashedPassword = $passwordHasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hashedPassword);

            $manager->getManager()->flush();
            $this->addFlash("success", "Votre mot de passe à été modifié");
            return $this->redirectToRoute('editUser', [
                'id' => $user->getId()
            ]);
        }
        
        return $this->render('edit/editUser.html.twig', [
            'infosForm' => $infosForm->createView(),
            'passwordsForm' => $passwordsForm->createView(),
            'user' => $user,
        ]);
    }
}