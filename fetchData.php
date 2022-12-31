<?php

include 'databaseManager.class2.php';
//include 'chart.php';

$db = new DatabaseManager();
$records = $db->fetchAllData();

header('Content-type: application/json');
print_r($records);
//exit(json_encode($records));

