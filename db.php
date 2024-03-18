<?php

$env = "";
$db;

if ($env === "dev") {
  $db = mysqli_connect("localhost", "root", "", "datingsite");
} else {
  include_once "../../../secret.php";
  $db = mysqli_connect($server, $username, $password, $db);
}

