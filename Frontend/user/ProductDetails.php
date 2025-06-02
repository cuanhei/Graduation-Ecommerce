<?php
    session_start();
    
    require_once '../../Backend/env.php';
    require '../../Backend/ProductBackend.php';
    require '../../Backend/CartBackend.php';
    
    $product = null;
    $addedToCart = false;
    
    if(isset($_POST['add'])){

        $productID = $_GET['ProductID'];
        $quantity = $_POST['quantity'];
        
        $id = (int)$productID;
        $product = getProductByID($id);
        
        if($quantity!=null && $quantity > 0){
        
            addItemToCart($_SESSION['LoggedUser']['ID'], $productID, $quantity);
            $addedToCart = true;
        }
        
    }else if(isset($_GET['ProductID'])){
        
        $id = (int)$_GET['ProductID'];
        $product = getProductByID($id);
        
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Img/Logo/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../Style/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../Style/css/style.css">
    <link rel="stylesheet" href="../Style/css/User/shop.css">
    <link rel="stylesheet" href="../Style/css/User/productDetails.css">
    <title>Product Details</title>
</head>
<body class="bg-light">
   
    <?php include "Header.php" ?>
    
    <?php
        
        if(!isset($product)){
            printf(
                '<div class="notification bg-danger shadow-sm text-white w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                    <p><b>%s</b> <a href="Shop.php?CategoryID=All" class="text-white ms-2">Click Here To Back</a></p>
                </div>',
                'Product Not Found'
            );
            
        }else{
            if(!isset($_SESSION['LoggedUser'])){
                printf(
                    '<div class="notification bg-warning shadow-sm w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                        <p><b>Warning : </b><i> You are required Log In before add item(s) into cart.</i></p>
                    </div>'
                );
            }
            
            if($addedToCart){
                printf(
                    '<div class="notification bg-success text-white shadow-sm w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                        <p><b>Successfully added %d unit(s) to your cart.</b></p>
                        <p onclick="hideContainer(\'%s\')" style="cursor:pointer;"><b>X</b></p>
                    </div>',$quantity,'notiContainer'
                );
            }
            printf('<form class="container-sm mx-auto bg-white shadow-sm rounded row productContainer p-lg-2 p-3 pb-lg-4 my-lg-4 mt-4" method="post" onsubmit="enableInputBeforeSubmit(\'quantity\')>
                        <input type="hidden" name="productID" value="%d">
                        <div class="col-lg-5 d-flex justify-content-center align-items-center">
                            <img class="productImg" src="%s" alt="">
                        </div>
                        <div class="col-lg-7 d-flex flex-column justify-content-between">
                            <div>
                                <h2>%s</h2>
                                <p>%s</p>
                                <div class="d-flex mt-2">',$product['ID'], $product['ImgPath'], $product['Name'],$product['Desc']);         
            foreach(getProductCategories($product['ID']) as $category){
                printf('<p class="badge rounded-pill text-bg-primary me-1">%s</p>',$category->Text);
            }
            printf('            </div>
                            </div>
                            <div class="d-flex flex-column py-3">
                                <h6>RM %.2f <small><i class="text-gray">(per unit)</i></small></h6>
                                <small><i class="text-gray">Date Released : %s</i></small>
                            </div>

                            <hr>

                            <small class="text-gray mb-2"><i>(Quantity of <b>%s</b> remaining in stock: <b class="ms-1">%d </b>units)</i></small>
                            <div class="d-flex align-items-center">
                                <p class="me-3">Quantity to purchase </p>
                                <button type="button" class="btn btn-sm btn-light shadow-sm border iconImg d-flex align-items-center justify-content-center" onclick="decrease(\'quantity\')">-</button>
                                <input  type="number" min="%d" max="%d" name="quantity" id="quantity" class="border mx-2">
                                <button type="button" class="btn btn-sm btn-light shadow-sm border iconImg d-flex align-items-center justify-content-center" onclick="increase(\'quantity\')">+</button>
                            </div>

                            <hr class="my-3">

                            <table>
                                <tr>
                                    <td><p>Shipping fees</p></td>
                                    <td><p class="text-end"><b>5%%</b></p></td>
                                </tr>
                                <tr>
                                    <td><p>SST Tax</p></td>
                                    <td><p class="text-end"><b>10%%</b></p></td>
                                </tr>
                                <tr>
                                    <td><p>Total</p></td>
                                    <td><p class="text-end"><b>RM %.2f</b></p></td>
                                </tr>
                            </table>
                            <input type="submit" value="Add To Cart" name="add" class="btn btn-primary btn-sm my-2" %s>
                            <a class="btn btn-dark btn-sm" href="Shop.php?CategoryID=All">Back</a>
                        </div>
                    </form>',$product['Price'],$product['DateRelease'],$product['Name'],$product['Quantity'], 0, $product['Quantity'], $product['Price']*1.15, isset($_SESSION['LoggedUser'])?"":"disabled");
        }
    ?>
    <script src="../Style/js/js.js"></script>
    <script src="../Style/js/User/productDetails.js"></script>
</body>
</html>