<?php

namespace GQLQueryBuilder;

function query(array $options): array
{
    if (array_is_list($options)) {
        $defaultAdapter = new DefaultQueryAdapter($options);
        return $defaultAdapter->queriesBuilder($options);
    } else {
        $defaultAdapter = new DefaultQueryAdapter($options);
        return $defaultAdapter->queryBuilder();
    }
}
