<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPESZ Drafting</title>
    <link rel="stylesheet" href="css/style.css">
    <?php
    /* Get Ready. This is by far the hardest file to understand. */

    // Some imports that we will need
    include "db_connect.php";
    include "php_files/sound_selector.php";
    include "php_files/champion_data.php";
    ?>
</head>

<?php
/**
 * We get the number of matches played in the entire season.
 */
function get_match_numbers()
{
    $conn = OpenCon();
    $sql = "SELECT match_number FROM played_matches";
    $result = $conn->query($sql);
    CloseCon($conn);
    return intval(mysqli_fetch_array($result)[0]);
}

/**
 * We get the number of matches where the champion has been picked in the entire season.
 */
function get_champion_pick_amount($champion)
{
    $conn = OpenCon();
    $sql = "SELECT picked FROM champion_data WHERE champion_name = '$champion'";
    $result = $conn->query($sql);
    CloseCon($conn);
    return intval(mysqli_fetch_array($result)[0]);
}
/**
 * We get the number of matches where the champion has won in the entire season.
 */
function get_champion_victory_amount($champion)
{
    $conn = OpenCon();
    $sql = "SELECT won FROM champion_data WHERE champion_name = '$champion'";
    $result = $conn->query($sql);
    CloseCon($conn);
    return intval(mysqli_fetch_array($result)[0]);
}
/**
 * We get the number of matches where the champion has been banned in the entire season.
 */
function get_champion_ban_amount($champion)
{
    $conn = OpenCon();
    $sql = "SELECT banned FROM champion_data WHERE champion_name = '$champion'";
    $result = $conn->query($sql);
    CloseCon($conn);
    return intval(mysqli_fetch_array($result)[0]);
}
/**
 * We calculate the champions pick rate here.
 */
function get_champion_pickrate($champion)
{
    if (get_match_numbers() == 0) {
        return 0;
    }
    return round(get_champion_pick_amount($champion) * 100 / get_match_numbers(), 0);
}
/**
 * We calculate the champions win rate here.
 */
function get_champion_winrate($champion)
{
    if (get_champion_pick_amount($champion) == 0) {
        return 0;
    }
    return round(get_champion_victory_amount($champion) * 100 / get_champion_pick_amount($champion), 0);
}
/**
 * We calculate the champions ban rate here.
 */
function get_champion_banrate($champion)
{
    if (get_match_numbers() == 0) {
        return 0;
    }
    return round(get_champion_ban_amount($champion) * 100 / get_match_numbers(), 0);
}
/**
 * We get the team names and store it in an array. Return with the array.
 */
function get_team_info()
{
    $file = fopen("data/teamnames.txt", "r");
    $teaminfo = [];
    while (!feof($file)) {
        array_push($teaminfo, fgets($file));
    }
    return $teaminfo;
}

/**
 * @param $player_num - the player number we get from output_players given by PyRez
 * @param $data_to_get - the data we need to get from the player
 * @return string - the data we wanted
 *
 * For example:
 * get_player_data(1, "champion") => Kinessa
 */
function get_player_data($player_num, $data_to_get)
{
    $handle = file_get_contents("PaladinsAPIStuff/output_players.json");
    $match_info = json_decode($handle, true);
    $data = "";

    foreach ($match_info["player" . $player_num] as $mi) {
        foreach ($mi as $m) {
            $data = $data . $m . " | ";
        }
    }

    $data = explode(" | ", $data);
    $data = ["name" => $data[0], "champion" => $data[1], "kda" => $data[2], "damage" => $data[3], "damage_taken" => $data[4],
        "healing" => $data[5], "shielding" => $data[6], "ot" => $data[7], "talent" => $data[8]];

    return $data[$data_to_get];
}

/**
 * We get the bans from output_match given by PyRez
 */
function get_bans()
{
    $handle = file_get_contents("PaladinsAPIStuff/output_match.json");
    $match_info = json_decode($handle);
    $bans = [];

    foreach ($match_info as $key => $value) {
        if ($key == "ban1" || $key == "ban2" || $key == "ban3" || $key == "ban4") {
            array_push($bans, $value);
        }
    }
    return $bans;
}
/**
 * We set the talent image graphic to the appropriate one
 */
