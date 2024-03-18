<?php
include_once "../../../secret.php";

$env = "";
$db;

if ($env === "dev") {
  $db = mysqli_connect("localhost", "root", "", "datingsite");
} else {
  $db = mysqli_connect($server, $username, $password, $db);
}

