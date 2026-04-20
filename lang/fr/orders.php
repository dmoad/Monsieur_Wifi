<?php

return [
    'page_title' => 'Mes Commandes - Monsieur WiFi',
    'heading' => 'Mes Commandes',
    'breadcrumb' => 'Mes Commandes',
    'btn_continue_shopping' => 'Continuer mes Achats',

    'empty_title' => 'Aucune commande pour le moment',
    'empty_subtitle' => 'Commencez vos achats pour voir vos commandes ici !',
    'btn_shop_now' => 'Acheter Maintenant',

    // JS-only strings (consumed via window.APP_I18N.orders)
    'js_toast_login_required' => 'Veuillez vous connecter pour voir vos commandes',
    'js_toast_load_failed' => 'Échec du chargement des commandes',
    'js_order_number_prefix' => 'Commande #',
    'js_label_ordered' => 'Commandé : {date}',
    'js_label_delivered_on' => 'Livré : {date}',
    'js_label_status' => 'Statut',
    'js_label_total' => 'Total',
    'js_label_tax' => '(Taxe : {amount} €)',
    'js_btn_view_details' => 'Voir Détails',
    'js_btn_invoice' => 'Facture',
    'js_tracking_html' => 'Suivi : <strong>{provider}</strong> - {id}',
    'js_status_cancelled' => 'Annulée',
    'js_status_awaiting_payment' => 'En attente de paiement',
    'js_status_delivered' => 'Livrée',
    'js_status_shipped' => 'Expédiée',
    'js_status_paid' => 'Paiement reçu',
    'js_toast_session_expired' => 'Session expirée. Veuillez vous reconnecter.',
    'js_toast_invoice_failed' => 'Échec du téléchargement de la facture',
    'js_toast_invoice_downloaded' => 'Facture téléchargée avec succès',
    'js_invoice_filename' => 'facture-{order}.pdf',
];
