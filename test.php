<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php $handle = file_get_contents("data/talents.json");
$talent_info = json_decode($handle, true);

foreach ($talent_info["Androxus"] as $talents) {
    echo $talents["Defiant Fist"];
}
?>

</body>
<script>

</script>
</html>