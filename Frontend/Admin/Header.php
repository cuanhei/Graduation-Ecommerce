<link rel="stylesheet" href="../Style/css/Admin/header.css">
<!--Header-->
<header class="w-100 shadow-sm d-flex align-items-center bg-white">
    <div class="container-xl d-flex justify-content-between align-items-center">
        <!--Logo-->
        <img src="../Img/Logo/logo.png" id="logoImg">

        <div class="d-flex justify-content-between align-items-center" id="headerMenuContainer">
            <?php
                printf("<a href='Profile.php'><img src='%s' class='smallProfileImg shadow-sm me-2'></a>",
                        (isset($_SESSION['LoggedUser']['ImgPath'])?$_SESSION['LoggedUser']['ImgPath']:"../Img/Profile/NoProfile.png"));
                printf('<div class="d-flex align-items-start flex-column">
                            <p class="text-gray"><b>%s</b></p>
                            <p class="text-gray"><small><i>%s</i></small></p>
                        </div>',
                        $_SESSION['LoggedUser']['Username'],
                        (isset($_SESSION['LoggedUser']['Person']['Email'])?$_SESSION['LoggedUser']['Person']['Email']:"Email"));
            ?>
        </div>
    </div>
</header>
