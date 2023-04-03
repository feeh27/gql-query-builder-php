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
}
