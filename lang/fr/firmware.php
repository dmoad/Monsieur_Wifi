<?php

return [
    'page_title' => 'Gestion de firmware - Monsieur WiFi',
    'heading' => 'Gestion de firmware',
    'breadcrumb' => 'Firmware',

    // Header action
    'upload_new' => 'Télécharger un nouveau firmware',

    // Summary stats
    'total_versions' => 'Total des versions de firmware',
    'enabled_firmware' => 'Firmware activés',
    'disabled_firmware' => 'Firmware désactivés',
    'total_size' => 'Taille totale',

    // Table
    'table_card_title' => 'Toutes les versions de firmware',
    'col_name' => 'Nom',
    'col_status' => 'Statut',
    'col_model' => 'Modèle d\'appareil',
    'col_default' => 'Par défaut',
    'col_size' => 'Taille',
    'col_actions' => 'Actions',

    // Upload modal
    'modal_upload_title' => 'Télécharger un nouveau firmware',
    'name_label' => 'Nom du firmware',
    'name_placeholder' => 'ex. v2.1.5 Mise à jour de sécurité',
    'status_label' => 'Statut',
    'status_enable' => 'Activer',
    'status_disable' => 'Désactiver',
    'model_label' => 'Modèle d\'appareil',
    'default_checkbox' => 'Définir comme firmware par défaut pour ce modèle',
    'default_help' => 'Lorsqu\'activé, ce firmware sera automatiquement assigné aux nouveaux appareils de ce modèle.',
    'description_label' => 'Description',
    'description_placeholder' => 'Description et changelog du firmware',
    'file_label' => 'Fichier firmware',
    'choose_file' => 'Choisir un fichier',
    'file_help_upload' => 'Taille max : 100MB. Formats acceptés : .tar.gz, .tgz, .tar',
    'cancel' => 'Annuler',
    'upload_btn' => 'Télécharger le firmware',

    // Edit modal
    'modal_edit_title' => 'Modifier le firmware',
    'file_optional_label' => 'Fichier firmware (Optionnel)',
    'choose_firmware_file' => 'Choisir un fichier firmware',
    'file_help_edit' => 'Formats acceptés : .tar.gz, .tgz, .tar',
    'save_changes' => 'Enregistrer les modifications',

    // JS: Select2 placeholders
    'select_status_placeholder' => 'Sélectionner le statut',
    'select_model_placeholder' => 'Sélectionner le modèle d\'appareil',
    'select_model_option' => 'Sélectionner un modèle',
    'loading' => 'Chargement...',

    // JS: badges
    'badge_enabled' => 'Activé',
    'badge_disabled' => 'Désactivé',
    'badge_default' => 'Par défaut',

    // JS: row action dropdown
    'action_edit' => 'Modifier',
    'action_download' => 'Télécharger',
    'action_set_default' => 'Définir par défaut',
    'action_delete' => 'Supprimer',

    // JS: toasts + dialogs
    'please_select_file' => 'Veuillez sélectionner un fichier firmware',
    'upload_success' => 'Firmware téléchargé avec succès',
    'upload_error' => 'Erreur lors du téléchargement du firmware',
    'update_success' => 'Firmware mis à jour avec succès',
    'update_error' => 'Erreur lors de la mise à jour du firmware',
    'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer ce firmware ?',
    'delete_success' => 'Firmware supprimé avec succès',
    'delete_error' => 'Erreur lors de la suppression du firmware',
    'set_default_confirm' => 'Êtes-vous sûr de vouloir définir « {name} » comme firmware par défaut pour les appareils {model} ?',
    'set_default_success' => 'Firmware défini par défaut avec succès',
    'set_default_error' => 'Erreur lors de la définition du firmware par défaut',
    'load_error' => 'Erreur lors du chargement des données de firmware',
    'model_not_specified' => 'Non spécifié',

    // JS: DataTable language pack
    'dt_info' => 'Affichage de _START_ à _END_ sur _TOTAL_ entrées',
    'dt_info_empty' => 'Affichage de 0 à 0 sur 0 entrées',
    'dt_info_filtered' => '(filtré à partir de _MAX_ entrées totales)',
    'dt_length_menu' => 'Afficher _MENU_ entrées',
    'dt_search' => 'Rechercher :',
    'dt_zero_records' => 'Aucun enregistrement correspondant trouvé',
    'dt_empty_table' => 'Aucune donnée disponible dans le tableau',
    'dt_loading_records' => 'Chargement...',
];
