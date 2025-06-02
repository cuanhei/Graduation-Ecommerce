<?php

function createTransaction($cardNo, $amount){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "INSERT INTO transaction (CardNo, Amount) VALUES (?,?)";
    
    $transactionID = null;
    
    $stmt = $con->prepare($sql); // use $conn instead of $con if that's your actual variable
    
    if ($stmt) {
        $stmt->bind_param("sd", $cardNo, $amount); 
        if ($stmt->execute()) {
            // Get the last inserted ID
            $transactionID = $con->insert_id;
        }
        $stmt->close();
    }
    
    return $transactionID;
}

function getTransactionByID($transactionID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM transaction WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $transactionID); 
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_object();
    
    $result->free();
    $con->close();
    
    return $transaction;
    
}

function getTotalAmountSpend($accountID){

    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Get all transaction IDs for the given account
    $sql = "SELECT * FROM orders WHERE AccountID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $accountID);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalSpend = 0; 
    $TransactionIDs = [];
    
    while ($row = $result->fetch_object()) {
        $TransactionIDs[] = $row->TransactionID;
    }
    $result->free();
    $stmt->close();

    // If there are no transaction IDs, avoid making an invalid query
    if (!empty($TransactionIDs)) {
        // Build placeholders for the number of IDs
        $placeholders = implode(',', array_fill(0, count($TransactionIDs), '?'));

        // Prepare the second query
        $sql = "SELECT * FROM transaction WHERE ID IN ($placeholders)";
        $stmt = $con->prepare($sql);

        // Dynamically bind parameters
        $types = str_repeat('i', count($TransactionIDs)); // all IDs are integers
        $stmt->bind_param($types, ...$TransactionIDs);

        $stmt->execute();
        $result = $stmt->get_result();

        
        while ($row = $result->fetch_object()) {
            $totalSpend += $row->Amount;
        }

        $result->free();
        $stmt->close();
    }

    $con->close();

    
    return $totalSpend;
}