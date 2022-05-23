<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPESZ Drafting</title>
    <link rel="stylesheet" href="css/style.css">
    <?php include "db_connect.php";
    ?>
</head>

<?php


// Getting team names into array
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
    $kda_data = explode('/', $data[2]);
    foreach ($kda_data as $d) {
        array_push($data, $d);
    }
    $data = ["name" => $data[0], "champion" => $data[1], "kda" => $data[2], "damage" => $data[3], "damage_taken" => $data[4],
        "healing" => $data[5], "shielding" => $data[6], "ot" => $data[7], "talent" => $data[8], "kills" => $data[10], "deaths" => $data[11],
        "assists" => $data[12]];

    return $data[$data_to_get];
}

// Fixing names -> This is required when the player has a letter in their name with unicode characters like á or ú
function fix_name($name, $team) {
    $new_name = "";
    if (str_contains($name, "\u00e1")) {
        $new_name = str_replace("\u00e1", "á", $name);
    } else {
        $new_name = $name;
    }
    if ($new_name == "" && $team == "Epsilon") {
        $new_name = "loneybee"; // I think this happened because the player has hidden their user or something... The player's name came out blank. This was a temporary fix
    }
    return $new_name;
}

// Making numbers easier to read
function prettify_number($number): string
{
    if (strlen($number) < 4) {
        return $number;
    } else {
        $str_to_ret = "";
        $number_of_read_values = 0;
        for ($i = strlen($number) - 1; $i >= 0; --$i) {
            $str_to_ret = $str_to_ret . $number[$i];
            $number_of_read_values += 1;
            if ($number_of_read_values == 3) {
                $number_of_read_values = 0;
                $str_to_ret .= " ";
            }
        }
        return strrev($str_to_ret);
    }
}

// Get match length
function get_length()
{
    $handle = file_get_contents("PaladinsAPIStuff/output_match.json");
    $match_info = json_decode($handle);

    foreach ($match_info as $key => $value) {
        if ($key == "match_length") {
            $length = $value;
        }
    }
    return $length;
}

// Get match result
function get_outcome()
{
    $handle = file_get_contents("PaladinsAPIStuff/output_match.json");
    $match_info = json_decode($handle);

    foreach ($match_info as $key => $value) {
        if ($key == "team1score") {
            $team1score = $value;
        }
        if ($key == "team2score") {
            $team2score = $value;
        }
    }
    return $team1score . " - " . $team2score;
}

// Get the bans
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

function get_outcome_values()
{
    $handle = file_get_contents("PaladinsAPIStuff/output_match.json");
    $match_info = json_decode($handle);

    foreach ($match_info as $key => $value) {
        if ($key == "team1score") {
            $team1score = $value;
        }
        if ($key == "team2score") {
            $team2score = $value;
        }
    }
    return [$team1score, $team2score];
}

// Update a champion by the new values (picked, won, banned)
function update_champion() {
    $conn = OpenCon();
    for ($i = 0; $i < 10; $i++) {
        $champion = get_player_data($i + 1, 'champion');
        $sql = "SELECT picked FROM champion_data WHERE champion_name = '$champion'";
        $result = $conn->query($sql);
        $result = mysqli_fetch_array($result);
        $picked = intval($result["picked"]) + 1;
        $sql = "UPDATE champion_data SET picked = '$picked' WHERE champion_name = '$champion'";
        mysqli_query($conn, $sql);

        $outcome = get_outcome_values();
        if ($outcome[0] > $outcome[1] && $i < 5) {
            $sql = "SELECT won FROM champion_data WHERE champion_name = '$champion'";
            $result = $conn->query($sql);
            $result = mysqli_fetch_array($result);
            $won = intval($result["won"]) + 1;
            $sql = "UPDATE champion_data SET won = '$won' WHERE champion_name = '$champion'";
            mysqli_query($conn, $sql);
        } elseif ($outcome[0] < $outcome[1] && $i >= 5) {
            $sql = "SELECT won FROM champion_data WHERE champion_name = '$champion'";
            $result = $conn->query($sql);
            $result = mysqli_fetch_array($result);
            $won = intval($result["won"]) + 1;
            $sql = "UPDATE champion_data SET won = '$won' WHERE champion_name = '$champion'";
            mysqli_query($conn, $sql);
        }

    }
    $bans = get_bans();
    foreach ($bans as $b) {
        $sql = "SELECT banned FROM champion_data WHERE champion_name = '$b'";
        $result = $conn->query($sql);
        $result = mysqli_fetch_array($result);
        $banned = intval($result["banned"]) + 1;
        $sql = "UPDATE champion_data SET banned = '$banned' WHERE champion_name = '$b'";
        mysqli_query($conn, $sql);
    }

    $sql = "SELECT match_number FROM played_matches";
    $result = $conn->query($sql);
    $result = mysqli_fetch_array($result);
    $played = intval($result["match_number"]) + 1;
    $sql = "UPDATE played_matches SET match_number = '$played' WHERE id = 0";
    mysqli_query($conn, $sql);

}

