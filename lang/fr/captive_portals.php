<?php

return [
    'page_title' => 'Concepteur de portail captif - Monsieur WiFi',
    'heading' => 'Concepteur de portail captif',
    'breadcrumb' => 'Portails captifs',

    // Onboarding timeline
    'timeline_step1_label' => 'Je design mon portail',
    'timeline_step1_sub' => 'Portail captif personnalisé',
    'timeline_step2_label' => 'Je m\'abonne',
    'timeline_step2_sub' => 'Choix de l\'offre et paiement',
    'timeline_step3_label' => 'Je reçois ma borne',
    'timeline_step3_sub' => 'Livraison + assistance mise en service',

    // Designs list
    'your_designs_title' => 'Vos conceptions de portail captif',
    'create_new_design' => 'Créer une nouvelle conception',
    'back_to_designs' => 'Retour aux conceptions',

    // Designer
    'designer_title' => 'Concevez votre page de connexion',
    'tab_general' => 'Général',
    'tab_branding' => 'Image de marque',
    'save_design' => 'Enregistrer la conception',

    // General tab
    'section_basic_info' => 'Informations de base',
    'label_portal_name' => 'Nom du portail',
    'placeholder_portal_name' => 'Entrez un nom pour cette page de connexion',
    'label_description' => 'Description',
    'placeholder_description' => 'Brève description de cette conception',
    'label_theme_color' => 'Couleur du thème',

    'section_portal_content' => 'Contenu du portail',
    'label_welcome_message' => 'Message de bienvenue',
    'label_button_text' => 'Texte du bouton',
    'label_login_instructions' => 'Instructions de connexion',
    'label_show_terms' => 'Afficher le lien Conditions générales',

    'section_legal_content' => 'Contenu légal',
    'label_terms_content' => 'Contenu des conditions de service',
    'placeholder_terms_content' => 'Entrez le contenu de vos conditions de service',
    'label_privacy_content' => 'Contenu de la politique de confidentialité',
    'placeholder_privacy_content' => 'Entrez le contenu de votre politique de confidentialité',

    // Branding tab
    'label_location_logo' => 'Logo de l\'emplacement',
    'upload_location_logo' => 'Déposez votre logo d\'emplacement ici ou cliquez pour parcourir',
    'recommended_logo' => 'Recommandé : PNG ou SVG, 200x100px',
    'note_location_logo' => 'Votre logo d\'emplacement apparaîtra en haut de la page de connexion.',
    'label_background_image' => 'Image d\'arrière-plan',
    'upload_background' => 'Déposez votre image d\'arrière-plan ici ou cliquez pour parcourir',
    'recommended_background' => 'Recommandé : JPG ou PNG, 1920x1080px',
    'note_background' => 'Cette image sera affichée comme arrière-plan de la page.',

    'section_gradient' => 'Dégradé d\'arrière-plan (Alternative à l\'image)',
    'note_gradient' => 'Créez un arrière-plan dégradé au lieu d\'utiliser une image. Cela remplacera l\'image d\'arrière-plan si les deux sont définis.',
    'label_gradient_start' => 'Couleur de début du dégradé',
    'label_gradient_end' => 'Couleur de fin du dégradé',
    'btn_clear_gradient' => 'Effacer le dégradé',
    'btn_preset_blue_purple' => 'Bleu vers violet',
    'btn_preset_orange_pink' => 'Orange vers rose',
    'btn_test_gradient' => 'Tester le dégradé',

    // Preview panel
    'preview_title' => 'Aperçu',
    'alt_location_logo' => 'Logo de l\'emplacement',
    'alt_brand_logo' => 'Logo de marque',
    'placeholder_email' => 'Adresse e-mail',
    'powered_by' => 'Propulsé par Monsieur WiFi',

    // Modals
    'modal_terms_title' => 'Conditions de service',
    'modal_privacy_title' => 'Politique de confidentialité',
    'modal_delete_title' => 'Confirmer la suppression',
    'modal_delete_body' => 'Êtes-vous sûr de vouloir supprimer cette conception ? Cette action ne peut pas être annulée.',
    'modal_delete_confirm' => 'Supprimer',
    'modal_change_owner_title' => 'Changer le propriétaire de la conception',
    'modal_change_owner_body' => 'Sélectionnez un nouveau propriétaire pour cette conception de portail captif :',
    'label_new_owner' => 'Nouveau propriétaire',
    'loading_users' => 'Chargement des utilisateurs...',
    'note_change_owner_html' => '<strong>Note :</strong> Cette action transférera la propriété de la conception à l\'utilisateur sélectionné. Les informations du créateur original seront préservées.',
    'btn_change_owner' => 'Changer le propriétaire',

    // Defaults reused as blade initial values (and by JS in a later commit)
    'none' => 'Aucun',
    'welcome_default' => 'Bienvenue sur notre WiFi',
    'button_default' => 'Se connecter au WiFi',
    'instructions_default' => 'Entrez votre adresse e-mail pour vous connecter à notre réseau WiFi',
    'terms_default' => 'En accédant à ce service WiFi, vous acceptez de vous conformer à toutes les lois applicables et à la politique d\'utilisation acceptable du réseau. Nous nous réservons le droit de surveiller le trafic et le contenu accessible via notre réseau, et de résilier l\'accès en cas de violations de ces conditions.',
    'privacy_default' => 'Nous collectons des informations limitées lorsque vous utilisez notre service WiFi, y compris les identifiants d\'appareils, les heures de connexion et les données d\'utilisation. Ces informations sont utilisées pour améliorer notre service, résoudre les problèmes techniques et respecter les exigences légales. Nous ne vendons pas vos informations personnelles à des tiers.',
];
