<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ChangePasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;

class AccountPasswordController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine){
        $this->entityManager = $doctrine->getmanager();
    }

    #[Route('/compte/modifier-mon-mot-de-passe', name: 'app_account_password')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = $this->getuser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request); // On récupère les données saisies pour les mettre dans l'objet $form

        if($form->isSubmitted() && $form->isValid()){
            
            $old_pwd = $form->get('old_password')->getData(); // On récupère l'ancien mdp que l'utilisateur à saisi

            if( $passwordHasher->isPasswordValid($user, $old_pwd) ){

                $new_pwd = $form->get('new_password')->getData();

                // Hashage du nouveau mdp
                $hashedPassword = $passwordHasher->hashPassword($user, $new_pwd);
                $user->setPassword($hashedPassword);
                // Pas besoin d'utiliser le methode persist puisque l'objet user est déjà géré par doctrine
                $this->entityManager->flush($user);

            }


        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
