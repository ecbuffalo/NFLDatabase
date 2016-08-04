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

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <form method="post" action="addCoachInfo.php">
                    <legend>Coach Team History</legend>
                    <p>Coach:
                        <select name="CoachID" >
                            <?php
                            $sql = "SELECT id,first_name,last_name FROM coach";
                            if(!($stmt = $mysqli->prepare($sql))){
                                echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                            }
                            if(!$stmt->execute()){
                                echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
                            }
                            if(!$stmt->bind_result($coach_id,$first_name,$last_name)){
                                echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                            }
                            while($stmt->fetch()){
                                if(isset($_GET['CoachID'])){
                                    if($_GET['CoachID'] == $coach_id){
                                        echo '<option selected="selected" value=" '. $coach_id . ' "> ' . $first_name . " " .$last_name . '</option>\n';
                                    }else{
                                        echo '<option value=" '. $coach_id . ' "> ' . $first_name . " " .$last_name . '</option>\n';
                                    }
                                }else{
                                    echo '<option value=" '. $coach_id . ' "> ' . $first_name . " " .$last_name . '</option>\n';
                                }
                            }
                            $stmt->close();
                            ?>
                        </select>
                    </p>
                    <p>Team:
                        <select name="TeamID">
                            <?php
                            $teams = array();
                            $sql = "SELECT id,name,region_name FROM team";
                            if(!($stmt = $mysqli->prepare($sql))){
                                echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
                            }
                            if(!$stmt->execute()){
                                echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
                            }
                            if(!$stmt->bind_result($team_id,$team_name,$team_region)){
                                echo "Bind failed: "  . $mysqli->connect_errno . " " . $mysqli->connect_error;
                            }
                            while($stmt->fetch()){
                                $teams[$team_id] = $team_region . " " . $team_name;
                            }
                            $stmt->close();

                            if (count($teams) == 0){
                                echo "</select><h1>No Teams in Database! Add at least one first...</h1>";
                                return;
                            }else{
                                foreach ($teams as $t => $t_value){
                                    echo "<option value=\"" . $t . "\">" .$t_value . "</li>";
                                }
                            }
                            ?>
                        </select>
                    </p>
                    <p>Job Title:<input type="text" name="JobTitle" value="Head Coach"/></p>
                    <p>Start Date Year<input type="number" name="StartYear" min="1900" value="2016"/></p>
                    <p>Start Date Month<input type="number" name="StartMonth" min="1" max="12" value="1"/></p>
                    <p>Start Date Day<input type="number" name="StartDay" min="1" max="31" value="1"/></p>
                    <p>End Date Year<input type="number" name="EndYear" min="1900"/></p>
                    <p>End Date Month<input type="number" name="EndYear" min="1" max="12"/></p>
                    <p>End Date Day<input type="number" name="EndYear" min="1" max="31"/></p>
                    <p><input type="submit"/></p>

                </form>
            </div>
        </div>
></div>
        <?php
        if(isset($_POST['CoachID']) && isset($_POST['TeamID'])){
            echo "Coach ID: " . $_POST['CoachID'];
            echo "Team ID: " . $_POST['TeamID'];
            $sql="INSERT INTO coached_for(coach_id, team_id, start_date, end_date, job_title) VALUES (?,?,?,?,?)";
            if(!($stmt = $mysqli->prepare($sql))){
                echo "Prepare failed: "  . $stmt->errno . " " . $stmt->error;
            }
            $StartDate = $_POST['StartYear'] . $_POST['StartMonth'] . $_POST['StartDay'] . " 00:00:00";
            $EndDate = "";
            if($_POST['EndYear'] != ""){
                $EndDate = $_POST['EndYear'] . $_POST['EndMonth'] . $_POST['EndDay'] . " 00:00:00";
            }
            if(!($stmt->bind_param("iisss",$_POST['CoachID'],$_POST['TeamID'],$StartDate,$EndDate,$_POST['JobTitle']))){
                echo "Bind failed: "  . $stmt->errno . " " . $stmt->error;
            }
            /*
            if(!$stmt->execute()){
                echo "Execute failed: "  . $stmt->errno . " " . $stmt->error;
            } else {
                echo "Successfully added to Coach History table.<br/>";
            }*/
            $stmt->close();
        }

        ?>



</div>
</body>
</html>