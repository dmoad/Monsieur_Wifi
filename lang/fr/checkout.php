<?php

return [
    'page_title' => 'Commander - Monsieur WiFi',
    'heading' => 'Commander',
    'breadcrumb' => 'Commander',

    // Shipping info section
    'section_shipping_info' => 'Informations de Livraison',
    'label_first_name' => 'Prénom *',
    'label_last_name' => 'Nom *',
    'label_company' => 'Entreprise',
    'label_address_1' => 'Adresse Ligne 1 *',
    'label_address_2' => 'Adresse Ligne 2',
    'label_city' => 'Ville *',
    'label_province' => 'Province *',
    'label_postal_code' => 'Code Postal *',
    'label_country' => 'Pays *',
    'label_phone' => 'Téléphone *',

    // Billing
    'checkbox_same_as_shipping' => 'Adresse de facturation identique à la livraison',
    'section_billing_info' => 'Informations de Facturation',

    // Shipping method
    'section_shipping_method' => 'Méthode de Livraison',

    // Order summary
    'section_order_summary' => 'Résumé de la Commande',
    'subtotal_label' => 'Sous-total :',
    'shipping_label' => 'Livraison :',
    'tax_label' => 'Taxe :',
    'total_label' => 'Total :',
    'btn_place_order' => 'Passer la Commande',

    // Payment modal
    'modal_complete_payment' => 'Finaliser le Paiement',
    'order_number_label' => 'Numéro de Commande :',
    'total_amount_label' => 'Montant Total :',
    'label_card' => 'Carte de Crédit ou Débit',
    'btn_pay_now' => 'Payer Maintenant',
    'processing_payment' => 'Traitement de votre paiement...',
    'processing_payment_subtitle' => 'Veuillez ne pas fermer cette fenêtre',

    // JS-only strings (consumed via window.APP_I18N.checkout)
    'js_toast_login_required' => 'Veuillez vous connecter pour passer à la caisse',
    'js_toast_cart_empty' => 'Votre panier est vide',
    'js_shipping_days_suffix' => 'jours',
    'js_toast_session_expired' => 'Session expirée. Veuillez vous reconnecter.',
    'js_processing' => 'Traitement...',
    'js_error_save_shipping' => 'Échec de l\'enregistrement de l\'adresse de livraison',
    'js_error_save_billing' => 'Échec de l\'enregistrement de l\'adresse de facturation',
    'js_error_place_order' => 'Échec de la commande',
    'js_toast_order_success' => 'Commande passée avec succès! Votre commande est en attente de confirmation de paiement.',
    'js_error_init_payment' => 'Échec de l\'initialisation du paiement',
    'js_toast_init_payment_failed' => 'Échec de l\'initialisation du paiement. Veuillez réessayer.',
    'js_toast_payment_success' => 'Paiement réussi! Confirmation de votre commande...',
    'js_toast_order_confirmed' => 'Commande confirmée! Redirection...',
    'js_toast_payment_confirmation_pending' => 'Paiement effectué mais confirmation en attente. Veuillez contacter le support si nécessaire.',
    'js_toast_payment_processed' => 'Paiement effectué. Redirection vers votre commande...',
    'js_toast_payment_processing' => 'Le paiement est en cours de traitement. Veuillez vérifier le statut de votre commande.',
    'js_toast_payment_failed' => 'Le paiement a échoué. Veuillez réessayer.',
    'js_confirm_cancel_payment' => 'Êtes-vous sûr de vouloir annuler le paiement? Votre commande restera en attente.',
    'js_confirm_cancel_payment_title' => 'Annuler le paiement ?',
    'js_cancel_payment_btn' => 'Annuler le paiement',
    'js_keep_paying_btn' => 'Continuer',
];
