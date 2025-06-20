<?php

namespace App\Command;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-categories',
    description: 'Vérifier et créer des catégories si nécessaire',
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

        // Vérifier s'il y a des catégories
        $categoriesCount = $this->entityManager->getRepository(Categories::class)->count([]);
        
        if ($categoriesCount === 0) {
            $io->warning('Aucune catégorie trouvée. Création de catégories par défaut...');
            
            $defaultCategories = [
                'Football',
                'Basketball',
                'Tennis',
                'Golf',
                'Natation',
                'Fitness',
                'Running',
                'Vélo',
                'Escalade',
                'Yoga'
            ];

            foreach ($defaultCategories as $categoryName) {
                $category = new Categories();
                $category->setTitle($categoryName);
                $this->entityManager->persist($category);
            }

            $this->entityManager->flush();
            $io->success(sprintf('%d catégories ont été créées avec succès !', count($defaultCategories)));
        } else {
            $io->success(sprintf('%d catégories trouvées dans la base de données.', $categoriesCount));
        }

        return Command::SUCCESS;
    }
} 