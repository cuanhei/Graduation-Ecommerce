<?php
    session_start();
        
    if(!isset($_SESSION['LoggedUser'])){
        header("Location: Home.php");
    }
    
    require_once '../../Backend/env.php';
    require '../../Backend/OrderBackend.php';
    require '../../Backend/ProductBackend.php';
    require '../../Backend/TransactionBackend.php';
    
    if(isset($_GET['OrderID']) && $_GET['OrderID'] != ""){
        $orders = getOrderByID($_GET['OrderID']);
    }else{
        $orders = array_reverse(getOrderByAccountID($_SESSION['LoggedUser']['ID']));
    }
    
    if (!is_array($orders)) {
        $orders = $orders ? [$orders] : [];
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
    <script src="../Style/js/js.js"></script>
    <title>Order History</title>
</head>
<body class="bg-light">

    <?php include "Header.php"; ?>
    
    
    <div class="container-sm bg-white shadow-sm rounded my-4 p-4">
        
        <!--Search Bar-->
        <form class="searchBar container-sm bg-white shadow-sm mt-3 mb-5 mx-auto rounded-pill border" action="" method="get">
           <input type="text" name="OrderID" placeholder="Order ID" value="<?php echo (isset($_GET['OrderID'])?$_GET['OrderID']:"")?>">
           <input type="submit" value="Search">
        </form>
    <?php
        $containerID = 0;
        foreach($orders as $order){

            $orderLists = getOrderList($order->ID);
            $transaction = getTransactionByID($order->TransactionID);
            $containerID++;
            
            printf('<div class="rounded shadow-sm border p-3 container-sm mt-3 d-flex cursor-pointer" onclick="toggleContainer(\'%d\')">
                        <img src="../Img/Icon/order.png" class="iconImg me-3">
                        <div class="row w-100">
                            <div class="col d-flex align-items-center">
                                <p><i class="text-gray">Order ID :</i><b> %06d</b></p>
                            </div>
                            <div class="col d-flex align-items-center justify-content-end">
                                <p><i class="text-gray">Date :</i><b> %s</b></p>
                            </div>
                        </div>
                    </div>', $containerID, $order->ID, $order->Date);
            
            printf('<div class="bg-light border shadow-sm p-3 enlarge" id="%d" style="display:none">
                        <table class="w-100">
                            <tr>
                                <td colspan="4">
                                    <div class="row">
                                        <div class="d-flex rounded p-2 align-items-center mb-3 col">
                                            <img src="../Img/Icon/delivery.png" class="iconImg me-3">
                                            <div>
                                                <p>Delivery Address: </p>
                                                <p><b>%s</b></p>
                                            </div>
                                        </div>
                                        <div class="d-flex rounded p-2 align-items-center mb-3 col">
                                            <img src="../Img/Icon/card.png" class="iconImg me-3">
                                            <div>
                                                <p>Card Number: </p>
                                                <p><b>%s</b></p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                             <tr>
                                <td colspan="4"><hr class="my-2"></td>
                            </tr>
                            <tr>
                                <th>Index</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Quantity</th>
                            </tr>
                            <tr>
                                <td colspan="4"><hr class="my-2"></td>
                            </tr>
                            ',$containerID, $order->Address, $transaction->CardNo);

            $index = 0;
            foreach($orderLists as $orderList){
                $index++;
                $product = getProductByID($orderList->ProductID);
                printf('<tr>
                            <td>%d.</td>
                            <td> <img src="%s" class="iconImg"></td>
                            <td>%s</td>
                            <td>%d</td>
                        </tr>',$index, $product['ImgPath'], $product['Name'], $orderList->Quantity);
            }
            
            printf(' 
                            <tr>
                                <td colspan="4"><hr class="my-2"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <th>Total</th>
                                <td>RM %.2f</td>
                            </tr>
                            <tr>
                                <td colspan="4"><hr class="my-2"></td>
                            </tr>
                            <tr>
                            <td colspan="4">
                        
                    ',$transaction->Amount);
            printf('<div class"d-flex">');
            foreach (getAllStatus() as $status) {
                printf('<p class="btn btn-sm me-1 btn-%s">%s</p>',
                    ($status->ID == $order->StatusID) ? "primary" : "secondary",
                    $status->Text
                );
            }
            printf('</div>'
                    . '</td>'
                    . '</tr>'
                    . '</table>'
                    . '</div>');
        }
    ?>

    </div>
</body>
</html>
