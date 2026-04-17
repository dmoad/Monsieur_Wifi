<?php

return [
    'page_title' => 'Détails de l\'emplacement - Contrôleur monsieur-wifi',
    'heading' => 'Détails de l\'emplacement',

    // Header buttons
    'clone_button' => 'Cloner',
    'networks_button' => 'Réseaux',

    // Router info card
    'mac_prefix' => 'MAC :',
    'edit_button' => 'Modifier',
    'router_model' => 'Modèle de routeur',
    'mac_address' => 'Adresse MAC',
    'firmware' => 'Firmware',
    'total_users' => 'Total utilisateurs',
    'daily_usage' => 'Utilisation quotidienne',
    'uptime' => 'Temps de fonctionnement',
    'restart_button' => 'Redémarrer',
    'update_button' => 'Mettre à jour',

    // Current usage card
    'current_usage' => 'Utilisation actuelle',
    'period_today' => 'Aujourd\'hui',
    'period_7days' => '7 derniers jours',
    'period_30days' => '30 derniers jours',
    'loading_usage_data' => 'Chargement des données d\'utilisation...',
    'stat_download' => 'Téléchargement',
    'stat_upload' => 'Téléversement',
    'stat_users_sessions' => 'Utilisateurs / Sessions',
    'stat_avg_session' => 'Session moyenne',
    'loading_data' => 'Chargement des données...',

    // Map card
    'location_map_title' => 'Emplacement',

    // Analytics section
    'analytics_title' => 'Analyses',
    'daily_usage_analytics' => 'Analyse d\'utilisation quotidienne',
    'captive_portal_activity' => 'Activité des utilisateurs du portail captif',
    'stat_sessions' => 'Sessions',
    'stat_daily_avg' => 'Moy. quotidienne',
    'live_users' => 'Utilisateurs en direct',
    'currently_connected' => 'Actuellement connectés',
    'online_label' => 'En ligne',
    'loading_online_users' => 'Chargement des utilisateurs en ligne...',

    // WiFi networks shortcut
    'wifi_networks' => 'Réseaux WiFi',
    'wifi_networks_description' => 'Gérez tous les réseaux WiFi associés à cet emplacement — ajoutez, supprimez ou configurez la sécurité, le portail captif, les paramètres IP et plus encore de chaque réseau.',
    'zone_networks_notice' => 'Les réseaux sont gérés par l\'emplacement principal de la zone.',
    'manage_networks_button' => 'Gérer les réseaux',

    // Location Configuration card
    'config_title' => 'Configuration de l\'emplacement',
    'tab_location_details' => 'Détails de l\'emplacement',
    'tab_router_settings' => 'Paramètres du routeur',

    // Location Details tab - Identity & Address panel
    'panel_identity_address' => 'Identité et adresse de l\'emplacement',
    'sublabel_identity' => 'Identité',
    'location_name' => 'Nom de l\'emplacement',
    'location_name_placeholder' => 'ex. Café du centre',
    'status_label' => 'Statut',
    'sublabel_address' => 'Adresse',
    'street_address' => 'Adresse postale',
    'street_placeholder' => '123 rue de la Paix',
    'city' => 'Ville',
    'city_placeholder' => 'Ville',
    'state_province' => 'État / Province',
    'state_placeholder' => 'État',
    'postal' => 'Code postal',
    'postal_placeholder' => 'Code',
    'country' => 'Pays',
    'country_placeholder' => 'Pays',
    'sublabel_notes' => 'Notes',
    'description_label' => 'Description',
    'description_optional' => '(optionnel)',
    'description_placeholder' => 'Brève description de cet emplacement…',
    'char_counter_suffix' => '/500 caractères',

    // Location Details tab - Contact & Ownership panel
    'panel_contact_ownership' => 'Contact et propriété',
    'manager_name' => 'Nom du gestionnaire',
    'manager_name_placeholder' => 'Nom complet',
    'email' => 'E-mail',
    'email_placeholder' => 'contact@exemple.com',
    'phone' => 'Téléphone',
    'phone_placeholder' => '+33 1 23 45 67 89',
    'owner' => 'Propriétaire',
    'admin_badge' => 'Admin',
    'select_owner_option' => 'Sélectionner un propriétaire',
    'shared_access' => 'Accès partagé',
    'shared_access_help' => 'Recherchez et sélectionnez les utilisateurs qui auront un accès complet aux paramètres de cet emplacement.',

    // Action bar
    'save_location_info' => 'Enregistrer les informations de l\'emplacement',

    // Router Settings tab - WAN Connection
    'wan_connection' => 'Connexion WAN',
    'edit_wan_settings' => 'Modifier les paramètres WAN',
    'connection_type' => 'Type de connexion',
    'ip_address' => 'Adresse IP',
    'subnet_mask' => 'Masque de sous-réseau',
    'gateway' => 'Passerelle',
    'primary_dns' => 'DNS principal',
    'username' => 'Nom d\'utilisateur',
    'service_name' => 'Nom du service',

    // Router Settings tab - Radio & Channel
    'wifi_radio_channel' => 'Configuration radio et canal WiFi',
    'country_region' => 'Pays / Région',
    'power_2g' => 'Puissance 2,4 GHz',
    'power_5g' => 'Puissance 5 GHz',
    'width_2g' => 'Largeur de canal 2,4 GHz',
    'width_5g' => 'Largeur de canal 5 GHz',
    'channel_2g' => 'Canal 2,4 GHz',
    'channel_5g' => 'Canal 5 GHz',
    'channel_optimization' => 'Optimisation des canaux',
    'scan_button' => 'Analyser',
    'scan_default_status' => 'Cliquez sur Analyser pour déterminer les canaux optimaux.',
    'best_2g' => 'Meilleur 2,4G',
    'best_5g' => 'Meilleur 5G',
    'no_scan_yet' => 'Aucune analyse effectuée',
    'apply_optimal' => 'Appliquer optimal',
    'save_all_radio' => 'Enregistrer tous les paramètres radio',

    // Router Settings tab - Traffic Prioritization (QoS)
    'qos_title' => 'Priorisation du trafic (QoS)',
    'save_qos' => 'Enregistrer QoS',
    'qos_zone_notice' => 'QoS est géré par l\'emplacement principal de la zone.',
    'qos_classification' => 'Classification',
    'qos_enable' => 'Activer la priorisation du trafic',
    'qos_enable_help' => 'Classez le trafic par nom d\'hôte (SNI) et appliquez une priorité basée sur DSCP. Nécessite un firmware de routeur compatible.',
    'qos_active_classes' => 'Classes de priorité actives',
    'qos_managed_globally' => 'Géré globalement par le SuperAdmin.',
    'qos_bandwidth_limits' => 'Limites de bande passante',
    'qos_bandwidth_intro' => 'Toutes les valeurs en <strong>Mbps</strong>. Définissez la capacité WAN et les minimums réservés optionnels par classe de trafic.',
    'qos_wan_use_local' => 'Utiliser les vitesses WAN de cet emplacement (au lieu de la valeur par défaut de la zone)',
    'qos_wan_use_local_help' => 'Les minimums de classe suivent l\'emplacement principal de la zone ; seuls les téléchargements peuvent différer ici.',
    'qos_wan_capacity' => 'Capacité WAN',
    'qos_min_per_class' => 'Minimum par classe',
    'qos_voip' => 'VoIP',
    'qos_streaming' => 'Streaming',
    'qos_best_effort' => 'Meilleur effort',
    'qos_bulk' => 'En vrac',

    // Router Settings tab - Web Content Filtering
    'web_content_filtering' => 'Filtrage de contenu Web',
    'save_web_filter' => 'Enregistrer les paramètres du filtre Web',
    'enable_content_filtering' => 'Activer le filtrage de contenu',
    'web_filter_help' => 'Appliquer le filtrage de contenu à tous les réseaux WiFi.',
    'web_filter_propagation' => '<strong>Veuillez noter :</strong> Après l\'enregistrement, il faut <strong>2 à 5 minutes</strong> pour que le blocage de domaine soit actif sur le routeur.',
    'block_categories' => 'Catégories à bloquer',
    'block_categories_help' => 'Sélectionnez les catégories de contenu à bloquer sur tous les réseaux.',
    'wan_primary_dns' => 'DNS principal WAN',
    'wan_secondary_dns' => 'DNS secondaire WAN',
    'wan_dns_hint' => 'Utilisé comme serveur DNS en amont lorsque le filtre Web est actif. Laissez vide pour utiliser 8.8.8.8 / 8.8.4.4 par défaut.',
];
