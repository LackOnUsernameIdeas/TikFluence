<?php

//Отпраща от страницата, ако не сте влезнали в профила си
function redirect($url) {
    header("location: " . $url);
    die("Redirected");
}