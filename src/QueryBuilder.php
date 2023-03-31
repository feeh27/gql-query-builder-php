<?php

namespace GQLQueryBuilder;

function query(array $options): array
{
    $defaultAdapter = new DefaultQueryAdapter($options);
    return $defaultAdapter->queryBuilder();
}
