<?php
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	array_walk(glob('./engine/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/weapons/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/tactical/*.php'), create_function('$v,$i', 'return require_once($v);'));
	session_start();
	
	
	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
	}
	
	if (isset($_GET["leaveslot"])){
		Manager::leaveLobbySlot($_SESSION["user"]);
		header('Location: games.php');
	}
	
	
	$gameid = null;
	
	if (isset($_GET["gameid"])){
		$gameid = $_GET["gameid"];
	}
	
	if (isset($_GET["gameid"]) && isset($_GET["slotid"])){
		Manager::takeSlot($_SESSION["user"], $gameid, $_GET["slotid"]);
	}
	
	$gamelobbydata = Manager::getGameLobbyData($_SESSION["user"], $gameid);
	
	if ($gamelobbydata->status != "LOBBY"){
		header('Location: games.php');
	}
	//var_dump($gamelobbydata);
	$gamelobbydataJSON = json_encode($gamelobbydata, JSON_NUMERIC_CHECK);
	
	$ships = json_encode(Manager::getAllShips(), JSON_NUMERIC_CHECK);
	
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>B5CGM</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="base.css" rel="stylesheet" type="text/css">
		<link href="lobby.css" rel="stylesheet" type="text/css">
		<link href="./engine/tactical/UI/confirm.css" rel="stylesheet" type="text/css">
		<script src="./engine/jquery-1.5.2.min.js"></script>
		<script src="./engine/setup/gamelobby.js"></script>
		<script src="./engine/tactical/ajaxInterface.js"></script>
		<script src="./engine/tactical/player.js"></script>
		<script src="./engine/tactical/UI/confirm.js"></script>
		<script>
			
			jQuery(function($){
            
				gamedata.parseServerData(<?php print($gamelobbydataJSON); ?>);
				gamedata.parseShips(<?php print($ships); ?>);
				$('.readybutton').bind("click", gamedata.onReadyClicked);
				ajaxInterface.startPollingGamedata();
			});
		
		</script>
	</head>
	<body style="background-image:url(./maps/<?php print($gamelobbydata->background); ?>)">
	
		<div class="panel large">
			<div class="logout"><a href="logout.php">LOGOUT</a></div>
			<div class="">	<span class="panelheader">GAME:</span><span class="panelsubheader"><?php print($gamelobbydata->name); ?></span>	</div>

			<div><span>TEAM 1</span></div>
			<div id="team1" class="subpanel slotcontainer">
				<div class="slot" data-slotid="1" data-playerid=""><span>SLOT 1:</span><span class="playername"></span><span class="status">READY</span><span class="takeslot clickable">Take slot</span></div>
			</div>
			
			<div><span>TEAM 2</span></div>
			<div id="team1" class="subpanel slotcontainer">
				<div class="slot" data-slotid="2" data-playerid=""><span>SLOT 2:</span><span class="playername"></span><span class="status">READY</span><span class="takeslot clickable">Take slot</span></div>
			</div>
			
			<a href="gamelobby.php?leaveslot">LEAVE GAME</a>
			
		</div>
		<div class="panel large buy" style="display:none;">
			<div><span class="panelheader" style="padding-right:20px;">PURCHASE YOUR FLEET</span>
				<span class="panelsubheader current">0</span>
				<span class="panelsubheader">/</span>
				<span class="panelsubheader max"><?php print($gamelobbydata->points)?></span>
				<span class="panelsubheader">points</span>
				</div>
			<table class="store" style="width:100%;">
				<tr><td style="width:50%;vertical-align:top;">
					<div id="fleet" class="subpanel">
				</td><td style="width:50%;">
					<div id="store" class="subpanel">
				</td></tr>
			</table>
			
			<div><span class="clickable readybutton">READY</span></div>
			
		</div>

	</body>
</html>