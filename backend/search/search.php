<?php 

require_once __DIR__ . '/../include/meilisearch.php';
require_once __DIR__ . '/../include/config.php';
$index = $client->index('products');

$products = R::getAll('SELECT p.*, c.name as category_name, b.name as brand_name FROM products p LEFT JOIN category c ON p.category_id = c.id INNER JOIN brands b ON p.brand_id = b.id');

$data = [];
foreach($products as $product)
{
    $data[] =
    [
        'id' => $product['id'],
        'name' => $product['name'],
        'subtitle' => $product['subtitle'],
        'category' => $product['category_name'],
        'brand' => $product['brand_name'],
        'specifications' => $product['specifications'],
        'img' => $product['img'],
        'url' => $product['url'],
        'created_at' => $product['created_at'], 
        'Article' => $product['Article'],
        'price' => $product['price'],
        'oldprice' => $product['oldprice'],
        'stock' => $product['stock'],
        'isNew' => $product['isNew'],
        'isPopular' => $product['isPopular']
    ];
}

if(!empty($data))
{
    $index = $client->index('products');
    $index->updateSearchableAttributes(['name', 'subtitle', 'category', 'article', 'brand']);
    $index->updateFilterableAttributes(['category', 'brand', 'price', 'isNew']);
    $index->addDocuments($data);
}

?>