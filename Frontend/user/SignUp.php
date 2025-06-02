<?php
    require "../../Backend/Validation.php";
    require_once '../../Backend/env.php';
    
    $successCreate = false;
    
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        
        $username = isset($_POST["username"])?$_POST["username"]:"";
        $password = isset($_POST["password"])?$_POST["password"]:"";
        $conPassword = isset($_POST["conPassword"])?$_POST["conPassword"]:"";
        
        $error["username"] = validateUsername($username);
        $error["password"] = validatePassword($password);
        $error["conPassword"] = validateConPassword($password, $conPassword);
        
        //remove null value
        $error = array_filter($error);
        if(empty($error)){
            
            $con = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
            //Create Person
            $sql = "INSERT INTO person (Name, Email, IC) VALUES (NULL, NULL, NULL)";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            
            //Create Account
            $personID = $con->insert_id; // This gets the ID of the last inserted record
            $password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO account (Username, Password, Role, Status, PersonID) VALUES (?,?,'M',1,?)";
            $stmt = $con->prepare($sql);
            $stmt ->bind_param('ssi', $username,$password,$personID);
            $stmt->execute();

            if($stmt->affected_rows > 0){//Added new account
                //Clear
                $username = null;
                $password = null;
                $conPassword = null;
                
                $successCreate = true;
            }
                
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
    <title>Sign Up</title>
</head>
<body class=" bg-light">
    
    <?php include "Header.php" ?>
    <?php
        if($successCreate){
             printf(
                '<div class="notification bg-success shadow-sm text-white w-100 rounded px-4 py-2 my-1 justify-content-between" id="notiContainer">
                    <p><b>%s</b></p>
                    <p onclick="hideContainer(\'%s\')" style="cursor:pointer;"><b>X</b></p>
                </div>',
                'Successfully Created An Account!',
                'notiContainer'
            );
        }
    ?>
    <div class="d-flex flex-column align-items-center justify-content-center mt-5">
       
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="bg-white shadow-sm p-4">
                        <h3 class="text-center mb-4 fw-bold">Sign Up</h3>
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
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label><br>
                                <input type="password" name="conPassword" class="w-100" value="<?php echo isset($conPassword)?$conPassword:""?>" required>
                                <p><i class="text-danger"><?php echo isset($error["conPassword"])?$error["conPassword"]:''?></i></p>
                            </div>
                            <div class="d-flex align-items-center my-4">
                                <input type="checkbox" class="ui-checkbox" required>
                                <label for="" class="text-gray d-flex align-items-center"><small>I agree to the <a href="terms.html" class="text-gray">Terms and Conditions</a> and <a href="privacy.html" class="text-gray">Privacy Policy</a>.</small> </label>
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-2">Sign Up</button>
                            <p class="mt-3 text-center">Already have an account? <a href="SignIn.php">Sign In here</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../Style/js/js.js"></script>
</body>
</html>