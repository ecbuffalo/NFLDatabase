<?php
//Turn on error reporting
ini_set('display_errors', 'On');
//Connects to the database
$mysqli = new mysqli("oniddb.cws.oregonstate.edu","fjerstam-db","C9VOP9KTCMKBpICU","fjerstam-db");
if(!$mysqli || $mysqli->connect_errno){
    echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title>NFL Database</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>


<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php">NFL Database</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="player.php">Players</a></li>
            <li><a href="coach.php">Coaches</a></li>
            <li><a href="team.php">Teams</a></li>
        </ul>
    </div>
</nav>

<?php
if(!($stmt = $mysqli->prepare("DELETE FROM player WHERE id = ?"))){
    echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
}

if(!($stmt->bind_param("i",$_POST['PlayerID']))){
    echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
}
if(!$stmt->execute()){
    echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
} else {
    echo "Successfully Removed " . $_POST['FirstName'] . " " . $_POST['LastName'] . " from player table.";
}
?>

</body>
</html>