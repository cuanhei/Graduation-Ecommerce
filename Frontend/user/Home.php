<?php
    session_start();
    
    require_once '../../Backend/env.php';
    require '../../Backend/ProductBackend.php';
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Img/Logo/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../Style/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../Style/css/style.css">
    <link rel="stylesheet" href="../Style/css/User/home.css">
    <title>Home</title>
</head>
<body class="bg-light">

    <?php include "Header.php"; ?>

    <div class="container-sm shadow-sm rounded my-4 p-0 bg-white">

        <!--Hero-->
        <div class="rounded shadow-sm w-100" >
            <div id="hero" class="rounded shadow-sm">
                <div id="heroDetailsContainer" class="p-5 text-white rounded">
                    <h2 class="mb-5">Graduation Shop</h2>
                    <p>Welcome to APLUS, where style meets convenience!</p>
                    <p>We are your premier online destination for the latest fashion trends, offering a wide range of clothing, footwear, and accessories for men, women, and children.</p> 
                    <a href="Shop.php?CategoryID=All" class="btn btn-sm rounded-pill shadow-sm mt-5 btn-dark px-4 py-1">Shop Now</a>
                </div>
            </div>
        </div>
        
        <!--New Product-->
        <div class="p-4">
            <h2 class="text-center mb-5">New Product</h2>
            <div class="row justify-content-around">
                <?php 
                    $count = 0;
                    $products = getAllProducts();
                    foreach($products as $product){
                        if($count >= 3){ break;}
                        printf('
                            <form class="col-lg-3 rounded shadow-sm border p-2" method="get" action="ProductDetails.php">
                                <input type="hidden" name="ProductID" value="%s">
                                <div class="d-flex align-items-center justify-content-center w-100">
                                    <img src="%s" class="newProductImg" alt=""/>
                                </div>
                                <div class="w-100">
                                    <h4>%s</h4>
                                    <p>%s</p>
                                    <p><b>RM %.2f</b></p>
                                    <input type="submit" class="btn btn-sm btn-dark w-100 mt-4" value="More">
                                </div>
                            </form>', $product->ID, $product->ImgPath, $product->Name, $product->Desc, $product->Price);
                        $count++;
                    }
                ?>
            </div>
        </div>
        
        <!--Website Details-->
    </div>
</body>
</html>
