<?php

use GQLQueryBuilder\Utils;

use function GQLQueryBuilder\query;

require_once 'vendor/autoload.php';

print_r(query([
    "operation" => "hello",
    "variables" => [
        "email" => [
            "value" => "raymond@hostlink.com.hk",
         
        ]
    ],
    "fields" => [
        "world",
        "universe",
    ],
]));
