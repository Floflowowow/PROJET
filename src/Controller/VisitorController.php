<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[Route('/', name: '')]


final class VisitorController extends AbstractController
{
    private ?UserPasswordHasherInterface $passwordHasher = null;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('', name: '')]
    public function indexAction(): Response
    {
        if ($this->getUser()){
            $args = array(
                'controller_name' => 'VisitorController',
                'user' => $this->getUser(),
            );
            return $this->render('visitor/accueil.html.twig', $args);
        }
        /*else {  //L'utilisateur est pas créé, on redirige

                $args = array(
                'controller_name' => 'VisitorController',
                'user' => null,
                'admin' => null,
            );

            return $this->redirectToRoute('form_new');
        }*/
         //REVOIR
        else {
            $args = array(
                'controller_name' => 'VisitorController',
                'user' => null,
            );
            return $this->render('visitor/accueil.html.twig',$args);
        }
    }

    public function menuAction(EntityManagerInterface $em): Response{
        if ($this->getUser()) {
            $cartRepository = $em->getRepository(Cart::class);
            $carts = $cartRepository->findAll();
            foreach ($carts as $cart) {
                if (($cart->getUser()) === $this->getUser()) {
                     $cart->setQuantite($cart->getQuantite() + 1);
                }
            }

            $args = array(
                'user' => $this->getUser(),
                'role' => $this->getUser()->getRoles(),
                'quantites' => $cart->getQuantite(),
            );
        }
        else {
            $args = array(
                'role' => null
            );
        }
        return $this->render('Layouts/menu.html.twig', $args);
    }

    public function headerAction(): Response{
        if ($this->getUser()) {
            $args = array(
                'role' => $this->getUser()->getRoles(),
                'user' => $this->getUser(),
            );
        }
        else {
            $args = array(
                'role' => null
            );
        }
        return $this->render('Layouts/header.html.twig', $args);
    }


}
