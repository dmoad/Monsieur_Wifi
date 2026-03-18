<?php

/**
 * Plan definitions for mrwifi organizations.
 *
 * Each plan defines:
 *   - limits: hard caps enforced by the backend
 *   - features: entitlements unlocked by this plan (on top of RBAC)
 *
 * "managed" is special: limits/features are set per-org via features_override + plan_metadata.
 */

return [

    'free' => [
        'label'    => 'Free',
        'limits'   => [
            'max_devices'        => 1,
            'max_locations'      => 1,
            'max_captive_portals'=> 1,
            'max_zones'          => 1,
            'max_team_members'   => 1,  // owner only
        ],
        'features' => [
            'dashboard',
            'devices',
            'locations',
            'zones',
            'captive-portals',
            'profile',
        ],
    ],

    'starter' => [
        'label'    => 'Starter',
        'limits'   => [
            'max_devices'        => 5,
            'max_locations'      => 3,
            'max_captive_portals'=> 5,
            'max_zones'          => 3,
            'max_team_members'   => 5,
        ],
        'features' => [
            'dashboard',
            'devices',
            'locations',
            'zones',
            'captive-portals',
            'profile',
            'team',
            'analytics-basic',
        ],
    ],

    'business' => [
        'label'    => 'Business',
        'limits'   => [
            'max_devices'        => 20,
            'max_locations'      => 10,
            'max_captive_portals'=> -1, // unlimited
            'max_zones'          => 10,
            'max_team_members'   => 20,
        ],
        'features' => [
            'dashboard',
            'devices',
            'locations',
            'zones',
            'captive-portals',
            'profile',
            'team',
            'domain-blocking',
            'analytics',
        ],
    ],

    'pro' => [
        'label'    => 'Pro',
        'limits'   => [
            'max_devices'        => 60,
            'max_locations'      => 30,
            'max_captive_portals'=> -1,
            'max_zones'          => 30,
            'max_team_members'   => 50,
        ],
        'features' => [
            'dashboard',
            'devices',
            'locations',
            'zones',
            'captive-portals',
            'profile',
            'team',
            'domain-blocking',
            'analytics',
            'analytics-advanced',
            'marketing-wifi',
        ],
    ],

    'enterprise' => [
        'label'    => 'Enterprise',
        'limits'   => [
            'max_devices'        => 200,
            'max_locations'      => -1,
            'max_captive_portals'=> -1,
            'max_zones'          => -1,
            'max_team_members'   => -1,
        ],
        'features' => [
            'dashboard',
            'devices',
            'locations',
            'zones',
            'captive-portals',
            'profile',
            'team',
            'domain-blocking',
            'analytics',
            'analytics-advanced',
            'marketing-wifi',
            'multi-site-vpn',
            'api-access',
        ],
    ],

    'managed' => [
        'label'    => 'Managed (WiFi clé en main)',
        'limits'   => [
            // Defaults for managed — overridden per-org via plan_metadata
            'max_devices'        => -1,
            'max_locations'      => -1,
            'max_captive_portals'=> -1,
            'max_zones'          => -1,
            'max_team_members'   => -1,
        ],
        'features' => [
            // Base features — extended per-org via features_override
            'dashboard',
            'devices',
            'locations',
            'zones',
            'captive-portals',
            'profile',
            'team',
            'domain-blocking',
            'analytics',
        ],
    ],

];
