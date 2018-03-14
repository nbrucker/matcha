<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if (!check_get('u'))
{
	header("Location: /");
	exit;
}
$req = $bdd->prepare('SELECT id, pic_0, pic_1, pic_2, pic_3, pic_4, first_name, last_name, bio, gender, orientation, popularity, last_log, age FROM users WHERE user_id = ?');
$req->execute(array($_GET['u']));
if ($req->rowCount() != 1)
{
	header("Location: /");
	exit;
}
$profile = $req->fetch();
if (blocked($profile['id'], $_SESSION['id'], $bdd))
{
	header("Location: /blocked_redirect.php");
	exit;
}
$me = [];
$me['pic_0'] = "";
if ($_SESSION['id'] != '-42')
{
	$req = $bdd->prepare('SELECT pic_0 FROM users WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	if ($req->rowCount() != 1)
	{
		header("Location: /");
		exit;
	}
	$me = $req->fetch();
}
if ($profile['id'] != $_SESSION['id'] && $_SESSION['id'] != "-42")
{
	$req = $bdd->prepare('INSERT INTO visits (visited_id, visiting_id, time) VALUES (:visited_id, :visiting_id, :time)');
	$req->execute(array(
		'visited_id' => intval($profile['id']),
		'visiting_id' => intval($_SESSION['id']),
		'time' => intval(time())
	));
	$req = $bdd->prepare('SELECT user_id FROM users WHERE id = ?');
	$req->execute(array(intval($_SESSION['id'])));
	$data = $req->fetch();
	$req = $bdd->prepare('INSERT INTO notifications (notification_id, user_id, seen, time, text, notifier_id, link) VALUES (:notification_id, :user_id, 0, :time, :text, :notifier_id, :link)');
	$req->execute(array(
		'notification_id' => getNotificationID($bdd),
		'user_id' => intval($profile['id']),
		'time' => intval(time()),
		'text' => "visited your profile",
		'notifier_id' => intval($_SESSION['id']),
		'link' => '/profile.php?u='.$data['user_id']
	));
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Matcha</title>
	<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
	<script src="/jquery.js"></script>
	<script src="/js.js"></script>
	<script src='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.js'></script>
	<link href='https://api.mapbox.com/mapbox.js/v3.0.1/mapbox.css' rel='stylesheet' />
	<script src="https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.0/mapbox-gl.js"></script>
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.2.0/mapbox-gl-geocoder.min.js'></script>
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.2.0/mapbox-gl-geocoder.css' />
	<link rel='stylesheet' href="https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.0/mapbox-gl.css" />
	<link rel="stylesheet" type="text/css" href="/css.css">
	<link rel="icon" type="image/png" href="/imgs/42.png" />
</head>
<body onload="getPositionProfile('<?php echo $_GET['u'] ?>');">
	<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php'); ?>
	<div class="profile">
		<div class="profile_info">
			<span class="profile_name"><?php echo $profile['first_name']." ".$profile['last_name'] ?></span>
			<br />
			<?php
			if ($profile['pic_0'] != "")
			{
				$src = "/imgs/like.svg";
				$req = $bdd->prepare('SELECT id FROM likes WHERE liking_id = ? AND liked_id = ?');
				$req->execute(array($_SESSION['id'], $profile['id']));
				if ($req->rowCount() == 1)
					$src = "/imgs/liked.svg";
				if ($profile['id'] != $_SESSION['id'] && !empty($me['pic_0']))
				{
					?>
					<img id="like" onclick="like('<?php echo $_GET['u'] ?>');" src="<?php echo $src ?>" class="profile_like">
					<?php
				}
			}
			?>
			<br />
			<br />
			<span class="information">Popularity : <span id="popularity"><?php echo $profile['popularity'] ?></span></span>
			<br />
			<br />
			<span class="information">Age : <?php echo $profile['age'] ?></span>
			<br />
			<br />
			<span class="information">Gender : <?php if ($profile['gender'] == 1) echo "Man"; else if ($profile['gender'] == 2) echo "Woman"; ?></span>
			<br />
			<br />
			<span class="information">Looking for : <?php if ($profile['orientation'] == 1) echo "Men"; else if ($profile['orientation'] == 2) echo "Women"; else if ($profile['orientation'] == 3) echo "Men, Women"; ?></span>
			<br />
			<br />
			<span class="information">Bio :</span>
			<br />
			<span class="profile_bio"><?php echo $profile['bio'] ?></span>
			<br />
			<br />
			<span class="information">Interest :</span>
			<br />
			<?php
			$req = $bdd->prepare('SELECT tags.tag as tag FROM tags INNER JOIN links ON links.tag_id = tags.id WHERE links.user_id = ? ORDER BY links.id');
			$req->execute(array($profile['id']));
			while ($data = $req->fetch())
			{
				?>
				<div class="profile_tags">
					<span class="profile_tags"><?php echo $data['tag'] ?></span>
				</div>
				<?php
			}
			?>
			<br />
			<br />
			<span class="information">Geolocation :</span>
			<br />
			<div id="map" class="profile_map"></div>
		</div>
		<div class="profile_left">
			<div class="profile_pics">
				<?php
				$i = 0;
				while ($i < 5)
				{
					$src = "/imgs/blank.png";
					if (!empty($profile['pic_'.$i]))
						$src = $profile['pic_'.$i];
					echo "<img id=\"img_".$i."\" onclick=\"changeProfileImg('".$i."');\" src=\"".$src."\" class=\"profile_pic".$i."\">";
					$i++;
				}
				?>
			</div>
			<?php
			$req = $bdd->prepare('SELECT id FROM likes WHERE liking_id = ? AND liked_id = ?');
			$req->execute(array($profile['id'], $_SESSION['id']));
			if ($req->rowCount() == 1)
			{
				?>
				<span style="text-decoration: underline;" class="information">This user likes you</span>
				<br />
				<br />
				<?php
			}
			if ($profile['last_log'] + 10 < time())
			{
				?>
				<span class="help">Offline since <?php echo date("d/m/Y H:i:s", $profile['last_log']); ?></span>
				<?php
			}
			else
			{
				?>
				<span class="help">Online</span>
				<?php
			}
			if ($profile['id'] != $_SESSION['id'])
			{
				?>
				<br />
				<br />
				<span onclick="block('<?php echo $_GET['u'] ?>');" class="profile_block">Block this user</span>
				<br />
				<br />
				<?php
				$req = $bdd->prepare('SELECT id FROM reports WHERE reported_id = ? AND reporting_id = ?');
				$req->execute(array($profile['id'], $_SESSION['id']));
				if ($req->rowCount() == 1)
				{
					?>
					<span class="profile_reported">You reported this user</span>
					<?php
				}
				else
				{
					?>
					<span id="report" onclick="report('<?php echo $_GET['u'] ?>');"  class="profile_block">Report this user</span>
					<?php
				}
			}
			?>
		</div>
	</div>
</body>
</html>
