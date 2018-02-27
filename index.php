<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Matcha</title>
	<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
	<script src="/jquery.js"></script>
	<script src="/js.js"></script>
	<script src="/awesomplete.js"></script>
	<script src='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.js'></script>
	<link href='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.css' rel='stylesheet' />
	<script src="https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.0/mapbox-gl.js"></script>
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.2.0/mapbox-gl-geocoder.min.js'></script>
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.2.0/mapbox-gl-geocoder.css' />
	<link rel='stylesheet' href="https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.0/mapbox-gl.css" />
	<link rel="stylesheet" href="/awesomplete.css" />
	<link rel="stylesheet" type="text/css" href="/css.css">
	<link rel="icon" type="image/png" href="/imgs/42.png" />
</head>
<body onload="getPositionIndex();">
	<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php'); ?>
	<div class="search_box">
		<select onchange="getIndex();" class="login" id="type">
			<?php if ($_SESSION['id'] != "-42") echo "<option selected value=\"0\">Suggestions</option>"; ?>
			<option <?php if ($_SESSION['id'] == "-42") echo "selected"; ?> value="1">Research</option>
		</select>
		<br />
		<div class="index_search_box">
			<input onchange="getIndex();" class="login index_left" type="number" id="age_min" />
			<span class="information"><= age <=</span>
			<input onchange="getIndex();" class="login index_right" type="number" id="age_max" />
		</div>
		<br />
		<div class="index_search_box">
			<input onchange="getIndex();" class="login index_left" type="number" id="popularity_min" />
			<span class="information"><= popularity <=</span>
			<input onchange="getIndex();" class="login index_right" type="number" id="popularity_max" />
		</div>
		<br />
		<div style="margin-top: 0px;" id="map" class="account_map"></div>
		<br />
		<div class="index_search_box">
			<input onchange="getIndex();" class="login index_left" type="number" id="distance_min" placeholder="km" />
			<span class="information"><= distance <=</span>
			<input onchange="getIndex();" class="login index_right" type="number" id="distance_max" placeholder="km" />
		</div>
		<div id="box_tags" class="box_account_tags"></div>
		<input onkeyup="changeInputIndex(event);" class="login awesomplete" list="mylist" type="text" id="tag" placeholder="#" value="#" maxlength="10" />
		<datalist id="mylist">
			<?php
			$req = $bdd->prepare('SELECT distinct tag FROM tags ORDER BY tag');
			$req->execute(array());
			while ($tags = $req->fetch())
				echo "<option id=\"".$tags['tag']."\">".$tags['tag']."</option>";
			?>
		</datalist>
		<br />
		<select onchange="getIndex();" class="login" id="order">
			<option selected value="0">Popularity ▼</option>
			<option value="1">Popularity ▲</option>
			<option value="2">Age ▼</option>
			<option value="3">Age ▲</option>
			<option value="4">Distance ▼</option>
			<option value="5">Distance ▲</option>
			<option value="6">Tags ▼</option>
			<option value="7">Tags ▲</option>
		</select>
	</div>
	<div id="index_box" class="index_box"></div>
</body>
</html>