?>

<style>
    @import url('https://fonts.cdnfonts.com/css/lucida-sans');

    th, td {
        width: 290px;
    }
</style>

<body style="margin-left: 50px;" onkeydown="return goto_stats(event);">
<div style="position:relative; height: 56px;">
<?php
    if (get_outcome_values()[0] > get_outcome_values()[1]) {
        echo '<span style="display: flex; flex-flow: row nowrap; align-items: center; justify-content: center;"><img style="height: 56px;" src="images/logos/'.get_team_info()[0].'.png"><h2 style="font-family: ' . "Lucida Sans" . ', sans-serif; color: white;">'.get_team_info()[0].' nyert!</h2><img style="height: 56px;" src="images/logos/'.get_team_info()[0].'.png"></span>';
    } else {
        echo '<span style="display: flex; flex-flow: row nowrap; align-items: center; justify-content: center;"><img style="height: 56px;" src="images/logos/'.get_team_info()[1].'.png"><h2 style="font-family: ' . "Lucida Sans" . ', sans-serif; color: white;">'.get_team_info()[1].' nyert!</h2><img style="height: 56px;" src="images/logos/'.get_team_info()[1].'.png"></span>';
    }
?>
</div>
<h3 style="font-family: 'Lucida Sans', sans-serif; color: white;">Meccsidő: <?php echo get_length(); ?></h3>
<h3 style="font-family: 'Lucida Sans', sans-serif; color: white;">Végeredmény: <?php echo get_outcome(); ?></h3>

