<?php

//namespace MeiliSearch\Client;
namespace App\Search;

use MeiliSearch\Client;

class ProductSearch
{
    private $index;

    public function __construct(Client $client)
    {
        $this->index = $client->index('products');
    }

    public function search(string $query, array $filters = [], int $limit = 10): array
    {
        $options = ['limit' => $limit];
        if(!empty($filters))
        {
            $options['filter'] = $filters;
        }

        $result = $this->index->search($query, $options);
        return $result->getHits();
    }
}

?>