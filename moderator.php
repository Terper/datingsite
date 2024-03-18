<?php

require_once "firstVisitCookie.php";
include_once "db.php";

session_start();
if (!isset($_SESSION["username"]) && ($_SESSION["role"] != "administator" || $_SESSION["role"] != "moderator")) {
  header("location: index.php");
  die();
}


$comments = mysqli_query($db, "SELECT t.id, t1.username AS onuser, t2.username AS byuser, t.text, t.created FROM comments t INNER JOIN users t1 ON t.onuser = t1.id INNER JOIN users t2 ON t.byuser = t2.id ORDER BY created DESC, onuser  ")
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datingsite | Moderator</title>
    <style>
        <?php include "css/style.css"; ?><?php include "css/moderator.css"; ?>
    </style>
</head>
<body>
  <?php include "header.php"; ?>
  <main>
    <div class="comments">
  <?php 
      while ($comment = mysqli_fetch_assoc($comments)) {
        $byuser = $comment["byuser"];
        $onuser = $comment["onuser"];
        $text = $comment["text"];
        $created = $comment["created"];
        print("<div>");
        print("<div>By: $byuser | On: $onuser</div>");
        print("<div>" . date("j.n.Y H:i", strtotime($created)) . "</div>");
        print("<div>$text</div>");
        print("<a href='deletecomment.php?c=" . $comment["id"] . "'>Delete</a>");
        print("</div>");
      }    
    ?>
    </div>
  </main>
</body>