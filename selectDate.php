<?php

    session_start();

    function setDate(){
        if(isset($_POST["setDate"])){
            $_SESSION["setDate"] = $_POST["setDate"];
            return $_POST["setDate"];
        }
    }

    echo setDate();
    // echo $_SESSION["setDate"];
    
?>