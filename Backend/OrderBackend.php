<?php

function getAllOrder(){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM orders";
    $result = $con->query($sql);
    
    $orders = [];
    while ($row = $result->fetch_object()) 
    { $orders[] = $row; }
    
    $result->free();
    $con->close();
    return $orders;
}

function createOrder($address, $accountID, $transactionID, $cart){
    
    $createdOrder = false;
    try{
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $sql = "INSERT INTO orders (Address, StatusID, AccountID, TransactionID) VALUES (?, 1, ?, ?)"; //1 is packing

        $stmt = $conn->prepare($sql); // use $conn instead of $con if that's your actual variable

        if ($stmt) {
            $stmt->bind_param("sii", $address, $accountID, $transactionID); 
            if ($stmt->execute()) {
                // Get the last inserted ID
                $orderID = $conn->insert_id;
            }
            $stmt->close();
        }

        if(isset($orderID)){
            $createdOrder = addOrderList($orderID, $cart);
        }
    }catch(Exception $e){
        $createdOrder = false;
    }
    
    return $createdOrder;
}

function addOrderList($orderID, $cart){
 
    $success = false;
    try{
        $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $sql = "INSERT INTO orderList VALUES (?, ?, ?)";
        $stmt = $con->prepare($sql);
        foreach( $cart as $cartRow){

            $stmt->bind_param("iii", $orderID, $cartRow->ProductID, $cartRow->Quantity); 
            $stmt->execute();
        }
        $success = true;
        
    }catch(Exception $e){
        $success = false;
    }
    
    $stmt->close();
    return $success;
}

function getOrderByAccountID($accountID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM orders WHERE AccountID = $accountID";
    $result = $con->query($sql);
    
    $orders = [];
    while ($row = $result->fetch_object()) 
    { $orders[] = $row; }
    
    $result->free();
    $con->close();
    return $orders;
}

function getOrderList($orderID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM orderlist WHERE OrderID = $orderID";
    $result = $con->query($sql);
    
    $orderlist = [];
    while ($row = $result->fetch_object()) 
    { $orderlist[] = $row; }
    
    $result->free();
    $con->close();
    return $orderlist;
}

function getAllStatus(){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM status";
    $result = $con->query($sql);
    
    $status = [];
    while ($row = $result->fetch_object()) 
    { $status[] = $row; }
    
    $result->free();
    $con->close();
    return $status;
}

function getOrderByID($orderID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM orders WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();

    $order = null;
    if ($result->num_rows === 1) {
        $order = $result->fetch_object();
    }
    
    $result->free();
    $con->close();
    return $order;
}

function getOrderByStatusID($statusID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM orders WHERE StatusID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $statusID);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_object()) 
    { $orders[] = $row; }
    
    $result->free();
    $con->close();
    return $orders;
}

function updateOrderStatus($orderID, $statusID){
    
    $success = false;
    try{
        $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $sql = "UPDATE orders SET StatusID = ? WHERE ID = ?";
        $stmt = $con->prepare($sql);
       
        $stmt->bind_param("ii",$statusID , $orderID); 
        $stmt->execute();

        $success = true;
        
    }catch(Exception $e){
        $success = false;
    }
    
    $stmt->close();
    return $success;
}