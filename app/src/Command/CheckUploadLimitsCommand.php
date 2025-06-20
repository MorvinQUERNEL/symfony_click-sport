<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:check-upload-limits',
    description: 'Vérifie les limites d\'upload PHP',
)]
class CheckUploadLimitsCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Vérification des limites d\'upload PHP');

        $limits = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
        ];

        $io->table(
            ['Paramètre', 'Valeur'],
            array_map(function($key, $value) {
                return [$key, $value ?: 'Non défini'];
            }, array_keys($limits), array_values($limits))
        );

        // Vérifier le dossier d'upload
        $uploadDir = $this->parameterBag->get('upload_directory');
        $io->section('Dossier d\'upload');
        $io->text("Chemin : $uploadDir");
        
        if (is_dir($uploadDir)) {
            $io->text('✅ Le dossier existe');
            if (is_writable($uploadDir)) {
                $io->text('✅ Le dossier est accessible en écriture');
            } else {
                $io->text('❌ Le dossier n\'est pas accessible en écriture');
            }
        } else {
            $io->text('❌ Le dossier n\'existe pas');
        }

        return Command::SUCCESS;
    }
} 