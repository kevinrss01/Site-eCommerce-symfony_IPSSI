<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\BasketRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale}/user')]
class UserController extends AbstractController
{

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, BasketRepository $basketRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $baskets = $basketRepository->findPaidBasketByUtilisateur($user->getId());
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            $this->addFlash('success', 'Compte modifié !✅');

            return $this->redirectToRoute('app_user_edit',array('id'=>$user->getId()), Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'baskets'=>$baskets,
            'form' => $form->createView(),
        ]);
    }
}
