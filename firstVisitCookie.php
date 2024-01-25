<?php
// saves the datetime of when a user visits the site for the first time for 400 days

if (isset($_COOKIE["firstVisit"])) {
    setCookie("firstVisit", $_COOKIE["firstVisit"], time() + (86400 * 400), "/");
} else {
    $date = date(DATE_ATOM);
    setCookie("firstVisit", $date, time() + (86400 * 400), "/");
    // generates a json object with the ip and timestamp
    $visit = [
        "ip" => $_SERVER["REMOTE_ADDR"],
        "timestamp" => $date
    ];
    $append = json_encode($visit) . "\n";
    // puts the json objecy in the visits file
    if (!file_exists("visits")) {
        $file = fopen("visits", "w");
        fclose($file);
    }
    file_put_contents("visits", $append, FILE_APPEND);
}
