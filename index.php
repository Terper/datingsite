<?php
require_once "firstVisitCookie.php";

session_start();

$htmlDateFormat = "Y-m-d\TH:i";
$errorNextDate = false;
$errorComment = false;
$nextDate;

// checks if nextDate has been posted
if (isset($_POST["nextDate"])) {
    // turn the date to unix time, will return false if the date is invalid
    $time = strtotime($_POST["nextDate"]);
    // checks if date is valid and ahead of the current time
    if ($time && $time > time()) {
        // puts the difference in $nextDate to be used in the page
        $nextDate = date_diff(date_create(), date_create(date($htmlDateFormat, $time)));
    } else {
        $errorNextDate = true;
    }
}

if (isset($_POST["comment"]) && isset($_SESSION["username"])) {
    // filters the input
    $comment = filter_input(INPUT_POST, "comment", FILTER_SANITIZE_SPECIAL_CHARS);
    do {
        // check if filtering was successfull
        if (!$comment) {
            $errorComment = true;
            break;
        }
        // creates a json object
        $newComment = [
            "username" => htmlspecialchars($_SESSION["username"]),
            "timestamp" => date(DATE_ATOM),
            "comment" => htmlspecialchars($comment)
        ];
        $append = json_encode($newComment) . "\n";
        // appends the json object to the comments file

        if (!file_exists("comments")) {
            file_put_contents("comments", "");
        }
        file_put_contents("comments", $append, FILE_APPEND);
    } while (false);
}

// retrieves the comments file
if (file_exists("comments")) {
    $comments = file_get_contents("comments");
    // put every line as an array item
    $comments = explode("\n", $comments);
    // removes the last item since its empty
    array_pop($comments);
    // decodes the json objects to an array
    foreach ($comments as $key => $value) {
        $comments[$key] = json_decode($value, true);
    }

    // reverse the array so the newest comments come on top
    $comments = array_reverse($comments);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datingsite</title>
    <style>
        <?php include "css/style.css"; ?><?php include "css/index.css"; ?>
    </style>
</head>

<body>
    <?php include "header.php"; ?>
    <main>
        <div>
            <div>
                <?php
                if (isset($nextDate)) {
                    print("<div class='nextDate'>");
                    print("<div>Your next date is in $nextDate->d days $nextDate->h hours $nextDate->m minutes and $nextDate->s seconds</div>");
                    print("<div>" . date("l, j F", strtotime($_POST["nextDate"])) . " (Week " . ceil(date("W", strtotime($_POST["nextDate"]))) . ")</div>");
                    print("</div>");
                }
                ?>
            </div>
        </div>
        <form method="post" class="nextDateForm">
            <?php if ($errorNextDate) {
                print("<div class='error'>Something went wrong or the date is invalid</div>");
            } ?>
            <label for="nextDate">Next date</label>
            <input type="datetime-local" id="nextDate" name="nextDate" min=<?php print(date($htmlDateFormat)); ?> value=<?php print(isset($nextDate) ? date($htmlDateFormat, strtotime($_POST["nextDate"])) : (date($htmlDateFormat))); ?>>
            <input type="submit" value="Submit">
        </form>
        <section>
            <a href="adverts.php"><h2>Dating adverts</h2></a>
        </section>
        <section>
            <h2>Comments</h2>
            <?php
            if (isset($_SESSION["username"])) {
                print("<form method='post' class='commentForm'>");
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
            <div class="comments">
                <?php
                if (isset($comments)) {

                    foreach ($comments as $comment) {
                        print("<div class='comment'>");
                        print("<div class='commentHeader'>");
                        print("<div>");
                        print($comment["username"]);
                        print("</div>");
                        print("<div>");
                        print(date("j.n.Y H:i", strtotime($comment["timestamp"])));
                        print("</div>");
                        print("</div>");
                        print("<div class='commentBody'>");
                        print_r($comment["comment"]);
                        print("</div>");
                        print("</div>");
                    }
                }
                ?>
            </div>
        </section>
    </main>
</body>

</html>