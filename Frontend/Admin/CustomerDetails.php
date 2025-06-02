<?php
    session_start();
    
    if(!isset($_SESSION['LoggedUser'])){ header('Location: ../user/Home.php');}
    if($_SESSION['LoggedUser']['Role'] != "A"){ header('Location: ../user/Home.php');}
    
    require_once '../../Backend/env.php';
    require '../../Backend/File.php';
    require '../../Backend/AccountBackend.php';
    require '../../Backend/OrderBackend.php';
    
    $updated = false;
    
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        
        $customerID = $_POST['customerID'];
       if(isset($_POST['Update'])){
            
            require_once "../../Backend/Validation.php";
            
            $personID = $_POST['personID'];
            $defaultUsername = $_POST['defaultUsername'];
            $username = isset($_POST['username'])?$_POST['username']:"";
            $password = isset($_POST['password'])?$_POST['password']:"";
            $name = isset($_POST['name'])?$_POST['name']:"";
            $email = isset($_POST['email'])?$_POST['email']:"";
            $ic = isset($_POST['ic'])?$_POST['ic']:"";
            
            //If remain username then no need check for duplicate
            if($username != $defaultUsername){
                $error['username'] = validateUsername($username);
            }
            //$error['password'] = validatePassword($password);
            $error['name'] = validateName($name);
            $error['email'] = validateEmail($email);
            $error['ic'] = validateIC($ic);
            
           
            //remove null value
            $error = array_filter($error);
            if(empty($error)){
                
                $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
                $sql = "UPDATE PERSON SET Name = ?, Email = ?, IC = ? WHERE ID = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("sssi", $name, $email, $ic, $personID);
                $stmt->execute();

                if(isset($_FILES['profilePicInput']) && $_FILES['profilePicInput']['name'] != null){
                    if(isset($_SESSION['LoggedUser']['ImgPath'])){
                        deleteFile($_SESSION['LoggedUser']['ImgPath']);
                    }
                    $imgPath = uploadedImg("../Img/Profile/",$_FILES['profilePicInput']);
                    if ($imgPath) {
                        $sql = "UPDATE ACCOUNT SET ImgPath = ? WHERE Username = ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("ss", $imgPath, $_SESSION['LoggedUser']['Username']); 
                        $stmt->execute();
                    }
                    $_FILES['profilePicInput'] = null;
                    $_SESSION['LoggedUser']['ImgPath'] = $imgPath;
                }
                
                if($password != ""){
                    $sql = "UPDATE ACCOUNT SET Username = ?, Password = ? WHERE ID = ?";
                    $stmt = $con->prepare($sql);
                    $encPassword = password_hash($password, PASSWORD_BCRYPT);
                    $stmt->bind_param("ssi", $username, $encPassword, $customerID); 
                    $stmt->execute();
                }else if($username != $defaultUsername){
                    $sql = "UPDATE ACCOUNT SET Username = ? WHERE ID = ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("si", $username, $customerID); 
                    $stmt->execute();
                }

                $stmt->close();
                $con->close();
                
                $updated = true;
            }
        }else if (isset($_POST['Ban'])){
            banAccount($customerID);
        }
        else if (isset($_POST['Unban'])){
            unbanAccount($customerID);
        }
    }
    
    if(isset($_GET['CustomerID'])){
        $customerID = isset($_GET['CustomerID'])?$_GET['CustomerID']:"";
        $customer = getAccount($customerID);
       
        if($customer == null){
            header("Location: Customer.php");
        }
        
        $orders = getOrderByAccountID($customerID);
        $person = getPerson($customer['PersonID']);
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
    <link rel="stylesheet" href="../Style/css/Admin/customerDetails.css">
    <title>Profile</title>
</head>
<body class="bg-light adminBody">
    
    <?php include "SideMenu.php" ?>
    <?php include "Header.php" ?>

    <div class="page d-flex align-items-center justify-content-center my-lg-4 mt-4">
        <form class="container-sm bg-white shadow-sm rounded p-3" method="post" action="" enctype="multipart/form-data">
             <?php
                if($updated){
                    printf(
                        '<div class="notification bg-success shadow-sm text-white w-100 rounded px-4 py-2 mt-1 mb-3 justify-content-between" id="notiContainer">
                            <p><b>%s</b></p>
                            <p onclick="hideContainer(\'%s\')" style="cursor:pointer;"><b>X</b></p>
                        </div>',
                        'Successfully Updated Profile Details!',
                        'notiContainer'
                    );

                }
            ?>
            <h2 class="text-center shadow-sm p-2 rounded bg-light mb-3">Customer Details</h2>
            
            <!--Fixed Information-->
            <div class="bg-light shadow-sm rounded border py-1">
                <p class="text-gray px-3"><i><b>Fixed Information</b></i></p><hr>
                <div class="row d-flex align-items-center justify-content-around p-2">
                    <div class="col-lg-3">
                        <p class="text-gray"><i>Role</i></p>
                        <div class="tag rounded-pill <?php echo ($customer['Role'] == 'M')?"bg-primary":"bg-warning";?>"><p><?php echo ($customer['Role'] == 'M')?"Member":"VIP";?></p></div>
                    </div>
                    <div class="col-lg-3">
                        <p class="text-gray"><i>Date Joined</i></p>
                        <p class="text-gray"><b><?php echo ($customer['DateReg'])?></b></p>
                    </div>
                </div>
            </div>

            <input type="hidden" name="customerID" value="<?php echo $customer['ID']?>">
            <input type="hidden" name="personID" value="<?php echo $customer['PersonID']?>">
            <input type="hidden" name="defaultUsername" value="<?php echo $customer['Username']?>">
            
            
            <!--Editable Information-->
            <div class="mt-4 row">
            
                <div class="col-lg-2">
                    <p class="text-gray text-center mb-2"><b>Profile Image</b></p>
                    <input type="file" name="profilePicInput" id="profilePicInput" accept="image/*" onchange="previewPhoto(event,'profileImg')" style="display: none;">
                    <img class="roundImg  w-80 d-flex mx-auto cursor-pointer shadow-sm border" id="profileImg"
                         src="<?php echo (isset($customer['ImgPath']))?$customer['ImgPath']:"../Img/Profile/NoProfile.png"?>" 
                            alt="Profile Pic" 
                            onclick="triggerFileInput('profilePicInput')">
                </div>

                <div class="col-lg-5 px-3 d-block">

                    <p class="text-gray"><b>Username</b></p>
                    <input class="w-100" type="text" name="username" value="<?php echo ((!isset($username))?$customer['Username']:$username); ?>"><br>
                    <?php echo (isset($error['username'])?"<small class='text-danger'><i>".$error['username']."</i></small><br>":"")?>
                    <small class="text-gray"><i>Please do not change the username of customer unless they required you to change for them.</i></small>
                    
                    <p class="text-gray mt-4"><b>Password</b></p>
                    <input class="w-100" type="password" name="password"><br>
                    <?php echo (isset($error['password'])?"<small class='text-danger'><i>".$error['password']."</i></small><br>":"")?>
                    <small class="text-gray"><i>You can only input new password for the account, you may not able to reveal the current password.</i></small>
                    
                    <p class="text-gray mt-4"><b>Name</b></p>
                    <input class="w-100" type="text" name="name" value="<?php echo (isset($person['Name'])?$person['Name']:'')?>"><br>
                    <?php echo (isset($error['name'])?"<small class='text-danger'><i>".$error['name']."</i></small><br>":"")?>
                    <small class="text-gray"><i>Customer name may appear around the system where they contribute or are mentioned.</i></small>

                    <p class="text-gray mt-4"><b>Email</b></p>
                    <input class="w-100" type="text" name="email" value="<?php echo (isset($person['Email'])?$person['Email']:'')?>"><br>
                    <?php echo (isset($error['email'])?"<small class='text-danger'><i>".$error['email']."</i></small><br>":"")?>
                    <small class="text-gray"><i>Customer email for contact them.</i></small>
                    
                    <p class="text-gray mt-4"><b>IC</b></p>
                    <input class="w-100" type="text" name="ic" value="<?php echo (isset($person['IC'])?$person['IC']:'')?>"><br>
                    <?php echo (isset($error['ic'])?"<small class='text-danger'><i>".$error['ic']."</i></small><br>":"")?>
                    <small class="text-gray"><i>Customer IC is just for us to know more details about them.</i></small>

                    <br>
                    <input class="btn btn-primary btn-sm mt-5 w-100" type="submit" name="Update" value="Update">
                    <a href="Customer.php" class="btn btn-dark btn-sm mt-2 w-100" >Back</a>
                    <?php printf('<input class="btn btn-outline-%s btn-sm mt-2 w-100" type="submit" name="%s" value="%s">', ($customer['Status']==1)?"danger":"primary",  ($customer['Status']==1)?"Ban":"Unban", ($customer['Status']==1)?"Ban":"Unban"); ?>
                </div>
                <div class="col-md-5" id="orderContainer">
                    <h6 class="text-center shadow-sm p-2 rounded bg-light mb-3 border">Customer Order</h6>
                    <?php 
                        foreach($orders as $order){
                            printf('<div class="rounded shadow-sm border p-3 container-sm mt-3 d-flex cursor-pointer">
                                            <img src="../Img/Icon/order.png" class="iconImg me-3">
                                            <div class="row w-100">
                                                <div class="col d-flex align-items-center">
                                                    <p><i class="text-gray">Order ID :</i><b> %06d</b></p>
                                                </div>
                                                <div class="col d-flex align-items-center justify-content-end">
                                                    <p><i class="text-gray">Date :</i><b> %s</b></p>
                                                </div>
                                                <div class="col d-flex align-items-center justify-content-end">
                                                    <a href="Order.php?OrderID=%d" class="btn btn-sm btn-outline-primary">More</a>
                                                </div>
                                            </div>
                                        </div>', $order->ID, $order->Date, $order->ID);
                        }
                    ?>
                </div>

            </div>

        </form>
    </div>
    <script src="../Style/js/js.js"></script>
</body>
</html>
