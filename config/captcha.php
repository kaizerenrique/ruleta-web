<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),

    'characters' => [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
        'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
    ],

    // ✅ Rutas correctas: apuntan a los assets dentro de vendor/
    'fontsDirectory' => base_path('vendor/mews/captcha/assets/fonts'),
    'bgsDirectory'   => base_path('vendor/mews/captcha/assets/backgrounds'),

    'default' => [
        'length'    => 5,
        'width'     => 200,
        'height'    => 50,
        'quality'   => 90,
        'math'      => false,
        'expire'    => 60,
        'encrypt'   => false,
    ],

    'flat' => [
        'length'     => 5,
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#8e44ad', '#303f9f', '#f57c00'],
        'width'      => 200,
        'height'     => 50,
        'math'       => false,
        'quality'    => 100,
        'lines'      => 6,
        'bgImage'    => false,
        'bgColor'    => '#f0f4f8',
        'contrast'   => 0,
    ],

    'mini' => [
        'length' => 3,
        'width'  => 60,
        'height' => 32,
    ],

    'inverse' => [
        'length'    => 5,
        'width'     => 200,
        'height'    => 50,
        'quality'   => 90,
        'sensitive' => true,
        'angle'     => 12,
        'sharpen'   => 10,
        'blur'      => 2,
        'invert'    => false,
        'contrast'  => -5,
    ],

    'math' => [
        'length' => 9,
        'width'  => 200,
        'height' => 50,
        'quality'=> 90,
    ],
];
