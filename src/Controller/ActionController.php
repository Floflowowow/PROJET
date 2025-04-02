<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/action', name: 'action')]
class ActionController extends AbstractController
{
    #[Route('/add/{productid}',
        name: '_add',
        requirements: ['productid' => '[0-9]+'],)]
    public function addAction(int $productid, EntityManagerInterface $em, Request $request): Response
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($this->getUser());

        $quantites = $request->request->get('quantite');

        //Modification du stock product
        $productRepository = $em->getRepository(Product::class);
        $product = $productRepository->find($productid);
        $product->setQuantite($product->getQuantite()-$quantites);

        $cartRepository = $em->getRepository(Cart::class);
        $carts = $cartRepository->findAll();


        foreach ($carts as $cart) {
            if (($cart->getProducts())->contains($product) && (($cart->getUsers())->contains($user))) {
                $cart->setQuantite($cart->getQuantite()+$quantites);

                $em->flush();

                $this->addFlash('info', 'ajout dans le cart de ' . $quantites . ' ' . $product->getLabel() . ' au total ' . $cart->getQuantite());
                return $this->redirectToRoute('');
            }
        }

        $cartNew = New Cart();
        $cartNew->addUser($user)
            ->addProduct($product)
            ->setQuantite($quantites);

        $em->persist($cartNew);

        $em->flush();

        $this->addFlash('info', 'ajout dans le cart de ' . $quantites . ' ' . $product->getLabel());
        return $this->redirectToRoute('');
    }

    #[Route('/supp/user/{userid}',
        name: '_supp_user',
        requirements: ['userid' => '[0-9]+'])]
    public function suppAction(int $userid, EntityManagerInterface $em, Request $request): Response
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($userid);

        $cartRepository = $em->getRepository(cart::class);
        $carts = $cartRepository->findAll();

        if ($this->getUser()->getRoles()[0] !== 'ROLE_ADMIN') {
            $this->addFlash('info', 'Suppression de ' . $userid . ' impossible');
            return $this->redirectToRoute('list_users');
        }

        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();

        foreach ($carts as $cart) {
            if (($cart->getUsers())->contains($user)) {
                foreach ($products as $product) {
                    if (($cart->getproducts())->contains($product)) {
                        $product->setQuantite($product->getQuantite()+$cart->getQuantite());

                        $em->remove($cart);
                    }
                }
            }
        }
        $em->remove($user);
        $em->flush();

        $this->addFlash('info', 'Suppression de ' . $userid);

        return $this->redirectToRoute('list_users');
    }

   /* #[Route('/commander',
        name: '_commander')]
    public function commanderAction(EntityManagerInterface $em): Response
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($this->getUser());

        $cartRepository = $em->getRepository(cart::class);
        $carts = $cartRepository->findAll();

        foreach ($carts as $cart) {
            if (($cart->getUsers())->contains($user)) {
                $em->remove($cart);
            }
        }
        $em->flush();
        $this->addFlash('info', 'Commander avec succes');

        return $this->redirectToRoute('liste_carts');
    }
    */
    #[Route('/empty',
        name: '_empty')]
    public function emptyAction(EntityManagerInterface $em): Response
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($this->getUser());

        $cartRepository = $em->getRepository(Cart::class);
        $carts = $cartRepository->findAll();

        $productRepository = $em->getRepository(product::class);
        $products = $productRepository->findAll();

        foreach ($carts as $cart) {
            if (($cart->getUsers())->contains($user)) {
                foreach ($products as $product) {
                    if (($cart->getproducts())->contains($product)) {
                        $product->setQuantite($product->getQuantite()+$cart->getQuantite());

                        $em->remove($cart);
                    }
                }
            }
        }
        $em->flush();
        $this->addFlash('info', 'Vider avec succes');

        return $this->redirectToRoute('');
    }

    #[Route('/supp/product/{productId}',
        name: '_supp_product',
        requirements: ['productId' => '[0-9]+'])]
    public function suppProductAction(int $productId, EntityManagerInterface $em): Response
    {
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($this->getUser());

        $cartRepository = $em->getRepository(Cart::class);
        $carts = $cartRepository->findAll();

        $productRepository = $em->getRepository(product::class);
        $product = $productRepository->find($productId);

        foreach ($carts as $cart) {
            if (($cart->getUsers())->contains($user) && ($cart->getproducts())->contains($product)) {
                $product->setQuantite($product->getQuantite() + $cart->getQuantite());
                $em->remove($cart);
            }
        }


        $em->flush();
        $this->addFlash('info', 'Vider avec succes');

        return $this->redirectToRoute('');
    }

    #[Route('/addtocart/{productId}', name: '_addtocart', requirements: ['productId' => '[0-9]+'])]
    public function addProductToCart(int $productId, EntityManagerInterface $em, Request $request): Response {
        // Vérifiez si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un produit au panier.');
            return $this->redirectToRoute('security_login');
        }

        // Trouver le produit
        $productRepository = $em->getRepository(Product::class);
        $product = $productRepository->find($productId);

        if (!$product) {
            $this->addFlash('error', 'Produit non trouvé.');
            return $this->redirectToRoute('home'); // Redirige vers la page d'accueil si le produit n'est pas trouvé
        }

        // Vérifiez si l'utilisateur a déjà un panier
        $cartRepository = $em->getRepository(Cart::class);
        $cart = $cartRepository->findOneBy(['user' => $user]);

        if (!$cart) {
            // Créer un panier si l'utilisateur n'en a pas
            $cart = new Cart();
            $cart->setUser($user); // Assurez-vous que la relation ManyToOne est bien configurée dans Cart
            $em->persist($cart);
        }

        // Ajouter le produit au panier
        $cart->addProduct($product);
        $product->setQuantity($product->getQuantity() - 1); // Décrémenter la quantité du produit

        // Sauvegarder les modifications
        $em->flush();

        $this->addFlash('success', 'Produit ajouté au panier avec succès.');

        // Redirige vers la page du panier ou une autre page appropriée
        return $this->redirectToRoute('');
    }


}
