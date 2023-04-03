<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use function GQLQueryBuilder\query;

final class QueryTest extends TestCase
{
    public function testQuery(): void
    {
        $query = query([
            "operation" => "thoughts",
            "fields" => ['id', 'name', 'thought']
        ]);

        $this->assertEquals(
            'query { thoughts { id, name, thought } }',
            $query["query"]
        );
    }

    public function testQueryWithVariables(): void
    {
        $query = query([
            "operation" => "thoughts",
            "variables" => [
                "id" => 1
            ],
            "fields" => ['id', 'name', 'thought']
        ]);

        $this->assertEquals(
            'query($id: Int) { thoughts(id: $id) { id, name, thought } }',
            $query["query"]
        );
    }

    public function testQueryWithNestedFields(): void
    {
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

        $this->assertEquals(
            'query { orders { id, amount, user { id, name, email, address { city, country } } } }',
            $query["query"]
        );
    }

    public function testQueryWithCustomArugmentName()
    {
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

        $this->assertEquals(
            'query($id: ID, $id2: ID) { someoperation(id: $id) { nestedoperation (id: $id2) { field1 } } }',
            $query["query"]
        );

        $this->assertEquals(
            [
                "id" => 456,
                "id2" => 123
            ],
            $query["variables"]
        );
    }

    public function testQueryWithRequiredVariables()
    {
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

        $this->assertEquals(
            'query($email: String!, $password: String!) { userLogin(email: $email, password: $password) { userId, token } }',
            $query["query"]
        );
    }

    public function testQueryWithEmptyFields()
    {
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

        $this->assertEquals(
            'query { getFilteredUsersCount, getAllUsersCount, getFilteredUsers { count } }',
            $query["query"]
        );
    }

    public function testQueryWithAlias()
    {
        $query = query([
            "operation" => [
                "name" => "thoughts",
                "alias" => "myThoughts"
            ],
            "fields" => ["id", "name", "thought"]
        ]);

        $this->assertEquals(
            'query { myThoughts: thoughts { id, name, thought } }',
            $query["query"]
        );
    }

    public function testQueryWithInlineFragment()
    {
        $query = query([
            "operation" => "thought",
            "fields" => [
                "id",
                "name",
                "thought",
                [
                    "operation" => "FragmentType",
                    "fields" => ["emotion"],
                    "fragment" => true
                ]
            ]
        ]);

        $this->assertEquals(
            'query { thought { id, name, thought, ... on FragmentType { emotion } } }',
            $query["query"]
        );
    }
}
