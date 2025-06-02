<?php
    session_start();
    require_once '../../Backend/env.php';
    require '../../Backend/ProductBackend.php';

    $products = [];

    //Category Filter
    if(isset($_GET['CategoryID'])){
        
        $categoryID = $_GET['CategoryID'];
        
        if($categoryID != "All"){
           $products = getProductsByCateID($categoryID);
        }
        else{ //Search with All
            $products = getAllProducts();
        }
    } 
    //Search With Name
    else if(isset($_GET['Name'])){
        $products = getProductByName($_GET['Name']);
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
    <title>Shop</title>
</head>
<body class="bg-light">
    
    <?php include "Header.php"; ?>
    
    <div class="container-sm bg-white shadow-sm rounded my-4 p-4">
    <!--Search Bar-->
     <form class="searchBar container-sm bg-white shadow-sm mt-3 mb-1 mx-auto rounded-pill border" action="" method="get">
         <input type="text" name="Name" placeholder="Name" value="<?php echo (isset($_GET['Name'])?$_GET['Name']:"")?>">
        <input type="submit" value="Search">
    </form>

    <div class="d-flex container-sm p-sm-0 py-2 ps-1 py-sm-3">
        <?php
            printf("<a class='btn-%s btn btn-sm me-1' href='?CategoryID=All'>All</a>",(isset($_GET['CategoryID']))?((($_GET['CategoryID'])!='All')?"secondary":"primary"):"primary");
            if(getCategory()!=null || !empty(getCategory())){
                foreach(getCategory() as $category){
                    printf(
                        "<a class='btn-%s btn btn-sm me-1' href='?CategoryID=%s'>%s</a>",
                        (isset($_GET['CategoryID']) && $_GET['CategoryID'] == $category->ID) ? "primary" : "secondary",
                        $category->ID,
                        $category->Text
                    );
                }
            }
        ?>
    </div>
        <?php 
            if($products == null || empty($products)){
                printf("<div class='bg-danger text-white p-2 rounded shadow-sm'>"
                        . "<p><b>No products were found %s. Please check back later or explore other categories for more options.</b></p>"
                   . "</div>",isset($_GET['Name'])?"with the Product Name like \"".$_GET['Name']."\"":"in this category");
            }
            foreach($products as $product){
                printf('
                    <div class="container-sm mx-auto bg-white shadow-sm rounded row productContainer p-lg-2 mb-4 border">
                        <div class="col-lg-5 d-flex justify-content-center align-items-center">
                            <img class="productImg" src="%s" alt="">
                        </div>
                        <div class="col-lg-7 d-flex flex-column justify-content-between">
                            <div>
                                <h2>%s</h2>
                                <p>%s</p>
                                <div class="d-flex mt-2">',$product->ImgPath, $product->Name,$product->Desc);
                
                foreach(getProductCategories($product->ID) as $category){
                    printf('<p class="badge rounded-pill text-bg-primary me-1">%s</p>',$category->Text);
                }
                
                printf('        
                            </div>
                        </div>
                        <div class="justify-content-between d-flex">
                            <div class="d-flex flex-column py-3">
                                <h6>RM %.2f</h6>
                                <small><i class="text-gray">Date Released : %s</i></small>
                            </div>
                            <div class="d-flex justify-content-center align-items-center">
                                %s
                            </div>
                        </div>
                    </div>
                    </div>',$product->Price, $product->DateRelease, ($product->Quantity >0)?'<a class="btn btn-sm btn-outline-primary" href="ProductDetails.php?ProductID='.$product->ID.'">More</a>':'<p class="btn btn-sm btn-secondary text-white opacity-50">Out Of Stock</p>');
            }
        ?>

    </div>

</body>
</html>