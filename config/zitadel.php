<?php

return [
    'issuer'        => env('ZITADEL_ISSUER', 'https://auth.digilan-edge.com'),
    'client_id'     => env('ZITADEL_CLIENT_ID'),
    'client_secret' => env('ZITADEL_CLIENT_SECRET'),
    'redirect_uri'  => env('ZITADEL_REDIRECT_URI'),
    'project_id'    => env('ZITADEL_PROJECT_ID'),
];
