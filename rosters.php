<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MPESZ Drafting</title>
</head>
<?php
include "db_connect.php";

// We read the output match file from PyRez and deserialize json into dictionary
$handle = file_get_contents("PaladinsAPIStuff/output_match.json");
$match_info = json_decode($handle);

// We get the map key's value from dictionary
foreach ($match_info as $key => $value) {
    if ($key == "map") {
        $map = $value;
    }
}

/**
 * @return array - the team names without extra whitespaces in an array
 */
function get_team_info()
{
    $file = fopen("data/teamnames.txt", "r");
    $teamnames = [];
    $teamnamesClean = [];
    while (!feof($file)) {
        array_push($teamnames, fgets($file));
    }
    foreach ($teamnames as $tm) {
        array_push($teamnamesClean, trim($tm));
    }
    return $teamnamesClean;
}

/**
 * @param $team - the team name
 * @param $role - the role we want
 * @return mixed|null - the person playing a specific role
 *
 * We get the player of a team on a specific role
 * For example:
 * get_roster_player("Circus of the Damned", "Frontline") => "Vanitaes"
 */
function get_roster_player($team, $role)
{
    // We read the rosters file and deserialize json into dictionary
    $handle = file_get_contents("data/rosters.json");
    $role_info = json_decode($handle, true);

    foreach ($role_info[$team] as $roles) {
        return $roles[$role];
    }

    return null;
}

/**
 * @param $stat - the stat we want the average of
 * @param $player - the player we want the average stat of
 * @return float|int - returns the given stats average
 *
 * Used for getting stats that require an average calculation
 * For example:
 * get_player_avg_stat("damage", "Vanitaes") => 51265.71
 */

function get_player_avg_stat($stat, $player) {
    // Opening a db connection instance
    $conn = OpenCon();
    // Querying the stat and the amount of played matches, refer to the database for stat names
    $sql = "SELECT $stat AS stat, played FROM player_data WHERE player_name = '$player'";
    $result = $conn->query($sql);
    $result = mysqli_fetch_array($result);
    // If the person hasn't played before
    // Add to Database
    if ($result["stat"] == null) {
        $sql = "INSERT INTO player_data(player_name) VALUES ('$player')";
        mysqli_query($conn, $sql);
        $sql = "SELECT $stat, played FROM player_data WHERE player_name = '$player'";
        $result = $conn->query($sql);
        $result = mysqli_fetch_array($result);
    }
    // If the person hasn't played before, no reason to calculate stat, it's 0
    if (intval($result["played"]) == 0) {
        return 0;
    }
    // Calculating the average stat
    return round($result["stat"] / $result["played"], 2);
}

/**
 * @param $stat - the stat we want
 * @param $player - the player we want the stat of
 * @return mixed - the stat flat out
 *
 * Used for getting stats that don't require any calculation
 * For Example:
 * get_player_absolute_stat("kda", "Vanitaes") => 1.95
 */
function get_player_absolute_stat($stat, $player) {
    $conn = OpenCon();
    $sql = "SELECT $stat AS stat FROM player_data WHERE player_name = '$player'";
    $result = $conn->query($sql);
    $result = mysqli_fetch_array($result);
    return $result["stat"];
}

?>
<body style="background-image: url('<?php echo "images/rosterBG/" . $map .  ".png" ?>'); margin: 0; padding: 0; height: 100%; overflow: hidden;">

<div id="start_screen" style="position:fixed; height: 100vh; width: 100vw; background-color: black; opacity: 1;"></div>

<!-- Here I have created 10 divs. These contain all the information about a player.
    The information is echoed with the help of the functions above.
 -->

