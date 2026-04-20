<?php

return [
    'page_title' => 'Blocage de domaines - Monsieur WiFi',
    'heading' => 'Blocage de domaines',

    // Header button
    'info_btn' => 'Info',

    // Blocking categories card
    'categories_title' => 'Catégories de blocage',
    'categories_help' => 'Activez ou désactivez les catégories pour activer ou désactiver le blocage de domaines par catégorie.',
    'cat_adult' => 'Contenu adulte',
    'cat_gambling' => 'Jeux d\'argent',
    'cat_malware' => 'Logiciels malveillants',
    'cat_social' => 'Réseaux sociaux',
    'cat_streaming' => 'Streaming',
    'cat_custom' => 'Liste personnalisée',
    'domains_suffix' => 'domaines',

    // Table
    'blocked_domains_title' => 'Domaines bloqués',
    'add_domain' => 'Ajouter un domaine',
    'col_domain' => 'Domaine',
    'col_category' => 'Catégorie',
    'col_added_date' => 'Date d\'ajout',
    'col_last_updated' => 'Dernière mise à jour',
    'col_actions' => 'Actions',

    // Add modal
    'add_modal_title' => 'Ajouter un nouveau domaine',
    'domain_label' => 'Domaine',
    'domain_placeholder' => 'exemple.com',
    'domain_help' => 'Entrez un domaine sans http:// ou https://',
    'category_label' => 'Catégorie',
    'notes_label' => 'Notes',
    'notes_placeholder' => 'Entrez des notes',
    'add_btn' => 'Ajouter le domaine',

    // Edit modal
    'edit_modal_title' => 'Modifier le domaine',
    'block_all_subdomains' => 'Bloquer tous les sous-domaines',
    'block_subdomains_help' => 'Tous les sous-domaines seront bloqués automatiquement si le domaine est bloqué.',
    'save_changes' => 'Sauvegarder les modifications',

    // Info modal
    'info_modal_title' => 'Comment fonctionne le blocage de domaines',
    'what_is_title' => 'Qu\'est-ce que le blocage de domaines ?',
    'what_is_body' => 'Le blocage de domaines empêche les utilisateurs de votre réseau d\'accéder à des sites web spécifiques en bloquant leurs noms de domaine. Lorsqu\'un utilisateur tente de visiter un domaine bloqué, la demande est interceptée et refusée, protégeant votre réseau contre le contenu indésirable, les menaces de sécurité ou les distractions de productivité.',
    'how_to_add_title' => 'Comment ajouter des domaines',
    'how_to_single' => 'Domaine unique :',
    'how_to_single_body' => 'Cliquez sur le bouton « Ajouter un domaine » pour ajouter des sites web individuels',
    'how_to_categories' => 'Catégories :',
    'how_to_categories_body' => 'Organisez les domaines en catégories prédéfinies pour une meilleure gestion',
    'why_multiple_title' => 'Pourquoi plusieurs domaines sont nécessaires',
    'why_multiple_body' => 'De nombreux sites web utilisent plusieurs domaines pour distribuer le contenu, éviter le blocage ou améliorer les performances. Pour bloquer efficacement un service, vous devez souvent bloquer plusieurs domaines associés :',
    'service_col' => 'Service',
    'domains_to_block_col' => 'Domaines à bloquer',
    'best_practices_title' => 'Meilleures pratiques',
    'bp_use_cats' => 'Utiliser les catégories :',
    'bp_use_cats_body' => 'Regroupez les domaines associés pour une gestion plus facile',
    'bp_research' => 'Recherchez minutieusement :',
    'bp_research_body' => 'Recherchez tous les domaines utilisés par un service avant de bloquer',
    'bp_test' => 'Tester le blocage :',
    'bp_test_body' => 'Vérifiez que le blocage fonctionne comme prévu',
    'bp_updates' => 'Mises à jour régulières :',
    'bp_updates_body' => 'Maintenez vos listes de blocage à jour car les services changent de domaines',
    'pro_tip' => 'Astuce :',
    'pro_tip_body' => 'Utilisez les outils de développement du navigateur (F12) pour inspecter les requêtes réseau et identifier tous les domaines utilisés par un site web. Cela permet d\'assurer un blocage complet.',
    'got_it' => 'Compris !',
];
