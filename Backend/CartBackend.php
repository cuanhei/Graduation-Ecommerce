<?php

//function getCart(){
//    
//    $cart = [];
//    
//    if (isset($_COOKIE['cart'])) {
//        $cart = json_decode($_COOKIE['cart'], true);
//    }
//    
//    return $cart;
//}
//
//function clearCart(){
//    
//    setcookie("cart", json_encode($cart), time() + 0, "/");
//}
//
//function addItemToCart($productID, $quantity){
//    
//    $cart = [];
//    
//    if (isset($_COOKIE['cart'])) {
//        $cart = json_decode($_COOKIE['cart'], true);
//    }
//    
//    if (isset($cart[$productID])) {
//        $cart[$productID] += $quantity; // Increase quantity
//    } else {
//        $cart[$productID] = $quantity;  // Add new product
//    }
//    
//    setcookie("cart", json_encode($cart), time() + 86400, "/");
//    
//    return $cart;
//}
//
//function removeItemFromCart($productID){
//    
//    if (isset($_COOKIE['cart'])) {
//        $cart = json_decode($_COOKIE['cart'], true);
//
//        // Remove product ID 
//        unset($cart[$productID]);
//
//        // Save the updated cart
//        setcookie("cart", json_encode($cart), time() + 86400, "/");
//    }
//}
//
//function removeItemQtyFromCart($productID, $quantity){
//    
//    $cart = [];
//    
//    if (isset($_COOKIE['cart'])) {
//        $cart = json_decode($_COOKIE['cart'], true);
//    }
//    
//    if (isset($cart[$productID])) {
//        $cart[$productID] -= $quantity; // Remove quantity
//    }
//    
//    if($cart[$productID] <= 0){
//        removeItemFromCart($productID);
//    }
//    
//    setcookie("cart", json_encode($cart), time() + 86400, "/");
//    
//}
//
//function updateItemInCart($productID, $quantity){
//    
//    $cart = [];
//    
//    if (isset($_COOKIE['cart'])) {
//        $cart = json_decode($_COOKIE['cart'], true);
//    }
//    
//    if (isset($cart[$productID])) {
//        $cart[$productID] = $quantity; // Update quantity
//    }
//    
//    if($cart[$productID] <= 0){
//        removeItemFromCart($productID);
//    }
//    
//    setcookie("cart", json_encode($cart), time() + 86400, "/");
//    
//}

function getCart($accountID){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "SELECT * FROM cart WHERE AccountID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $accountID);  
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart = [];
    while ($row = $result->fetch_object()) 
    { $cart[] = $row; }

    $result->free();
    $con->close();
    
    return $cart;
}

function addItemToCart($accountID, $productID, $quantity){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "SELECT * FROM cart WHERE AccountID = ? AND ProductID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $accountID, $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        
        $sql = "UPDATE cart SET quantity = quantity + ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $quantity);
        $stmt->execute();
    }else {
        
        $sql = "INSERT INTO cart VALUES (?,?,?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("iii",$accountID, $productID, $quantity);
        $stmt->execute();
    }
}

function removeItemQtyFromCart($accountID, $productID, $quantity){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "SELECT * FROM cart WHERE AccountID = ? AND ProductID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $accountID, $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_object();
    
    if($product->Quantity > $quantity){
        
         $sql = "UPDATE cart SET quantity = quantity - ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $quantity);
        $stmt->execute();
    }else{
        removeItemFromCart($accountID, $productID);
    }
}

function updateItemInCart($accountID, $productID, $quantity){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "UPDATE cart SET quantity = ? WHERE AccountID = ? AND ProductID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iii", $quantity, $accountID, $productID);
    $stmt->execute();
}

function clearCart($accountID){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "DELETE FROM cart WHERE AccountID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $accountID);
    $stmt->execute();
}

function removeItemFromCart($accountID, $productID){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "DELETE FROM cart WHERE AccountID = ? AND ProductID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $accountID, $productID);
    $stmt->execute();
}

//Remove the all product from cart when the product status is 0
function removeDeletedProduct($productID){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "DELETE FROM cart WHERE ProductID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
}