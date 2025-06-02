<?php

    session_start();
    require_once '../../Backend/env.php';
    require '../../Backend/Validation.php';
    require '../../Backend/ProductBackend.php';
    require '../../Backend/File.php';
    
    $product = null;
    if(isset($_GET['productID'])){
        
        $productID = $_GET['productID'];
        $product = getProductByID($productID);
        $productCategory = getProductCategories($productID);
        
    }
    
    if($product == null){
        header("Location: Product.php?CategoryID=All");
    }
    
    if (isset($_POST['Update'])){
        
        if(isset($_FILES['productImgInput']) && $_FILES['productImgInput']['name'] != null){
            deleteFile($product['ImgPath']);
            $newImgPath = uploadedImg("../Img/Product/", $_FILES['productImgInput']);
            updateProductImgPath($productID, $newImgPath);
        }
        
        $productName = (isset($_POST['productName']))?$_POST['productName']:"";
        $productDetails = (isset($_POST['productDetails']))?$_POST['productDetails']:"";
        $productCategory = (isset($_POST['productCategory']))?$_POST['productCategory']:"";
        $productSellingPrice = (isset($_POST['productPrice']))?$_POST['productPrice']:0.01;
        $productStock = (isset($_POST['productStock']))?$_POST['productStock']:0;
        
        updateProduct($productID, $productName, $productDetails, $productSellingPrice, $productStock);
        removeProductCategory($productID);
        addProductCategory($productID, $productCategory);
        
        $product = getProductByID($productID);
        $productCategory = getProductCategories($productID);
        
    }else if(isset($_POST['Delete'])){
        
        $deleted = deleteProduct($productID);
        if($deleted){
            removeDeletedProduct($productID);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../Img/Logo/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="../Style/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../Style/css/style.css">
    <link rel="stylesheet" href="../Style/css/Admin/product.css">
    <link rel="stylesheet" href="../Style/css/Admin/productDetails.css">
    <script src="../Style/js/js.js"></script>
    <title>Product</title>
</head>
<body class="bg-light adminBody">
    
    <?php include "SideMenu.php"; ?>
    <?php include "Header.php"; ?>
    
     <div class="page bg-light d-flex align-items-center">
         
         <div class="bg-white rounded shadow-sm p-3 container-lg <?php echo ($product == null)?"d-none":""?>">
            <h2 class="text-center shadow-sm p-2 rounded bg-light mb-3">Product Details</h2>
            <form class="row" method="post" enctype="multipart/form-data">
                 <div class="col-12 col-md-5">
                    <p class="text-center mb-2"><b>Product Image</b></p>
                    <input type="file" name="productImgInput" id="productImgInput" accept="image/*" onchange="previewPhoto(event,'productImage')" style="display: none;">
                    <img class="w-100 d-flex mx-auto cursor-pointer shadow-sm border" id="productImage"
                         src="<?php echo isset($product['ImgPath'])?$product['ImgPath']:"../Img/Product/blank.jpg"; ?>" 
                         alt="Product Image" 
                         onclick="triggerFileInput('productImgInput')">
                 </div>
                 <div class="col-12 col-md-7 d-flex flex-column justify-content-between">
                    <div>
                        <p><b>Product Name</b></p>
                        <input class="w-100" type="text" name="productName" value="<?php echo (isset($product['Name'])?$product['Name']:"")?>" required>
                        <small class="text-gray w-100"><i>This field is for the product showing name.</i></small><br>
                        <small><i class="text-danger"><?php echo isset($error['name'])?$error['name']:""?></i></small>

                        <p class="mt-4"><b>Product Details</b></p>
                        <textarea class="border w-100 mt-2" name="productDetails" required><?php echo isset($product['Desc']) ? htmlspecialchars($product['Desc']) : ''; ?></textarea>
                        <small class="text-gray w-100"><i>This field is for the describing the details of the product.</i></small><br>
                        <small><i class="text-danger"><?php echo isset($error['details'])?$error['details']:""?></i></small>

                        <p class="mt-4"><b>Product Category</b></p>
                        <div class="d-flex flex-wrap align-items-center justify-content-between border rounded p-2 mt-2">
                            <?php
                                foreach(getCategory() as $category){
                                    printf('<label class="d-flex align-items-center"><input type="checkbox" name="productCategory[]" value="%d" class="ui-checkbox"',$category->ID);
                                    if(isset($productCategory)){
                                        foreach($productCategory as $prodCategory){
                                            printf('%s', ($category->ID == $prodCategory->ID)?"checked":"");
                                        }
                                    }
                                    printf('> %s</label>',$category->Text);
                                }
                            ?>
                        </div>
                        <small class="text-gray w-100"><i>Please select the related categories for the product.</i></small><br>
                        <small><i class="text-danger"><?php echo isset($error['category'])?$error['category']:""?></i></small>

                        <p class="mt-4"><b>Product Selling Price (RM)</b></p>
                        <input class="w-100" type="text" name="productPrice" min="0" value="<?php echo (isset($product['Price'])?$product['Price']:"")?>" required>
                        <small class="text-gray w-100"><i>This field will be the selling price for the product.</i></small><br>
                        <small><i class="text-danger"><?php echo isset($error['price'])?$error['price']:""?></i></small>

                        <p class="mt-4"><b>Stock Quantity</b></p>
                        <input class="w-100" type="text" name="productStock" min="0" value="<?php echo (isset($product['Quantity'])?$product['Quantity']:"")?>" required> 
                        <small class="text-gray w-100"><i>This quantity in the stock.</i></small><br>
                        <small><i class="text-danger"><?php echo isset($error['stock'])?$error['stock']:""?></i></small>
                    </div>

                    <div class="mt-4">
                        <input class="btn btn-sm btn-primary w-100" type="submit" name="Update" value="Update Product">
                        <a href="Product.php?CategoryID=All" class="btn btn-sm btn-dark w-100 mt-2">Cancel</a>
                        <input class="btn btn-sm btn-outline-danger w-100 mt-2" type="submit" name="Delete" value="Delete Product">
                    </div> 
                </div>
            </form>
         </div>
   
     </div>
</body>
</html>