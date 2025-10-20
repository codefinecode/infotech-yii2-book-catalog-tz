<?php

return [
    'manageBooks' => [
        'type' => 2,
        'description' => 'Manage books (CRUD)',
    ],
    'user' => [
        'type' => 1,
        'description' => 'Authenticated User',
        'children' => [
            'manageBooks',
        ],
    ],
];
