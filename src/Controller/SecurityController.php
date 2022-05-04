<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/admin/register', name: 'register')]
    public function register(Request $request, ManagerRegistry $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        //Creation d'un nouvel objet User
        $user = new User();
        //Creation du formulaire sur base d'un formulaire crée au préalable 
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        //Vérification de la conformité des données entrées par l'utilisateur
        if ($form->isSubmitted() && $form->isValid()) {
            //Chiffrement du mot de passe selon l'algorytme Bcrypt
            $hashedPassword = $passwordHasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hashedPassword);

            if ($user->getAccountType() == ""){
                $user->setAccountType("ROLE_ADMIN");
            }
            $manager->getManager()->persist($user);
            //Envoie des données vers la base de données
            $manager->getManager()->flush();

            $this->addFlash("success", "Le compte à bien été créé");
            return $this->redirectToRoute('index');
        }
        
        return $this->render('security/register.html.twig', [
            //Envoie du formulaire à la vue
            'registrationForm' => $form->createView()
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(Request $request, ManagerRegistry $manager,AuthenticationUtils $authenticationUtils, UserRepository $userRepo, UserPasswordHasherInterface $passwordHasher): Response
    {

        $userList = $userRepo->findAll();

        if($userList == null){
            $user = new User();
            $user->setFirstname("Tanguy");
            $user->setLastname("Baldewyns");  
            $user->setEmail("tanguy.baldewyns@gmail.com");
            $hashedPassword = $passwordHasher->hashPassword($user,"aaaaaa");
            $user->setPassword($hashedPassword);

            $user->setAccountType("ROLE_SUPERADMIN");
            $manager->getManager()->persist($user);
            //Envoie des données vers la base de données
            $manager->getManager()->flush(); 
        }
        
        if ($this->getUser() != null){
            return $this->redirectToRoute('userprofile', [
            'id' => $this->getUser()->getUserIdentifier()
        ]);
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig', [
            "error" => $error
        ]);
    }

    #[Route('/autoRedirect', name: 'autoRedirect')]
    public function autoRedirect()
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('error404');
        }

        return $this->redirectToRoute('userprofile', [
            'id' => $this->getUser()->getUserIdentifier()
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
    }

    #[Route('/error404', name: 'error404')]
    public function error404()
    {
        return $this->render('error/error404.html.twig');
    }

    #[Route('/error403', name: 'error403')]
    public function error403()
    {
        return $this->render('error/error403.html.twig');
    }

    #[Route('/admin/userprofile/{id}', name: 'userprofile')]
    public function userprofile(User $user): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('error404');
        }

        if ($this->getUser()->getUserIdentifier() != $user->getId()) {
            return $this->redirectToRoute('error403');
        }
        

        return $this->render('/security/userprofile.html.twig', [
            'user' => $user
        ]);
    }
    #[Route('/api/login', name: 'api_login')]
    public function apiLogin(#[CurrentUser] ?User $user): Response
    {
        return $this->json([
            'id' => $user->getId(),
            'accountType' => $user->getAccountType(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);
    }
}