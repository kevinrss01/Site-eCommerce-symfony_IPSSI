<?php

namespace App\Controller;


use App\Entity\BasketContent;
use App\Entity\Products;
use App\Entity\User;
use App\Form\BasketContentType;
use App\Repository\BasketContentRepository;
use App\Repository\BasketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale}/basket/content')]
class BasketContentController extends AbstractController
{
    #[Route('/', name: 'app_basket_content_index', methods: ['GET'])]
    public function index(BasketContentRepository $basketContentRepository): Response
    {
        return $this->render('basket_content/index.html.twig', [
            'basket_contents' => $basketContentRepository->findAll(),
        ]);
    }

    #[Route('/new/{product}/{user}', name: 'app_basket_content_new', methods: ['GET', 'POST'])]
    public function new(Products $product =null,User $user = null, Request $request, BasketContentRepository $basketContentRepository, BasketRepository $basketRepository): Response
    {
       
        $basketContent = new BasketContent();

        $basket = $basketRepository->findOneByUtilisateur($user);
        // dd($basket);
        $basketContent->setBasket($basket);
        $basketContent->addProduct($product);

        if($product == null){
            return $this->redirectToRoute('app_products_index');
        }


        if($user == null){
            return $this->redirectToRoute('app_register');
        }
        
        $form = $this->createForm(BasketContentType::class, $basketContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $basketContentRepository->save($basketContent, true);

            return $this->redirectToRoute('app_basket_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('basket_content/new.html.twig', [
            'basket_content' => $basketContent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_basket_content_show', methods: ['GET'])]
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

        return $this->renderForm('basket_content/edit.html.twig', [
            'basket_content' => $basketContent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_basket_content_delete', methods: ['POST'])]
    public function delete(Request $request, BasketContent $basketContent, BasketContentRepository $basketContentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$basketContent->getId(), $request->request->get('_token'))) {
            $basketContentRepository->remove($basketContent, true);
        }

        return $this->redirectToRoute('app_basket_content_index', [], Response::HTTP_SEE_OTHER);
    }
}
