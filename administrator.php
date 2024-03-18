<?php

require_once "firstVisitCookie.php";
include_once "db.php";

session_start();
if (!isset($_SESSION["username"]) && $_SESSION["role"] != "administator") {
  header("location: index.php");
  die();
}

$users = mysqli_query($db, "SELECT * FROM users WHERE registered = 1");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datingsite | Admin</title>
    <style>
        <?php include "css/style.css"; ?><?php include "css/administrator.css"; ?>
    </style>
</head>
<body>
  <?php include "header.php"; ?>
  <main>
  <?php while ($user = mysqli_fetch_assoc($users)) {
      print("<div class='user'>");
      print("<hr>");
      print("<div>Email: " . $user["email"] . "</div>");
      print("<div>Username: " . $user["username"] . "</div>");
      print("<div>Role: " . $user["role"] . "</div>");
      print("<div>First name: " . $user["firstname"] . "</div>");
      print("<div>Last name: " . $user["lastname"] . "</div>");
      print("<div>City: " . $user["city"] . "</div>");
      print("<div>Salary: " . $user["salary"] . "</div>");
      print("<div>Gender: " . $user["gender"] . "</div>");
      print("<div>Preference: " . $user["preference"] . "</div>");
      print("<div>Ad: " . $user["ad"] . "</div>");
      print("</div>");
      print("<div class='userActions'>");
      print("<a href='advert.php?u=" . $user["id"] . "'>See Advert</a>");
      print("<a href='deleteuser.php?u=" . $user["id"] . "'>Delete</a>");
      print("<a href='update.php?u=" . $user["id"] . "'>Update</a>");
      print("</div>");
      print("</div>");
    }
    ?>
  </main>
</body>