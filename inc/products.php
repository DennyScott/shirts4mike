<?php

function get_products_count(){
    require(ROOT_PATH . "inc/database.php");

    try{
        $results = $db -> query("
            SELECT COUNT(sku)
            FROM products
            ");
    }catch(Exception $e){
        echo "Data could not be retrieved from the database";
    }

    return intval($results -> fetchColumn(0));
}

function get_products_recent() {

    require(ROOT_PATH . "inc/database.php");

    try{
        $results = $db -> query("
            SELECT name, price, img, sku, paypal
            FROM products
            ORDER BY sku DESC
            LIMIT 4");
    }catch(Exception $e){
        echo "Data could not be retrieved from the database";
    }
    $recent = array();
    $all = $results -> fetchAll(PDO::FETCH_ASSOC);

    $total_products = count($all);
    $position = 0;
    
    foreach($all as $product) {
        $position = $position + 1;
        if ($total_products - $position < 4) {
            $recent[] = $product;
        }
    }
    return array_reverse($recent);
}

function get_products_subset($positionStart, $positionEnd){


    $offset = $positionStart - 1;
    $rows = $positionEnd - $positionStart + 1;

    require(ROOT_PATH . "inc/database.php");

    try{
        $results = $db -> prepare("
            SELECT name, price, img, sku, paypal
            FROM products
            ORDER BY sku
            LIMIT ?, ?
            ");

        $results -> bindParam(1, $offset, PDO::PARAM_INT);
        $results -> bindParam(2, $rows, PDO::PARAM_INT);
        $results -> execute();
    }catch(Exception $e){
        echo "Data could not be retrieved from the database.";
    }
    $subset = $results -> fetchAll(PDO::FETCH_ASSOC);

    return $subset;
}

function get_products_search($search_term){

    require(ROOT_PATH . "inc/database.php");

    try{
       $results = $db -> prepare("
        SELECT name, price, img, sku, paypal
        FROM products
        WHERE name LIKE ?
        ORDER BY sku
        ");
       $results -> bindValue(1, "%" . $search_term . "%");
       $results -> execute();
   }catch(Exception $e){
    echo "Data could not be retrieved from the database";
    }

    $matches = $results -> fetchAll(PDO::FETCH_ASSOC);

return $matches;
}

function get_products_all() {

    require(ROOT_PATH . "inc/database.php");

    try{
        $results = $db -> query("SELECT name, price, img, sku, paypal FROM products ORDER BY sku ASC");

    } catch(Exception $e){
        echo "Data could not be retrieved from the database.";
        exit;
    }

    $products = $results->fetchAll(PDO::FETCH_ASSOC);

    return $products;
}


/*
 * Returns an array of product information for the product that 
 * matches the sku; returns a boolean false if no product matches
 * the sku.
 *
 *@param    int     $sku    the sku
 *@return   mixed   array   list of product information for the one matching product
 * bool             false   if no product matches
 */
function get_product_single($sku){
    require(ROOT_PATH . "inc/database.php");

    try{
        $results = $db -> prepare("SELECT name, price, img, sku, paypal FROM products WHERE sku = ?");
        $results -> bindParam(1, $sku);
        $results -> execute();
    }catch(Exception $e){
        echo "Data coould not be retrieved from the database.";
        exit;
    }

    $product = $results -> fetch(PDO::FETCH_ASSOC);


    if($product === false)
        return $product;
    
    $product["sizes"] = array();


    try{
        $results = $db -> prepare("
            SELECT size 
            FROM products_sizes ps 
            INNER JOIN sizes s ON ps.size_id = s.id
            WHERE product_sku = ?
            ORDER BY `order`");
        $results -> bindParam(1, $sku);
        $results -> execute();
    }catch(Exception $e){
        echo "Data could not be retrieved from the database.";
        exit;
    }

    while($row = $results -> fetch(PDO::FETCH_ASSOC)){
        $product["sizes"][] = $row["size"];
    }

    return $product;
    

}







?>