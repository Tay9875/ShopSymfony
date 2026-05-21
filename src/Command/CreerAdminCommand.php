<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/*
Exercice 2 - Commande interactive

Créez une commande app:creer-admin qui :

    N’accepte aucun argument, tout est saisi interactivement
    Demande l’email (avec validation format)
    Demande le mot de passe (avec confirmation)
    Demande le rôle parmi ['ROLE_ADMIN', 'ROLE_MODERATEUR', 'ROLE_EDITOR']
    Affiche un récapitulatif et demande confirmation avant création
    Hache le mot de passe via UserPasswordHasherInterface

*/

#[AsCommand(
    name: 'app:creer-admin',
    description: 'Création d un compte admin interactif',
)]

class CreerAdminCommand extends Command
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $prenom = $io->ask('Quel est ton prenom ? ');
        $nom = $io->ask('Quel est ton nom ? ');
        $email = $io->ask('Email du nouvel admin');
        $mdp = $io->askHidden('Mot de passe: ');
        $confirmationMdp = $io->askHidden('Confirmez le mot de passe');
        while ($mdp !== $confirmationMdp) {
            $io->error('Les mots de passe ne correspondent pas. Veuillez réessayer.');
            $mdp = $io->askHidden('Mot de passe');
            $confirmationMdp = $io->askHidden('Confirmez le mot de passe');
        }

        $role = $io->choice('Rôle:', ['ROLE_ADMIN', 'ROLE_MODERATEUR', 'ROLE_EDITOR']);
        $io->section('Récapitulatif');
        $io->table(
            ['Email', 'Rôle'],
            [[$email, $role]]
        );
        $confirmation = $io->confirm('Confirmez-vous la création de ce compte admin ?',false);
        
        if (!$confirmation) {
            $io->warning('Création annulée');
            return Command::SUCCESS;
        }

        $user = new Client();
        $user->setPrenom($prenom);
        $user->setNom($nom);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $mdp));
        $user->setRoles([$role]);

        $this->em->persist($user);
        $this->em->flush();
        $io->success('Admin ' . $email . ' créé avec le rôle ' . $role . ' !');
        
        return Command::SUCCESS;
    }
}
