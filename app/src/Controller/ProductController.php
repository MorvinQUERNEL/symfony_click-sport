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
            // Debug: Afficher les données du formulaire
            $this->addFlash('info', 'Formulaire soumis - Début du traitement');
            
            if ($form->isValid()) {
                $this->addFlash('info', 'Formulaire valide - Traitement des images');
                
                // Gérer l'upload des images
                $uploadedFiles = $form->get('images')->getData();
                $uploadedImagesCount = 0;
                $uploadedImagePaths = [];
                
                // Debug: Vérifier si des fichiers ont été reçus
                if ($uploadedFiles) {
                    $this->addFlash('info', 'Fichiers reçus : ' . count($uploadedFiles) . ' fichier(s)');
                } else {
                    $this->addFlash('warning', 'Aucun fichier reçu dans le formulaire');
                }
                
                if ($uploadedFiles) {
                    $uploadDirectory = $this->getParameter('upload_directory');
                    
                    // Debug: Afficher le chemin du dossier
                    $this->addFlash('info', 'Dossier d\'upload : ' . $uploadDirectory);
                    
                    // Vérifier que le dossier existe et est accessible en écriture
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0755, true);
                        $this->addFlash('info', 'Dossier d\'upload créé');
                    }
                    
                    if (!is_writable($uploadDirectory)) {
                        $this->addFlash('error', 'Le dossier d\'upload n\'est pas accessible en écriture');
                        return $this->render('product/add.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    } else {
                        $this->addFlash('info', 'Dossier d\'upload accessible en écriture');
                    }
                    
                    // Validation des fichiers
                    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                    $maxFileSize = 5 * 1024 * 1024; // 5MB
                    
                    foreach ($uploadedFiles as $index => $uploadedFile) {
                        $this->addFlash('info', 'Traitement du fichier ' . ($index + 1) . ' : ' . $uploadedFile->getClientOriginalName());
                        
                        if ($uploadedFile && $uploadedFile->isValid()) {
                            // Vérifier le type MIME
                            $mimeType = $uploadedFile->getMimeType();
                            $this->addFlash('info', 'Type MIME : ' . $mimeType);
                            
                            if (!in_array($mimeType, $allowedMimeTypes)) {
                                $this->addFlash('error', 'Le fichier "' . $uploadedFile->getClientOriginalName() . '" n\'est pas une image valide. Types acceptés : JPG, PNG, WebP, GIF');
                                continue;
                            }
                            
                            // Vérifier la taille
                            $fileSize = $uploadedFile->getSize();
                            $this->addFlash('info', 'Taille du fichier : ' . ($fileSize / 1024) . ' KB');
                            
                            if ($fileSize > $maxFileSize) {
                                $this->addFlash('error', 'Le fichier "' . $uploadedFile->getClientOriginalName() . '" est trop volumineux (max 5MB)');
                                continue;
                            }
                            
                            try {
                                // Générer un nom de fichier unique et sécurisé
                                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                                $safeFilename = $slugger->slug($originalFilename);
                                $extension = $uploadedFile->guessExtension();
                                $fileName = $safeFilename . '-' . uniqid() . '.' . $extension;
                                
                                $this->addFlash('info', 'Nom de fichier généré : ' . $fileName);
                                
                                // Chemin complet du fichier
                                $filePath = $uploadDirectory . '/' . $fileName;
                                
                                // Déplacer le fichier
                                $uploadedFile->move($uploadDirectory, $fileName);
                                
                                // Vérifier que le fichier a bien été créé
                                if (file_exists($filePath)) {
                                    $uploadedImagePaths[] = '/upload/' . $fileName;
                                    $uploadedImagesCount++;
                                    
                                    // Créer l'entité Pictures et l'ajouter au produit
                                    $picture = new Pictures();
                                    $picture->setPath('/upload/' . $fileName);
                                    $product->addPicture($picture);
                                    
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

                // Essayer de sauvegarder en base de données, sinon afficher un résumé
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
                    // Si la base de données échoue, afficher un résumé des uploads
                    $this->addFlash('warning', '⚠️ Base de données non disponible - Les images ont été uploadées avec succès !');
                    
                    if ($uploadedImagesCount > 0) {
                        $this->addFlash('success', '✅ ' . $uploadedImagesCount . ' image(s) uploadée(s) dans le dossier public/upload/');
                        $this->addFlash('info', '📁 Chemins des images : ' . implode(', ', $uploadedImagePaths));
                    }
                    
                    $this->addFlash('info', '💡 Pour sauvegarder en base de données, exécutez : php bin/console doctrine:migrations:migrate');
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
} 