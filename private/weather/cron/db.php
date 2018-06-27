<?php

defined('__ROOT__') or define("__ROOT__", dirname(dirname(__FILE__)));
require_once(__ROOT__.'/config.php');

function dbConnect() {
    $con = mysqli_connect(CONST_DB_HOST,CONST_DB_USERNAME,CONST_DB_PASSWORD,CONST_DB_NAME);

    if (mysqli_connect_errno()) {
        error_log("Failed to connect to MySQL: " . mysqli_connect_error());
        die("Didn't connect to db");
    }

    return $con;
}