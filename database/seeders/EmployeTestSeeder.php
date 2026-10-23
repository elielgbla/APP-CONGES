<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeTestSeeder extends Seeder
{
    public function run(): void
    {
        // Désactivation des contraintes de clés étrangères pour autoriser l'insertion propre
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // Optionnel : Nettoyage préalable des tables secondaires pour éviter les doublons
        DB::table('employe')->truncate(); // Ajouté pour vider la table employe au rafraîchissement
        DB::table('genre')->truncate();
        DB::table('categorie')->truncate();
        DB::table('poste')->truncate();
        DB::table('localite')->truncate();
        DB::table('service')->truncate();
        DB::table('type_permission')->truncate();
        DB::table('type_contact')->truncate();

        // 1. Insertion des Genres
        DB::table('genre')->insert([
            ['code_genre' => 'M', 'libelle_genre' => 'Masculin'],
            ['code_genre' => 'F', 'libelle_genre' => 'Féminin']
        ]);

        // 2. Insertion des Catégories Professionnelles
        DB::table('categorie')->insert([
            ['code_categorie' => 'CIV', 'libelle_categorie' => 'Civil'],
            ['code_categorie' => 'MIL', 'libelle_categorie' => 'Militaire']
        ]);

        // 3. Insertion des Postes / Fonctions (Dictionnaire de référence)
        DB::table('poste')->insert([
            ['code_poste' => 'EMPLOYE',      'libelle_poste' => 'Employé ordinaire',               'est_responsable' => 0, 'niveau_validation' => null, 'acces_journalisation' => 0],
            ['code_poste' => 'RESP_SERVICE', 'libelle_poste' => 'Responsable de service',          'est_responsable' => 1, 'niveau_validation' => 1,    'acces_journalisation' => 0],
            ['code_poste' => 'RH',           'libelle_poste' => 'Responsable Ressources Humaines', 'est_responsable' => 1, 'niveau_validation' => 2,    'acces_journalisation' => 0],
            ['code_poste' => 'DIR_EXPLOIT',  'libelle_poste' => 'Directeur d\'Exploitation',       'est_responsable' => 1, 'niveau_validation' => 3,    'acces_journalisation' => 0],
            ['code_poste' => 'DIR_GENERAL',  'libelle_poste' => 'Directeur Général',               'est_responsable' => 1, 'niveau_validation' => 4,    'acces_journalisation' => 0],
            ['code_poste' => 'ADMIN',        'libelle_poste' => 'Administrateur système',          'est_responsable' => 1, 'niveau_validation' => null, 'acces_journalisation' => 1]
        ]);

        // 4. Insertion des Localités Géographiques (District d'Abidjan initial)
        DB::table('localite')->insert([
            ['code_localite' => 'ABD_PLT', 'libelle_localite' => 'PLATEAU'],
            ['code_localite' => 'ABD_YOP', 'libelle_localite' => 'YOPOUGON'],
            ['code_localite' => 'ABD_KSS', 'libelle_localite' => 'KOUMASSI'],
            ['code_localite' => 'ABD_COCD', 'libelle_localite' => 'COCODY'],
        ]);

        // 5. Insertion des Services / Directions d'affectation
        DB::table('service')->insert([
            ['code_service' => 'IT_DEV', 'libelle_service' => 'Informatique & Développement'],
            ['code_service' => 'RH_SERV', 'libelle_service' => 'Ressources Humaines'],
            ['code_service' => 'DIR_GEN', 'libelle_service' => 'Direction Générale'],
        ]);

        // 6. Insertion des Types de Permissions
        DB::table('type_permission')->insert([
            ['code_type_permission' => 'CONGE_AN', 'libelle_type_permission' => 'Congé Annuel'],
            ['code_type_permission' => 'PERM_EXC', 'libelle_type_permission' => 'Permission Exceptionnelle'],
        ]);

        // 7. Insertion des Types de Contacts (Dictionnaire de référence)
        DB::table('type_contact')->insert([
            ['code_type_contact' => 'TEL_PERSO',       'libelle_type_contact' => 'Téléphone Mobile Personnel',       'description' => 'Numéro de téléphone portable personnel de l\'employé'],
            ['code_type_contact' => 'TEL_PRO',         'libelle_type_contact' => 'Téléphone Professionnel / Flotte', 'description' => 'Ligne professionnelle ou flotte d\'entreprise octroyée'],
            ['code_type_contact' => 'TEL_FIXE',        'libelle_type_contact' => 'Téléphone Fixe Domicile',           'description' => 'Ligne téléphonique fixe du domicile'],
            ['code_type_contact' => 'WHATSAPP',        'libelle_type_contact' => 'Numéro WhatsApp',                  'description' => 'Numéro joignable sur WhatsApp pour les urgences ou notifications'],
            ['code_type_contact' => 'EMAIL_PERSO',     'libelle_type_contact' => 'Email Personnel',                  'description' => 'Adresse électronique personnelle pour l\'envoi des bulletins/notifications'],
            ['code_type_contact' => 'EMAIL_PRO',       'libelle_type_contact' => 'Email Professionnel',              'description' => 'Adresse électronique d\'entreprise officielle'],
            ['code_type_contact' => 'URGENCE_PROCHE',  'libelle_type_contact' => 'Contact d\'Urgence (Famille/Proche)','description' => 'Personne à contacter en cas d\'urgence médicale ou accident'],
            ['code_type_contact' => 'URGENCE_MEDECIN', 'libelle_type_contact' => 'Médecin / Clinique Traitant(e)',   'description' => 'Coordonnées du médecin traitant ou de la clinique de référence'],
            ['code_type_contact' => 'TUTEUR_LEGAL',    'libelle_type_contact' => 'Tuteur / Représentant Légal',      'description' => 'Personne garante ou représentant légal'],
            ['code_type_contact' => 'LINKEDIN',        'libelle_type_contact' => 'Profil LinkedIn',                  'description' => 'Lien vers le profil professionnel LinkedIn'],
        ]);

               // 8. Insertion de l'employé demandé (GBLA TONGA ELIEL)
        DB::table('employe')->insert([
            'matricule_employe'       => 'GBLA',
            'nom'                     => 'GBLA',
            'prenom'                  => 'TONGA ELIEL',
            'date_naissance'          => '2006-01-10',
            'code_localite_naissance' => 'ABD_KSS',
            'mot_de_passe'            => Hash::make('P@lmConge26'),
            'statut_compte'           => 'actif',
            'email'                   => 'elielgbla@gmail.com',
            'photo'                   => null,
            'total_conge_restant'     => 0,
            'code_genre'              => 'M',
            'code_categorie'          => 'CIV'
        ]);

        // 9. Insertion de l'historique de poste (Attribution du poste ADMIN)
        DB::table('historique_poste')->insert([
            'code_historique'   => 'HIST_GBLA_01',             // Clé primaire personnalisée (varchar(15))
            'matricule_employe' => 'GBLA',                     // Clé étrangère vers l'employé
            'code_poste'        => 'ADMIN',                    // Poste Administrateur défini au point 3
            'code_service'      => 'IT_DEV',                   // Service Informatique défini au point 5
            'date_debut'        => now()->format('Y-m-d'),     // Date du jour comme début d'affectation
            'date_fin'          => null                        // NULL car le poste est actuellement actif
        ]);

        // Réactivation des contraintes de clés étrangères après l'exécution
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

    }
}

