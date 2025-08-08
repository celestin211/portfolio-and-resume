<?php

namespace App\Util;

use App\Entity\Utilisateur;
use Symfony\Component\Routing\Router;

class Menu
{
    const ICON_HOME = 'fa-home';
    public static function getMenu(Utilisateur $utilisateur, $securityAuthorizationChecker, Router $router, int $nbMessagesNonLus, array $messagesNonLus): array
    {
        $acceuil = [
            'libelle' => 'Tableau de bord',
            'icone' => 'fa-home',
            'href' => 'bac_homepage',
            'href_params' => [],
            'routes' => ['bac_homepage'],
            'active' => false,
            'sousMenu' => null,
        ];

        $agents = [
            'libelle' => 'Agents',
            'icone' => 'fa-user',
            'href' => 'agent',
            'href_params' => [],
            'routes' => ['agent', 'agent_show', 'agent_new', 'agent_edit'],
            'active' => false,
            'sousMenu' => null,
        ];

        $annuaire = [
            'libelle' => 'Annuaire',
            'icone' => 'fa-tty',
            'href' => 'annuaire_index',
            'href_params' => [],
            'routes' => ['annuaire_index'],
            'active' => false,
            'sousMenu' => null,
        ];

        $depensePersonnlle = [
            'libelle' => 'Dépense',
            'icone' => 'fas fa-credit-card',
            'href' => 'depense_index',
            'href_params' => [],
            'routes' => ['annuaire_index'],
            'active' => false,
            'sousMenu' => null,
        ];

        $depenseRepetive = [
            'libelle' => 'Dépense Répétive',
            'icone' => 'fas fa-wallet',
            'href' => 'depense_repetitive_index',
            'href_params' => [],
            'routes' => ['annuaire_index'],
            'active' => false,
            'sousMenu' => null,
        ];

        $depense = [
            'libelle' => 'Depenses',
            'icone' => 'fas fa-money-bill-wave',
            'href_params' => [],
            'active' => false,
            'routes' => [],
            'sousMenu' => [
                $depensePersonnlle,
                $depenseRepetive,

            ],
        ];

        $categorie = [
            'libelle' => 'Catégorie',
            'icone' => 'fa-tty',
            'href' => 'categorie_liste',
            'href_params' => [],
            'routes' => ['categorie_liste'],
            'active' => false,
            'sousMenu' => null,
        ];
        $poseConge = [
            'libelle' => 'Pose de congés',
            'icone' => 'fa-umbrella-beach fa-fw',
            'href' => 'conge_new',
            'href_params' => [],
            'routes' => [],
            'active' => false,
            'sousMenu' => null,
        ];

        $planningEquipe = [
            'libelle' => 'Planning',
            'icone' => 'fa-calendars',
            'href' => 'gestion-equipe',
            'href_params' => [],
            'routes' => [],
            'active' => false,
            'sousMenu' => null,
        ];

        $demandeConge = [
            'libelle' => 'Démande de congés',
            'icone' => 'fa-paper-plane fa-fw"',
            'href' => 'conge',
            'href_params' => [],
            'routes' => [],
            'active' => false,
            'sousMenu' => null,
        ];

        $solde = [
            'libelle' => 'Mes soldes',
            'icone' => 'fa-scale-balanced fa-fw',
            'href' => 'soldes',
            'href_params' => [],
            'routes' => [],
            'active' => false,
            'sousMenu' => null,
        ];

        $poseTeleTravail = [
            'libelle' => 'Télétravail',
            'icone' => 'fa-house fa-fw',
            'href' => 'teletravail',
            'href_params' => [],
            'routes' => [],
            'active' => false,
            'sousMenu' => null,
        ];

        $conge = [
            'libelle' => 'Absence et temps de travail',
            'icone' => 'fa-file-text',
            'href_params' => [],
            'active' => false,
            'routes' => [],
            'sousMenu' => [
                $poseConge,
                $planningEquipe,
                $demandeConge,
                $solde,
                $poseTeleTravail,

            ],
        ];



        // Define other menu items similarly
        // ...

        $messagerie = [
            'libelle' => 'Messagerie',
            'icone' => 'fa-envelope',
            'href' => '#',
            'href_params' => [],
            'routes' => ['show_message_boite_reception'],
            'active' => false,
            'sousMenu' => [],
        ];

        if ($nbMessagesNonLus > 0) {
            $messagerie['sousMenu'][] = [
                'libelle' => 'Vous avez ' . $nbMessagesNonLus . ' message' . ($nbMessagesNonLus > 1 ? 's' : ''),
                'icone' => 'fa-envelope-o',
                'href' => '#',
                'href_params' => [],
                'routes' => [],
                'active' => false,
                'sousMenu' => array_map(function($message) {
                    return [
                        'libelle' => $message->getObjetMessage(), // Use the correct method
                        'icone' => 'fa-envelope-o',
                        'href' => 'show_message_boite_reception', // Adjust the path if needed
                        'href_params' => ['id' => $message->getId()], // Ensure getId() exists
                        'routes' => [],
                        'active' => false,
                        'sousMenu' => null,
                    ];
                }, $messagesNonLus),
            ];
        }

        // Define other menu items similarly
        // ...

        // Adjust role-based menu configuration
        $menu = [$acceuil, $messagerie, /* Add other items here */];
        $mailtest = [
            'libelle' => 'Testeur de mail',
            'icone' => 'fa-envelope',
            'href' => 'mail_test',
            'href_params' => [],
            'routes' => ['mail_test'],
            'active' => false,
            'sousMenu' => null,
        ];

        $profiles = [
            'libelle' => 'Profil',
            'icone' => 'fas fa-user fa-3x',
            'href' => 'utilisateur_profil',
            'href_params' => [],
            'routes' => ['utilisateur_profil'],
            'active' => false,
            'sousMenu' => null,
        ];

        $nouveauUtilisateur = [
            'libelle' => 'Nouveau utilisateur',
            'icone' => 'fas fa-user-plus',
            'href' => 'utilisateur_new',
            'href_params' => [],
            'routes' => ['utilisateur_new'],
            'active' => false,
            'sousMenu' => null,
        ];

        $mesCollaborateurs = [
            'libelle' => 'Utilisateur',
            'icone' => 'fas fa-users',
            'href' => 'utilisateur',
            'href_params' => [],
            'routes' => ['utilisateur'],
            'active' => false,
            'sousMenu' => null,
        ];

        $utilisateurs = [
            'libelle' => 'Team',
            'icone' => 'fas fa-globe ',
            'href' => 'utilisateur',
            'href_params' => [],
            'routes' => [
                'utilisateur',
                'utilisateur_show',
                'utilisateur_create',
                'utilisateur_edit',
                'utilisateur_update'
            ],
            'active' => false,
            'sousMenu' => [$profiles, $nouveauUtilisateur, $mesCollaborateurs],
        ];

        $parametrage = [
            'libelle' => 'Parametrage',
            'icone' => 'fa-user',
            'href' => 'agent',
            'href_params' => [],
            'routes' => ['parametrage', 'parametrage_show', 'parametrage_edit'],
            'active' => false,
            'sousMenu' => null,
        ];

        $produitListe = [
            'libelle' => 'Produits',
            'icone' => 'fas fa-list',
            'href' => 'produits_liste',
            'href_params' => [],
            'routes' => ['produits_liste'],
            'active' => false,
            'sousMenu' => null,
        ];

        $produitNew = [
            'libelle' => 'Produits',
            'icone' => 'fas fa-plus',
            'href' => 'produits_new',
            'href_params' => [],
            'routes' => ['produits_new'],
            'active' => false,
            'sousMenu' => null,
        ];

        $produits = [
            'libelle' => 'Produits',
            'icone' => 'fas fa-box',
            'href_params' => [],
            'active' => false,
            'routes' => [],
            'sousMenu' => [
                $produitListe,
                $produitNew,

            ],
        ];

        $calendrier = [
            'libelle' => 'Calendrier',
            'icone' => 'fa-tty',
            'href' => 'calendrier_index',
            'href_params' => [],
            'routes' => ['calendrier_index'],
            'active' => false,
            'sousMenu' => null,
        ];


        $facturation = [
            'libelle' => 'Facturation',
            'icone' => 'fa-file-text',
            'href' => 'facturation_index',
            'href_params' => [],
            'routes' => ['facturation_index'],
            'active' => false,
            'sousMenu' => null,
        ];

        $rapprochementBancaire = [
            'libelle' => 'Rapprochement bancaire',
            'icone' => 'fa-file-text',
            'href' => 'rapprochement_bancaire',
            'href_params' => [],
            'routes' => ['rapprochement_bancaire'],
            'active' => false,
            'sousMenu' => null,
        ];


        $gestionCompte = [
            'libelle' => 'Gestion de compte',
            'icone' => 'fa fa-user-cog',
            'routes' => [''],
            'active' => false,
            'sousMenu' => [$facturation, $rapprochementBancaire],
        ];

        $faq = [
            'libelle' => 'FAQ',
            'icone' => 'fa-comments-o',
            'href' => 'faq',
            'href_params' => [],
            'routes' => ['faq'],
            'active' => false,
            'sousMenu' => null,
        ];


        $bulletinPaie = [
            'libelle' => 'Bulletin de paie',
            'icone' => 'fa-tag',
            'href' => 'fichepaye',
            'href_params' => [],
            'routes' => self::getRoutesWithPrefix($router, 'corps_'),
            'active' => false,
            'sousMenu' => null,
        ];


        $suivreLesContras = [
            'libelle' => 'Suivre les contrats',
            'icone' => 'fa-tag',
            'href' => 'suivre_contrat',
            'href_params' => [],
            'routes' => self::getRoutesWithPrefix($router, 'suivre_contrat_'),
            'active' => false,
            'sousMenu' => null,
        ];

        $verificationPaiement = [
            'libelle' => 'Vérifeir et clôturer la paie',
            'icone' => 'fa-tag',
            'routes' => [''],
            'active' => false,
            'sousMenu' => [
                $bulletinPaie,
                $suivreLesContras
            ],
        ];

        $statistiquesConge = [
            'libelle' => 'Statisques congés',
            'icone' => 'fa-bug',
            'href' => 'statisques_conge',
            'href_params' => [],
            'routes' => self::getRoutesWithPrefix($router, 'statisques_'),
            'active' => false,
            'sousMenu' => null,
        ];

        $statistiquesPaiement = [
            'libelle' => 'Statisques paiements',
            'icone' => 'fa-bug',
            'href' => 'statisques_paiement',
            'href_params' => [],
            'routes' => ['statisques_paiement'],
            'active' => false,
            'sousMenu' => null,
        ];

        $statistiquesTeletravil = [
            'libelle' => 'Statisques télétravail',
            'icone' => 'fa-bug',
            'href' => 'statisques_teletravail',
            'href_params' => [],
            'routes' => ['statisques_teletravail'],
            'active' => false,
            'sousMenu' => null,
        ];

        $statistiques = [
            'libelle' => 'Statisques',
            'icone' => 'fa-bug',
            'routes' => [''],
            'active' => false,
            'sousMenu' => [$statistiquesConge, $statistiquesPaiement, $statistiquesTeletravil],
        ];

        $parametres = [
            'libelle' => 'Paramètres',
            'icone' => 'fa-cogs',
            'href' => 'parametre_index',
            'href_params' => [],
            'routes' => ['parametre_index'],
            'active' => false,
            'sousMenu' => null,
        ];

        // Constitution du menu en fonction des roles de l'utilisateur

        $menu = [];
        $menu[] = $acceuil;

        $menu[] = $agents;
        $menu[] = $messagerie;
        $menu[] = $annuaire;
        $menu[] = $calendrier;
        $menu[] = $conge;


        if ($securityAuthorizationChecker->isGranted('ROLE_GESTIONNAIRE')) {
            $menu[] = $agents;
            $menu[] = $messagerie;
            $menu[] = $annuaire;
            $menu[] = $calendrier;
            $menu[] = $conge;
        }
        if ($securityAuthorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu[] = $verificationPaiement;
            $menu[] = $parametres;
            $menu[] = $agents;
            $menu[] = $conge;
        }

        if ($utilisateur->hasRole('ROLE_ADMIN_VIP')) {
            $menu = [];
            $menu[] = $acceuil;
            $menu[] = $depense;
            $menu[] = $gestionCompte;
            $menu[] = $messagerie;
            $menu[] = $annuaire;
            $menu[] = $parametres;
            $menu[] = $utilisateurs;
            $menu[] = $verificationPaiement;
            $menu[] = $agents;
            $menu[] = $faq;
            $menu[] = $parametres;
            $menu[] = $calendrier;
            $menu[] = $statistiques;
            $menu[] = $produits;
            $menu[] = $parametrage;
            $menu[] = $conge;
            $menu[] = $mailtest;
            $menu[] = $parametres;
            $menu[] = $categorie;

        }

        return $menu;
    }

