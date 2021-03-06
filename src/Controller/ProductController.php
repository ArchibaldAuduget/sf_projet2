<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', priority: -1)]
    // On attribue le chemin {slug} à une variable
    public function category($slug, CategoryRepository $categoryRepository)
    {
        // On stocke dans $category la catégorie qui correspond au slug dans l'url
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        // On affiche une erreur si la catégorie n'éexiste pas
        if (!$category) {
            throw new NotFoundHttpException("La catégorie demandée n'éxiste pas");
            // $this->createNotFoundException("La catégorie demandée n'éxiste pas");
        }

        // On envoie la page avec le slug et la categorie en variable
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show')]
    public function show($slug, ProductRepository $productRepository) 
    {

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Ce produit n'éxiste pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);

    }

    #[Route('/admin/product/create', name: 'product_create')]
    public function create(FormFactoryInterface $factory, CategoryRepository $categoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        // On crée le builder via FormFactoryInterface
        // $builder = $factory->createBuilder(ProductType::class);

        // On renseigne les champs qu'on veut rentrer
        // La fonction add prend 3 params : le nom du champ, le type de champ (use Symfony\Component\Form\Extension\Core\Type\TextType; à importer pour chaque type)
        // et un tableau associatif d'options (Ex : label, attr (pour gérer les attributs html, comme une classe))
        // $builder
            // ->add('name', TextType::class, [
            //     'label' => 'Nom du produit',
            //     'attr' => ['placeholder' => 'Tapez le nom du produit']
            // ])
            // ->add('shortDescription', TextareaType::class, [
            //     'label' => 'Description courte',
            //     'attr' => ['placeholder' => 'Tapez une description courte mais parlante pour le visiteur']
            // ])
            // ->add('price', MoneyType::class, [
            //     'label' => 'Prix du produit',
            //     'attr' => ['placeholder' => 'Tapez le prix du produit en €']
            // ])
            // ->add('picture', UrlType::class, [
            //     'label' => 'Image du produit',
            //     'attr' => ['placeholder' => 'Entrez le lien de l\'image']
            // ])
            // ->add('category', EntityType::class, [
            //     'label' => 'Catégorie',
            //     // !! Le placeholder sur menus déroulants ne sont pas dans attr mais directement dans le form
            //     'placeholder' => '-- choisir une catégorie --',
            //     // choix possibles dans les menus déroulants.
            //     'class' => Category::class,
            //     'choice_label' => 'name',
            //     // Exemple de comment faire passer une fonction dessus
            //     // 'choice_label' => function(Category $category) {
            //     //     return strtoupper($category->getName());
            //     // }
            // ]);


        //     // on crée un tableau option pour stocker nos catégories
        //     $options = [];

        // // boucle qui récupère nos cat name et cat id et les envoie dans le tableau option
        // foreach($categoryRepository->findAll() as $category) {
        //     $options[$category->getName()] = $category->getId();
        // }

        // $builder->add('category', ChoiceType::class, [
        //     'label' => 'Catégorie',
        //     'attr' => ['class' => 'form-control'],
        //     // !! Le placeholder sur menus déroulants ne sont pas dans attr mais directement dans le form
        //     'placeholder' => '-- choisir une catégorie --',
        //     // choix possibles dans les menus déroulants. Ici on lui donne les valeurs stockées dans option
        //     'choices' => $options
        // ]);

        // la méthode getForm retourne un immense tableau de bcp de données, on doit encore la traiter
        // $form = $builder->getForm();

        // Permet de se passer du builder uniquement si une classe du formulaire existe
        $form = $this->createForm(ProductType::class);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            // $product = new Product;
            // $product
            //     ->setName($data['name'])
            //     ->setShortDescription($data['shortDescription'])
            //     ->setPrice($data['price'])
            //     ->setCategory($data['category']);

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getName(),
                'slug' => $product->getSlug()
            ]);

        }

        // On renseigne ici que l'on veut extraire de GetForm
        $formview = $form->createView();


        return $this->render('product/create.html.twig', [
            'formview' => $formview
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    // Annottation qui controle l'accès. Ne pas oublier importer classe IsGranted
    // #[IsGranted("ROLE_ADMIN", message: 'ptdr ta pa le droi')]
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator, Security $security)
    {
        // $client = [
        //     'nom' => 'Chamla',
        //     'prenom' => 'Lior',
        //     'voiture' => [
        //         'marque' => 'Hyundai',
        //         'couleur' => 'Noire'
        //     ]
        // ];
        
        // $collection = new Collection([
        //     'nom' => new NotBlank(['message' => "Le nom ne doit pas être vide"]),
        //     'prenom' => [
        //         new NotBlank(['message' => "Le prenom ne doit pas être vide"]),
        //         new Length(['min' => 3, 'minMessage' => "Le prénom ne doit pas faire moins de 3 caractères"])
        //     ],
        //     'voiture' => new Collection([
        //         'marque' => new NotBlank(['message' => 'La marque de la voiture est obligatoire']),
        //         'couleur' => new NotBlank(['message' => "La couleur de la voiture est obligatoire"])
        //     ])
            
        // ]);

        // $user = $security->getUser();
        // $user = $this->getUser();

        // if ($user === null) {
        //     return $this->redirectToRoute('app_login');
        // }

        // if ($this->isGranted("ROLE_ADMIN") === false) {
        //     throw new AccessDeniedHttpException("Vous n'avez paaaaas le droouuaaat !");
        // }
        // Methode qui permet de controler l'accès
        // $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'accès");

        $product = $productRepository->find($id);

        // $product permet de préremplir les champs et de se passer du setData()
        $form = $this->createForm(ProductType::class, $product);
        // // Permet de préremplir les champs avec ceux de la BDD
        // $form->setData($product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On peut se passer du getData aussi puisqu'on a renseigné $product dans le createForm
            // $product = $form->getData();
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }
        $formview = $form->createView();


        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formview' => $formview
        ]);
    }

}
