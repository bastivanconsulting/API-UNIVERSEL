<?php

namespace Bastivan\UniversalApi\Hook\Controllers;

use Bastivan\UniversalApi\Core\Database;

class ShopController
{
    public function products()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, name, price, stock FROM products");
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode(['products' => $products]);
    }
}
