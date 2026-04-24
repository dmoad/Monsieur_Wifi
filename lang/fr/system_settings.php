<?php

return [
    'page_title' => 'Paramètres globaux - Monsieur WiFi',
    'heading' => 'Paramètres globaux',
    'breadcrumb' => 'Paramètres',

    // Tabs
    'tab_captive_portal' => 'Portail captif',
    'tab_radius' => 'Configuration RADIUS',
    'tab_branding' => 'Image de marque',
    'tab_system' => 'Système',

    // Buttons
    'save_changes' => 'Enregistrer les modifications',

    // Captive Portal tab
    'section_default_wifi' => 'Paramètres WiFi par défaut',
    'label_default_essid' => 'ESSID par défaut',
    'help_default_essid' => 'Cet ESSID sera utilisé par défaut pour tous les nouveaux points d\'accès',
    'label_default_guest_essid' => 'ESSID invité par défaut',
    'help_default_guest_essid' => 'Cet ESSID sera utilisé par défaut pour les réseaux invités',
    'label_default_password' => 'Mot de passe par défaut',
    'help_default_password' => 'Mot de passe par défaut pour les nouveaux points d\'accès (minimum 8 caractères)',

    'section_portal_behavior' => 'Comportement du portail captif',
    'label_portal_timeout' => 'Délai d\'expiration de session par défaut',
    'unit_hours' => 'Heures',
    'help_portal_timeout' => 'Durée pendant laquelle les utilisateurs restent authentifiés avant de devoir se reconnecter',
    'label_idle_timeout' => 'Délai d\'inactivité par défaut',
    'unit_minutes' => 'Minutes',
    'help_idle_timeout' => 'Déconnecter les utilisateurs inactifs après cette période',
    'label_bandwidth_limit' => 'Limite de bande passante par défaut',
    'unit_mbps' => 'Mbps',
    'help_bandwidth_limit' => 'Limite de bande passante par défaut par utilisateur',
    'label_user_limit' => 'Nombre maximum d\'utilisateurs par défaut',
    'help_user_limit' => 'Nombre maximum d\'utilisateurs simultanés par point d\'accès',
    'label_enable_terms' => 'Afficher les Conditions d\'utilisation',
    'help_enable_terms' => 'Exiger l\'acceptation des Conditions d\'utilisation avant la connexion',

    // RADIUS tab
    'section_primary_radius' => 'Serveur RADIUS principal',
    'label_radius_ip' => 'Adresse IP du serveur',
    'help_radius_ip' => 'Adresse IP de votre serveur RADIUS principal',
    'label_radius_port' => 'Port d\'authentification',
    'help_radius_port' => 'Port utilisé pour l\'authentification RADIUS (par défaut : 1812)',
    'label_radius_secret' => 'Secret partagé',
    'help_radius_secret' => 'Secret partagé pour l\'authentification RADIUS',
    'label_accounting_port' => 'Port de comptabilité',
    'help_accounting_port' => 'Port utilisé pour la comptabilité RADIUS (par défaut : 1813)',

    // Branding tab
    'section_company_info' => 'Informations de l\'entreprise',
    'label_company_name' => 'Nom de l\'entreprise',
    'help_company_name' => 'Le nom de votre entreprise tel qu\'affiché sur le portail captif',
    'label_company_website' => 'Site web de l\'entreprise',
    'help_company_website' => 'L\'URL du site web de votre entreprise',
    'label_contact_email' => 'Email de contact',
    'help_contact_email' => 'Email de contact affiché sur le portail captif',
    'label_support_phone' => 'Téléphone de support',
    'help_support_phone' => 'Numéro de téléphone de support affiché sur le portail captif',

    'section_logo_images' => 'Logo et images',
    'label_logo' => 'Logo de l\'entreprise',
    'choose_file' => 'Choisir un fichier',
    'help_logo' => 'Taille recommandée : 300px x 100px (PNG ou SVG avec transparence)',
    'label_current_logo' => 'Logo actuel',
    'alt_current_logo' => 'Logo actuel',
    'label_favicon' => 'Favicon',
    'help_favicon' => 'Taille recommandée : 32px x 32px (ICO, PNG ou GIF)',
    'label_current_favicon' => 'Favicon actuel',
    'alt_current_favicon' => 'Favicon actuel',
    'label_splash_background' => 'Arrière-plan du portail captif',
    'help_splash_background' => 'Taille recommandée : 1920px x 1080px (JPG ou PNG)',

    'section_portal_customization' => 'Personnalisation du portail',
    'label_primary_color' => 'Couleur principale',
    'help_primary_color' => 'Couleur principale pour les boutons et les mises en évidence',
    'label_secondary_color' => 'Couleur secondaire',
    'help_secondary_color' => 'Couleur secondaire pour les accents et les éléments alternatifs',
    'label_font_family' => 'Police principale',
    'help_font_family' => 'Famille de police utilisée dans tout le portail',
    'label_portal_theme' => 'Thème du portail',
    'theme_light' => 'Clair',
    'theme_dark' => 'Sombre',
    'theme_auto' => 'Automatique (préférence système)',
    'help_portal_theme' => 'Thème par défaut pour le portail captif',

    // System tab
    'section_email_config' => 'Configuration email',
    'label_smtp_server' => 'Serveur SMTP',
    'help_smtp_server' => 'Serveur SMTP pour l\'envoi de notifications par email',
    'label_smtp_port' => 'Port SMTP',
    'help_smtp_port' => 'Port pour la connexion au serveur SMTP',
    'label_sender_email' => 'Email expéditeur',
    'help_sender_email' => 'Adresse email à partir de laquelle les notifications sont envoyées',
    'label_smtp_password' => 'Mot de passe SMTP',
    'help_smtp_password' => 'Mot de passe pour l\'authentification avec le serveur SMTP',
    'send_test_email' => 'Envoyer un email de test',

    // JS: button busy states
    'saving' => 'Enregistrement...',
    'sending' => 'Envoi...',

    // JS: toasts
    'toast_saved_title' => 'Paramètres enregistrés',
    'toast_saved_body' => 'Vos paramètres ont été enregistrés avec succès.',
    'toast_save_failed' => 'Échec de l\'enregistrement des paramètres.',
    'toast_error_title' => 'Erreur',
    'toast_test_email_title' => 'Email envoyé',
    'toast_test_email_body' => 'Email de test envoyé à {email}',
    'toast_test_email_failed' => 'Échec de l\'envoi de l\'email de test. Vérifiez vos paramètres SMTP.',
    'toast_load_failed' => 'Impossible de charger les paramètres. Veuillez réessayer.',
];