<div id="player1"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 42px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Frontline.png" alt="frontline"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[0], "Frontline"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Frontline</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[0], "Frontline")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[0], "Frontline")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[0], "Frontline")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[0], "Frontline")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[0], "Frontline")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[0], "Frontline")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams frontline, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[0]) == get_roster_player(get_team_info()[0], "Frontline")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player2"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 418px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Damage.png" alt="damage"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[0], "Damage"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Damage</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[0], "Damage")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[0], "Damage")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[0], "Damage")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[0], "Damage")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[0], "Damage")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[0], "Damage")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams damage, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[0]) == get_roster_player(get_team_info()[0], "Damage")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player3"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 790px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Support.png" alt="support"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[0], "Support"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Support</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[0], "Support")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[0], "Support")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[0], "Support")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[0], "Support")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[0], "Support")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[0], "Support")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams support, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[0]) == get_roster_player(get_team_info()[0], "Support")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player4"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 1162px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Flex.png" alt="flex1"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[0], "Flex1"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Flex</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[0], "Flex1")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[0], "Flex1")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[0], "Flex1")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[0], "Flex1")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[0], "Flex1")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[0], "Flex1")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams flex1, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[0]) == get_roster_player(get_team_info()[0], "Flex1")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player5"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 1530px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Flex.png" alt="flex2"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[0], "Flex2"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Flex</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[0], "Flex2")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[0], "Flex2")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[0], "Flex2")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[0], "Flex2")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[0], "Flex2")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[0], "Flex2")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams flex2, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[0]) == get_roster_player(get_team_info()[0], "Flex2")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player6"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 42px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Frontline.png" alt="frontline"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[1], "Frontline"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Frontline</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[1], "Frontline")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[1], "Frontline")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[1], "Frontline")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[1], "Frontline")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[1], "Frontline")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[1], "Frontline")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams frontline, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[1]) == get_roster_player(get_team_info()[1], "Frontline")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player7"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 418px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Damage.png" alt="damage"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[1], "Damage"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Damage</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[1], "Damage")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[1], "Damage")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[1], "Damage")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[1], "Damage")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[1], "Damage")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[1], "Damage")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams damage, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[1]) == get_roster_player(get_team_info()[1], "Damage")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player8"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 790px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Support.png" alt="support"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[1], "Support"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Support</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[1], "Support")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[1], "Support")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[1], "Support")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[1], "Support")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[1], "Support")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[1], "Support")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams support, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[1]) == get_roster_player(get_team_info()[1], "Support")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player9"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 1162px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Flex.png" alt="flex1"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[1], "Flex1"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Flex</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[1], "Flex1")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[1], "Flex1")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[1], "Flex1")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[1], "Flex1")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[1], "Flex1")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[1], "Flex1")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams flex1, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[1]) == get_roster_player(get_team_info()[1], "Flex1")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px;'>";
    }
    ?>
</div>
<div id="player10"
     style="background-color: rgba(0, 0, 0, 0.2); position:absolute; left: 1530px; top: 304px; width: 340px; height: 740px; z-index: 50; opacity: 0;">
    <img src="images/rosterIcons/Flex.png" alt="flex2"
         style="position:relative; width: 100px; left: calc(340px / 2 - 100px / 2); top: -50px;">
    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; color: white;">
        <h2><?php echo get_roster_player(get_team_info()[1], "Flex2"); ?></h2><br>
        <h3 style="margin: 0 0 50px;">Flex</h3>
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">K/D/A arány:</h3>
            <h4 style="margin: 0;"><?php echo get_player_absolute_stat("kda", get_roster_player(get_team_info()[1], "Flex2")); ?></h4>
        </div>

        <div style="margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("damage", get_roster_player(get_team_info()[1], "Flex2")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Gyógyítás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("healing", get_roster_player(get_team_info()[1], "Flex2")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Elszenvedett sebzés átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("mitigated", get_roster_player(get_team_info()[1], "Flex2")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Pajzsolás átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("shielding", get_roster_player(get_team_info()[1], "Flex2")); ?></h4>
        </div>

        <div style=" margin-top: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h3 style="margin: 0;">Obj. átlag:</h3>
            <h4 style="margin: 0;"><?php echo get_player_avg_stat("ot", get_roster_player(get_team_info()[1], "Flex2")); ?></h4>
        </div>
    </div>
    <?php
    // We have a team in rosters called "Key". It contains all the CSK's.
    // If the CSK from the Key team is equal to the teams flex2, we put a graphic there.
    if (get_roster_player("Key", get_team_info()[1]) == get_roster_player(get_team_info()[1], "Flex2")) {
        echo "<img src='images/rosterIcons/Key.png' alt='key' style='position:relative; width: 50px; left: calc(340px / 2 - 50px / 2); top: 25px; top: 25px;'>";
    }
    ?>
</div>
<!-- Echoing team names, and map backgrounds -->
<img id="blurred_bg" src="<?php echo 'images/rosterBG/' . $map . '_blur.png' ?>" alt="bg_blur"
     style="position:relative; margin: 0; padding: 0; opacity: 0; z-index: 0;">
<div id="team_name"
     style="position:absolute; width: 600px; display: flex; justify-content: center; align-items: center; left: -610px; top: 10px; background-color: rgba(0, 0, 0, 0.3); color: white; padding-right: 10px;">
    <img style="width: 160px;" src="images/logos/<?php echo get_team_info()[0] . ".png" ?>">
    <span style="flex: 1 1;"></span>
    <h2><?php echo get_team_info()[0]; ?></h2>
</div>
<div id="team_name2"
     style="position:absolute; width: 600px; display: flex; justify-content: center; align-items: center; right: -610px; top: 10px; background-color: rgba(0, 0, 0, 0.3); color: white; padding-right: 10px;">
    <h2><?php echo get_team_info()[1]; ?></h2>
    <span style="flex: 1 1;"></span>
    <img style="width: 160px;" src="images/logos/<?php echo get_team_info()[1] . ".png" ?>">
