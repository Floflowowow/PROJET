<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/form', name: 'form')]
final class FormController extends AbstractController
{
    private ?UserPasswordHasherInterface $passwordHasher = null;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('', name: '')]
    public function index(): Response
    {
        return $this->render('form/index.html.twig', [
            'controller_name' => 'FormController',
        ]);
    }
    #[Route('/new', name: '_new')]
    public function newAction(Request $request ,EntityManagerInterface $entityManager): Response
    {
        //Création de l'utilisateur et du formulaire correspondant
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est valide et submited alors on affiche un message et on revient au début (revoir le lien)
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $user->setAdmin(false); //On met admin à false par défaut
            $user->setRoles(['ROLE_USER']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $entityManager->flush();
            $this->addFlash('success', 'Compte utilisateur créé');
            return $this->redirectToRoute('security_login');
        }
        //Sinon on renvoie vers la création
        return $this->render('form/newClient.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/newproduct', name: '_newproduct')]
    public function newProductAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Création de l'utilisateur et du formulaire correspondant
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        // Si le formulaire est valide et submited alors on affiche un message et on revient au début (revoir le lien)
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Produit ajouté à la boutique');
            return $this->redirectToRoute('form_newproduct');
        }
        //Une fois terminé on affiche
        return $this->render('form/newProduct.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/newadmin', name: '_newadmin')]
    public function newAdminAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Création de l'utilisateur et du formulaire correspondant
        $user = new User();
        $user->setAdmin(true);
        $user->setRoles(['ROLE_ADMIN']);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est valide et submited alors on affiche un message et on revient au début (revoir le lien)
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Compte utilisateur créé');
            return $this->redirectToRoute('security_login');
        }
        //Une fois terminé on affiche
        return $this->render('form/newAdmin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/edit/{id}', name: '_edit')]
    public function editAction(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser()->getRoles()[0] === 'ROLE_ADMIN' || $this->getUser()->getId() === $user->getId()) {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $this->addFlash('success', 'Utilisateur modifié');

                return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
            }

            return $this->render('form/editClient.html.twig', [
                'myform' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('');
    }
}
