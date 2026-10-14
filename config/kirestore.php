<?php

return [
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin Kire Store'),
        'email' => env('ADMIN_EMAIL', 'admin@kirestore.test'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],
    'topup' => [
        'mobile_legends' => [
            'packages' => [
                'ml-86' => ['name' => '86 Diamonds', 'diamonds' => 86, 'price' => 20000],
                'ml-172' => ['name' => '172 Diamonds', 'diamonds' => 172, 'price' => 40000],
                'ml-257' => ['name' => '257 Diamonds', 'diamonds' => 257, 'price' => 60000],
                'ml-344' => ['name' => '344 Diamonds', 'diamonds' => 344, 'price' => 80000],
                'ml-514' => ['name' => '514 Diamonds', 'diamonds' => 514, 'price' => 120000],
                'ml-706' => ['name' => '706 Diamonds', 'diamonds' => 706, 'price' => 160000],
            ],
        ],
    ],
    'joki' => [
        'mobile_legends' => [
            'services' => [
                'rank' => 'Joki Rank',
                'winrate' => 'Push Win Rate',
                'placement' => 'Placement Match',
            ],
            'ranks' => [
                'Warrior',
                'Elite',
                'Master',
                'Grandmaster',
                'Epic',
                'Legend',
                'Mythic',
                'Mythical Honor',
                'Mythical Glory',
                'Mythical Immortal',
            ],
        ],
    ],
];