function set_talent($champion, $talent) {
    return "images/talents/".$champion."/".$talent.".png";
}

/**
 * We get the talents description, which is stored in data/talents.json
 */
function get_talent_desc($champion, $talent_to_get) {
    $handle = file_get_contents("data/talents.json");
    $talent_info = json_decode($handle, true);

    foreach ($talent_info[$champion] as $talents) {
        return $talents[$talent_to_get];
    }

    return null;
}

?>

<style>
    @import url('https://fonts.cdnfonts.com/css/lucida-sans');

    .text {
        position: relative;
        left: 360px;
    }
</style>

<body onload="disable_banned();" onkeydown="return goto_stats(event);">

<div id="start_screen" style="position:fixed; height: 100vh; width: 100vw; background-color: black; opacity: 1;"></div>

<div id="logo" style="display: flex; opacity: 0; position:fixed; left: calc(100vw / 2 - 150px / 2); top: 10px; z-index: 50;">
    <h3 style="color: white; position:fixed; top: 0; left: 800px; font-size: 35px; font-family: 'Lucida Sans', sans-serif"><?php echo get_team_info()[2]; ?></h3>
    <img src="images/logo.png" alt="logo" style="width: 150px; height: 150px;">
    <h3 style="color: white; position:fixed; top: 0; left: 1105px; font-size: 35px; font-family: 'Lucida Sans', sans-serif"><?php echo get_team_info()[3]; ?></h3>
</div>

<img style="position:fixed; top: 45px; left: 0; width: 1920px; height: 1080px; z-index: 50;" src="images/borders.png"
     alt="border">

<div style="position:fixed; top: 0; left: 0; width: 420px; height: 120px;">
    <img id="team1pic" style="position:absolute; width: 120px;" alt="team 1 logo">
    <h3 id="team1name"
        style="margin: 0; color: white; position:fixed; top: 20px; left: 150px; font-size: 35px; font-family: 'Lucida Sans', sans-serif;"></h3>
</div>

<div style="position:fixed; top: 0; right: 0; width: 420px; height: 120px;">
    <img id="team2pic" style="position:absolute; width: 120px; right: 0" alt="team 2 logo">
    <h3 id="team2name"
        style="margin:0; color: white; position:fixed; top: 35px; right: 150px; font-size: 35px; font-family: 'Lucida Sans', sans-serif;"></h3>
</div>

<audio id="sound" style="display:none;">
    <source id="sound2" src="sounds/Silence.ogg" type="audio/ogg">
</audio>
<audio id="banned" style="display:none;">
    <source id="banned2" src="sounds/Banned.ogg" type="audio/ogg">
</audio>
<div>

    <img alt="team1pick1cover" id="team1pick1cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 120px; left: 0; opacity: 0">
    <img alt="team1pick2cover" id="team1pick2cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 280px; left: 0; opacity: 0">
    <img alt="team1pick3cover" id="team1pick3cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 440px; left: 0; opacity: 0">
    <img alt="team1pick4cover" id="team1pick4cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 600px; left: 0; opacity: 0">
    <img alt="team1pick5cover" id="team1pick5cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 760px; left: 0; opacity: 0">


    <img alt="team1pick1" id="team1pick1" src="images/null.png"
         style="width: 420px; position: fixed; top: 120px; left: 0; opacity: 0">
    <img alt="team1pick2" id="team1pick2" src="images/null.png"
         style="width: 420px; position: fixed; top: 280px; left: 0; opacity: 0">
    <img alt="team1pick3" id="team1pick3" src="images/null.png"
         style="width: 420px; position: fixed; top: 440px; left: 0; opacity: 0">
    <img alt="team1pick4" id="team1pick4" src="images/null.png"
         style="width: 420px; position: fixed; top: 600px; left: 0; opacity: 0">
    <img alt="team1pick5" id="team1pick5" src="images/null.png"
         style="width: 420px; position: fixed; top: 760px; left: 0; opacity: 0">

    <img alt="team1pick1talent" id="team1pick1talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 100px; left: 385px; opacity: 0; z-index: 75">
    <img alt="team1pick2talent" id="team1pick2talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 260px; left: 385px; opacity: 0; z-index: 75">
    <img alt="team1pick3talent" id="team1pick3talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 420px; left: 385px; opacity: 0; z-index: 75">
    <img alt="team1pick4talent" id="team1pick4talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 580px; left: 385px; opacity: 0; z-index: 75">
    <img alt="team1pick5talent" id="team1pick5talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 740px; left: 385px; opacity: 0; z-index: 75">

    <h4 style="color: white; writing-mode: bt-rl; position:fixed; font-size: 20pt; transform: rotate(270deg); left: 80px; bottom: 40px; font-family: 'Lucida Sans', sans-serif;">
        BANNOK</h4>

    <img alt="team1ban1" id="team1ban1" src="images/null.png"
         style="width: 160px; position: fixed; top: 920px; left: 200px; opacity: 0; transform: scaleX(-1);">
    <img alt="team1ban2" id="team1ban2" src="images/null.png"
         style="width: 160px; position: fixed; top: 1000px; left: 240px; opacity: 0; transform: scaleX(-1);">

