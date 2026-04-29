<?php

return [
    'page_title' => 'Emplacements',
    'heading' => 'Emplacements',
    'preview_banner' => 'Une zone réunit plusieurs emplacements pour permettre le roaming Wi-Fi. Tous héritent de la configuration de l’emplacement principal.',

    // Tabs
    'tab_aps' => 'Emplacements',
    'tab_zones' => 'Zones',

    // Search / filters
    'search_aps' => 'Recherche par nom, adresse, MAC…',
    'search_zones' => 'Recherche zones…',

    // AP table columns
    'col_name' => 'Nom',
    'col_status' => 'État',
    'col_address' => 'Adresse',
    'col_zone' => 'Zone',
    'col_last_seen' => 'Vu pour la dernière fois',
    'col_actions' => 'Actions',

    // Zone table columns
    'col_zone_name' => 'Nom de la zone',
    'col_ap_count' => 'Emplacements',
    'col_primary_ap' => 'Emplacement principal',
    'col_owner' => 'Propriétaire',

    // Cell values
    'standalone' => 'Sans zone',
    'no_address' => '—',
    'no_primary' => '—',
    'no_owner' => '—',
    'never_seen' => 'Jamais',
    'status_online' => 'En ligne',
    'status_offline' => 'Hors ligne',

    // Empty states
    'no_aps' => 'Aucun emplacement pour le moment.',
    'no_zones' => 'Aucune zone. Les emplacements fonctionnent en autonome.',
    'no_aps_match' => 'Aucun emplacement ne correspond à votre recherche.',
    'no_zones_match' => 'Aucune zone ne correspond à votre recherche.',
    'all_in_zones' => 'Tous vos emplacements sont dans des zones. Passez à l’onglet Zones pour les voir.',

    // Actions
    'action_open' => 'Ouvrir',
    'action_open_zone' => 'Voir la zone',

    // Grouped view
    'primary' => 'Emplacement principal — les autres de la zone partagent sa configuration',
    'primary_pill' => 'Principal',
    'open_zone' => 'Ouvrir la zone',
    'ap_singular' => 'emplacement',
    'ap_plural' => 'emplacements',

    // Header action buttons
    'add_ap' => 'Ajouter un emplacement',
    'create_zone' => 'Créer une zone',

    // Filter chips
    'filter_all' => 'Tous',
    'filter_online' => 'En ligne',
    'filter_offline' => 'Hors ligne',

    // Summary + rollup
    'zone_singular' => 'zone',
    'zone_plural' => 'zones',
    'meta_online' => 'en ligne',
    'meta_offline' => 'hors ligne',

    // Summary cards (top of page)
    'metric_total_aps' => 'Emplacements',
    'metric_online_aps' => 'En ligne',
    'metric_total_users' => 'Utilisateurs connectés',
    'metric_total_data' => 'Données utilisées',

    // New columns
    'col_users' => 'Utilisateurs',
    'col_data' => 'Données',

    // Row actions
    'action_clone' => 'Cloner',
    'action_delete' => 'Supprimer',
    'action_edit' => 'Modifier',
    'confirm_delete_ap' => 'Supprimer l’emplacement « {name} » ? Cette action est irréversible.',
    'confirm_delete_zone' => 'Supprimer cette zone ? Ses emplacements deviendront indépendants.',
    'ap_deleted' => 'Emplacement supprimé',
    'ap_cloned' => 'Emplacement cloné',
    'zone_deleted' => 'Zone supprimée',
    'zone_created' => 'Zone créée',
    'create_zone_prompt' => 'Nom de la nouvelle zone :',
    'action_failed' => 'Action échouée — veuillez réessayer',
];
