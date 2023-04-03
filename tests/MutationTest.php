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
    }
}