</div>
</body>

<script>
    // Animation in JS... YAY!

    // Immediately after the page loads, we hide the black background on top of the page
    let blackness = setInterval(function () {
        if (document.getElementById("start_screen").style.opacity > 0) {
            document.getElementById("start_screen").style.opacity = parseFloat(document.getElementById("start_screen").style.opacity) - 0.05;
        } else {
            clearInterval(blackness);
        }
    }, 1000 / 30);

    let team1PlayerIds = ["player1", "player2", "player3", "player4", "player5"];
    let team2PlayerIds = ["player6", "player7", "player8", "player9", "player10"];

    // Setting some timeouts
    // If you want to mess with timing, here is where you do it
    setTimeout(function () {
        increase_opacity("blurred_bg");
        slide_in_from_left("team_name");
        increase_team1_opacities();
    }, 1500);
    setTimeout(function () {
        slide_out_from_left("team_name");
        decrease_team1_opacities();
    }, 8000);
    setTimeout(function () {
        slide_in_from_right("team_name2");
        increase_team2_opacities();
    }, 9000);
    setTimeout(function () {
        slide_out_from_right("team_name2");
        decrease_team2_opacities();
    }, 15500);
    setTimeout(function () {
        window.location = "main_frame.php";
    }, 16500);

    /**
     * @param id - the element id
     *
     * Increases opacity the given element until it is fully visible
     */
    function increase_opacity(id) {
        let variable = setInterval(() => {
            if (document.getElementById(id).style.opacity < 1) {
                document.getElementById(id).style.opacity = parseFloat(document.getElementById(id).style.opacity) + 0.1;
            } else {
                clearInterval(variable);
            }
        }, 1000 / 30)
    }

    /*
    We increase and decrease opacity
     */
    function increase_team1_opacities() {
        let variable = setInterval(() => {
            team1PlayerIds.forEach(function (id) {
                if (document.getElementById(id).style.opacity < 1) {
                    document.getElementById(id).style.opacity = parseFloat(document.getElementById(id).style.opacity) + 0.1;
                } else {
                    clearInterval(variable);
                }
            });
        }, 1000 / 30)
    }

    function decrease_team1_opacities() {
        let variable = setInterval(() => {
            team1PlayerIds.forEach(function (id) {
                if (document.getElementById(id).style.opacity > 0) {
                    document.getElementById(id).style.opacity = parseFloat(document.getElementById(id).style.opacity) - 0.1;
                } else {
                    clearInterval(variable);
                }
            });
        }, 1000 / 30)
    }

    function increase_team2_opacities() {
        let variable = setInterval(() => {
            team2PlayerIds.forEach(function (id) {
                if (document.getElementById(id).style.opacity < 1) {
                    document.getElementById(id).style.opacity = parseFloat(document.getElementById(id).style.opacity) + 0.1;
                } else {
                    clearInterval(variable);
                }
            });
        }, 1000 / 30)
    }

    function decrease_team2_opacities() {
        let variable = setInterval(() => {
            team2PlayerIds.forEach(function (id) {
                if (document.getElementById(id).style.opacity > 0) {
                    document.getElementById(id).style.opacity = parseFloat(document.getElementById(id).style.opacity) - 0.1;
                } else {
                    clearInterval(variable);
                }
            });
        }, 1000 / 30)
    }


    /*
     * We do a simple slide in - slide out animation
     */
    function slide_in_from_left(id) {
        let variable = setInterval(() => {
            if (document.getElementById(id).style.left !== '0px') {
                document.getElementById(id).style.left = parseFloat(document.getElementById(id).style.left) + 10 + "px";
            } else {
                clearInterval(variable);
            }
        }, 1000 / 75)
    }

    function slide_out_from_left(id) {
        let variable = setInterval(() => {
            console.log(document.getElementById(id).style.left);
            if (document.getElementById(id).style.left !== '-610px') {
                document.getElementById(id).style.left = parseFloat(document.getElementById(id).style.left) - 10 + "px";
            } else {
                clearInterval(variable);
            }
        }, 1000 / 75)
    }

    function slide_in_from_right(id) {
        let variable = setInterval(() => {
            console.log(document.getElementById(id).style.right);
            if (document.getElementById(id).style.right !== '0px') {
                document.getElementById(id).style.right = parseFloat(document.getElementById(id).style.right) + 10 + "px";
            } else {
                clearInterval(variable);
            }
        }, 1000 / 75)
    }

    function slide_out_from_right(id) {
        let variable = setInterval(() => {
            console.log(document.getElementById(id).style.right);
            if (document.getElementById(id).style.right !== '-610px') {
                document.getElementById(id).style.right = parseFloat(document.getElementById(id).style.right) - 10 + "px";
            } else {
                clearInterval(variable);
            }
        }, 1000 / 75)
    }
</script>
</html>