<table style="display: flex; flex-flow: row wrap; justify-content: space-between; color: white; font-family: 'Lucida Sans', sans-serif; font-size: 14pt; text-align: center; width: 100%">
    <tr style="font-size: 16pt;">
        <th></th>
        <th>Név</th>
        <th>K/D/A</th>
        <th>Sebzés</th>
        <th>Elszenvedett Sebzés</th>
        <th>Gyógyítás</th>
        <th>Pajzs</th>
        <th>Obj.</th>
    </tr>
    <br>
    <?php
    // Idk some object oriented shit cuz I felt smart
    class Player
    {
        private $name;
        private $kda;
        private $damage;
        private $healing;
        private $shielding;
        private $mitigated;
        private $ot;
        private $played;
        private $kills;
        private $deaths;
        private $assists;
        private $team;

        public function __construct($name, $kda, $damage, $healing, $shielding, $mitigated, $ot, $played, $kills, $deaths, $assists, $team)
        {
            $this->name = $name;
            $this->kda = $kda;
            $this->damage = $damage;
            $this->healing = $healing;
            $this->shielding = $shielding;
            $this->mitigated = $mitigated;
            $this->ot = $ot;
            $this->played = $played;
            $this->kills = $kills;
            $this->deaths = $deaths;
            $this->assists = $assists;
            $this->team = $team;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getKda()
        {
            return $this->kda;
        }

        public function getDamage()
        {
            return $this->damage;
        }

        public function getHealing()
        {
            return $this->healing;
        }

        public function getShielding()
        {
            return $this->shielding;
        }

        public function getMitigated()
        {
            return $this->mitigated;
        }

        public function getOt()
        {
            return $this->ot;
        }

        private function addPlayers(Player $player): Player {
            return new Player(
                    $this->name,
                    0,
                    floatval($this->damage) + floatval($player->damage),
                    floatval($this->healing) + floatval($player->healing),
                    floatval($this->shielding) + floatval($player->shielding),
                    floatval($this->mitigated) + floatval($player->mitigated),
                    floatval($this->ot) + floatval($player->ot),
                    intval($this->played) + intval($player->played),
                    intval($this->kills) + intval($player->kills),
                    intval($this->deaths) + intval($player->deaths),
                    intval($this->assists) + intval($player->assists),
                "some team"
            );
        }

        public function echoPlayer(Player $player) {
            echo $player->name . " " . $player->kda . " " . $player->damage . " " . $player->healing . " " .
                $player->shielding . " " . $player->mitigated . " " . $player->ot . " " . $player->played .
                " " . $player->kills . " " . $player->deaths . " " . $player->assists;
        }

        public function getAllPlayerNames() {
            $conn = OpenCon();
            $sql = "SELECT player_name FROM player_data";
            $result = $conn->query($sql);
            $result = mysqli_fetch_array($result);
            CloseCon($conn);
            return $result;
        }

        // Updates player in database by the values from the match
        public function updatePlayer()
        {
            $name = fix_name($this->name, "some team");
            $conn = OpenCon();
            foreach ($this->getAllPlayerNames() as $p) {
                if ($p == $this->name) {
                    break;
                } else {
                    $sql = "INSERT INTO player_data(player_name) VALUES ('$this->name')";
                    mysqli_query($conn, $sql);
                }
            }

            $sql = "SELECT kda, damage, healing, shielding, mitigated, ot, played, kills, deaths, assists FROM player_data WHERE player_name = '$name'";
            $result = $conn->query($sql);
            $result = mysqli_fetch_array($result);

            $currentPlayer = new Player($this->name, $result["kda"], $result["damage"], $result["healing"],
                $result["shielding"], $result["mitigated"], $result["ot"], $result["played"], $result["kills"], $result["deaths"], $result["assists"], "some_team");
            //$this->echoPlayer($currentPlayer);

            $new_player = $this->addPlayers($currentPlayer);
            if ($new_player->deaths == 0) {
                $new_player->kda = 'INF';
            } else {
                $new_player->kda = ($new_player->kills + ($new_player->assists / 3)) / $new_player->deaths;
                $new_player->kda = round($new_player->kda, 2);
            }
            //$this->echoPlayer($new_player);
            $sql = "UPDATE player_data SET kda = '$new_player->kda', damage = '$new_player->damage', healing = '$new_player->healing',
                    shielding = '$new_player->shielding', mitigated = '$new_player->mitigated', ot = '$new_player->ot', played = '$new_player->played',
                    kills = '$new_player->kills', deaths = '$new_player->deaths', assists = '$new_player->assists'
                    WHERE player_name = '$name'";
            mysqli_query($conn, $sql);
            CloseCon($conn);
        }

    }

    // Generates Team 1 into Player objects and pushing them into an array
    $players = [];

    for ($i = 0; $i < 5; $i++) {
        $player = new Player(
            fix_name(get_player_data($i + 1, 'name'), get_team_info()[0]),
            get_player_data($i + 1, 'kda'),
            get_player_data($i + 1, 'damage'),
            get_player_data($i + 1, 'healing'),
            get_player_data($i + 1, 'shielding'),
            get_player_data($i + 1, 'damage_taken'),
            get_player_data($i + 1, 'ot'),
            1,
            get_player_data($i + 1, 'kills'),
            get_player_data($i + 1, 'deaths'),
            get_player_data($i + 1, 'assists'),
            get_team_info()[0]
        );

        array_push($players, $player);

        // This is only required because I wanted different background colors for every other player
        // Could've done it smarter, but I don't like breaking up echoes, leads to errors
        if ($i % 2 == 0) {
            echo '<tr style="background-color: rgba(0, 0, 0, 0.4)">
            <td style="display: flex; justify-content: center; align-items: center; width: 216px;"><img style="height: 72px;" src="images/champions/' . get_player_data($i + 1, 'champion') . '.png"></td>           
            <td>' . $player->getName() . '</td>
            <td>' . $player->getKda() . '</td>
            <td>' . prettify_number($player->getDamage()) . '</td>
            <td>' . prettify_number($player->getMitigated()) . '</td>
            <td>' . prettify_number($player->getHealing()) . '</td>
            <td>' . prettify_number($player->getShielding()) . '</td>
            <td>' . $player->getOt() . '</td>
        </tr>';
        } else {
            echo '<tr style="background-color: rgba(0, 0, 0, 0.2)">
            <td style="display: flex; justify-content: center; align-items: center; width: 216px;"><img style="height: 72px;" src="images/champions/' . get_player_data($i + 1, 'champion') . '.png"></td>           
            <td>' . $player->getName() . '</td>
            <td>' . $player->getKda() . '</td>
            <td>' . prettify_number($player->getDamage()) . '</td>
            <td>' . prettify_number($player->getMitigated()) . '</td>
            <td>' . prettify_number($player->getHealing()) . '</td>
            <td>' . prettify_number($player->getShielding()) . '</td>
            <td>' . $player->getOt() . '</td>
        </tr>';
        }
    }
    ?>
</table>
<hr>
<table style="display: flex; flex-flow: row wrap; justify-content: space-between; color: white; font-family: 'Lucida Sans', sans-serif; font-size: 14pt; text-align: center; width: 100%">
    <tr style="opacity: 0;">
        <th></th>
        <th>Név</th>
        <th>K/D/A</th>
        <th>Sebzés</th>
        <th>Elszenvedett Sebzés</th>
        <th>Gyógyítás</th>
        <th>Pajzs</th>
        <th>Obj.</th>
    </tr>
    <?php

    // Generates Team 2 into Player objects and pushing them into an array
    for ($i = 0; $i < 5; $i++) {
        $player = new Player(
            fix_name(get_player_data(5 + $i + 1, 'name'), get_team_info()[1]),
            get_player_data(5 + $i + 1, 'kda'),
            get_player_data(5 + $i + 1, 'damage'),
            get_player_data(5 + $i + 1, 'healing'),
            get_player_data(5 + $i + 1, 'shielding'),
            get_player_data(5 + $i + 1, 'damage_taken'),
            get_player_data(5 + $i + 1, 'ot'),
            1,
            get_player_data(5 + $i + 1, 'kills'),
            get_player_data(5 + $i + 1, 'deaths'),
            get_player_data(5 + $i + 1, 'assists'),
            get_team_info()[1]
        );

        array_push($players, $player);

        if ($i % 2 == 0) {
            echo '<tr style="background-color: rgba(0, 0, 0, 0.4)">
            <td style="display: flex; justify-content: center; align-items: center; width: 216px;"><img style="height: 72px;" src="images/champions/' . get_player_data(5 + $i + 1, 'champion') . '.png"></td>
            <td>' . $player->getName() . '</td>
            <td>' . $player->getKda() . '</td>
            <td>' . prettify_number($player->getDamage()) . '</td>
            <td>' . prettify_number($player->getMitigated()) . '</td>
            <td>' . prettify_number($player->getHealing()) . '</td>
            <td>' . prettify_number($player->getShielding()) . '</td>
            <td>' . $player->getOt() . '</td>
        </tr>';
        } else {
            echo '<tr style="background-color: rgba(0, 0, 0, 0.2)">
            <td style="display: flex; justify-content: center; align-items: center; width: 216px;"><img style="height: 72px;" src="images/champions/' . get_player_data(5 + $i + 1, 'champion') . '.png"></td>
            <td>' . $player->getName() . '</td>
            <td>' . $player->getKda() . '</td>
            <td>' . prettify_number($player->getDamage()) . '</td>
            <td>' . prettify_number($player->getMitigated()) . '</td>
            <td>' . prettify_number($player->getHealing()) . '</td>
            <td>' . prettify_number($player->getShielding()) . '</td>
            <td>' . $player->getOt() . '</td>
        </tr>';
        }
    }
    $debug_mode = false;

    // Update the database - save tables into backup
    if (isset($_GET["update"]) && !$debug_mode) {
        SaveDatabase(OpenCon(), "player_data");
        SaveDatabase(OpenCon(), "champion_data");
        SaveDatabase(OpenCon(), "played_matches");

        foreach ($players as $p) {
            $p->updatePlayer();
        }
        update_champion();

        echo "<script>window.location = 'stats.php';</script>";
    }
    ?>
</table>
<script>
    // On Pressing X, update database
    function goto_stats(e) {
        var keynum;

        if(window.event) { // IE
            keynum = e.keyCode;
        } else if(e.which){ // Netscape/Firefox/Opera
            keynum = e.which;
        }

        if (String.fromCharCode(keynum) === "X") {
            window.location = "stats.php?update=abc";
        }
    }
</script>
</body>
</html>