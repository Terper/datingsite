<?php

require_once "firstVisitCookie.php";
include_once "db.php";

session_start();
if (!isset($_SESSION["username"]) && $_SESSION["role"] != "administator") {
  header("location: index.php");
  die();
}

if (isset($_GET["u"]) && is_numeric($_GET["u"])) {
  $userId = $_GET["u"];
  mysqli_query($db, "DELETE FROM users WHERE id = $userId");
  print("Deleted user<br>");
  print("<a href='administrator.php'>Go back</a>");
} else {
  print("Invalid user id<br>");
  print("<a href='administrator.php'>Go back</a>");
}
?>