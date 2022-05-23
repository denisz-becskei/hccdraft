<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPESZ Drafting</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body onkeydown="return start_slideshow(event);">

<!-- Form -> Sends data to control_write -->
<form method="POST" action="control_write.php">
    <div class="divElement vertical-center" style="display: flex; justify-content: center; align-items: center;">
        <input style="height: 20px;" type="text" autocomplete="off" name="match_id" placeholder="Match ID"><br>
        <select style="height: 20px;" name="team1name">
            <!-- Include in PHP -> Just pastes code. These are used to make the code less crowded. -->
            <?php include "php_files/teams.php"; ?>
        </select><br>
        <select style="height: 20px;" name="team2name">
            <?php include "php_files/teams.php"; ?>
        </select><br>
        <input type="number" placeholder="Team 1 Score" name="team1score">
        <input type="number" placeholder="Team 2 Score" name="team2score">
        <input style="height: 25px" type="submit" value="Slideshow Indítása">
    </div>
</form>

<script>
    function start_slideshow(e) {
        var keynum;

        // Get Key Press
        if(window.event) { // IE
            keynum = e.keyCode;
        } else if(e.which){ // Netscape/Firefox/Opera
            keynum = e.which;
        }

        // If KeyPress is Enter, start
        if (String.fromCharCode(keynum) === "Enter") {
            window.location = "control_write.php"; // I used this for something, don't remember :(
        }
    }
</script>

</body>
</html>