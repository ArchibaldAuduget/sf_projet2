<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Product;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(EntityManagerInterface $em, ProductRepository $productRepository) {

        // On instancie un objet de la classe entity/Product.php (qui représente la table produit) pour créer un produit
        // $product = new Product;


        // $product->setName('Table en métal');
        // $product->setPrice(3000);
        // $product->setSlug('table_en_metal');

        // $product
        //     ->setName('Table en métal')
        //     ->setPrice(3000)
        //     ->setSlug('table_en_metal');

        // Prépare la requete sql !!! LE persist ne se fait que pour une entité qui n'éxistait pas auparavant !!!
        // $em->persist($product);

        // Ou on passe par le repo pour update
        // $productRepository = $em->getRepository(Product::class);
        // $product = $productRepository->find(3);
        // $product->SetPrice(2500);

        // // Envoie les requetes preparées
        // $em->flush();

        // dd($product);

        $products = $productRepository->findBy([], [], 3);

        return $this->render('home.html.twig', [
            'products' => $products
        ]);
    }
}