</div>

<canvas id="middle_part"
        style="position:absolute; top: calc(1080px / 2 - 768px / 2); left: calc(1920px / 2 - 768px / 2);">

</canvas>

<script>
    function disable_banned() {
        document.getElementById("banned").pause();
        document.getElementById("banned").currentTime = 0;
    }
</script>

<script>

    setTimeout(function () {
        increase_opacity("logo");
    }, 1500);

    function draw_image(champ) {
        const canvas = document.getElementById('middle_part');
        const ctx = canvas.getContext('2d');
        canvas.width = 768;
        canvas.height = 768;

        const image = new Image();
        image.onload = drawImageActualSize;

        image.src = 'images/splashes/' + champ + '.png';

        function drawImageActualSize() {
            if (image.width > image.height) {
                ctx.drawImage(image, 0, 0, 768, 768 * image.height / image.width);
            } else if (image.height > image.width) {
                ctx.drawImage(image, 768 / 2 - image.width / 2, 0, 768 * image.width / image.height, 768);
            } else {
                ctx.drawImage(image, 768 / 2 - image.width / 2, 0, 768, 768);
            }
        }
    }

    function draw_image_banned(champ) {
        const canvas = document.getElementById('middle_part');
        const ctx = canvas.getContext('2d');
        canvas.width = 768;
        canvas.height = 768;

        const image = new Image();
        const image2 = new Image();
        const image3 = new Image();
        image.onload = drawImageActualSize;

        image.src = 'images/splashes/' + champ + '.png';
        image2.src = 'images/champions/banned1.png';
        image3.src = 'images/champions/banned2.png';

        function drawImageActualSize() {
            if (image.width > image.height) {
                ctx.drawImage(image, 0, 0, 768, 768 * image.height / image.width);
            } else if (image.height > image.width) {
                ctx.drawImage(image, 768 / 2 - image.width / 2, 0, 768 * image.width / image.height, 768);
            } else {
                ctx.drawImage(image, 768 / 2 - image.width / 2, 0, 768, 768);
            }
            setTimeout(() => {
                console.log(image2);
                ctx.drawImage(image2, 0, 0, 768, 768);
                document.getElementById("banned").play();
            }, 1500);
            setTimeout(() => {
                ctx.drawImage(image3, 0, 0, 768, 768);

                document.getElementById("banned").play();
            }, 2000);
        }
    }

    function clear_canvas() {
        const canvas = document.getElementById('middle_part');
        const ctx = canvas.getContext('2d');
        canvas.width = 768;
        canvas.height = 768;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
</script>

<div id="talent_box" style="position:fixed; top: 740px; left: 660px; opacity: 0;">
    <div style="width: 600px; height: 200px; background-color: rgba(20, 17, 19, 0.5); opacity: 1; ">
    <h4 id="talent_en" style="position:absolute; left: 25px; top: 5px; color: white; font-size: 20px; font-family: 'Lucida Sans', sans-serif; margin: 5px 0 0 5px;">Mother's Grace</h4>
    <img id="talent_img" src="images/talents/Inara/Mother's Grace.png" style="position:absolute; height: 96px; left: calc(600px / 2 - 96px / 2);">
    <h4 id="talent_hu" style="position:absolute; right: 25px; top: 5px; color: white; font-size: 20px; font-family: 'Lucida Sans', sans-serif; margin: 5px 0 0 5px;">
            Édesanya Kegyelme</h4>
    </div>
    <div style="display: flex; justify-content: center; align-items: center; width: 600px; height: 90px; position:relative; bottom: 115px; text-align: center; font-family: 'Lucida Sans', sans-serif; color: white; font-size: 14px">
        <h4 style="width: 580px" id="talent_desc">Csökkenti a beérkezdő sebzést további 10%-kal és tömegirányítás-immunitást biztosít az Earthen Guard ideje alatt.</h4>
    </div>
</div>

<div id="bottom_bar"
     style="width: 600px; height: 100px; background-color: rgba(20, 17, 19, 0.3); opacity: 0; position:fixed; top: 950px; left: 660px;">
    <img alt="current_pick" id="current_pick" src="images/champions/Androxus.png"
         style="width: 300px; position: absolute;">
    <div class="text" style="letter-spacing: 2px;">
        <h4 id="pickrate" style="color: white; font-size: 14pt; font-family: 'Lucida Sans', sans-serif; margin: 10px 0 0;">
            Választási ráta:</h4>
        <h4 id="banrate" style="color: white; font-size: 14pt; font-family: 'Lucida Sans', sans-serif; margin: 5px 0 0;">Bannolási
            ráta:</h4>
        <h4 id="winrate" style="color: white; font-size: 14pt; font-family: 'Lucida Sans', sans-serif; margin: 5px 0 0;">Győzelmi
            ráta:</h4>
    </div>
</div>

<div>
    <img alt="team2pick1cover" id="team2pick1cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 120px; right: 0; opacity: 0; transform: scaleX(-1);">
    <img alt="team2pick2cover" id="team2pick2cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 280px; right: 0; opacity: 0; transform: scaleX(-1);">
    <img alt="team2pick3cover" id="team2pick3cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 440px; right: 0; opacity: 0; transform: scaleX(-1);">
    <img alt="team2pick4cover" id="team2pick4cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 600px; right: 0; opacity: 0; transform: scaleX(-1);">
    <img alt="team2pick5cover" id="team2pick5cover" src="images/picking.gif"
         style="width: 420px; position: fixed; top: 760px; right: 0; opacity: 0; transform: scaleX(-1);">

    <img alt="team2pick1" id="team2pick1" src="images/null.png"
         style="width: 420px; position: fixed; top: 120px; right: 0; opacity: 0; transform: scaleX(-1);">
    <img alt="team2pick2" id="team2pick2" src="images/null.png"
         style="width: 420px; position: fixed; top: 280px; right: 0; opacity: 0; transform: scaleX(-1);">
    <img alt="team2pick3" id="team2pick3" src="images/null.png"
         style="width: 420px; position: fixed; top: 440px; right: 0; opacity: 0; transform: scaleX(-1);">
    <img alt="team2pick4" id="team2pick4" src="images/null.png"
         style="width: 420px; position: fixed; top: 600px; right: 0; opacity: 0; transform: scaleX(-1);">
    <img alt="team2pick5" id="team2pick5" src="images/null.png"
         style="width: 420px; position: fixed; top: 760px; right: 0; opacity: 0; transform: scaleX(-1);">

    <img alt="team2pick1talent" id="team2pick1talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 100px; right: 385px; opacity: 0; z-index: 75">
    <img alt="team2pick2talent" id="team2pick2talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 260px; right: 385px; opacity: 0; z-index: 75">
    <img alt="team2pick3talent" id="team2pick3talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 420px; right: 385px; opacity: 0; z-index: 75">
    <img alt="team2pick4talent" id="team2pick4talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 580px; right: 385px; opacity: 0; z-index: 75">
    <img alt="team2pick5talent" id="team2pick5talent" src="images/null.png"
         style="width: 70px; position: fixed; top: 740px; right: 385px; opacity: 0; z-index: 75">

    <h4 style="color: white; writing-mode: bt-rl; font-size: 20pt; position:fixed; transform: rotate(90deg); right: 80px; bottom: 40px; font-family: 'Lucida Sans', sans-serif;">
        BANNOK</h4>

    <img alt="team2ban1" id="team2ban1" src="images/null.png"
         style="width: 160px; position: fixed; top: 920px; right: 200px; opacity: 0; transform: scaleX(-1);">
    <img alt="team2ban2" id="team2ban2" src="images/null.png"
         style="width: 160px; position: fixed; top: 1000px; right: 240px; opacity: 0; transform: scaleX(-1);">

</div>

<script>
    // Animation in JS, fml

    // Timings
    let ban_length = 5000; //5000 ms
    let ban_length_float = 9000; //9000 ms
    let pick_timer = 13000; //13000 ms
    let clear_timer = 9000; //9000 ms
    let start_delay = 36000; //36000 ms

    // Simple opacity animations
    function increase_opacity(id) {
        let variable = setInterval(() => {
            if (document.getElementById(id).style.opacity < 1) {
                document.getElementById(id).style.opacity = parseFloat(document.getElementById(id).style.opacity) + 0.1;
            } else {
                clearInterval(variable);
            }
        }, 1000 / 30)
    }

    function decrease_opacity(id) {
        let variable = setInterval(() => {
            if (document.getElementById(id).style.opacity > 0) {
                document.getElementById(id).style.opacity = parseFloat(document.getElementById(id).style.opacity) - 0.1;
            } else {
                clearInterval(variable);
            }
        }, 1000 / 30)
    }

    // Setting the team names
    // Chungus is special, the team name is too long, so it needs a line break
    document.getElementById("team1name").innerHTML = "<?php if(trim(get_team_info()[0]) == "Big Toxic Chungus Smack That 420") { echo "Big Toxic Chungus <br> Smack That 420"; } else { echo trim(get_team_info()[0]); }  ?>";
    document.getElementById("team2name").innerHTML = "<?php if(trim(get_team_info()[1]) == "Big Toxic Chungus Smack That 420") { echo "Big Toxic Chungus <br> Smack That 420"; } else { echo trim(get_team_info()[1]); }  ?>";

    document.getElementById("team1name").style.top = "<?php if(trim(get_team_info()[0]) == "Big Toxic Chungus Smack That 420") { echo "15px"; } else { echo "35px" ;} ?>"
    document.getElementById("team2name").style.top = "<?php if(trim(get_team_info()[1]) == "Big Toxic Chungus Smack That 420") { echo "15px"; } else { echo "35px" ;} ?>"

    document.getElementById("team1pic").src = "images/logos/" + "<?php echo trim(get_team_info()[0]); ?>" + ".png";
    document.getElementById("team2pic").src = "images/logos/" + "<?php echo trim(get_team_info()[1]); ?>" + ".png";

    let ran = -4;


    let blackness = setInterval(function () {
        if (document.getElementById("start_screen").style.opacity > 0) {
            document.getElementById("start_screen").style.opacity = parseFloat(document.getElementById("start_screen").style.opacity) - 0.05;
        } else {
            clearInterval(blackness);
        }
    }, 1000 / 30);

    // AAAAAND HERE WE GO...
    // We are starting an interval every ban_length_float milliseconds
    let variable = setInterval(() => {
        switch (ran) {
            case -4:
                // We make the bottom bar (where the information about the champion is) visible
                increase_opacity("bottom_bar");
                // We run the sound selector for the appropriate champion
                run_audio("<?php echo ban_sound_selector(trim(get_bans()[0])); ?>");
                // We get first ban, and set it's image to the side
                set_image("<?php echo trim(get_bans()[0]); ?>", "team1ban1");
                // We make that image visible
                increase_opacity("team1ban1");
                // We set the image of the champion (splash) on the center of the screen
                set_image("<?php echo trim(get_bans()[0]); ?>", "current_pick");
                // We draw that image
                draw_image_banned("<?php echo trim(get_bans()[0])?>");
                // Setting the text for the rates
                document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_bans()[0]))?> +"%";
                document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_bans()[0]))?> +"%";
                document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_bans()[0]))?> +"%";
                document.getElementById("bottom_bar").hidden = false;
                // We are starting a timeout for ban_length
                // After it's done, we clear the center of the screen and hide the bottom information bar
                setTimeout(() => {
                    decrease_opacity("bottom_bar");
                    clear_canvas();
                }, ban_length);

                // ... and we do this for the rest of the bans
                break;
            case -3:
                increase_opacity("bottom_bar");
                run_audio("<?php echo ban_sound_selector(trim(get_bans()[2])); ?>");
                set_image("<?php echo trim(get_bans()[2]); ?>", "team2ban1");
                increase_opacity("team2ban1");
                set_image("<?php echo trim(get_bans()[2]); ?>", "current_pick");
                draw_image_banned("<?php echo trim(get_bans()[2])?>");
                document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_bans()[2]))?> +"%";
                document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_bans()[2]))?> +"%";
                document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_bans()[2]))?> +"%";
                document.getElementById("bottom_bar").hidden = false;
                setTimeout(() => {
                    decrease_opacity("bottom_bar");
                    clear_canvas();
                }, ban_length);
                break;
            case -2:
                increase_opacity("bottom_bar");
                run_audio("<?php echo ban_sound_selector(trim(get_bans()[1])); ?>");
                set_image("<?php echo trim(get_bans()[1]); ?>", "team1ban2");
                increase_opacity("team1ban2");
                set_image("<?php echo trim(get_bans()[1]); ?>", "current_pick");
                draw_image_banned("<?php echo trim(get_bans()[1])?>");
                document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_bans()[1]))?> +"%";
                document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_bans()[1]))?> +"%";
                document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_bans()[1]))?> +"%";
                document.getElementById("bottom_bar").hidden = false;
                setTimeout(() => {
                    decrease_opacity("bottom_bar");
                    clear_canvas();
                }, ban_length);
                break;
            case -1:
                increase_opacity("bottom_bar");
                run_audio("<?php echo ban_sound_selector(trim(get_bans()[3])); ?>");
                set_image("<?php echo trim(get_bans()[3]); ?>", "team2ban2");
                increase_opacity("team2ban2");
                set_image("<?php echo trim(get_bans()[3]); ?>", "current_pick");
                draw_image_banned("<?php echo trim(get_bans()[3])?>");
                document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_bans()[3]))?> +"%";
                document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_bans()[3]))?> +"%";
                document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_bans()[3]))?> +"%";
                document.getElementById("bottom_bar").hidden = false;
                setTimeout(() => {
                    decrease_opacity("bottom_bar");
                    clear_canvas();
                }, ban_length);
                clearInterval(variable);
                break;
        }
        ran++;
    }, ban_length_float);
    setTimeout(() => {
        let variable = setInterval(() => {
            // AND NOW, PICKS
            switch (ran) {
                // Team 1
                case 0: //team1pick1
                    // We make the bottom bar (where the information about the champion is) visible
                    increase_opacity("bottom_bar");
                    // We run the sound selector for the appropriate champion
                    run_audio("<?php echo sound_selector(trim(get_player_data(1, "champion"))); ?>");
                    // We get first pick, and set it's image to the side
                    set_image("<?php echo trim(get_player_data(1, "champion")); ?>", "team1pick1");
                    // We make that image visible
                    increase_opacity("team1pick1");
                    // We set the image of the champion (splash) on the center of the screen
                    set_image("<?php echo trim(get_player_data(1, "champion")); ?>", "current_pick");
                    // We draw that image
                    draw_image("<?php echo trim(get_player_data(1, "champion"))?>");
                    // Setting the text for the rates
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(1, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(1, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(1, "champion")))?> +"%";
                    // Setting talents
                    // - Name - English
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(1, "talent"); ?>";
                    // - Name - Hungarian
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(1, "talent")); ?>";
                    // - Icon - For bottom bar
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(1, "champion"), get_player_data(1, "talent")); ?>"
                    // - Icon - For side bar
                    document.getElementById("team1pick1talent").src = "<?php echo set_talent(get_player_data(1, "champion"), get_player_data(1, "talent")); ?>";
                    // - Description
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(1, "champion"), get_player_data(1, "talent")); ?>"
                    // Making the talent bottom bar no longer hidden
                    document.getElementById("bottom_bar").hidden = false;
                    // Preparing next two picks
                    document.getElementById("team2pick1cover").style.opacity = "0.7";
                    document.getElementById("team2pick2cover").style.opacity = "0.7";
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team1pick1talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 3:
                    document.getElementById("team1pick2cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(2, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(2, "champion")); ?>", "team1pick2");
                    increase_opacity("team1pick2");
                    set_image("<?php echo trim(get_player_data(2, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(2, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(2, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(2, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(2, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(2, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(2, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(2, "champion"), get_player_data(2, "talent")); ?>"
                    document.getElementById("team1pick2talent").src = "<?php echo set_talent(get_player_data(2, "champion"), get_player_data(2, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(2, "champion"), get_player_data(2, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team1pick2talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 4:
                    document.getElementById("team1pick3cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(3, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(3, "champion")); ?>", "team1pick3");
                    increase_opacity("team1pick3");
                    set_image("<?php echo trim(get_player_data(3, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(3, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(3, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(3, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(3, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(3, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(3, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(3, "champion"), get_player_data(3, "talent")); ?>"
                    document.getElementById("team1pick3talent").src = "<?php echo set_talent(get_player_data(3, "champion"), get_player_data(3, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(3, "champion"), get_player_data(3, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    document.getElementById("team2pick3cover").style.opacity = "0.7";
                    document.getElementById("team2pick4cover").style.opacity = "0.7";
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team1pick3talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 7:
                    document.getElementById("team1pick4cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(4, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(4, "champion")); ?>", "team1pick4");
                    increase_opacity("team1pick4");
                    set_image("<?php echo trim(get_player_data(4, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(4, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(4, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(4, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(4, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(4, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(4, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(4, "champion"), get_player_data(4, "talent")); ?>"
                    document.getElementById("team1pick4talent").src = "<?php echo set_talent(get_player_data(4, "champion"), get_player_data(4, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(4, "champion"), get_player_data(4, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team1pick4talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 8:
                    document.getElementById("team1pick5cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(5, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(5, "champion")); ?>", "team1pick5");
                    increase_opacity("team1pick5");
                    set_image("<?php echo trim(get_player_data(5, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(5, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(5, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(5, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(5, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(5, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(5, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(5, "champion"), get_player_data(5, "talent")); ?>"
                    document.getElementById("team1pick5talent").src = "<?php echo set_talent(get_player_data(5, "champion"), get_player_data(5, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(5, "champion"), get_player_data(5, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    document.getElementById("team2pick5cover").style.opacity = "0.7";
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team1pick5talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 1:
                    document.getElementById("team2pick1cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(6, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(6, "champion")); ?>", "team2pick1");
                    increase_opacity("team2pick1");
                    set_image("<?php echo trim(get_player_data(6, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(6, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(6, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(6, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(6, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(6, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(6, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(6, "champion"), get_player_data(6, "talent")); ?>"
                    document.getElementById("team2pick1talent").src = "<?php echo set_talent(get_player_data(6, "champion"), get_player_data(6, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(6, "champion"), get_player_data(6, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team2pick1talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 2:
                    document.getElementById("team2pick2cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(7, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(7, "champion")); ?>", "team2pick2");
                    increase_opacity("team2pick2");
                    set_image("<?php echo trim(get_player_data(7, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(7, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(7, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(7, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(7, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(7, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(7, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(7, "champion"), get_player_data(7, "talent")); ?>"
                    document.getElementById("team2pick2talent").src = "<?php echo set_talent(get_player_data(7, "champion"), get_player_data(7, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(7, "champion"), get_player_data(7, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    document.getElementById("team1pick2cover").style.opacity = "0.7";
                    document.getElementById("team1pick3cover").style.opacity = "0.7";
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team2pick2talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 5:
                    document.getElementById("team2pick3cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(8, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(8, "champion")); ?>", "team2pick3");
                    increase_opacity("team2pick3");
                    set_image("<?php echo trim(get_player_data(8, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(8, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(8, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(8, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(8, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(8, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(8, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(8, "champion"), get_player_data(8, "talent")); ?>"
                    document.getElementById("team2pick3talent").src = "<?php echo set_talent(get_player_data(8, "champion"), get_player_data(8, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(8, "champion"), get_player_data(8, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team2pick3talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 6:
                    document.getElementById("team2pick4cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(9, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(9, "champion")); ?>", "team2pick4");
                    increase_opacity("team2pick4");
                    set_image("<?php echo trim(get_player_data(9, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(9, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(9, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(9, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(9, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(9, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(9, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(9, "champion"), get_player_data(9, "talent")); ?>"
                    document.getElementById("team2pick4talent").src = "<?php echo set_talent(get_player_data(9, "champion"), get_player_data(9, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(9, "champion"), get_player_data(9, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    document.getElementById("team1pick4cover").style.opacity = "0.7";
                    document.getElementById("team1pick5cover").style.opacity = "0.7";
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team2pick4talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                case 9:
                    document.getElementById("team2pick5cover").style.opacity = "0";
                    increase_opacity("bottom_bar");
                    run_audio("<?php echo sound_selector(trim(get_player_data(10, "champion"))); ?>");
                    set_image("<?php echo trim(get_player_data(10, "champion")); ?>", "team2pick5");
                    increase_opacity("team2pick5");
                    set_image("<?php echo trim(get_player_data(10, "champion")); ?>", "current_pick");
                    draw_image("<?php echo trim(get_player_data(10, "champion"))?>");
                    document.getElementById("pickrate").innerText = "Választási ráta: " + <?php echo get_champion_pickrate(trim(get_player_data(10, "champion")))?> +"%";
                    document.getElementById("banrate").innerText = "Bannolási ráta: " + <?php echo get_champion_banrate(trim(get_player_data(10, "champion")))?> +"%";
                    document.getElementById("winrate").innerText = "Győzelmi ráta: " + <?php echo get_champion_winrate(trim(get_player_data(10, "champion")))?> +"%";
                    document.getElementById("talent_en").innerText = "<?php echo get_player_data(10, "talent"); ?>";
                    document.getElementById("talent_hu").innerText = "<?php echo get_hungarian_translation(get_player_data(10, "talent")); ?>";
                    document.getElementById("talent_img").src = "<?php echo set_talent(get_player_data(10, "champion"), get_player_data(10, "talent")); ?>"
                    document.getElementById("team2pick5talent").src = "<?php echo set_talent(get_player_data(10, "champion"), get_player_data(10, "talent")); ?>";
                    document.getElementById("talent_desc").innerText = "<?php echo get_talent_desc(get_player_data(10, "champion"), get_player_data(10, "talent")); ?>"
                    document.getElementById("bottom_bar").hidden = false;
                    setTimeout(() => {
                        increase_opacity("talent_box");
                        increase_opacity("team2pick5talent");
                    }, 2500);
                    setTimeout(() => {
                        decrease_opacity("bottom_bar");
                        decrease_opacity("talent_box");
                        clear_canvas();
                    }, clear_timer);
                    break;
                default:
                    console.log("no");
                    break;
            }
            ran++;
            if (ran === 10) {
                clearInterval(variable);
            }
        }, pick_timer);
    }, start_delay);

    // Run audio of champion
    function run_audio(champ) {
        document.getElementById("sound").pause();
        document.getElementById("sound2").src = champ;
        document.getElementById("sound").currentTime = 0;
        document.getElementById("sound").load();
        document.getElementById("sound").play();
    }

    // Set Image to the index
    function set_image(champ, index) {
        document.getElementById(index).src = "images/champions/" + champ + ".png";
    }

    // Set splash to the index
    function set_splash(champ, index) {
        document.getElementById(index).src = "images/splashes/" + champ + ".png";
    }

    // We can go to the stats screen by pressing S
    function goto_stats(e) {
        var keynum;

        if(window.event) { // IE
            keynum = e.keyCode;
        } else if(e.which){ // Netscape/Firefox/Opera
            keynum = e.which;
        }

        if (String.fromCharCode(keynum) === "S") {
            window.location = "stats.php";
        }
    }
</script>

</body>
</html>