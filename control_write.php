<?php

// Getting ID from POST request
$match_id = $_POST["match_id"];

// Calling the system shell. Running PyRez
shell_exec("py ./PaladinsAPIStuff/main.py " . $match_id . " 2>&1");

// Setting the Team Names we got from POST request in teamnames.txt for future reading separated by Line Breaks (\n)
$file = fopen("data/teamnames.txt", "w");
fwrite($file, $_POST["team1name"] . "\n" . $_POST["team2name"] . "\n" . $_POST["team1score"] . "\n" . $_POST["team2score"]);
fclose($file);
// Heading to Map Showcase
header("Location: map_showcase.php");