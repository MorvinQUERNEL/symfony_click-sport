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
    name: 'app:create-test-data',
    description: 'Créer des données de test pour l\'application',
)]
class CreateTestDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Création des données de test');

        // Créer des catégories
        $categories = [
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

        $createdCategories = 0;
        foreach ($categories as $categoryName) {
            // Vérifier si la catégorie existe déjà
            $existingCategory = $this->entityManager->getRepository(Categories::class)
                ->findOneBy(['title' => $categoryName]);

            if (!$existingCategory) {
                $category = new Categories();
                $category->setTitle($categoryName);
                $this->entityManager->persist($category);
                $createdCategories++;
                $io->text("✅ Catégorie créée : $categoryName");
            } else {
                $io->text("ℹ️  Catégorie existante : $categoryName");
            }
        }

        $this->entityManager->flush();

        $io->success("$createdCategories nouvelles catégories créées avec succès !");
        $io->text('Vous pouvez maintenant tester le formulaire d\'ajout de produits.');

        return Command::SUCCESS;
    }
} 