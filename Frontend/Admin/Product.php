<?php

    session_start();
    
    require_once '../../Backend/env.php';
    require '../../Backend/Validation.php';
    require '../../Backend/ProductBackend.php';
    require '../../Backend/File.php';
    
    if(!isset($_SESSION['LoggedUser'])){
        header("Location: ../user/Home.php");
    }
    
    $products = [];
    $createdNewProduct = false;

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
    
    if(isset($_POST["add"])){
        
        //Create Product
        $productImg = $_FILES['productImgInput'];
        $productName = (isset($_POST['productName']))?$_POST['productName']:"";
        $productDetails = (isset($_POST['productDetails']))?$_POST['productDetails']:"";
        $productCategory = (isset($_POST['productCategory']))?$_POST['productCategory']:"";
        $productSellingPrice = (isset($_POST['productPrice']))?$_POST['productPrice']:"";
        $productStock = (isset($_POST['productStock']))?$_POST['productStock']:0;
        
        $error['img'] = validateProductImg($productImg);
        $error['name'] = validateProductName($productName);
        $error['details'] = validateProductDetails($productDetails);
        $error['category'] = validateProductCategory($productCategory);
        $error['price'] = validateProductPrice($productSellingPrice);
        $error['stock'] = validateDefaultStockQty($productStock);
        
        //remove null value
        $error = array_filter($error);
        if(empty($error)){
 
            $imgPath = uploadedImg("../Img/Product/", $productImg);
            $productID = createProduct($productName, $productDetails, $productStock, $productSellingPrice, $imgPath);
            addProductCategory($productID, $productCategory);
            header("Location: Product.php?CategoryID=All");
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="../Img/Logo/logo.png" type="image/x-icon">
        <link rel="stylesheet" href="../Style/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" href="../Style/css/style.css">
        <link rel="stylesheet" href="../Style/css/Admin/product.css">
        <script src="../Style/js/js.js"></script>
        <title>Product</title>
    </head>
    <body class="bg-light adminBody" style="overflow-y: hidden">
        <?php include "SideMenu.php"; ?>
        <?php include "Header.php"; ?>
        
        <div class="page bg-light d-flex align-items-center justify-content-center">
            <div class="bg-white rounded shadow-sm maxPageHeight p-3 container-lg">
                <?php
                    if($createdNewProduct){
                         printf(
                            '<div class="notification bg-success shadow-sm text-white w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                                <p><b>%s</b></p>
                                <p onclick="hideContainer(\'%s\')" style="cursor:pointer;"><b>X</b></p>
                            </div>',
                            'Successfully Created A New Product!',
                            'notiContainer'
                        );
                    }
                ?>
                <h2 class="text-center shadow-sm p-2 rounded bg-light mb-3">Product</h2>
                <div class="row mb-3">
                    <div class="col-12 col-sm-10 mb-2 mb-sm-0">
                         <?php
                            printf("<a class='btn-%s btn btn-sm me-1' href='?CategoryID=All'>All</a>",(isset($_GET['CategoryID']))?((($_GET['CategoryID'])!='All')?"secondary":"primary"):"primary");
                            foreach(getCategory() as $category){
                                printf(
                                    "<a class='btn-%s btn btn-sm me-1' href='?CategoryID=%s'>%s</a>",
                                    (isset($_GET['CategoryID']) && $_GET['CategoryID'] == $category->ID) ? "primary" : "secondary",
                                    $category->ID,
                                    $category->Text
                                );
                            }
                        ?>
                    </div>
                    <div class="col-12 col-sm-2">
                        <button class="btn btn-sm btn-outline-primary shadow-sm w-100" onclick="showContainer('addProductContainer')">Add</button>
                        <div class="popUpContainer align-items-center justify-content-center" id="addProductContainer" style="<?php echo (!empty($error))?"display: flex":"" ?>">
                            <div class="popUpContent bg-light p-5 pt-2 rounded">
                                <h2 class="w-100 text-center">Add Product</h2>
                                <form class="shadow bg-white border rounded p-3 row" method="POST" enctype="multipart/form-data">
                                    <div class="col-12 col-md-5 mb-4">
                                        <p class="text-center mb-2"><b>Product Image</b></p>
                                        <input type="file" name="productImgInput" id="productImgInput" accept="image/*" onchange="previewPhoto(event,'productImage')" value="<?php //echo (isset($productImg)?$productImg:"")?>" style="display: none;">
                                        <img class="w-100 d-flex mx-auto cursor-pointer shadow-sm border" id="productImage"
                                             src="../Img/Product/blank.jpg" 
                                                alt="Product Image" 
                                                onclick="triggerFileInput('productImgInput')">
                                        <small><i class="text-danger"><?php if(!empty($error)){
                                            echo "<b>Please Re-select Product Image</b>, due to server lost the selected image.";
                                        }?></i></small>
                                    </div>
                                    <div class="col-12 col-md-7 d-flex flex-column justify-content-between">
                                        <div>
                                            <p><b>Product Name</b></p>
                                            <input class="w-100" type="text" name="productName" value="<?php echo (isset($productName)?$productName:"")?>" required>
                                            <small class="text-gray w-100"><i>This field is for the product showing name.</i></small><br>
                                            <small><i class="text-danger"><?php echo isset($error['name'])?$error['name']:""?></i></small>
                                            
                                            <p class="mt-4"><b>Product Details</b></p>
                                            <textarea class="border w-100 mt-2" name="productDetails" required><?php echo isset($productDetails) ? htmlspecialchars($productDetails) : ''; ?></textarea>
                                            <small class="text-gray w-100"><i>This field is for the describing the details of the product.</i></small><br>
                                            <small><i class="text-danger"><?php echo isset($error['details'])?$error['details']:""?></i></small>
                                            
                                            <p class="mt-4"><b>Product Category</b></p>
                                            <div class="d-flex flex-wrap align-items-center justify-content-between border rounded p-2 mt-2">
                                                <?php
                                                    foreach(getCategory() as $category){
                                                        printf('<label class="d-flex align-items-center"><input type="checkbox" name="productCategory[]" value="%d" class="ui-checkbox"',$category->ID);
                                                        if( isset($productCategory)){
                                                            if($productCategory != ""){
                                                                printf('%s', (in_array($category->ID, $productCategory)?"checked":""));
                                                            } 
                                                        }
                                                        printf('> %s</label>',$category->Text);
                                                    }
                                                ?>
                                            </div>
                                            <small class="text-gray w-100"><i>Please select the related categories for the product.</i></small><br>
                                            <small><i class="text-danger"><?php echo isset($error['category'])?$error['category']:""?></i></small>
                                            
                                            <p class="mt-4"><b>Product Selling Price</b></p>
                                            <input class="w-100" type="text" name="productPrice" min="0" value="<?php echo (isset($productSellingPrice)?$productSellingPrice:"")?>" required>
                                            <small class="text-gray w-100"><i>This field will be the selling price for the product.</i></small><br>
                                            <small><i class="text-danger"><?php echo isset($error['price'])?$error['price']:""?></i></small>
                                            
                                            <p class="mt-4"><b>Default Stock Quantity</b></p>
                                            <input class="w-100" type="text" name="productStock" min="0" value="<?php echo (isset($productStock)?$productStock:"")?>" required> 
                                            <small class="text-gray w-100"><i>This quantity in the stock.</i></small><br>
                                            <small><i class="text-danger"><?php echo isset($error['stock'])?$error['stock']:""?></i></small>
  
                                        </div>
                                        
                                        <div class="mt-4">
                                            <input  class="btn btn-sm btn-primary w-100" type="submit" name="add" value="Add Product">
                                            <a href="Product.php?CategoryID=All" class="btn btn-sm btn-dark w-100 mt-2">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Search Bar-->
                <form class="searchBar container-lg bg-white shadow-sm mb-1 mx-auto rounded-pill border" action="" method="get">
                    <input type="text" name="Name" placeholder="Name" value="<?php echo (isset($_GET['Name'])?$_GET['Name']:"")?>">
                    <input type="submit" value="Search">
                </form>

                <p class="text-gray text-center my-2"><small><i>(<?php echo count($products)?> Record<?php echo (count($products)>1)?"s":""; ?> Found)</i></small></p>

                <div id="productMainContainer">
                    
                    <?php 
                        foreach($products as $product){
                            
                            printf('<div class="row p-2 shadow-sm rounded border mx-1 mb-2" method="GET" action="ProductDetails.php">
                                        <div class="col-sm-3 d-flex py-1">
                                            <div class="d-flex align-items-center ">
                                                <img src="%s" alt="" class="iconImg">
                                            </div>

                                            <div class="d-flex flex-column justify-content-center ms-3 text-start text-gray">
                                                <h6 class="m-0">%s</h6>
                                                <p><small>%s</small></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-center flex-wrap py-1">', $product->ImgPath, $product->Name, $product->DateRelease);

                            foreach(getProductCategories($product->ID) as $category){
                                printf('<p class="badge rounded-pill text-bg-primary me-1">%s</p>',$category->Text);
                            }
                            printf('    </div>

                                        <div class="col-md-2 d-flex align-items-center py-1">
                                            <p class="m-0 ms-1"><b>RM %.2f</b></p>
                                            <p class="ms-1 d-md-none d-lg-block text-gray"><small><i>(per unit)</i></small></p>
                                        </div>

                                        <div class="col-md-2 d-flex align-items-center py-1">
                                            <p class="m-0 ms-1"><b>%d</b></p>
                                            <p class="ms-1 d-md-none d-lg-block text-gray"><small><i>(unit in stock)</i></small></p>
                                        </div>

                                        <div class="col-md-1 d-flex align-items-center justify-content-end py-1">
                                            <a href="ProductDetails.php?productID=%d" class="btn btn-sm btn-outline-primary w-100 my-1 my-sm-0">More</a>
                                        </div>

                                    </div>',$product->Price, $product->Quantity, $product->ID);
                        }
                    ?>
                </div>
            </div>
        </div>
        <script src="../Style/js/js.js"></script>
    </body>
</html>
