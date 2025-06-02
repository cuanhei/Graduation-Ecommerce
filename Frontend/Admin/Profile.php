<?php
    session_start();
    
    if(!isset($_SESSION['LoggedUser'])){ header('Location: ../user/Home.php');}
    if($_SESSION['LoggedUser']['Role'] != "A"){ header('Location: ../user/Home.php');}
    
    require_once '../../Backend/env.php';
    require '../../Backend/File.php';
    
    $updated = false;
    
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        
        if(isset($_POST['SignOut'])){
            
            session_destroy();
            $_SESSION = array();
            header("Location: ../user/Home.php");
            
        } else if(isset($_POST['Update'])){
            
            require_once "../../Backend/Validation.php";
            
            $username = isset($_POST['username'])?$_POST['username']:"";
            $password = isset($_POST['password'])?$_POST['password']:"";
            $name = isset($_POST['name'])?$_POST['name']:"";
            $email = isset($_POST['email'])?$_POST['email']:"";
            $ic = isset($_POST['ic'])?$_POST['ic']:"";
            
            //If remain username then no need check for duplicate
            if($username != $_SESSION['LoggedUser']['Username']){
                $error['username'] = validateUsername($username);
            }
            $error['password'] = validatePassword($password);
            $error['name'] = validateName($name);
            $error['email'] = validateEmail($email);
            $error['ic'] = validateIC($ic);
            
           
            //remove null value
            $error = array_filter($error);
            if(empty($error)){
                
                $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
                $sql = "UPDATE PERSON SET Name = ?, Email = ?, IC = ? WHERE ID = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("sssi", $name, $email, $ic, $_SESSION['LoggedUser']['PersonID']);
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
                
                $sql = "UPDATE ACCOUNT SET Username = ?, Password = ? WHERE Username = ?";
                $stmt = $con->prepare($sql);
                $encPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt->bind_param("sss", $username, $encPassword, $_SESSION['LoggedUser']['Username']); 
                $stmt->execute();
                
                $stmt->close();
                $con->close();
                
                $_SESSION['LoggedUser']['Username'] = $username;
                $_SESSION['LoggedUser']['Password'] = $password;
                $_SESSION['LoggedUser']['Person']['Name'] = $name;
                $_SESSION['LoggedUser']['Person']['Email'] = $email;
                $_SESSION['LoggedUser']['Person']['IC'] = $ic;
                
                $updated = true;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../Img/Logo/logo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../Style/css/style.css">
    <title>Profile</title>
</head>
<body class="bg-light adminBody">
    
    <?php include "SideMenu.php" ?>
    <?php include "Header.php" ?>

    <?php
        if($updated){
            printf(
                '<div class="notification bg-success shadow-sm text-white w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                    <p><b>%s</b></p>
                    <p onclick="hideContainer(\'%s\')" style="cursor:pointer;"><b>X</b></p>
                </div>',
                'Successfully Updated Profile Details!',
                'notiContainer'
            );

        }
    ?>
    <div class="page d-flex align-items-center justify-content-center my-lg-4 mt-4">
        <form class="container-sm bg-white shadow-sm rounded p-3" method="post" action="" enctype="multipart/form-data">
            <h2 class="text-center shadow-sm p-2 rounded bg-light mb-3">Profile</h2>
            <!--Fixed Information-->
            <div class="bg-light shadow-sm rounded border py-1">
                <p class="text-gray px-3"><i><b>Fixed Information</b></i></p><hr>
                <div class="row d-flex align-items-center justify-content-around p-2">
                    <div class="col-lg-3">
                        <p class="text-gray"><i>Role</i></p>
                        <div class="tag rounded-pill bg-danger"><p>Admin</p></div>
                    </div>
                    <div class="col-lg-3">
                        <p class="text-gray"><i>Date Joined</i></p>
                        <p class="text-gray"><b><?php echo ($_SESSION['LoggedUser']['DateReg'])?></b></p>
                    </div>
                </div>
            </div>

           
            <!--Editable Information-->
            <div class="mt-4 row">
            
                <div class="col">
                    <p class="text-gray text-center mb-2"><b>Profile Image</b></p>
                    <input type="file" name="profilePicInput" id="profilePicInput" accept="image/*" onchange="previewPhoto(event,'profileImg')" style="display: none;">
                    <img class="roundImg  w-80 d-flex mx-auto cursor-pointer shadow-sm border" id="profileImg"
                         src="<?php echo (isset($_SESSION['LoggedUser']['ImgPath']))?$_SESSION['LoggedUser']['ImgPath']:"../Img/Profile/NoProfile.png"?>" 
                            alt="Profile Pic" 
                            onclick="triggerFileInput('profilePicInput')">
                </div>

                <div class="col-lg-8 px-3 d-block">

                    <p class="text-gray"><b>Username</b></p>
                    <input class="w-100" type="text" name="username" value="<?php echo ((!isset($username))?$_SESSION['LoggedUser']['Username']:$username); ?>"><br>
                    <?php echo (isset($error['username'])?"<small class='text-danger'><i>".$error['username']."</i></small><br>":"")?>
                    <small class="text-gray"><i>Once username had changed, you are required to use the changed username to sign in the system.</i></small>
                    
                    <p class="text-gray mt-4"><b>Password</b></p>
                    <input class="w-100" type="password" name="password" value="<?php echo ((!isset($password))?$_SESSION['LoggedUser']['Password']:$password); ?>"><br>
                    <?php echo (isset($error['password'])?"<small class='text-danger'><i>".$error['password']."</i></small><br>":"")?>
                    <small class="text-gray"><i>You are not able to reveal your password to prevent sensitive information leaked.</i></small>
                    
                    <p class="text-gray mt-4"><b>Name</b></p>
                    <input class="w-100" type="text" name="name" value="<?php echo (isset($_SESSION['LoggedUser']['Person']['Name'])?$_SESSION['LoggedUser']['Person']['Name']:'')?>"><br>
                    <?php echo (isset($error['name'])?"<small class='text-danger'><i>".$error['name']."</i></small><br>":"")?>
                    <small class="text-gray"><i>Your name may appear around the system where you contribute or are mentioned.</i></small>

                    <p class="text-gray mt-4"><b>Email</b></p>
                    <input class="w-100" type="text" name="email" value="<?php echo (isset($_SESSION['LoggedUser']['Person']['Email'])?$_SESSION['LoggedUser']['Person']['Email']:'')?>"><br>
                    <?php echo (isset($error['email'])?"<small class='text-danger'><i>".$error['email']."</i></small><br>":"")?>
                    <small class="text-gray"><i>Your email is for password recovery.</i></small>
                    
                    <p class="text-gray mt-4"><b>IC</b></p>
                    <input class="w-100" type="text" name="ic" value="<?php echo (isset($_SESSION['LoggedUser']['Person']['IC'])?$_SESSION['LoggedUser']['Person']['IC']:'')?>"><br>
                    <?php echo (isset($error['ic'])?"<small class='text-danger'><i>".$error['ic']."</i></small><br>":"")?>
                    <small class="text-gray"><i>Your IC is just for us to record your more details.</i></small>

                    <br>
                    <input class="btn btn-primary btn-sm mt-5 w-100" type="submit" name="Update" value="Update Profile">
                    <input class="btn btn-dark btn-sm mt-3 w-100" type="submit" name="SignOut" value="Sign Out">
                </div>

            </div>

        </form>
    </div>
    <script src="../Style/js/js.js"></script>
</body>
</html>
