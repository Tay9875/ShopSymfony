<?php
namespace App\Command;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/*
Exercice 1 - Import CSV

Voilà un fichier CSV formatté pour l’exercice.

Créez une commande app:import-produits qui :

    - Prend en argument le chemin vers un fichier CSV
    - Lit le CSV ligne par ligne (utiliser fgetcsv)
    - Crée ou met à jour les produits en base (upsert basé sur la référence)
    Affiche un tableau récapitulatif : créés / mis à jour / erreurs
    Supporte --format=table|json pour la sortie

*/

// A CONTINUER DE CORRIGER

#[AsCommand(
    name: 'app:import-produits',
    description: 'Import les donnees d un fichier CSV',
)]
class ImportCsvCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file_path', InputArgument::REQUIRED, 'Chemin fichier CSV à importer')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Format de sortie table|json', 'table')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file_path = $input->getArgument('file_path');
        $format = $input->getOption('format');

        if (!file_exists($file_path)) {
            $io->error('Fichier introuvable : ' . $file_path);
            return Command::FAILURE;
        }

        $file = fopen($file_path, 'r');
        $headers = fgetcsv($file, 0, ';'); // Ligne d'en-têtes

        $crees = 0;
        $majCount = 0;
        $erreurs = 0;

        while (($ligne = fgetcsv($file, 0, ';')) !== false) {
            if (empty(array_filter($ligne))) {
                $io->warning("Données manquantes, le produit a été ignoré.");
                $erreurs++;    
                continue;
            }
            $data = array_combine($headers, $ligne);
            // Upsert basé sur la référence
            $produit = $this->em->getRepository(Produit::class)
                ->findOneBy(['reference' => $data['Reference']]);

            if (!$produit) {
                $produit = new Produit();
                $crees++;
                $io->info("Création du produit : " . $data['Reference']);
            } else {
                $majCount++;
                $io->info("Mise à jour du produit : " . $data['Reference']);
            }

            $produit->setReference($data['Reference']);
            $produit->setNom($data['Nom']);
            $produit->setPrix($data['Prix']);

            $this->em->persist($produit);
        }

        fclose($file);

        if($crees > 0 || $majCount > 0) {
            $io->info("Enregistrement en base...");
            $this->em->flush();
        }
        
        if ($format === 'json') {
            $output->writeln(json_encode([
                'créés' => $crees,
                'mis à jour' => $majCount,
                'erreurs' => $erreurs,
            ]));
        } else {
            $io->table(
                ['Créés', 'Mis à jour', 'Erreurs'],
                [[$crees, $majCount, $erreurs]]
            );
        }

        $io->success('Import terminé !');
        return Command::SUCCESS;
    }
}