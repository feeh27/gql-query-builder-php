<?php

require_once __DIR__ . "/vendor/autoload.php";

use function GQLQueryBuilder\mutation;

$mutation = mutation([
    "operation" => "createThought",
    "variables" => [
        "name" => "John Doe",
        "thought" => "Hello World"
    ],
    "fields" => ["id", "name", "thought"]
]);

print_r($mutation);
