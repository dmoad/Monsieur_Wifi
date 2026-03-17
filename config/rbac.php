<?php

/**
 * RBAC role definitions for mrwifi.
 *
 * These roles are seeded into the authz service on every application boot
 * via the AuthzServiceProvider. The SeedRoles endpoint is idempotent.
 *
 * Resource hierarchy (checked by AuthzService):
 *   org > zone > location > device
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Resource targets
    |--------------------------------------------------------------------------
    */
    'targets' => [
        'org'      => 'mrwifi:org',
        'zone'     => 'mrwifi:zone',
        'location' => 'mrwifi:location',
        'device'   => 'mrwifi:device',
    ],

    /*
    |--------------------------------------------------------------------------
    | Role definitions
    |--------------------------------------------------------------------------
    | Each role has a unique ID, name, and a list of actions per target.
    | The authz service stores these in the roles + role_actions tables.
    */
    'roles' => [
        [
            'id'          => 1,
            'name'        => 'owner',
            'alias'       => 'Owner',
            'description' => 'Full control including billing, delete, and invite',
            'actions'     => [
                ['target' => 'mrwifi:org',      'action' => '*'],
                ['target' => 'mrwifi:zone',     'action' => '*'],
                ['target' => 'mrwifi:location', 'action' => '*'],
                ['target' => 'mrwifi:device',   'action' => '*'],
            ],
        ],
        [
            'id'          => 2,
            'name'        => 'admin',
            'alias'       => 'Admin',
            'description' => 'Manage settings, invite users, configure resources',
            'actions'     => [
                ['target' => 'mrwifi:org',      'action' => 'read'],
                ['target' => 'mrwifi:org',      'action' => 'write'],
                ['target' => 'mrwifi:org',      'action' => 'manage'],
                ['target' => 'mrwifi:zone',     'action' => '*'],
                ['target' => 'mrwifi:location', 'action' => '*'],
                ['target' => 'mrwifi:device',   'action' => '*'],
            ],
        ],
        [
            'id'          => 3,
            'name'        => 'operator',
            'alias'       => 'Operator',
            'description' => 'Day-to-day operations: manage devices, networks, portals',
            'actions'     => [
                ['target' => 'mrwifi:org',      'action' => 'read'],
                ['target' => 'mrwifi:zone',     'action' => 'read'],
                ['target' => 'mrwifi:zone',     'action' => 'write'],
                ['target' => 'mrwifi:location', 'action' => 'read'],
                ['target' => 'mrwifi:location', 'action' => 'write'],
                ['target' => 'mrwifi:device',   'action' => 'read'],
                ['target' => 'mrwifi:device',   'action' => 'write'],
            ],
        ],
        [
            'id'          => 4,
            'name'        => 'viewer',
            'alias'       => 'Viewer',
            'description' => 'Read-only access to dashboards and analytics',
            'actions'     => [
                ['target' => 'mrwifi:org',      'action' => 'read'],
                ['target' => 'mrwifi:zone',     'action' => 'read'],
                ['target' => 'mrwifi:location', 'action' => 'read'],
                ['target' => 'mrwifi:device',   'action' => 'read'],
            ],
        ],
        [
            'id'          => 5,
            'name'        => 'partner',
            'alias'       => 'Partner',
            'description' => 'External installer: manage devices and locations for a client org',
            'actions'     => [
                ['target' => 'mrwifi:org',      'action' => 'read'],
                ['target' => 'mrwifi:zone',     'action' => 'read'],
                ['target' => 'mrwifi:zone',     'action' => 'write'],
                ['target' => 'mrwifi:location', 'action' => 'read'],
                ['target' => 'mrwifi:location', 'action' => 'write'],
                ['target' => 'mrwifi:device',   'action' => 'read'],
                ['target' => 'mrwifi:device',   'action' => 'write'],
                ['target' => 'mrwifi:device',   'action' => 'manage'],
            ],
        ],
    ],

];
