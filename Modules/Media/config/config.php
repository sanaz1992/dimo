<?php

return [
    'name' => 'Media',

    'validations' => [
        'image' => [
            'mimes' => 'jpeg,png,jpg',
            'max' => 5120,
        ]
    ]
];
