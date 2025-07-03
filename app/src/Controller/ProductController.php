<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Pictures;
use App\Form\ProductType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class ProductController extends AbstractController
{
    #[Route('/admin/product/add', name: 'app_product_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Gérer l'upload des images
            $uploadedFiles = $form->get('pictureFiles')->getData();
            
            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    try {
                        $picture = $fileUploader->upload($uploadedFile);
                        $product->addPicture($picture);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Une erreur est survenue lors de l\'upload d\'une image.');
                    }
                }
            }

            // 3. Persister le produit (et les images grâce à la cascade)
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit "' . $product->getTitle() . '" a été ajouté avec succès !');
            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/products', name: 'app_product_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Products::class)->findAll();
        
        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/uploaded-images', name: 'app_uploaded_images')]
    public function showUploadedImages(): Response
    {
        $uploadDirectory = $this->getParameter('upload_directory');
        $images = [];
        
        if (is_dir($uploadDirectory)) {
            $files = glob($uploadDirectory . '/*');
            foreach ($files as $file) {
                if (is_file($file) && in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $images[] = [
                        'name' => basename($file),
                        'path' => '/upload/' . basename($file),
                        'size' => filesize($file),
                        'date' => filemtime($file)
                    ];
                }
            }
        }
        
        return $this->render('product/uploaded_images.html.twig', [
            'images' => $images,
        ]);
    }

    #[Route('/test-upload', name: 'app_test_upload')]
    public function testUpload(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $uploadedFiles = $request->files->get('test_files');
            
            if ($uploadedFiles) {
                $uploadDirectory = $this->getParameter('upload_directory');
                $uploadedCount = 0;
                
                foreach ($uploadedFiles as $file) {
                    if ($file && $file->isValid()) {
                        $fileName = 'test-' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadDirectory, $fileName);
                        $uploadedCount++;
                    }
                }
                
                $this->addFlash('success', $uploadedCount . ' fichier(s) uploadé(s) avec succès !');
            } else {
                $this->addFlash('error', 'Aucun fichier reçu');
            }
        }
        
        return $this->render('product/test_upload.html.twig');
    }

    #[Route('/admin/product/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Products $product, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF pour la sécurité
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            // Supprimer les fichiers images associés
            foreach ($product->getPictures() as $picture) {
                $filePath = $this->getParameter('upload_directory') . '/' . basename($picture->getPath());
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit "' . $product->getTitle() . '" a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token de sécurité invalide.');
        }

        return $this->redirectToRoute('app_product_list');
    }

    #[Route('/admin/product/{id}/edit', name: 'app_product_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Products $product, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload des nouvelles images
            $uploadedFiles = $form->get('pictureFiles')->getData();
            
            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile) {
                    try {
                        $picture = $fileUploader->upload($uploadedFile);
                        $product->addPicture($picture);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Une erreur est survenue lors de l\'upload d\'une image.');
                    }
                }
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Le produit "' . $product->getTitle() . '" a été modifié avec succès.');
            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/picture/{id}/delete', name: 'app_product_delete_picture', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deletePicture(Pictures $picture, EntityManagerInterface $entityManager): Response
    {
        $product = $picture->getProducts();
        $filePath = $this->getParameter('upload_directory') . '/' . basename($picture->getPath());
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $entityManager->remove($picture);
        $entityManager->flush();

        $this->addFlash('success', 'L\'image a été supprimée avec succès.');

        return $this->redirectToRoute('app_product_edit', ['id' => $product->getId()]);
    }
} 