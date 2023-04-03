<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use function GQLQueryBuilder\subscription;

final class SubscriptionTest extends TestCase
{

    public function testSubscription()
    {

        $query = subscription([
            "operation" => "thoughtCreate",
            "variables" => [
                "name" => "Tyrion Lannister",
                "thought" => "I drink and I know things."
            ],
            "fields" => ["id"]
        ]);

        $this->assertEquals(
            'subscription($name: String, $thought: String) { thoughtCreate(name: $name, thought: $thought) { id } }',
            $query["query"]
        );
    }
}
