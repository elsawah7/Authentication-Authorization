<?php

return [
    'providers' => [
        'google' => [
            'url' => url('/auth/google/redirect'),
            'color' => 'red',
            'icon' => 'fab fa-google',
            'name' => 'Google',
        ],
        'github' => [
            'url' => url('/auth/github/redirect'),
            'color' => 'gray',
            'icon' => 'fab fa-github',
            'name' => 'GitHub',
        ],
        'facebook' => [
            'url' => url('/auth/facebook/redirect'),
            'color' => 'blue',
            'icon' => 'fab fa-facebook-f',
            'name' => 'Facebook',
        ],
    ]

];
