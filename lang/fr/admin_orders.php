<?php

return [
    'page_title' => 'Gérer les Commandes - Monsieur WiFi',
    'heading' => 'Gérer les Commandes',
    'breadcrumb' => 'Gérer les Commandes',

    'filter_orders' => 'Filtrer les Commandes',
    'search_placeholder' => 'Rechercher un numéro de commande...',
    'btn_apply_filter' => 'Appliquer le Filtre',

    'status_all' => 'Tous les Statuts',
    'status_pending' => 'En attente',
    'status_processing' => 'En traitement',
    'status_shipped' => 'Expédiée',
    'status_delivered' => 'Livrée',
    'status_cancelled' => 'Annulée',
    'status_payment_failed' => 'Paiement échoué',

    'modal_assign_title' => 'Assigner l\'Inventaire à la Commande',
    'btn_assign_devices' => 'Assigner et Créer les Appareils',

    // JS-only strings (consumed via window.APP_I18N.admin_orders)
    'js_session_expired' => 'Session expirée. Veuillez vous reconnecter.',
    'js_no_permission' => 'Vous n\'avez pas la permission d\'accéder à cette page.',
    'js_load_orders_failed' => 'Échec du chargement des commandes : {message}',
    'js_no_orders' => 'Aucune commande trouvée',
    'js_no_tracking' => 'Aucun suivi',
    'js_btn_view' => 'Voir',
    'js_btn_tracking' => 'Suivi',

    'js_load_details_failed' => 'Échec du chargement des détails de la commande',
    'js_label_customer' => 'Client',
    'js_label_payment' => 'Paiement',
    'js_label_shipping_info' => 'Livraison',
    'js_label_tracking_info' => 'Suivi',
    'js_summary_title' => 'Résumé de la commande',
    'js_summary_subtotal' => 'Sous-total',
    'js_summary_shipping' => 'Livraison',
    'js_summary_tax' => 'Taxes',
    'js_summary_total' => 'Total',

    'js_badge_awaiting_payment' => 'En attente de paiement',
    'js_badge_paid' => 'Paiement reçu',

    'js_btn_invoice' => 'Facture',
    'js_btn_update_inventory' => 'Modifier inventaire',
    'js_btn_assign_inventory' => 'Assigner inventaire',
    'js_btn_update_tracking' => 'Modifier suivi',
    'js_btn_add_tracking' => 'Ajouter suivi',
    'js_btn_confirm_payment' => 'Confirmer paiement',
    'js_btn_mark_shipped' => 'Expédier',
    'js_btn_mark_delivered' => 'Livrer',
    'js_btn_cancel_order' => 'Annuler',

    'js_confirm_payment_received' => 'Confirmer que le paiement a été reçu ?',
    'js_confirm_cancel' => 'Êtes-vous sûr de vouloir annuler cette commande ?',

    'js_status_info_cancelled' => 'Commande annulée',
    'js_status_info_completed' => 'Commande terminée',

    'js_payment_stripe_confirmed' => 'Paiement Stripe confirmé avec succès',
    'js_payment_confirmed' => 'Paiement confirmé avec succès',
    'js_payment_confirm_failed' => 'Échec de la confirmation du paiement',

    'js_tracking_add_title' => 'Ajouter les informations d\'expédition',
    'js_tracking_provider' => 'Transporteur',
    'js_tracking_select_provider' => 'Sélectionnez un transporteur...',
    'js_tracking_major_carriers' => 'Principaux transporteurs européens',
    'js_tracking_other_providers' => 'Autre',
    'js_tracking_other_provider_name' => 'Nom du transporteur',
    'js_tracking_enter_provider' => 'Entrez le nom du transporteur',
    'js_tracking_id' => 'Numéro de suivi',
    'js_tracking_enter_tracking' => 'Entrez le numéro de suivi',
    'js_tracking_btn_save' => 'Enregistrer le suivi',
    'js_tracking_error_select_provider' => 'Veuillez sélectionner un transporteur',
    'js_tracking_error_enter_provider' => 'Veuillez entrer le nom du transporteur',
    'js_tracking_error_enter_tracking' => 'Veuillez entrer le numéro de suivi',
    'js_tracking_updated' => 'Informations de suivi mises à jour avec succès',
    'js_tracking_update_failed' => 'Échec de la mise à jour du suivi',

    'js_status_updated' => 'Statut de la commande mis à jour',
    'js_status_update_failed' => 'Échec de la mise à jour du statut',

    'js_inv_instructions' => 'Sélectionnez les articles d\'inventaire à assigner à chaque article de commande. Le nombre d\'articles doit correspondre à la quantité commandée.',
    'js_inv_label_quantity' => 'Quantité',
    'js_inv_label_available' => 'Articles disponibles',
    'js_inv_select_items' => 'Sélectionner les articles (exactement {n})',
    'js_inv_click_to_select' => 'Cliquez pour sélectionner/désélectionner les articles',
    'js_inv_error_load_order' => 'Erreur lors du chargement de la commande',
    'js_inv_error_select_exactly' => 'Vous devez sélectionner exactement {n} article(s)',
    'js_inv_assigned_success' => 'Inventaire assigné et appareils créés avec succès',
    'js_inv_error_assign' => 'Erreur lors de l\'assignation de l\'inventaire',

    'js_invoice_download_failed' => 'Échec du téléchargement de la facture',
    'js_invoice_downloaded' => 'Facture téléchargée avec succès',
    'js_invoice_filename' => 'facture-{order}.pdf',
];
