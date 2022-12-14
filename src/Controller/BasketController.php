<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\User;
use App\Form\BasketType;
use App\Repository\BasketContentRepository;
use App\Repository\BasketRepository;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale}/basket')]
class BasketController extends AbstractController
{

    #[Route('/', name: 'app_basket_index', methods: ['GET'])]
    public function index(BasketRepository $basketRepository, BasketContentRepository $basketContentRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('basket/index.html.twig', [
            'baskets' => $basketRepository->findByUtilisateur($this->getUser()),
            'basket_contents' => $basketContentRepository->findByBasket('basket'),
        ]);
    }

    //Récupère tous les paniers de tous les utilisateurs et les affiche
    #[Route('/allBaskets', name: 'app_all_basket_index', methods: ['GET'])]
    public function indexAll(BasketRepository $basketRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('basket/allBaskets.html.twig', [
            'baskets' => $basketRepository->findAll(),
        ]);
    }

    #[Route('basket/action/new/{basket}', name: 'app_basket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BasketRepository $basketRepository, Basket $basket = null,): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $userid = $user->getId();
        if ($basket == null) {
            return $this->redirectToRoute('app_basket_content_index', array('user' => $userid), Response::HTTP_SEE_OTHER);
        }
        $basket =$basketRepository->findById($basket);
        $basket->setBuyDate(new \DateTime());
        $basket->setState(True);
        $basketRepository->save($basket, true);
        $newBasket = new Basket();
        $newBasket->setOwner($basket->getOwner());
        $basketRepository->save($newBasket, true);
        return $this->redirectToRoute('app_basket_content_index', array('user' => $userid), Response::HTTP_SEE_OTHER);
    }

    #[Route('/basket/{id}', name: 'app_basket_show', methods: ['GET'])]
    public function show(Basket $basket): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('basket/show.html.twig', [
            'basket' => $basket,
        ]);
    }
    #[Route('basket/action/{id}/edit', name: 'app_basket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Basket $basket, BasketRepository $basketRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $basketRepository->save($basket, true);

            return $this->redirectToRoute('app_basket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('basket/edit.html.twig', [
            'basket' => $basket,
            'form' => $form,
        ]);
    }

    #[Route('/basket/action/{id}', name: 'app_basket_delete', methods: ['POST'])]
    public function delete(Request $request, Basket $basket, BasketRepository $basketRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('delete' . $basket->getId(), $request->request->get('_token'))) {
            $basketRepository->remove($basket, true);
        }

        return $this->redirectToRoute('app_basket_index', [], Response::HTTP_SEE_OTHER);
    }
}
