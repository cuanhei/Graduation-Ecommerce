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
  
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="../Img/Logo/logo.png" type="image/x-icon">
        <link rel="stylesheet" href="../Style/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" href="../Style/css/style.css">
        <script src="../Style/js/js.js"></script>
        <title>Dashboard</title>
    </head>
    <body class="bg-light adminBody" style="overflow-y: hidden">
        <?php include "SideMenu.php"; ?>
        <?php include "Header.php"; ?>
        
        <div class="page bg-light d-flex align-items-center justify-content-center">
            <div class="bg-white rounded shadow-sm maxPageHeight p-3 container-lg">
                
                <h2 class="text-center shadow-sm p-2 rounded bg-light mb-3">Dashboard</h2>
                
            </div>
        </div>
        <script src="../Style/js/js.js"></script>
    </body>
</html>
