<?php

$env = "dev";
$db;

if ($env === "dev") {
  $db = mysqli_connect("localhost", "root", "");
} else {
  $db = mysqli_connect("localhost", "root", "");
}

