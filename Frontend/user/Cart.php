<?php
    session_start();
    
    if(!isset($_SESSION['LoggedUser'])){
        header("Location: Home.php");
    }
    $total = 0.0;
    $accountID = $_SESSION['LoggedUser']['ID'];
    
    require_once '../../Backend/env.php';
    require "../../Backend/CartBackend.php";
    require "../../Backend/ProductBackend.php";

    if (!empty($_POST)) {
        
        $id = $_POST['productID'];
        $qty = isset($_POST['productQty']) ? $_POST['productQty'] : 0;

        if (isset($_POST['remove'])) {
            removeItemFromCart($accountID, $id);
        } elseif (isset($_POST['add'])) {
            addItemToCart($accountID, $id, 1);
        } elseif (isset($_POST['decrease'])) {
            removeItemQtyFromCart($accountID, $id, 1);
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }

    
    $cart = getCart($accountID);
    if (!is_array($cart)) {
        $cart = $cart ? [$cart] : [];
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
    <script src="../Style/js/User/cart.js"></script>
    <script src="../Style/js/js.js"></script>
    <title>Cart</title>
</head>
<body class="bg-light">
    
    <?php include "Header.php"; ?>
    
    <div class="shadow-sm rounded p-3 bg-white mt-4 container-lg d-flex flex-column">
        <div class="mx-auto row order-2 w-100">
            <div class="col-lg-8">
                <?php
                    foreach($cart as $cartRow){
                        
                        $updatedItemQty = false;
                        
                        $product = getProductByID($cartRow->ProductID);
                        if($cartRow->Quantity > $product['Quantity']){
                            
                            $quantity = $product['Quantity'];
                            updateItemInCart($accountID, $cartRow->ProductID, $quantity);
                            $cartRow->Quantity = $quantity;
                            $updatedItemQty = true;
                        }
                        printf('                
                            <form class="container-sm mx-auto bg-white border rounded row productContainer shadow-sm p-lg-2 mb-3 cartProduct" method="post">
                                <input type="hidden" name="productID" value="%d">
                                <div class="col-sm-5 d-flex justify-content-center align-items-center">
                                    <img class="productImg" src="%s" alt="">
                                </div>
                                <div class="col-sm-7 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="w-80">
                                                <h2>%s</h2>
                                            </div>
                                        </div>
                                        <p>%s</p>
                                        <div class="d-flex mt-2">',$product['ID'],$product['ImgPath'],$product['Name'],$product['Desc']);
                        
                        foreach(getProductCategories($product['ID']) as $category){
                            printf('<p class="badge rounded-pill text-bg-primary me-1">%s</p>',$category->Text);
                        }
                        printf('
                                        </div>
                                        <h6 class="mt-3">RM %.2f <small><i class="text-gray">(per unit)</i></small></h6>
                                        <small class="text-gray mb-2"><i>(Quantity of <b>%s</b> remaining in stock: <b class="ms-1">%d </b>units)</i></small>
                                        <small class="text-danger mb-2"><i><br>%s</i></small>
                                        <div class="d-flex align-items-center my-2">
                                            <p class="me-3">Quantity to purchase </p>
                                            <input type="submit" name="%s" %s class="btn btn-sm btn-light shadow-sm border iconImg d-flex align-items-center justify-content-center" value="-">
                                            <input type="number" min="%d" max="%d" class="border mx-2" style="width: 10vh;" value="%d" name="productQty">
                                            <input type="submit" name="add" class="btn btn-sm btn-light shadow-sm border iconImg d-flex align-items-center justify-content-center" value="+">
                                            <div class="w-100 d-flex justify-content-end">
                                                <input type="submit" name="remove" onclick="return confirmRemove()" value="Remove" class="btn btn-sm btn-outline-danger ms-3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>',$product['Price'], $product['Name'], $product['Quantity'], ($updatedItemQty)?"Note : The quantity has been adjusted due to stock balance doesn't reach the original quantity in cart.":"", ($cartRow->Quantity==1)?"remove":"decrease", ($cartRow->Quantity ==1)?' onclick="return confirmRemove()"':"",1, $product['Quantity'], $cartRow->Quantity);
                    
                        $total += $product['Price'] * $cartRow->Quantity;
                    }
                
                ?>
            </div>
            <?php if(empty($cart)){
                printf(
                    '<div class="notification bg-danger shadow-sm text-white w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                        <p><b>%s</b></p>
                        <p onclick="hideContainer(\'%s\')" style="cursor:pointer;"><b>X</b></p>
                    </div>',
                    'There is empty in your cart!',
                    'notiContainer'
                );
            }?>
            <div class="col-lg-4">
                <div class="border rounded shadow-sm p-2 mt-4 mt-sm-0 container-sm <?php echo (empty($cart))?"d-none":""?>">
                    <table class=" w-100">
                        <tr>
                            <th><p>No.</p></th>
                            <th><p>Product Name</p></th>
                            <th class="text-center"><p>Quantity</p></th>
                            <th class="text-center"><p>Amount (RM)</p></th>
                        </tr>
                        <tr>
                            <td colspan="4"><hr class="my-2"></td>
                        </tr>
                        <?php
                            $index = 0;
                            $subtotal = 0.0;
                            
                            foreach($cart as $cartRow){
                                 
                                $product = getProductByID($cartRow->ProductID);
                                $index++;
                                printf(
                                   '<tr class="text-gray">
                                        <td><p>%d</p></td>
                                        <td><p>%s</p></td>
                                        <td class="text-center"><p>%d</p></td>
                                        <td class="text-center"><p>%.2f</p></td>
                                    </tr>',$index,$product['Name'], $cartRow->Quantity, $product['Price'] * $cartRow->Quantity);
                                $subtotal += $product['Price'] * $cartRow->Quantity;
                             }
                        ?>
                        <tr>
                            <td colspan="4"><hr class="my-2"></td>
                        </tr>
                        <tr>
                            <th colspan="3"><p>Sub-Total</p></th>
                            <td class="text-center text-gray"><p><?php printf("%.2f",$subtotal)?></p></td>
                        </tr>
                        <tr>
                            <th colspan="3"><p>SST Tax (10%)</p></th>
                            <td class="text-center text-gray">
                                <p>
                                    <?php 
                                        $tax = $subtotal *0.1;
                                        printf("%.2f",$tax)
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3"><p>Shipping Fees (5%)</p></th>
                            <td class="text-center text-gray">
                                <p>
                                    <?php 
                                        $shippingFees = ($subtotal >= 1000)?0:$subtotal *0.05;
                                        printf("%.2f",$shippingFees)
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="3"><p>VIP Discount</p></th>
                            <td class="text-center text-gray">
                                <p>
                                    <?php
                                        $discount = 0;
                                        printf("- %.2f",$discount);
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><hr class="my-2"></td>
                        </tr>
                        <tr>
                            <th colspan="3"><p>Total</p></th>
                            <td class="text-center text-gray">
                                <p>
                                    <?php 
                                        $total = $subtotal + $tax + $shippingFees - $discount;
                                        printf("RM %.2f",$total)
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><hr class="my-2"></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <form action="Payment.php" method="POST">
                                    <input type="hidden" name="key" value="key"><!--Use this to confirm payment page link from here-->
                                    <input type="submit" class="btn btn-primary w-100" value="Proceed Payment">
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="d-flex flex-wrap justify-content-sm-around border rounded shadow-sm p-3 mb-4 mx-2 order-1">
            <?php 
                printf('
                    <div class="d-flex justify-content-between mb-3 mb-sm-0">
                        <div class="bigProfileImg shadow-sm border d-flex justify-content-center align-items-center" id="freeShippingPercentage">
                           <div class="midProfileImg shadow-sm bg-white border d-flex justify-content-center align-items-center">
                                <p class="text-gray"><b>%.1f%%</b></p>
                           </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center ms-2">
                            <h6 class="text-gray">Shipping Fees Offer</h6>
                            <p class="text-gray">You currently hit RM %.2f / RM 1000.00</p>
                            <p class="text-gray"><small><i>After hitting RM 1000.00 will have a free shipping fees offer.</i></small></p>
                        </div>
                    </div>
                    <script>
                        updatePercent("freeShippingPercentage",%.1f);
                    </script>',(($subtotal/1000.0)*100)>=100?100:($subtotal/1000.0)*100, $subtotal, (($subtotal/1000.0)*100)>=100?100:($subtotal/1000.0)*100);
                
                $totalSpend = $_SESSION['TotalSpend'];
                printf(
                    '<div class="d-flex justify-content-between">
                        <div class="bigProfileImg shadow-sm border d-flex justify-content-center align-items-center" id="memberPercentage">
                           <div class="midProfileImg shadow-sm bg-white border d-flex justify-content-center align-items-center">
                                <p class="text-gray"><b>%.1f%%</b></p>
                           </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center ms-2">
                            <h6 class="text-gray">VIP Offer</h6>
                            <p class="text-gray">You currently a %s role.</p>
                            <p class="text-gray"><small><i>When total spending reached RM 5000.00 will upgrade to VIP role.</i></small></p>
                        </div>
                    </div>
                    <script>
                        updatePercent("memberPercentage",%f);
                    </script>',(($totalSpend/5000.0)*100)>=100?100:($totalSpend/5000.0)*100, ($_SESSION['LoggedUser']['Role'] == 'M')?"Member":"VIP", (($totalSpend/5000.0)*100)>=100?100:($totalSpend/5000.0)*100);
            ?>
            

        </div>
    </div>
</body>
</html>