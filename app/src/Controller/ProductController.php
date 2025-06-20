<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Pictures;
use App\Form\ProductType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/add', name: 'app_product_add')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Gérer l'upload des images
                $uploadedFiles = $form->get('images')->getData();
                $uploadedImagesCount = 0;
                
                if ($uploadedFiles) {
                    $uploadDirectory = $this->getParameter('upload_directory');
                    
                    // Vérifier que le dossier existe et est accessible en écriture
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0755, true);
                    }
                    
                    if (!is_writable($uploadDirectory)) {
                        $this->addFlash('error', 'Le dossier d\'upload n\'est pas accessible en écriture');
                        return $this->render('product/add.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    }
                    
                    foreach ($uploadedFiles as $uploadedFile) {
                        if ($uploadedFile && $uploadedFile->isValid()) {
                            try {
                                // Générer un nom de fichier unique et sécurisé
                                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                                $safeFilename = $slugger->slug($originalFilename);
                                $extension = $uploadedFile->guessExtension();
                                $fileName = $safeFilename . '-' . uniqid() . '.' . $extension;
                                
                                // Chemin complet du fichier
                                $filePath = $uploadDirectory . '/' . $fileName;
                                
                                // Déplacer le fichier
                                $uploadedFile->move($uploadDirectory, $fileName);
                                
                                // Vérifier que le fichier a bien été créé
                                if (file_exists($filePath)) {
                                    // Créer l'entité Picture avec le chemin relatif
                                    $picture = new Pictures();
                                    $picture->setPath('/upload/' . $fileName);
                                    $product->addPicture($picture);
                                    $uploadedImagesCount++;
                                    
                                    $this->addFlash('success', 'Image "' . $originalFilename . '" uploadée avec succès');
                                } else {
                                    $this->addFlash('error', 'Erreur : Le fichier n\'a pas été créé sur le serveur');
                                }
                                
                            } catch (\Exception $e) {
                                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image "' . $uploadedFile->getClientOriginalName() . '" : ' . $e->getMessage());
                            }
                        } else {
                            $this->addFlash('error', 'Fichier invalide : ' . $uploadedFile->getClientOriginalName());
                        }
                    }
                }

                // Sauvegarder le produit et ses images
                try {
                    $entityManager->persist($product);
                    $entityManager->flush();
                    
                    $successMessage = 'Le produit "' . $product->getTitle() . '" a été ajouté avec succès !';
                    if ($uploadedImagesCount > 0) {
                        $successMessage .= ' (' . $uploadedImagesCount . ' image(s) uploadée(s))';
                    }
                    
                    $this->addFlash('success', $successMessage);
                    return $this->redirectToRoute('app_product_list');
                    
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de la sauvegarde en base de données : ' . $e->getMessage());
                }
                
            } else {
                // Afficher les erreurs de validation détaillées
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $fieldName = $error->getOrigin() ? $error->getOrigin()->getName() : 'Formulaire';
                    $errors[] = "Champ '$fieldName': " . $error->getMessage();
                }
                
                if (!empty($errors)) {
                    $this->addFlash('error', 'Erreurs de validation : ' . implode(', ', $errors));
                }
            }
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/products', name: 'app_product_list')]
    public function list(ProductsRepository $productsRepository): Response
    {
        $products = $productsRepository->findAll();
        
        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }
} 