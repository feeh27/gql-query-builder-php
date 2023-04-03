<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use function GQLQueryBuilder\mutation;

final class MutationTest extends TestCase
{

    public function testMutation()
    {
        $query = mutation([
            "operation" => "thoughtCreate",
            "variables" => [
                "name" => "Tyrion Lannister",
                "thought" => "I drink and I know things."
            ],
            "fields" => ["id"]
        ]);

        $this->assertEquals(
            'mutation($name: String, $thought: String) { thoughtCreate(name: $name, thought: $thought) { id } }',
            $query["query"]
        );

        $this->assertEquals(
            [
                "name" => "Tyrion Lannister",
                "thought" => "I drink and I know things."
            ],
            $query["variables"]
        );
    }

    public function testMutationWithRequiredVariables()
    {
        $query = mutation([
            "operation" => "userSignup",
            "variables" => [
                "name" => [
                    "value" => "Jon Doe",
                ],
                "email" => [
                    "value" => "jon.doe@example.com", "required" => true
                ],
                "password" => [
                    "value" => "123456", "required" => true
                ],
            ],
            "fields" => ["userId"]
        ]);

        $this->assertEquals(
            'mutation($name: String, $email: String!, $password: String!) { userSignup(name: $name, email: $email, password: $password) { userId } }',
            $query["query"]
        );

        $this->assertEquals(
            [
                "name" => "Jon Doe",
                "email" => "jon.doe@example.com",
                "password" => "123456"
            ],
            $query["variables"]
        );
    }

    public function testMutationWithCustomTypes()
    {
        $query = mutation([
            "operation" => "userPhoneNumber",
            "variables" => [
                "phone" => [
                    "value" => [
                        "prefix" => "+91",
                        "number" => "9876543210",
                    ],
                    "type" => "PhoneNumber",
                    "required" => true
                ]
            ],
            "fields" => ["id"]
        ]);

        $this->assertEquals(
            'mutation($phone: PhoneNumber!) { userPhoneNumber(phone: $phone) { id } }',
            $query["query"]
        );

        $this->assertEquals(
            [
                "phone" => [
                    "prefix" => "+91",
                    "number" => "9876543210",
                ]
            ],
            $query["variables"]
        );
    }
}
