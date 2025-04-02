<?php

namespace App\Controller;


use App\Entity\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Country;
use App\Entity\User;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/list', name: 'list')]
final class ListController extends AbstractController
{
    #[Route('', name: '')]
    public function index(): Response
    {
        return $this->render('list/index.html.twig', [
            'controller_name' => 'ListController',
        ]);
    }

    #[Route('/users',name : '_users')]
    public function listUsersAction(EntityManagerInterface $em): Response{
        if ($this->getUser()->getRoles()[0] !== 'ROLE_ADMIN') {
            return $this->redirectToRoute('');
        }
            $userRepository = $em->getRepository(User::class);
            $users = $userRepository->findAll();

            $args = array("users" => $users);
            return $this->render('list/usersList.html.twig', $args);
    }

    #[Route('/products', name: '_products')]
    public function listeproductsAction(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $userCountry = $user->getCountry();

        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();

        /*$cartRepository = $em->getRepository(Cart::class);
        $carts = $cartRepository->findAll();

        $cartsuser = [];

        foreach ($carts as $cart) {
            if (($cart->getUsers())->contains($user)){
                $cartsuser[] = $cart;
            }
        }*/

        $args = array(
            'products' => $products,
        );

        return $this->render('list/productList.html.twig', $args);
    }

}