    public static function setActiveMenu($route, &$menu)
    {
        foreach ($menu as &$item) {
            self::calculItemActif($route, $item);
        }

        return $menu;
    }

    private static function calculItemActif($route, &$menu): bool
    {
        if (null == $menu['sousMenu']) {
            $menu['active'] = in_array($route, $menu['routes']);

            return $menu['active'];
        } else {
            $is_active = false;

            foreach ($menu['sousMenu'] as &$item) {
                $is_active = $is_active || self::calculItemActif($route, $item);
            }
            $menu['active'] = $is_active;

            return $is_active;
        }
    }

    public static function getPathMenu($menu, &$breadCrumb = [])
    {
        foreach ($menu as $item) {
            if (true === $item['active']) {
                if (0 != strcmp($item['libelle'], 'Accueil')) {
                    $route = $item['routes'][0] ?? null;

                    $breadCrumb[] = ['libelle' => $item['libelle'], 'route' => $route];
                }
                if (null !== $item['sousMenu']) {
                    Menu::getPathMenu($item['sousMenu'], $breadCrumb);
                }
            }
        }

        return $breadCrumb;
    }

    private static function getRoutes(Router $router, $suffixe): array
    {
        $routes = [];

        $allRoutes = $router->getRouteCollection()->all();

        foreach ($allRoutes as $nom_route => $objet_route) {
            $tab_route = explode('_', $nom_route);

            if (end($tab_route) == $suffixe) {
                $routes[] = $nom_route;
            }
        }

        return $routes;
    }

    private static function getRoutesWithPrefix(Router $router, $prefix): array
    {
        $routes = [];

        $allRoutes = $router->getRouteCollection()->all();

        foreach ($allRoutes as $nom_route => $objet_route) {
            if (Util::str_starts_with($nom_route, $prefix)) {
                $routes[] = $nom_route;
            }
        }

        return $routes;
    }
}
