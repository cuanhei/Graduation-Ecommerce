<?php
    
    session_start();
    
    require '../../Backend/Validation.php';
    require '../../Backend/TransactionBackend.php';
    require_once '../../Backend/env.php';
    
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        
        $username = isset($_POST["username"])?$_POST["username"]:"";
        $oriPassword = isset($_POST["password"])?$_POST["password"]:"";
        
        $error["username"] = (isNull($username))?"Please enter <b>Username</b>.":"";
        $error["password"] = validatePassword($oriPassword);
        
        //remove null value
        $error = array_filter($error);
        if(empty($error)){

            $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
            $sql = "SELECT * FROM account WHERE Username = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $username);  // Bind the username parameter
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                
                $user = $result->fetch_assoc();
                $password = $oriPassword;
                if (password_verify($password, $user['Password'])) {
                    
                    if ($user['Status'] == 0){
                        $error['Banned'] = "Sorry, your account has been BANNED!";
                    }
                    else{
                        //Valid Sign In
                        $_SESSION['LoggedUser'] = $user;
                        $_SESSION['LoggedUser']['Password'] = $oriPassword;

                        //Get Personal Details
                        $sql = "SELECT * FROM person WHERE ID = ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("i", $_SESSION['LoggedUser']['PersonID']);  // Bind the username parameter
                        $stmt->execute();
                        $_SESSION['LoggedUser']['Person'] = ($stmt->get_result())->fetch_assoc();

                        $_SESSION['TotalSpend'] = getTotalAmountSpend($_SESSION['LoggedUser']['ID']);

                        if($_SESSION['LoggedUser']['Role'] == 'M' || $_SESSION['LoggedUser']['Role'] == 'V'){ // M = Member  V = VIP
                            header("Location: Home.php");
                        }else{ //Admin
                            header("Location: ../Admin/Dashboard.php");
                        }
                        exit;
                    }
                } else {
                    $error['password'] = "<b>Incorrect Password</b>. Please try again.";
                }
            } else {
                $error['username'] = "Username <b>NOT</b> found. Please try again.";
            }
            
            $result->free();
            $stmt->close();
            $con->close();
       }
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
    <title>Sign In</title>
</head>
<body class=" bg-light">
    <?php include "Header.php" ?>
    <?php
        if(isset($error['Banned'])){
             printf(
                '<div class="notification bg-danger shadow-sm text-white w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                    <p><b>%s</b></p>
                    <p onclick="hideContainer(\'%s\')" style="cursor:pointer;"><b>X</b></p>
                </div>',
                $error['Banned'],
                'notiContainer'
            );
        }
    ?>
    <div class="d-flex align-items-center justify-content-center mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="bg-white shadow-sm p-4">
                        <h3 class="text-center mb-4 fw-bold">Sign In</h3>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label class="form-label">Username</label><br>
                                <input type="text" name="username" class="w-100" value="<?php echo isset($username)?$username:""?>" required>
                                <p><i class="text-danger"><?php echo isset($error["username"])?$error["username"]:''?></i></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label><br>
                                <input type="password" name="password" class="w-100" value="<?php echo isset($password)?$password:""?>" required>
                                <p><i class="text-danger"><?php echo isset($error["password"])?$error["password"]:''?></i></p>
                            </div>
<!--                            <p class="mt-3 text-end"><a href="SignUp.html" class="text-gray">Forgot Password</a></p>
                            <div class="d-flex align-items-center mt-3">
                                <input type="checkbox" class="ui-checkbox">
                                <label for="" class="text-gray d-flex align-items-center"><small>Remember Me</small></label>
                            </div>-->
                            <button type="submit" class="btn btn-dark w-100 py-2 mt-3">Sign In</button>
                            <p class="mt-3 text-center">Don't have an account? <a href="SignUp.php">Sign Up here</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../Style/js/js.js"></script>
</body>
</html>