<?php

return [
    'page_title' => 'Paramètres QoS - Monsieur WiFi',
    'heading' => 'Priorisation du trafic (QoS)',
    'breadcrumb' => 'Paramètres QoS',

    // Info banner
    'info_title' => 'Comment fonctionne la QoS :',
    'info_body_html' => 'Le trafic est classifié par SNI (nom d\'hôte) sur le routeur et marqué avec une priorité DSCP. Les quatre classes ci-dessous sont fixes — seules leurs listes de domaines peuvent être modifiées ici. L\'activation/désactivation par emplacement se configure dans la page Paramètres de l\'emplacement. Le trafic non classifié tombe automatiquement dans la classe <strong>Par défaut (BE)</strong>.',

    // Loading / empty states
    'loading_classes' => 'Chargement des classes QoS…',
    'load_failed' => 'Impossible de charger les classes QoS.',
    'be_placeholder' => 'Aucune règle de domaine — tout le trafic non classifié tombe dans cette classe automatiquement.',
    'no_domains' => 'Aucun domaine configuré.',

    // Add-domain form
    'add_domain_placeholder' => 'ex. *.exemple.com',
    'add_btn' => 'Ajouter',
    'remove_title' => 'Supprimer',

    // Class labels (shown in card header)
    'class_label_EF' => 'Temps réel',
    'class_label_AF41' => 'Streaming',
    'class_label_BE' => 'Par défaut',
    'class_label_CS1' => 'Arrière-plan',

    // Class priority descriptions
    'priority_desc_EF' => 'Priorité maximale — latence minimale garantie',
    'priority_desc_AF41' => 'Haute priorité — inférieure au Temps réel',
    'priority_desc_BE' => 'Priorité normale — trafic non classifié & QoS désactivée',
    'priority_desc_CS1' => 'Priorité minimale — différé en cas de congestion',

    // Toasts / confirms
    'generic_error' => 'Une erreur est survenue.',
    'domain_empty' => 'Le domaine ne peut pas être vide.',
    'domain_added' => 'Domaine ajouté à {class}.',
    'domain_removed' => 'Domaine supprimé de {class}.',
    'confirm_remove' => 'Supprimer « {domain} » de {class} ?',
];
