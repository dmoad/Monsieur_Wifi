<?php

return [
    'page_title' => 'Paramètres réseau - Contrôleur monsieur-wifi',
    'heading' => 'Paramètres réseau',

    // Breadcrumb + back button
    'breadcrumb_networks' => 'Réseaux',
    'back_to_location' => 'Retour à l\'emplacement',

    // Location info bar
    'vlan_support' => 'Support VLAN',
    'add_network' => 'Ajouter un réseau',

    // Tab nav
    'loading_networks' => 'Chargement des réseaux…',
    'tab_label_default' => 'Réseau',

    // Per-network pane header
    'pane_title_default' => 'Réseau',
    'delete_network' => 'Supprimer le réseau',
    'save_settings' => 'Enregistrer les paramètres',

    // Network type select options (full names)
    'type_password_wifi' => 'WiFi avec mot de passe',
    'type_captive_portal' => 'Portail captif',
    'type_open_essid' => 'ESSID ouvert',

    // Network type pills (short labels next to icon)
    'pill_password' => 'Mot de passe',
    'pill_captive_portal' => 'Portail captif',
    'pill_open' => 'Ouvert',

    // SSID + visibility
    'ssid_placeholder' => 'Nom du réseau (SSID)',
    'visibility_broadcast' => 'Diffuser le SSID',
    'visibility_hidden' => 'SSID masqué',

    // QoS + radio band
    'full_qos' => 'QoS complet',
    'band_all' => '2.4 et 5 GHz',
    'band_2_4_only' => '2.4 GHz uniquement',
    'band_5_only' => '5 GHz uniquement',

    // Security & Encryption panel (password networks)
    'panel_security_encryption' => 'Sécurité & Chiffrement',
    'wifi_password' => 'Mot de passe WiFi',
    'wifi_password_placeholder' => 'Minimum 8 caractères',
    'security_protocol' => 'Protocole de sécurité',
    'security_wpa2_psk_rec' => 'WPA2-PSK (Recommandé)',
    'security_wpa_wpa2_mixed' => 'WPA/WPA2-PSK Mixte',
    'security_wpa3_psk_secure' => 'WPA3-PSK (Plus sécurisé)',
    'security_wep_legacy' => 'WEP (Hérité)',
    'cipher_suites' => 'Suites de chiffrement',

    // Captive Portal Configuration panel
    'panel_captive_portal_config' => 'Configuration du portail captif',
    'sub_authentication' => 'Authentification',
    'login_methods' => 'Méthodes de connexion',
    'login_methods_hint' => '(une ou plusieurs)',
    'method_click_through' => 'Clic',
    'method_password' => 'Mot de passe',
    'method_sms' => 'SMS',
    'method_email' => 'E-mail',
    'method_social' => 'Social',
    'multiple_methods_hint' => 'Si plusieurs méthodes sont sélectionnées, les invités pourront choisir lors de la connexion.',
    'shared_password' => 'Mot de passe partagé',
    'social_provider' => 'Réseau social',
    'portal_design' => 'Design du portail',
    'default_design' => 'Design par défaut',
    'redirect_url' => 'URL de redirection',
    'redirect_url_placeholder' => 'https://exemple.com',
    'session_timeout' => 'Délai de session',
    'idle_timeout' => 'Délai d\'inactivité',

    // Timeout / duration options
    'dur_15_min' => '15 minutes',
    'dur_30_min' => '30 minutes',
    'dur_45_min' => '45 minutes',
    'dur_1_hour' => '1 heure',
    'dur_2_hours' => '2 heures',
    'dur_3_hours' => '3 heures',
    'dur_4_hours' => '4 heures',
    'dur_5_hours' => '5 heures',
    'dur_6_hours' => '6 heures',
    'dur_12_hours' => '12 heures',
    'dur_1_day' => '1 jour',
    'dur_1_week' => '1 semaine',
    'dur_3_months' => '3 mois',
    'dur_1_year' => '1 an',

    // Bandwidth
    'sub_bandwidth_limits' => 'Limites de bande passante',
    'download_mbps' => 'Téléchargement (Mbps)',
    'upload_mbps' => 'Envoi (Mbps)',
    'unlimited' => 'Illimité',

    // Working hours + Open panel
    'working_hours' => 'Heures de travail',
    'panel_open_network' => 'Réseau ouvert',
    'no_auth_required' => 'Aucune authentification requise',
    'open_network_warning' => 'Toute personne à portée peut se connecter sans mot de passe ni portail. À utiliser uniquement dans des environnements de confiance.',

    // IP & DHCP Settings collapsible
    'section_ip_dhcp' => 'Paramètres IP & DHCP',
    'panel_ip_config' => 'Configuration IP',
    'sub_addressing' => 'Adressage',
    'ip_mode' => 'Mode IP',
    'ip_mode_static' => 'IP statique',
    'ip_mode_bridge_lan' => 'Pont vers port LAN',
    'ip_mode_bridge' => 'Pont vers WAN',
    'lan_dhcp_mode' => 'Mode DHCP LAN',
    'lan_dhcp_client' => 'Client DHCP',
    'lan_dhcp_server' => 'Serveur DHCP',
    'lan_dhcp_client_not_captive' => 'Le mode Client DHCP n\'est pas disponible pour les réseaux Portail Captif.',
    'ip_address' => 'Adresse IP',
    'ip_address_placeholder' => '192.168.x.1',
    'netmask' => 'Masque de sous-réseau',
    'gateway' => 'Passerelle',
    'gateway_placeholder' => 'Auto',
    'primary_dns' => 'DNS primaire',
    'alt_dns' => 'DNS alt.',
    'dns_field_title' => 'Le DNS est géré par le filtre Web. Désactivez le filtre Web pour définir le DNS par réseau.',

    // DHCP address pool
    'dhcp_pool_title' => 'Plage d’adresses DHCP',
    'dhcp_pool_desc' => 'Attribue des adresses LAN aux appareils sur ce réseau.',
    'dhcp_server_label' => 'Serveur DHCP',
    'enable_dhcp' => 'Activer le DHCP',
    'start_ip' => 'Adresse de début',
    'start_ip_placeholder' => 'ex. 192.168.1.100',
    'start_ip_hint' => 'Première adresse de la plage (IPv4).',
    'pool_size' => 'Taille du pool',
    'pool_size_placeholder' => 'ex. 101',
    'pool_size_hint' => 'Nombre d’adresses (doit tenir dans le sous-réseau).',

    // VLAN
    'sub_vlan' => 'VLAN',
    'vlan_id' => 'ID VLAN',
    'vlan_id_range' => '(1–4094)',
    'vlan_none' => 'Aucun',
    'tagging' => 'Marquage',
    'tagging_disabled' => 'Désactivé',
    'tagging_tagged' => 'Marqué',
    'tagging_untagged' => 'Non marqué',

    // MAC Filtering & IP Reservations
    'section_mac_filter_reservations' => 'Filtrage MAC & Réservations IP',
    'mac_filtering' => 'Filtrage des adresses MAC',
    'mac_add_type_block' => 'Bloquer',
    'mac_add_type_bypass' => 'Contourner',
    'table_col_type' => 'Type',
    'table_col_mac' => 'Adresse MAC',
    'mac_list_empty' => 'Aucune règle MAC ajoutée',
    'dhcp_reservations' => 'Réservations IP DHCP',
    'reservation_mac_placeholder' => 'MAC  00:11:22:33:44:55',
    'reservation_ip_placeholder' => 'IP  192.168.1.50',
    'table_col_reserved_ip' => 'IP réservée',
    'reservation_list_empty' => 'Aucune réservation ajoutée',
    'mac_save_hint' => 'Ajoutez ou supprimez des entrées, puis cliquez sur Enregistrer pour appliquer.',
    'mac_save_btn' => 'Enregistrer',

    // JS-injected messages (window.APP_CONFIG_V5.messages)
    'js_messages' => [
        'networkSaved' => 'Paramètres réseau enregistrés.',
        'routerReconfigure' => 'Configuration du routeur mise à jour — l\'appareil va se reconfigurer.',
        'workingHoursSaved' => 'Heures de travail enregistrées.',
        'macFilterSaved' => 'Paramètres de filtrage MAC enregistrés.',
        'networkAdded' => 'Réseau ajouté.',
        'networkDeleted' => 'Réseau supprimé.',
        'invalidMac' => 'Format d\'adresse MAC invalide.',
        'macBadgeBypass' => 'Contourner',
        'macBadgeBlock' => 'Bloquer',
        'pageOf' => 'Page {page} sur {total}',
        'invalidSsid' => 'Le SSID ne peut pas être vide.',
        'ssidTooLong' => 'Le SSID doit contenir 32 caractères maximum (limite 802.11).',
        'passwordRequired' => 'Un mot de passe est requis pour les réseaux de type mot de passe.',
        'passwordTooShort' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'savingSchedule' => 'Enregistrement…',
        'macFilterHintPassword' => 'Seul le blocage est disponible sur les réseaux protégés par mot de passe. Le contournement de l\'authentification n\'est pas applicable ici.',
        'macFilterHintOpen' => 'Seul le blocage est disponible sur les réseaux ouverts. Il n\'y a pas de portail ni de mot de passe à contourner.',
        'macFilterHintCaptive' => 'Le blocage (refus d\'accès) et le contournement (accès au réseau sans passer par le portail captif) sont disponibles pour les réseaux à portail captif.',
        'portalPasswordRequired' => 'Un mot de passe partagé est requis lorsque la méthode de connexion par mot de passe est activée.',
    ],

    // JS-injected scheduler labels (window.APP_CONFIG_V5.schedulerLabels)
    'js_scheduler' => [
        'title' => 'Heures de travail',
        'subtitle' => 'Horaires d\'accès au portail captif',
        'quickSet' => 'Accès rapide :',
        'businessHours' => 'Heures ouvrables',
        'clearAll' => 'Tout effacer',
        'saveSchedule' => 'Enregistrer l\'horaire',
        'hint' => 'Cliquez sur une cellule vide pour créer un créneau. Glissez pour déplacer, redimensionnez avec les poignées, survolez pour supprimer.',
        'days' => [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche',
        ],
        'msgOverlap' => 'Impossible de créer le créneau : chevauchement avec un créneau existant.',
        'msgInvalidMove' => 'Position invalide : le créneau chevaucherait ou dépasserait les limites.',
        'msgInvalidResize' => 'Redimensionnement invalide : chevaucherait ou dépasserait les limites.',
        'msgBusinessApplied' => 'Heures ouvrables appliquées.',
        'msgCleared' => 'Tous les créneaux effacés.',
        'msgSaved' => 'Horaire enregistré !',
    ],

    // JS-injected network type badge labels (window.APP_CONFIG_V5.typeLabels)
    'js_type_labels' => [
        'password' => 'Mot de passe WiFi',
        'captive_portal' => 'Portail captif',
        'open' => 'Ouvert',
    ],
];
