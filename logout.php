<?php

// destroys everything having to do with the session
session_start();
unset($_SESSION["username"]);
unset($_SESSION["profilePicture"]);
session_destroy();
header("location: login.php");
die();
