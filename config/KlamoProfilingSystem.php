<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\GlobalColor;
use App\Models\GlobalSize;
use App\Models\Category;
use App\Models\KlamoValue;
use App\Models\Tag;
use App\Models\Vendor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Setup Queue
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */
    'queues' => [
        'setup' => 'ProfilingSetup',
        'process' => 'ProfilingProcess',
    ],
    'chunk_size' => 10,

    /*
    |--------------------------------------------------------------------------
    | Default Model Connections
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */
    'models' => [
        'consumer' => Customer::class,

        'product' => Product::class,

        'profiling_tag_types' => [
            [
                'class' => GlobalColor::class,
                'column' => 'label',
                'type' => 'globalColors',
                'relationship' => 'many',
            ],
            [
                'class' => GlobalSize::class,
                'column' => 'label',
                'type' => 'globalSizes',
                'relationship' => 'many',
            ],
            [
                'class' => KlamoValue::class,
                'column' => 'label',
                'type' => 'klamoValues',
                'relationship' => 'many',
            ],
            [
                'class' => Category::class,
                'column' => 'label',
                'type' => 'categories',
                'relationship' => 'many',
            ],
            [
                'class' => Tag::class,
                'column' => 'name',
                'type' => 'tags',
                'relationship' => 'many',
            ],
            [
                'class' => Vendor::class,
                'column' => 'name',
                'type' => 'vendor',
                'relationship' => 'one',
            ]
        ],
    ],
];

?>