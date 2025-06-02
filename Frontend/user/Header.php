<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="../Style/css/User/header.css">
<!--Header-->
<header class="w-100 shadow-sm py-2 bg-white">
    <div class="container-sm d-flex justify-content-sm-between justify-content-center align-items-center">
        <!--Logo-->
        <img src="../Img/Logo/logo.png" id="logoImg">

        <!--Menus-->
        <div class="d-flex justify-content-between align-items-center bg-white" id="headerMenuContainer">
            <a href="Home.php"><img src="../Img/Icon/home.png" alt="" class="iconImg ms-sm-3 <?php echo ($currentPage == 'Home.php' ? '' : 'opacity-50'); ?>"></a>
            <a href="Shop.php?CategoryID=All"><img src="../Img/Icon/shop.png" alt="" class="iconImg ms-sm-3 <?php echo ($currentPage == 'Shop.php' ? '' : 'opacity-50'); ?>"></a>
            
            <?php
            if(isset($_SESSION['LoggedUser'])) {
                echo "<a href='Cart.php'><img src='../Img/Icon/cart.png' class='iconImg ms-sm-3 " . ($currentPage == 'Cart.php' ? '' : 'opacity-50') . "'></a>"
                   . "<a href='OrderHistory.php'><img src='../Img/Icon/orderHistory.png' class='iconImg ms-sm-3 " . ($currentPage == 'OrderHistory.php' ? '' : 'opacity-50') . "'></a>";
                printf("<a href='Profile.php'><img src='%s' class='iconImg ms-sm-3 rounded-pill'></a>", (isset($_SESSION['LoggedUser']['ImgPath']) ? $_SESSION['LoggedUser']['ImgPath'] : "../Img/Profile/NoProfile.png"));
            } else {
                echo "<a href='SignUp.php'><img src='../Img/Icon/guest.png' class='iconImg ms-sm-3 ";
                echo ($currentPage == 'SignUp.php' || $currentPage == 'SignIn.php' ? '' : 'opacity-50');
                echo "'></a>";
            }
            ?>
        </div>
    </div>
</header>

