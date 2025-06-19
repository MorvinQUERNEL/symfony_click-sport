<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{
    #[Route('/add', name: 'app_product_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a été ajouté avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
} 