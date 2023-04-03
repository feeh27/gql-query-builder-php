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


function mutation(array $options): array
{
    if (array_is_list($options)) {
        $defaultAdapter = new DefaultMutationAdapter($options);
        return $defaultAdapter->mutationsBuilder($options);
    } else {
        $defaultAdapter = new DefaultMutationAdapter($options);
        return $defaultAdapter->mutationBuilder();
    }
}

function subscription(array $options): array
{
    if (array_is_list($options)) {
        $defaultAdapter = new DefaultSubscriptionAdapter($options);
        return $defaultAdapter->subscriptionsBuilder($options);
    } else {
        $defaultAdapter = new DefaultSubscriptionAdapter($options);
        return $defaultAdapter->subscriptionBuilder();
    }
}
