<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\BasketContent;
use App\Entity\Products;
use App\Entity\User;
use App\Form\BasketContentType;
use App\Repository\BasketContentRepository;
use App\Repository\BasketRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale}/basket/content')]
class BasketContentController extends AbstractController
{
    #[Route('/{user}', name: 'app_basket_content_index', methods: ['GET'])]
    public function index(User $user = null, BasketContentRepository $basketContentRepository, BasketRepository $basketRepository): Response
    {
        $basket = $basketRepository->findOneByUtilisateur($user);
        return $this->render('basket_content/index.html.twig', [
            'basket_contents' => $basketContentRepository->findByBasket($basket),
        ]);
    }

    #[Route('/new/{product}/{user}', name: 'app_basket_content_new', methods: ['GET', 'POST'])]
    // function qui crée un nouvelle élément du panier aprés avoir renseigner la quantité voulu
    public function new(Products $product = null, User $user = null, Request $request, BasketContentRepository $basketContentRepository, BasketRepository $basketRepository, ProductsRepository $productRepository): Response
    {

        $basketContent = new BasketContent();

        $basket = $basketRepository->findOneByUtilisateur($user);
        $basketContent->setBasket($basket);
        $basketContent->setProducts($product);

        if ($product == null) {
            return $this->redirectToRoute('app_products_index');
        }


        if ($user == null) {
            return $this->redirectToRoute('app_register');
        }

        $form = $this->createForm(BasketContentType::class, $basketContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($product->getStock() < $form->get('quantity')->getData()) {

                $this->addFlash('warning', 'Quantité supérieur au stock disponible');
                return $this->redirectToRoute('app_products_index');
            }
            $oldBasketContents= $basketContentRepository->findByBasket($basket);
            foreach ($oldBasketContents as $oldBasketContent) {
                if ($oldBasketContent->getProducts()->getId() == $basketContent->getProducts()->getId()) {
                    $quantity = $oldBasketContent->getQuantity() + $basketContent->getQuantity();
                    $oldBasketContent->setQuantity($quantity);
                    $product->setStock($product->getStock() - $form->get('quantity')->getData());

                    $productRepository->save($product, true);
                    $basketContentRepository->save($oldBasketContent, true);

                    $this->addFlash('success', 'Produit ajouté au panier');
                    return $this->redirectToRoute('app_basket_content_index', array('user' => $user->getId()), Response::HTTP_SEE_OTHER);
                }
            }

            $product->setStock($product->getStock() - $form->get('quantity')->getData());

            $productRepository->save($product, true);
            $basketContentRepository->save($basketContent, true);
            $this->addFlash('success', 'Produit ajouté au panier');
            return $this->redirectToRoute('app_basket_content_index', array('user' => $user->getId()), Response::HTTP_SEE_OTHER);
        }

        return $this->render('basket_content/new.html.twig', [
            'basket_content' => $basketContent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'app_basket_content_show', methods: ['GET'])]
    public function show(BasketContent $basketContent): Response
    {
        return $this->render('basket_content/show.html.twig', [
            'basket_content' => $basketContent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_basket_content_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BasketContent $basketContent, BasketContentRepository $basketContentRepository): Response
    {
        $form = $this->createForm(BasketContentType::class, $basketContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $basketContentRepository->save($basketContent, true);

            return $this->redirectToRoute('app_basket_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('basket_content/edit.html.twig', [
            'basket_content' => $basketContent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_basket_content_delete', methods: ['POST'])]
    public function delete(Request $request, BasketContent $basketContent, BasketContentRepository $basketContentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $basketContent->getId(), $request->request->get('_token'))) {
            $basketContentRepository->remove($basketContent, true);
        }

        return $this->redirectToRoute('app_basket_content_index', array('user' => $this->getUser()->getId()), Response::HTTP_SEE_OTHER);
    }
}
