<?php

return [
    'page_title' => 'Gérer l\'inventaire - Monsieur WiFi',
    'heading' => 'Gérer l\'inventaire',
    'breadcrumb' => 'Gérer l\'inventaire',

    'btn_manage_models' => 'Gérer les Modèles',

    'summary_total_products' => 'Total des Produits',
    'summary_out_of_stock' => 'En Rupture de Stock',
    'summary_low_stock' => 'Stock Faible',
    'summary_total_value' => 'Valeur Totale',

    'section_heading' => 'Gestion de l\'inventaire',
    'info_hint' => 'Cliquez sur le bouton <strong>"Ajouter/Voir Appareils"</strong> pour ajouter des articles. Les adresses MAC sont automatiquement normalisées en MAJUSCULES avec délimiteur "-".',

    'stock_filter_all' => 'Tous les Statuts de Stock',
    'stock_in_stock' => 'En Stock',
    'stock_low' => 'Stock Faible',
    'stock_out' => 'En Rupture de Stock',

    'search_placeholder' => 'Rechercher des produits...',
    'btn_apply_filter' => 'Appliquer le Filtre',

    'modal_update_title' => 'Mettre à jour l\'inventaire',

    // JS-only strings (consumed via window.APP_I18N.admin_inventory)
    'js_session_expired' => 'Session expirée. Veuillez vous reconnecter.',
    'js_no_permission' => 'Vous n\'avez pas la permission d\'accéder à cette page.',
    'js_load_summary_failed' => 'Échec du chargement du résumé de l\'inventaire',
    'js_load_inventory_failed' => 'Échec du chargement de l\'inventaire : {message}',
    'js_load_devices_failed' => 'Échec du chargement des appareils',
    'js_load_devices_failed_prefix' => 'Échec du chargement des appareils : {message}',

    'js_no_products' => 'Aucun produit trouvé',
    'js_no_devices' => 'Aucun appareil trouvé',

    'js_label_in_stock' => 'En Stock :',
    'js_label_reserved' => 'Réservé :',
    'js_label_available' => 'Disponible :',
    'js_label_threshold' => 'Seuil :',

    'js_btn_add_view_devices' => 'Ajouter/Voir Appareils',
    'js_btn_add_view_devices_title' => 'Voir/Ajouter des appareils individuels',

    'js_badge_out_of_stock' => 'Rupture de Stock',
    'js_badge_low_stock' => 'Stock Faible',
    'js_badge_in_stock' => 'En Stock',

    'js_device_based_tracking' => 'Suivi basé sur les appareils',
    'js_device_based_tracking_desc' => 'La quantité d\'inventaire est automatiquement calculée en fonction des appareils individuels que vous ajoutez avec des adresses MAC et des numéros de série.',
    'js_label_devices_in_stock' => 'Appareils en Stock',
    'js_label_low_stock_threshold' => 'Seuil de Stock Faible',
    'js_threshold_hint' => 'Vous serez alerté lorsque le nombre d\'appareils disponibles est inférieur ou égal à ce seuil.',
    'js_modify_stock_heading' => 'Pour modifier la quantité en stock :',
    'js_btn_add_manage_devices' => 'Ajouter/Gérer les Appareils Individuels',
    'js_btn_save_threshold' => 'Enregistrer le Seuil',

    'js_threshold_updated' => 'Seuil de stock mis à jour avec succès',
    'js_threshold_update_failed' => 'Échec de la mise à jour du seuil',
    'js_save_failed_prefix' => 'Échec de l\'enregistrement : {message}',

    'js_pagination_page' => 'Page',
    'js_pagination_of' => 'sur',
    'js_pagination_devices' => 'appareils',
    'js_btn_previous' => 'Précédent',
    'js_btn_next' => 'Suivant',

    'js_devices_modal_desc' => 'Gérez les appareils individuels avec leurs adresses MAC et numéros de série',
    'js_btn_add_device' => 'Ajouter un Appareil',
    'js_btn_import_csv' => 'Importer CSV',

    'js_col_mac_address' => 'Adresse MAC',
    'js_col_serial_number' => 'Numéro de Série',
    'js_col_status' => 'Statut',
    'js_col_notes' => 'Notes',
    'js_col_actions' => 'Actions',

    'js_btn_close' => 'Fermer',
    'js_btn_edit' => 'Modifier',
    'js_btn_delete' => 'Supprimer',

    'js_device_status_available' => 'Disponible',
    'js_device_status_reserved' => 'Réservé',
    'js_device_status_sold' => 'Vendu',
    'js_device_status_defective' => 'Défectueux',

    'js_form_add_heading' => 'Ajouter un Nouvel Appareil',
    'js_form_edit_heading' => 'Modifier l\'Appareil',
    'js_mac_formats_hint' => 'Formats acceptés: 00-11-22-33-44-55 ou 00:11:22:33:44:55 (normalisé automatiquement)',
    'js_notes_placeholder' => 'Notes optionnelles sur cet appareil',
    'js_form_received_date' => 'Date de Réception',
    'js_btn_add_device_submit' => 'Ajouter l\'Appareil',
    'js_btn_update_device' => 'Mettre à Jour',

    'js_mac_serial_required' => 'L\'adresse MAC et le numéro de série sont requis',
    'js_device_added' => 'Appareil ajouté avec succès',
    'js_add_device_failed' => 'Échec de l\'ajout de l\'appareil',
    'js_add_device_failed_prefix' => 'Échec de l\'ajout de l\'appareil : {message}',
    'js_device_updated' => 'Appareil mis à jour avec succès',
    'js_update_device_failed' => 'Échec de la mise à jour de l\'appareil',
    'js_update_device_failed_prefix' => 'Échec de la mise à jour de l\'appareil : {message}',
    'js_confirm_delete_device' => 'Êtes-vous sûr de vouloir supprimer cet appareil ?',
    'js_confirm_delete_device_title' => 'Supprimer l\'appareil ?',
    'js_delete_btn' => 'Supprimer',
    'js_device_deleted' => 'Appareil supprimé avec succès',
    'js_delete_device_failed' => 'Échec de la suppression de l\'appareil',
    'js_delete_device_failed_prefix' => 'Échec de la suppression de l\'appareil : {message}',

    'js_csv_upload_heading' => 'Importer des Appareils depuis CSV',
    'js_btn_download_template' => 'Télécharger le Modèle CSV',
    'js_csv_format_label' => 'Format du fichier CSV :',
    'js_csv_format_desc' => 'Le fichier doit contenir les colonnes suivantes (avec en-tête) :',
    'js_csv_col_mac_desc' => 'Adresse MAC (formats acceptés: 00-11-22-33-44-55 ou 00:11:22:33:44:55)',
    'js_csv_col_serial_desc' => 'Numéro de série (requis)',
    'js_csv_col_notes_desc' => 'Notes (optionnel)',
    'js_csv_example_label' => 'Exemple :',
    'js_csv_mac_normalize_note' => 'Note: Les adresses MAC sont automatiquement normalisées (MAJUSCULES, délimiteur -)',
    'js_csv_select_label' => 'Sélectionner le fichier CSV',
    'js_csv_max_size' => 'Taille maximale: 5MB',
    'js_csv_skip_duplicates' => 'Ignorer les doublons (MAC/Série existants)',
    'js_btn_upload_import' => 'Télécharger et Importer',

    'js_csv_select_file_error' => 'Veuillez sélectionner un fichier CSV',
    'js_csv_too_large' => 'Le fichier est trop volumineux (max 5MB)',
    'js_csv_invalid_file' => 'Veuillez sélectionner un fichier CSV valide',
    'js_csv_uploading' => 'Téléchargement...',
    'js_csv_processing' => 'Traitement...',
    'js_csv_import_failed' => 'Échec de l\'importation',
    'js_csv_upload_failed_prefix' => 'Échec du téléchargement : {message}',

    'js_import_results_heading' => 'Résultats de l\'importation',
    'js_stat_imported' => 'Importés',
    'js_stat_duplicates' => 'Doublons (Ignorés)',
    'js_stat_errors' => 'Erreurs',
    'js_error_details' => 'Détails des Erreurs',
    'js_btn_back_to_list' => 'Retour à la Liste',

    'js_csv_template_downloaded' => 'Modèle CSV téléchargé',
];
