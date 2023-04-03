<?php

use GQLQueryBuilder\Utils;

use function GQLQueryBuilder\query;

require_once 'vendor/autoload.php';


$query = query([
    "operation" => "userLogin",
    "variables" => [
        "email" => [
            "value" => "jon.doe@example.com",
            "required" => true
        ],
        "password" => [
            "value" => "123456",
            "required" => true
        ]
    ],
    "fields" => ["userId", "token"]
]);

print_r($query);



