<?php

function getProductByID($id){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM product WHERE ID = $id";
    $result = $con->query($sql);
    
    $product = null;
    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    }
    
    $result->free();
    $con->close();
    return $product;
}

function getAllProducts(){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM product WHERE Status = 1";
    $result = $con->query($sql);

    $products = [];
    while ($row = $result->fetch_object()) 
    { $products[] = $row; }

    $result->free();
    $con->close();
    return $products;
}

function getProductByName($productName){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $productName = "%" . $productName . "%";
    $sql = "SELECT * FROM product WHERE Name LIKE ? AND Status = 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $productName); 
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_object()) 
    { $products[] = $row; }

    $result->free();
    $con->close();
    return $products;
}

function getCategory() {

    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT * FROM category";
    $result = $con->query($sql);

    while ($row = $result->fetch_object()) 
    { $category[] = $row; }

    $result->free();
    $con->close();
    return $category;
}

function getProductsByCateID($category_ID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $categoryID = (int)$category_ID;
    $sql = "SELECT ProductID FROM productcategory WHERE CategoryID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $categoryID); 
    $stmt->execute();
    $result = $stmt->get_result();

    $productIDs = [];
    while ($row = $result->fetch_object()) { $productIDs[] = $row->ProductID;}

    $products = [];
    if($productIDs != null){
        $IDs = implode(',',$productIDs);
        $sql = "SELECT * FROM product WHERE ID IN ($IDs) AND Status = 1";
        $result = $con->query($sql);

        while ($row = $result->fetch_object()) 
        { $products[] = $row; }
    }
    
    $result->free();
    $con->close();
    return $products;
}

function getProductCategories($productID){

    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "SELECT CategoryID FROM productcategory WHERE ProductID = $productID";
    $result = $con->query($sql);

    $categoriesID = [];
    while ($row = $result->fetch_object()) 
    { $categoriesID[] = $row->CategoryID; }

    $categories = [];
    if($categoriesID != null){
        $IDs = implode(',',$categoriesID);
        $sql = "SELECT * FROM category WHERE ID IN ($IDs)";
        $result = $con->query($sql);

        while ($row = $result->fetch_object()) 
        { $categories[] = $row; }
    }

    $result->free();
    $con->close();
    return $categories;
}

function removeProductQty($productID, $quantity){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "UPDATE product SET quantity = (quantity - ?) WHERE ID = ?;";
    $stmt = $con->prepare($sql);
    $deleted = false;
    
    if ($stmt) {
        $stmt->bind_param("ii", $quantity, $productID); 
        if ($stmt->execute()) {
            $deleted = true;
        }
    }
    $con->close();
    return $deleted;
}

function deleteProduct($productID){
    
    $success = false;
    try{
        $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $sql = "UPDATE product SET Status = 0 WHERE ID = ?";
        $stmt = $con->prepare($sql);
       
        $stmt->bind_param("i",$productID); 
        $stmt->execute();

        $success = true;
        
    }catch(Exception $e){
        $success = false;
    }
    
    $stmt->close();
    return $success;
}

function createProduct($name, $desc, $quantity, $price, $imgPath){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "INSERT INTO product (Name, `Desc`, Quantity, Price, ImgPath) VALUES (?,?,?,?,?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssids", $name,$desc, $quantity, $price, $imgPath); 
    
    $productID = null;
    if ($stmt->execute()) {
        // Get the last inserted ID
        $productID = $con->insert_id;
    }
    
    $con->close();
    return $productID;
    
}

function addProductCategory($productID, $category){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "INSERT INTO productcategory (ProductID, CategoryID) VALUES (?,?)";
    $stmt = $con->prepare($sql);
    
    if(empty($category)){
        $categoryID = 4;// 4 is Other
        $stmt->bind_param("ii", $productID, $categoryID); 
        $stmt->execute();
    } 
    else{
        foreach($category as $categoryID){

            $stmt->bind_param("ii", $productID, $categoryID); 
            $stmt->execute();
        }
    }

    $con->close();
}

function updateProduct($productID, $productName, $productDetails, $productPrice, $productStock){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "UPDATE product SET Name = ?, `Desc` = ?, Quantity = ?, Price = ? WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssidi", $productName, $productDetails, $productStock, $productPrice, $productID); 
    $stmt->execute();
    $con->close();
}

function updateProductImgPath($productID, $imgPath){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "UPDATE product SET ImgPath = ? WHERE ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $imgPath, $productID); 
    $stmt->execute();
    $con->close();
}

function removeProductCategory($productID){
    
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $sql = "DELETE FROM productcategory WHERE ProductID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $productID); 
    $stmt->execute();
    $con->close();
}