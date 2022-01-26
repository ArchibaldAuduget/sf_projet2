<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartController extends AbstractController
{

  protected $productRepository;
  protected $cartService;

  public function __construct(ProductRepository $productRepository, CartService $cartService)
  {
    $this->productRepository = $productRepository;
    $this->cartService = $cartService;
  }

    #[Route('/cart/add/{id}', name: 'cart_add', requirements: ['id' => '\d+'])]
    public function add($id, Request $request)
    {
      $product = $this->productRepository->find($id);

      if (!$product) {
          throw $this->createNotFoundException("LE produit $id n'éxiste pas");
      }

      $this->cartService->add($id);

      $this->addFlash('success', "Produit ajouté au panier");

      if ($request->query->get('returnToCart') == true) {
        $this->redirectToRoute("cart_show");
      }

      return $this->redirectToRoute('product_show', [
          'category_slug' => $product->getCategory()->getSlug(),
          'slug' => $product->getSlug()
      ]);
    }

    #[Route('/cart', name: 'cart_show')]
    public function show()
    {
      $detailedCart = $this->cartService->getDetailedCartItems();
      $total = $this->cartService->GetTotal();

      return $this->render('cart/index.html.twig', [
        'items' => $detailedCart,
        'total' => $total
      ]);
    }

    #[Route('/cart/delete/{id}', name: 'cart_delete', requirements:['id' => '\d+'])]
    public function delete($id)
    {
      $product = $this->productRepository->find($id);

      if(!$product) {
        throw $this->createNotFoundException("La produit $id n'existe pas et ne peut pas être suprrimé");
      }

      $this->cartService->remove($id);

      $this->addFlash("success", "Le produit a bien été supprimé du panier");

      return $this->redirectToRoute("cart_show");
    }

    #[Route('/cart/decrement/{id}', name: 'cart_decrement', requirements:['id' => '\d+'])]
    public function decrement($id)
    {
      $product = $this->productRepository->find($id);

      if(!$product) {
        throw $this->createNotFoundException("La produit $id n'existe pas et ne peut pas être retiré");
      }

      $this->cartService->decrement($id);

      $this->addFlash("success", "Le produit a bien été retiré");

      return $this->redirectToRoute("cart_show");
    }
}
