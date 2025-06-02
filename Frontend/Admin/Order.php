<?php
    session_start();
        
    if(!isset($_SESSION['LoggedUser'])){
        header("Location: ../user/Home.php");
    }
    
    require_once '../../Backend/env.php';
    require '../../Backend/OrderBackend.php';
    require '../../Backend/ProductBackend.php';
    require '../../Backend/TransactionBackend.php';
    require '../../Backend/AccountBackend.php';
    
    
    $updatedStatus = false;
    if(isset($_POST['Update'])){
        $status = $_POST['status'];
        $orderID = $_POST['orderID'];
        $updatedStatus = updateOrderStatus($orderID, $status);
    }
    
    if(isset($_GET['OrderID'])){
        $orders = getOrderByID($_GET['OrderID']);
    }else if(isset($_GET['StatusID'])){
        $statusID = $_GET['StatusID'];
        if($statusID == "All"){
            $orders = getAllOrder();
        }
        else{
            $orders = getOrderByStatusID($statusID);
        }
        $orders = array_reverse($orders);
    }else{
        $orders = getAllOrder();
        $orders = array_reverse($orders);
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
    <link rel="stylesheet" href="../Style/css/Admin/order.css">
    <script src="../Style/js/js.js"></script>
    <title>Order</title>
</head>
<body class="bg-light adminBody">

    <?php include "SideMenu.php"; ?>
    <?php include "Header.php"; ?>
    
    
    <div class="page bg-light d-flex align-items-center justify-content-center">
        <div class="bg-white rounded shadow-sm maxPageHeight p-3 container-lg">
            <h2 class="text-center shadow-sm p-2 rounded bg-light mb-3">Order</h2>
            <?php
                    if($updatedStatus){
                         printf(
                            '<div class="notification bg-success shadow-sm text-white w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                                <p><b>%s</b></p>
                                <p onclick="hideContainer(\'%s\')" style="cursor:pointer;"><b>X</b></p>
                            </div>',
                            'Successfully Updated Order Status!',
                            'notiContainer'
                        );
                    }
                ?>
            <div class="d-flex mb-2 flex-wrap">
            <?php
                printf("<a class='btn-%s btn btn-sm me-1' href='?StatusID=All'>All</a>",(isset($_GET['StatusID']))?((($_GET['StatusID'])!='All')?"secondary":"primary"):"primary");
                foreach(getAllStatus() as $status){
                    printf(
                        "<a class='btn-%s btn btn-sm me-1' href='?StatusID=%s'>%s</a>",
                        (isset($_GET['StatusID']) && $_GET['StatusID'] == $status->ID) ? "primary" : "secondary",
                        $status->ID,
                        $status->Text
                    );
                }
            ?>
            </div>
            
            <!--Search Bar-->
            <form class="searchBar container-lg bg-white shadow-sm mb-1 mx-auto rounded-pill border mt-3" action="" method="get">
               <input type="text" name="OrderID" placeholder="Order ID" value="<?php echo (isset($_GET['OrderID'])?$_GET['OrderID']:"")?>">
               <input type="submit" value="Search">
            </form>
            
            <p class="text-gray text-center my-2">
                <small><i>(<?php echo is_array($orders) ? count($orders) : 1 ?> Record<?php echo is_array($orders) && count($orders) > 1 ? "s" : "" ?> Found)</i></small>
            </p>

            
            <div id="orderMainContainer">
                <?php
                    $containerID = 0;

                    // Normalize to array
                    if (!is_array($orders)) {
                        $orders = $orders ? [$orders] : [];
                    }

                    foreach ($orders as $order) {
                        $containerID++;
                        $orderLists = getOrderList($order->ID);
                        $transaction = getTransactionByID($order->TransactionID);
                        $account = getAccount($order->AccountID ?? null); // Only needed if you expect AccountID

                        // Order card
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

                        // Details container
                        printf('<div class="bg-light border shadow-sm p-3 container-sm flex-column enlarge" style="display: %s" id="%d">
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
                                                    </div>',
                                  (isset($orderID)?($orderID == $order->ID)?"flex":"none":"none"),  $containerID, $order->Address, $transaction->CardNo);

                        // If account info is available, show it
                        if ($account) {
                            printf('<div class="d-flex rounded p-2 align-items-center mb-3 col">
                                        <img src="%s" class="smallProfileImg me-3">
                                        <div>
                                            <p>Customer: </p>
                                            <p><b>%s</b></p>
                                        </div>
                                    </div>', $account['ImgPath'], $account['Username']);
                        }

                        echo '          </div>
                                            </td>
                                        </tr>
                                        <tr><td colspan="4"><hr class="my-2"></td></tr>
                                        <tr>
                                            <th>Index</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Quantity</th>
                                        </tr>
                                        <tr><td colspan="4"><hr class="my-2"></td></tr>';

                        // Products in the order
                        $index = 0;
                        foreach ($orderLists as $orderList) {
                            $index++;
                            $product = getProductByID($orderList->ProductID);
                            printf('<tr>
                                        <td>%d.</td>
                                        <td><img src="%s" class="iconImg"></td>
                                        <td>%s</td>
                                        <td>%d</td>
                                    </tr>', $index, $product['ImgPath'], $product['Name'], $orderList->Quantity);
                        }

                        // Total and status update (if needed)
                        printf('<tr><td colspan="4"><hr class="my-2"></td></tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <th>Total</th>
                                    <td>RM %.2f</td>
                                </tr>
                                <tr><td colspan="4"><hr class="my-2"></td></tr>
                            </table>', $transaction->Amount);

                        // Status update form
                        echo '<form class="w-100 d-flex flex-wrap justify-content-between mt-3 mb-2" method="post">
                                <input type="hidden" name="orderID" value="' . $order->ID . '">
                                <div class="radio-inputs">';

                        foreach (getAllStatus() as $status) {
                            printf('<label class="radio">
                                        <input type="radio" name="status" value="%d" %s/>
                                        <span class="name px-2">%s</span>
                                    </label>',
                                $status->ID,
                                ($status->ID == $order->StatusID) ? "checked" : "",
                                $status->Text
                            );
                        }

                        echo '  </div>
                                <input class="btn btn-sm btn-outline-primary px-4" type="submit" name="Update" value="Update">
                              </form>
                            </div>';
                    }
                    ?>

            </div>
        </div>
    </div>
</body>
</html>
