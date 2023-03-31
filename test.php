<?php

use GQLQueryBuilder\Utils;

use function GQLQueryBuilder\query;

require_once 'vendor/autoload.php';

print_r(query([
    "operation" => "hello",
    "variables" => [
        "id" => 1
    ],
    "fields" => [
        "world",
        "universe",
        "user" => [
            "id",
            "name"
        ]
    ],
]));
