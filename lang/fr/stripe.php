<?php

return [
    // Stripe Checkout line items
    'deposit_name' => 'Caution borne WiFi',
    'deposit_desc' => 'Caution remboursable pour la borne WiFi Monsieur WiFi',
    'shipping_name' => 'Frais de livraison',
    'shipping_desc' => 'Livraison de la borne WiFi',

    // Custom text shown on Stripe Checkout. Placeholders:
    //   :deposit  formatted deposit amount in euros
    //   :shipping formatted shipping fee in euros
    //   :days     trial period length in days
    'submit_message' => "Votre abonnement inclut : borne WiFi avec portail captif pré-paramétré et assistance à la mise en service. Aujourd'hui vous payez uniquement la caution (:deposit€) + frais de livraison (:shipping€). Votre abonnement est offert pendant :days jours, puis facturé automatiquement.",

    // :url URL des conditions générales de vente
    'terms_message' => "J'accepte les [Conditions Générales de Vente](:url)",
];
