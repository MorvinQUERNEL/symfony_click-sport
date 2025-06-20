<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Pictures;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/add', name: 'app_product_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Products();
        // Initialiser la collection avec un objet Pictures pour afficher le champ d'image
        $product->getPictures()->add(new \App\Entity\Pictures());
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des images uploadées
            $picturesForms = $form->get('pictures');
            foreach ($picturesForms as $pictureForm) {
                $imageFile = $pictureForm->get('path')->getData();
                $picture = $pictureForm->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', "Erreur lors de l'upload de l'image : " . $e->getMessage());
                        continue;
                    }

                    $picture->setPath($newFilename);
                    $picture->setProducts($product);
                    $entityManager->persist($picture);
                }
            }

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