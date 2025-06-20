<?php

namespace App\Command;

use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\Pictures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-categories',
    description: 'Vérifie les catégories et teste la création de produits avec images',
)]
class CheckCategoriesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Vérification des catégories et test de création de produits');

        // Vérifier les catégories existantes
        $categories = $this->entityManager->getRepository(Categories::class)->findAll();
        
        if (empty($categories)) {
            $io->warning('Aucune catégorie trouvée !');
            $io->text('Création de catégories de test...');
            
            $testCategories = [
                'Football',
                'Basketball', 
                'Tennis',
                'Golf',
                'Natation',
                'Course à pied',
                'Musculation',
                'Yoga'
            ];
            
            foreach ($testCategories as $categoryName) {
                $category = new Categories();
                $category->setTitle($categoryName);
                $this->entityManager->persist($category);
            }
            
            $this->entityManager->flush();
            $io->success(count($testCategories) . ' catégories créées !');
            
            // Récupérer les catégories créées
            $categories = $this->entityManager->getRepository(Categories::class)->findAll();
        }

        $io->section('Catégories disponibles :');
        foreach ($categories as $category) {
            $io->text("• {$category->getTitle()} (ID: {$category->getId()})");
        }

        // Vérifier les produits existants
        $products = $this->entityManager->getRepository(Products::class)->findAll();
        $io->section('Produits existants :');
        
        if (empty($products)) {
            $io->text('Aucun produit trouvé.');
        } else {
            foreach ($products as $product) {
                $imageCount = $product->getPictures()->count();
                $io->text("• {$product->getTitle()} - {$product->getPrice()}€ - {$imageCount} image(s)");
            }
        }

        // Vérifier les images en base de données
        $pictures = $this->entityManager->getRepository(Pictures::class)->findAll();
        $io->section('Images en base de données :');
        
        if (empty($pictures)) {
            $io->text('Aucune image trouvée en base de données.');
        } else {
            foreach ($pictures as $picture) {
                $productTitle = $picture->getProducts() ? $picture->getProducts()->getTitle() : 'Aucun produit';
                $io->text("• {$picture->getPath()} (Produit: {$productTitle})");
            }
        }

        return Command::SUCCESS;
    }
} 