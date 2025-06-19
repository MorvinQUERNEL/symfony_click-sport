<?php

namespace App\Command;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un utilisateur administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si l'admin existe déjà
        $existingAdmin = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => 'admin@clicksport.com']);
        
        if ($existingAdmin) {
            $io->warning('Un administrateur avec l\'email admin@clicksport.com existe déjà.');
            $io->info('Email: admin@clicksport.com');
            $io->info('Mot de passe: admin123');
            return Command::SUCCESS;
        }

        $admin = new Users();
        $admin->setEmail('admin@clicksport.com');
        $admin->setName('Administrateur');
        $admin->setFirstname('Admin');
        $admin->setPhoneNumber('0123456789');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Administrateur créé avec succès !');
        $io->info('Email: admin@clicksport.com');
        $io->info('Mot de passe: admin123');
        $io->info('Rôle: ROLE_ADMIN');
        $io->info('Vous pouvez maintenant vous connecter et accéder aux fonctionnalités d\'administration');

        return Command::SUCCESS;
    }
} 