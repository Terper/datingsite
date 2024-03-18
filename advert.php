<?php

require_once "firstVisitCookie.php";
include_once "db.php";

session_start();

$loggedIn = isset($_SESSION["id"]);
$advertId;

if (isset($_GET["u"]) && is_numeric($_GET["u"])) {
  $advertId = $_GET["u"];

} else {
  header("location: index.php");
  die();
}

if ($loggedIn) {
  $hasLiked = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM likes WHERE likedby = " . $_SESSION["id"] . " AND user = " . $advertId));
  if ($hasLiked) {
    if (isset($_POST["unlike"])) {
      $id = $hasLiked["id"];
      mysqli_query($db, "DELETE FROM likes WHERE id = $id");
      unset($hasLiked);
    }
  } else {
    if (isset($_POST["like"])) {
      mysqli_query($db, "INSERT INTO likes (user, likedby) VALUES ($advertId, " . $_SESSION["id"] . ")");
      $hasLiked = true;
    }
  }
}

$errorComment = false;

if (isset($_POST["comment"])) {
  $comment = filter_input(INPUT_POST, "comment", FILTER_SANITIZE_SPECIAL_CHARS);
  if ($comment) {
    $result = mysqli_query($db, "INSERT INTO comments (onuser, byuser, text) VALUES ($advertId, " . $_SESSION["id"] . ", '$comment')");
    if (!$result) {
      $errorComment = true;
    }
  }
}

$advertQuery = "SELECT * FROM users WHERE id = $advertId";
$advert = mysqli_fetch_assoc(mysqli_query($db, $advertQuery));
$commentsQuery = "SELECT firstname, lastname, text, created FROM comments INNER JOIN users ON comments.byuser = users.id WHERE (onuser = $advertId) ORDER BY created DESC";
$comments = mysqli_query($db, $commentsQuery);
$advertFullName = $advert["firstname"] . " " . $advert["lastname"];


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Datingsite | <?php print($advertFullName);?></title>
    <style>
        <?php include "css/style.css"; ?><?php include "css/advert.css"; ?>
    </style>
</head>
<body>
  <?php include "header.php"; ?>
  <main>
    <section>
    <h2><?php print($advertFullName);?></h2>
    <?php if ($loggedIn) {
      print("<div>" . $advert["salary"] . " Yearly salary</div>");
      print("<div>" . $advert["email"] . "</div>");
    } else {
      print("<a href='login.php'>Log in to see email and salary</a>");
    }?>
    <div><?php print($advert["city"]);?></div>
    <div><?php print($advert["likes"]);?> Likes</div>
    <?php if ($loggedIn) {
      print("<form method='post'>");
      if (isset($hasLiked)) {
        print("<input type='submit' value='Unlike' name='unlike'>");
      } else {
        print("<input type='submit' value='Like' name='like'>");
      }
      print("</form>");
    }?>
    <div><?php print($advert["ad"]);?></div>
  </section>
  <section>
    <h2>Comments</h2>
    <?php
      if (isset($_SESSION["username"])) {
          print("<form method='post'>");
          if ($errorComment) {
              print("<div class='error'>Something went wrong</div>");
          }
          print("<label for='comment'>Add a comment</label>");
          print("<input type='text' id='comment' name='comment'>");
          print("<div>");
          print("<input type='submit' value='Comment'>");
          print("<input type='reset' value='Cancel'>");
          print("</div>");
          print("</form>");
      } else {
          print("<div>You need to <a href='login.php'>log in</a> to comment</div>");
      }
    ?>
    <?php 
      while ($comment = mysqli_fetch_assoc($comments)) {
        $name = $comment["firstname"] . " " . $comment["lastname"];
        $text = $comment["text"];
        $created = $comment["created"];
        print("<div>");
        print("<div>$name | " . date("j.n.Y H:i", strtotime($created)) . "</div>");
        print("<div>$text</div>");
        print("</div>");
      }    
    ?>
  </section>
  </main>
</body>
</html>