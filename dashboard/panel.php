<?php 

session_start();
//Brain
if(isset($_SESSION['username']))
    {
        include '../dashboard/init.php';
        include "$tpl/navbar.php";
    } 
    else 
    {
        echo 'Acces Denied from panel page';
        header('Location: index.php');
        exit();
    }


?>