<?php

    session_start();
    
    require_once '../../Backend/env.php';
    require '../../Backend/Validation.php';
    require '../../Backend/AccountBackend.php';
    require '../../Backend/File.php';
    require '../../Backend/TransactionBackend.php';
    
    if(!isset($_SESSION['LoggedUser'])){
        header("Location: ../user/Home.php");
    }
    
    $customers = [];
    $customers = getAllCustomer();
    
    if(isset($_GET['username'])){
        $customers = getAccountByUsername($_GET['username']);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="../Img/Logo/logo.png" type="image/x-icon">
        <link rel="stylesheet" href="../Style/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" href="../Style/css/style.css">
        <link rel="stylesheet" href="../Style/css/Admin/customer.css">
        <script src="../Style/js/js.js"></script>
        <title>Product</title>
    </head>
    <body class="bg-light adminBody" style="overflow-y: hidden">
        <?php include "SideMenu.php"; ?>
        <?php include "Header.php"; ?>
        
        <div class="page bg-light d-flex align-items-center justify-content-center">
            <div class="bg-white rounded shadow-sm maxPageHeight p-3 container-lg">
                
                <h2 class="text-center shadow-sm p-2 rounded bg-light mb-3">Customer</h2>
                
                <!--Search Bar-->
                <form class="searchBar container-lg bg-white shadow-sm mb-1 mx-auto rounded-pill border" action="" method="get">
                    <input type="text" name="username" placeholder="Username" value="<?php echo (isset($_GET['username'])?$_GET['username']:"")?>">
                    <input type="submit" value="Search">
                </form>

                <p class="text-gray text-center my-2"><small><i>(<?php echo count($customers)?> Record<?php echo (count($customers)>1)?"s":""; ?> Found)</i></small></p>

                <div id="customerMainContainer">
                    
                    <?php 
                        foreach($customers as $customer){
                            
                            printf('<div class="row p-2 shadow-sm rounded border mx-1 mb-2" method="GET" action="ProductDetails.php">
                                        <div class="col-md-4 d-flex py-1">
                                            <div class="d-flex align-items-center ">
                                                <img src="%s" alt="" class="smallProfileImg shadow-sm me-2">
                                            </div>

                                            <div class="d-flex flex-column justify-content-center ms-3 text-start text-gray">
                                                <h6 class="m-0">%s</h6>
                                                <p><small>%s</small></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-center justify-content-between flex-wrap py-1">
                                            <div class="tag rounded-pill %s"><p>%s</p></div>
                                            <div class="tag rounded-pill bg-danger"><p>%s</p></div>
                                            <p><b>RM %.2f</b></p>
                                        </div>
                                        <div class="col-md-3 d-flex justify-content-end align-items-center py-1">
                                            
                                        </div>
                                        <div class="col-md-1 d-flex justify-content-end align-items-center py-1">
                                            <a href="CustomerDetails.php?CustomerID=%d" class="btn btn-sm btn-outline-primary w-100">More</a>
                                        </div>
                                    </div>
                                        ', ($customer->ImgPath == null)?"../Img/Profile/NoProfile.png":$customer->ImgPath, $customer->Username, $customer->DateReg
                                         , ($customer->Role == 'M')?"bg-primary":"bg-warning", ($customer->Role == 'M')?"Member":"VIP", ($customer->Status == 0)?"Banned":"",getTotalAmountSpend($customer->ID), $customer->ID);
                        }
                    ?>
                </div>
            </div>
        </div>
        <script src="../Style/js/js.js"></script>
    </body>
</html>
