<?php

require_once "firstVisitCookie.php";
include_once "db.php";

session_start();
if (!isset($_SESSION["username"]) && ($_SESSION["role"] != "administator" || $_SESSION["role"] != "moderator")) {
  header("location: index.php");
  die();
}

if (isset($_GET["c"]) && is_numeric($_GET["c"])) {
  $commentId = $_GET["c"];
  mysqli_query($db, "DELETE FROM comments WHERE id = $commentId");
  print("Deleted comment<br>");
  print("<a href='moderator.php'>Go back</a>");
} else {
  print("Invalid comment id<br>");
  print("<a href='moderator.php'>Go back</a>");
}
?>