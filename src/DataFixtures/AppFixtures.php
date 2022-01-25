<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    // Necessite le composer require string
    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {

        //// version de fixtures sans Faker
        //  for($p = 0; $p < 100; $p++) {
        //     $product = new Product;
        //      $product
        //         ->setName("Produit $p")
        //         ->setPrice(mt_rand(100, 200))
        //         ->setSlug("produit_$p");
                    
        //     $manager->persist($product);
        //         }


        // Fonctionnalitée de la librairie faker
        $faker = Factory::create('fr_FR');
        // On ajoute des méthodes à faker qui viennent des extensions dl de faker
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User;

        $hash = $this->encoder->hashPassword($admin, "password");

        $admin
            ->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullName("admin")
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        for($u = 0; $u < 5; $u++) {
            $user = new User();
            $hash = $this->encoder->hashPassword($user, "password");
            $user
                ->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);
            $manager->persist($user);

        }

        // Ici on persiste 3 catégories, et pour chacune de ces 3 catégories on persiste entre 15 et 20 produits qui prendra sa catégorie.
        for ($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category
                ->setName($faker->department())
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

            for($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product;
                $product
                    // methode de l'extension Bezhanov
                    ->setName($faker->productName)
                    // methode de l'extension de Lior
                    ->setPrice($faker->price(4000, 20000))
                    // methode qui need le construct. Prend le name du product pour le passer en slug
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setPicture('https://picsum.photos/400/400?image=' . mt_rand(100, 700));
                
                $manager->persist($product);
            }
    
        
        }

        $manager->flush();
    }
}