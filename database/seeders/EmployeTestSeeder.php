<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeTestSeeder extends Seeder
{
    public function run(): void
    {
        // Désactivation des contraintes de clés étrangères pour autoriser l'insertion propre
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // Optionnel : Nettoyage préalable des tables secondaires pour éviter les doublons
        DB::table('genre')->truncate();
        DB::table('categorie')->truncate();
        DB::table('poste')->truncate();
        DB::table('localite')->truncate();
        DB::table('service')->truncate();
        DB::table('type_permission')->truncate();

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

        // Réactivation des contraintes de clés étrangères après l'exécution
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}