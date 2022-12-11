<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use SebastianBergmann\CodeCoverage\Util\Filesystem as UtilFilesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('{_locale}')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'app_products_index', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('products/index.html.twig', [
            'products' => $productsRepository->findAll(),
        ]);
    }

    #[Route('/product/action/new', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductsRepository $productsRepository,TranslatorInterface $translator): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoFile = $form->get('photo')->getData();

             // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if($photoFile){
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('danger', $e->getMessage());
                    return $this->redirectToRoute('app_products_index');
                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setPhoto($newFilename);
            }

            $productsRepository->save($product, true);
            $this->addFlash('success',$translator->trans('produits.add'));

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}', name: 'app_products_show', methods: ['GET'])]
    public function show(Products $product = null,TranslatorInterface $translator): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        if($product == null){
            $this->addFlash('danger', $translator->trans('produits.not_found'));
            // On retourne une redirection vers la liste des catégories
            return $this->redirectToRoute('app_products_index');
        }
        
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/action/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, ProductsRepository $productsRepository,Filesystem $fs,TranslatorInterface $translator): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->get('photo')->getData() != null){
            $photoFile = $form->get('photo')->getData();

             // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if($photoFile){
                $newFilename = uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    if($product->getPhoto() != null){
                    $fs->remove($this->getParameter('upload_directory').'/'.$product->getPhoto());
                    }
                    $photoFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                        
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('danger', $e->getMessage());
                    return $this->redirectToRoute('app_products_index');
                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setPhoto($newFilename);
            }
        }
            $productsRepository->save($product, true);
            $this->addFlash('success',$translator->trans('produits.maj'));
            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/action/{id}', name: 'app_products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, ProductsRepository $productsRepository, TranslatorInterface $translator): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productsRepository->remove($product, true);
        }

        $this->addFlash('warning',$translator->trans('produits.delete'));
        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }
}
