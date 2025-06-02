<?php

function updateMemberToVIP($accountID, $totalSpend){
    
    if($totalSpend >= 5000.0){
        $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $sql = "UPDATE account SET Role = 'V' WHERE ID = $accountID";
        $con->query($sql);
        $con->close();
    }
}

function getAccount($accountID){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "SELECT * FROM account WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $accountID);  
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $account = $result->fetch_assoc();
    }
    $result->free();
    $con->close();
    
    return $account;
}

function getAllCustomer(){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $sql = "SELECT * FROM account WHERE Role IN ('M','V')";
    $result = $con->query($sql);
    
    $customers = [];
    while ($row = $result->fetch_object()) 
    { $customers[] = $row; }
    
    
    $result->free();
    $con->close();
    
    return $customers;
}

function getPerson($personID){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    $sql = "SELECT * FROM person WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $personID); 
    $stmt->execute();
    
    $person = ($stmt->get_result())->fetch_assoc();
    $con->close();
    
    return $person;
                    
}

function getAccountByUsername($username){
    
    $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
    $username = "%{$username}%";
    $stmt = $con->prepare("SELECT * FROM account WHERE Username LIKE ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    
    $accounts = [];
    while ($row = $result->fetch_object()) 
    { $accounts[] = $row; }

    $result->free();
    $con->close();
    
    return $accounts;
    
}

function banAccount($accountID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "UPDATE account SET Status = 0 WHERE ID = $accountID";
    $con->query($sql);
    $con->close();
}

function unbanAccount($accountID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "UPDATE account SET Status = 1 WHERE ID = $accountID";
    $con->query($sql);
    $con->close();
}