# GraphQL Query Builder for PHP

A simple helper function to build GraphQL queries, mutations and subscriptions use PHP.

This project is based on [GraphQL Query Builder](https://www.npmjs.com/package/gql-query-builder) for Node.js.

## Install

`composer require mathsgod/gql-query-builder`

## Usage

```php
use function GQLQueryBuilder\query;
use function GQLQueryBuilder\mutation;
use function GQLQueryBuilder\subscription;

$query=query($options);
$mutation=mutation($options);
$subscription=subscription($options);
```

## Options
   
`options is ["operation","field","variables"] or an array of options`

### Examples

#### Query:

```php
$query=query([
    "operation"=>"thoughts",
    "fields"=>['id', 'name', 'thought']
]);

print_r($query);

// output:
/*
Array
(
    [query] => query { thoughts { id name thought } }
    [variables] => Array
        (
        )
)
*/
```



#### Query (with variables):

```php
$query=query([
    "operation"=>"thoughts",
    "fields"=>['id', 'name', 'thought'],
    "variables"=>[
        "id"=>1,
    ]
]);

print_r($query);

// output:
/*
Array
(
    [query] => query ($id: Int) { thoughts(id: $id) { id name thought } }
    [variables] => Array
        (
            [id] => 1
        )
)
*/
```

#### Query (with nested fields selection):
    
```php
$query = query([
    "operation" => "orders",
    "fields" => [
        "id",
        "amount",
        "user" => [
            "id",
            "name",
            "email",
            "address" => [
                "city",
                "country",
            ]
        ]
    ]
]);

// output:
/*
Array
(
    [query] => query { orders { id, amount, user { id, name, email, address { city, country } } } }
    [variables] => Array
        (
        )

)
*/
```

#### Query (with custom argument name):

```php
$query = query([
    "operation" => "someoperation",
    "fields" => [
        [
            "operation" => "nestedoperation",
            "fields" => ['field1'],
            "variables" => [
                "id2" => [
                    "name" => "id",
                    "type" => "ID",
                    "value" => 123
                ]
            ],
        ]
    ],
    "variables" => [
        "id" => [
            "name" => "id",
            "type" => "ID",
            "value" => 456
        ]
    ]
]);

/*
Array
(
    [query] => query($id: ID, $id2: ID) { someoperation(id: $id) { nestedoperation (id: $id2)  { field1 }  } }
    [variables] => Array
        (
            [id] => 456
            [id2] => 123
        )

)
*/
```

#### Query (with required variables)
```php

$query=query([
    "operation"=>"userLogin",
    "variables"=>[
        "email"=>[
            "value"=>"jon.doe@example.com",
            "required"=>true
        ],
        "password"=>[
            "value"=>"123456",
            "required"=>true
        ]
    ],
    "fields"=>["userId","token"]
]);

/*

Array
(
    [query] => query($email: String!, $password: String!) { userLogin(email: $email, password: $password) { userId, token } }
    [variables] => Array
        (
            [email] => jon.doe@example.com
            [password] => 123456
        )

)

*/
```



#### Query (with empty fields):

```php
$query = query([
    [
        "operation" => "getFilteredUsersCount",
    ],
    [
        "operation" => "getAllUsersCount",
        "fields" => []
    ],
    [
        "operation" => "getFilteredUsers",
        "fields" => [
            "count" => []
        ]
    ]
]);

print_r($query);

/*
Array
(
    [query] => query { getFilteredUsersCount getAllUsersCount getFilteredUsers { count } }
    [variables] => Array
        (
        )

)
*/
```

#### Mutation:

```php

$mutation=mutation([
    "operation"=>"createThought",
    "variables"=>[
        "name"=>"John Doe",
        "thought"=>"Hello World"
    ],
    "fields"=>["id","name","thought"]
]);

print_r($mutation);

/*

Array
(
    [query] => mutation($name: String, $thought: String) { createThought(name: $name, thought: $thought) { id name thought } }
    [variables] => Array
        (
            [name] => John Doe
            [thought] => Hello World
        )

)

*/
```




