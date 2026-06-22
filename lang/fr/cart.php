<?php

return [
    'page_title' => 'Panier - Monsieur WiFi',
    'heading' => 'Panier',
    'breadcrumb' => 'Panier',
    'btn_continue_shopping' => 'Continuer mes Achats',

    'empty_title' => 'Votre panier est vide',
    'empty_subtitle' => 'Ajoutez des produits pour commencer!',
    'btn_shop_now' => 'Acheter Maintenant',

    'order_summary' => 'Résumé de la Commande',
    'subtotal' => 'Sous-total :',
    'total' => 'Total :',
    'btn_checkout' => 'Passer la Commande',

    // JS-only strings (consumed via window.APP_I18N.cart)
    'js_toast_login_required' => 'Veuillez vous connecter pour voir votre panier',
    'js_toast_load_failed' => 'Échec du chargement du panier',
    'js_each' => 'chacun',
    'js_toast_update_failed' => 'Échec de la mise à jour de la quantité',
    'js_confirm_remove' => 'Êtes-vous sûr de vouloir retirer cet article?',
    'js_confirm_remove_title' => 'Retirer l\'article ?',
    'js_remove_btn' => 'Retirer',
    'js_toast_item_removed' => 'Article retiré du panier',
    'js_toast_remove_failed' => 'Échec du retrait de l\'article',

    // Controller response messages (returned by CartController, surfaced via data.message)
    'added' => 'Article ajouté au panier avec succès.',
    'insufficient_stock' => 'Stock insuffisant.',
    'add_failed' => 'Échec de l\'ajout de l\'article au panier.',
    'updated' => 'Panier mis à jour avec succès.',
    'update_failed' => 'Échec de la mise à jour du panier.',
    'item_removed' => 'Article retiré du panier.',
    'remove_failed' => 'Échec du retrait de l\'article.',
    'cleared' => 'Panier vidé.',
    'clear_failed' => 'Échec du vidage du panier.',
];
