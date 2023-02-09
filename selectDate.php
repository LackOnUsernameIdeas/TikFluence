<?php

    session_start();

    function setDate(){
        if(isset($_GET["setDate"])){
            $_SESSION["setDate"] = $_GET["setDate"];
            return $_GET["setDate"];
        }
    }

    setDate();

    if(isset($_GET["redirectURI"])){
        header('Location: '.$_GET["redirectURI"]);
        die();
    }
?>