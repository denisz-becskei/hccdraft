<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPESZ Drafting</title>
</head>
<?php
// Opening the match info we got from PyRez
$handle = file_get_contents("PaladinsAPIStuff/output_match.json");
$match_info = json_decode($handle);

// Deserializing json into a dictionary
foreach ($match_info as $key => $value){
    // Selecting the "map" key
    if ($key == "map") {
        $map = $value;
    }
}

// Include in PHP -> Just pastes code. Used to make files cleaner.
include "php_files/map_selector.php";
?>

<body style="overflow: hidden; margin: 0; background-color: black">
<audio id="sound" style="display:none;">
    <!-- Play BOOM BOOM BOOM BOOOOM -->
    <source id="sound2" src="sounds/Map_Click.ogg" type="audio/ogg">
</audio>

<div>
    <!-- We can use echo to insert the output of something into HTML -->
    <!-- We get the map's images by the index one by one, without displaying them, JS will take care of the displaying -->
    <img src="<?php echo select_map(trim($map), 0);?>" alt="bg" id="pic1" style="width: 100vw; height: 100vh; opacity: 1; display: none;">
    <img src="<?php echo select_map(trim($map), 1);?>" alt="bg" id="pic2" style="width: 100vw; height: 100vh; opacity: 1; display: none;">
    <img src="<?php echo select_map(trim($map), 2);?>" alt="bg" id="pic3" style="width: 100vw; height: 100vh; opacity: 1; display: none;">
    <img src="<?php echo select_map(trim($map), 3);?>" alt="bg" id="pic4" style="width: 100vw; height: 100vh; opacity: 1; display: none;">
    <img src="<?php echo select_map(trim($map), 4);?>" alt="bg" id="pic5" style="width: 100vw; height: 100vh; opacity: 1; display: none;">
    <img src="<?php echo select_map(trim($map), 5);?>" alt="bg" id="pic6" style="width: 100vw; height: 100vh; opacity: 1; display: none;">

    <img src="<?php echo select_map(trim($map), 6);?>" alt="logo" id="logo" style="position:fixed; top: calc(100vh / 2 - 512px / 2); left: calc(100vw / 2 - 512px / 2); opacity: 0;">
</div>

</body>

<script>

    // Get all the images into an array
    // NOTE: the loop goes from 1 and not 0.
    let images = [];
    for (let i = 1; i <= 6; i++) {
        images.push(document.getElementById("pic" + i));
    }

    // Get logo and audio elements
    let logo = document.getElementById("logo");
    let audio = document.getElementById("sound");

    // Buckle up, here is where things get messy
    // There is a reason no one does animation in JS :)
    let i = 0;

    // We are starting an interval. It will trigger every 3500ms
    let int = setInterval(function () {
        // If this is the first image - we have nothing to make invisible before it, so we just make that appear
        if (i === 0) {
            images[i].style.display = "block";
            audio.play();
            // We are starting an interval. This will trigger every 1000 frames at 30fps, or until the opacity is greater than 0.
            let inner = setInterval(function () {
                if (parseFloat(images[i-1].style.opacity) > 0) {
                    images[i-1].style.opacity = parseFloat(images[i-1].style.opacity) - 0.015;
                } else {
                    clearInterval(inner);
                }
            }, 1000/30);
        // If this is the last image - we stop the interval. No more pictures available.
        } else if (i === 6) {
            clearInterval(int);
            // We are starting an interval. This will trigger every 1000 frames at 30fps
            // We pop up the logo to the screen.
            let inner = setInterval(function () {
                if (logo.style.opacity < 1) {
                    logo.style.opacity = parseFloat(logo.style.opacity) + 0.025;
                } else {
                    // After the logo is fully visible, we clear the interval and set a timeout
                    clearInterval(inner);

                    // Interval - triggers every X milliseconds
                    // Timeout - triggers once after X milliseconds

                    // After 2500ms, head to rosters.php
                    setTimeout(function () {
                        window.location = "rosters.php";
                    }, 2500);
                }
            }, 1000/30)
        // If this isn't the first or last image, we do the same as the first if, but we make the previous image display = none
        } else {
            images[i-1].style.display = "none";
            images[i].style.display = "block";
            audio.play();
            let inner = setInterval(function () {
                if (parseFloat(images[i-1].style.opacity) > 0) {
                    images[i-1].style.opacity = parseFloat(images[i-1].style.opacity) - 0.015;
                } else {
                    clearInterval(inner);
                }
            }, 1000/30);
        }
        // We iterate the images
        i++;
    }, 3500);
</script>

</html>