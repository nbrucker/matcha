<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] == '-42')
{
	header('Location: /not_signed_in.php');
	exit;
}
$req = $bdd->prepare('SELECT last_name, first_name, gender, orientation, bio, auto_loc, pic_0, pic_1, pic_2, pic_3, pic_4, age FROM users WHERE id = ?');
$req->execute(array($_SESSION['id']));
$data = $req->fetch();
?>
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
<body onload="loadTags();getPosition();">
	<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php'); ?>
	<div class="information">
		<input class="login" type="text" id="last_name" placeholder="Last name" value="<?php echo $data['last_name'] ?>" />
		<br />
		<input class="login" type="text" id="first_name" placeholder="First name" value="<?php echo $data['first_name'] ?>" />
		<br />
		<input class="login" type="number" id="age" placeholder="Age" value="<?php echo $data['age'] ?>" />
		<br />
		<span class="information">Gender :</span>
		<br />
		<select class="login" id="gender">
			<option <?php if ($data['gender'] == 0) echo "selected"; ?>></option>
			<option <?php if ($data['gender'] == 1) echo "selected"; ?> value="1">Man</option>
			<option <?php if ($data['gender'] == 2) echo "selected"; ?> value="2">Woman</option>
		</select>
		<br />
		<span class="information">Looking for :</span>
		<br />
		<label>
			<input type="checkbox" class="checkbox_input" id="men" value="men" <?php if (($data['orientation'] & 1) == 1) echo "checked"; ?> />
			<span class="information">Men</span>
		</label>
		<br />
		<label>
			<input type="checkbox" class="checkbox_input" id="women" value="women" <?php if (($data['orientation'] & 2) == 2) echo "checked"; ?> />
			<span class="information">Women</span>
		</label>
		<br />
		<textarea class="textarea_input" id="bio" placeholder="Bio"><?php echo $data['bio']; ?></textarea>
		<br />
		<span class="information">Interest :</span>
		<br />
		<div id="box_tags" class="box_account_tags"></div>
		<span class="help">Type a word, then press enter to add it !</span>
		<br />
		<input onkeyup="changeInput(event);" class="login awesomplete" list="mylist" type="text" id="tag" placeholder="#" value="#" maxlength="10" />
		<datalist id="mylist">
			<?php
			$req = $bdd->prepare('SELECT distinct tag FROM tags WHERE tag NOT IN (SELECT tag FROM tags WHERE user_id = ?) ORDER BY tag');
			$req->execute(array($_SESSION['id']));
			while ($tags = $req->fetch())
				echo "<option id=\"".$tags['tag']."\">".$tags['tag']."</option>";
			?>
		</datalist>
		<br />
		<span class="information">Geolocation :</span>
		<br />
		<label>
			<input onclick="getRealPosition();" type="radio" class="checkbox_input radio" id="auto_loc" name="loc" value="auto" <?php if ($data['auto_loc'] == 1) echo "checked"; ?> />
			<span class="information">Auto Geolocation</span>
		</label>
		<br />
		<label>
			<input type="radio" class="checkbox_input radio" id="nauto_loc" name="loc" value="nauto" <?php if ($data['auto_loc'] == 0) echo "checked"; ?> />
			<span class="information">Set my position myself</span>
		</label>
		<br />
		<div id="map" class="account_map"></div>
		<br />
		<span class="information">Images :</span>
		<br />
		<span class="help">png or jpg, max 1Mo</span>
		<br />
		<div class="account_pics">
			<div class="account_pic0">
				<?php
				$src = "";
				$before = "initial";
				$after = "none";
				$margin = "-30px";
				if (!empty($data['pic_0']))
				{
					$src = $data['pic_0'];
					$before = "none";
					$after = "initial";
					$margin = "0px";
				}
				?>
				<img style="display: <?php echo $after ?>;" id="pic_0_trash" src="/imgs/trash.svg" class="account_pics_trash0" onclick="removePic(0);">
				<input style="margin-left: <?php echo $margin ?>;" class="pic_deposit0" type="file" id="pic_0" onchange="upload_pic(0);" onmouseover="srcOverPic(0);" onmouseout="srcOutPic(0);" />
				<img style="display: <?php echo $before ?>;" id="pic_0_before" src="/imgs/plus.svg" class="account_pics_before">
				<img style="display: <?php echo $after ?>;" id="pic_0_after" src="<?php echo $src ?>" class="account_pics_after">
			</div>
			<div class="account_pic1">
				<?php
				$src = "";
				$before = "initial";
				$after = "none";
				$margin = "-15px";
				if (!empty($data['pic_1']))
				{
					$src = $data['pic_1'];
					$before = "none";
					$after = "initial";
					$margin = "0px";
				}
				?>
				<img style="display: <?php echo $after ?>;" id="pic_1_trash" src="/imgs/trash.svg" class="account_pics_trash1" onclick="removePic(1);">
				<input style="margin-left: <?php echo $margin ?>;" class="pic_deposit1" type="file" id="pic_1" onchange="upload_pic(1);" onmouseover="srcOverPic(1);" onmouseout="srcOutPic(1);" />
				<img style="display: <?php echo $before ?>;" id="pic_1_before" src="/imgs/plus.svg" class="account_pics_before">
				<img style="display: <?php echo $after ?>;" id="pic_1_after" src="<?php echo $src ?>" class="account_pics_after">
			</div>
			<div class="account_pic2">
				<?php
				$src = "";
				$before = "initial";
				$after = "none";
				$margin = "-15px";
				if (!empty($data['pic_2']))
				{
					$src = $data['pic_2'];
					$before = "none";
					$after = "initial";
					$margin = "0px";
				}
				?>
				<img style="display: <?php echo $after ?>;" id="pic_2_trash" src="/imgs/trash.svg" class="account_pics_trash1" onclick="removePic(2);">
				<input style="margin-left: <?php echo $margin ?>;" class="pic_deposit2" type="file" id="pic_2" onchange="upload_pic(2);" onmouseover="srcOverPic(2);" onmouseout="srcOutPic(2);" />
				<img style="display: <?php echo $before ?>;" id="pic_2_before" src="/imgs/plus.svg" class="account_pics_before">
				<img style="display: <?php echo $after ?>;" id="pic_2_after" src="<?php echo $src ?>" class="account_pics_after">
			</div>
			<div class="account_pic1">
				<?php
				$src = "";
				$before = "initial";
				$after = "none";
				$margin = "-15px";
				if (!empty($data['pic_3']))
				{
					$src = $data['pic_3'];
					$before = "none";
					$after = "initial";
					$margin = "0px";
				}
				?>
				<img style="display: <?php echo $after ?>;" id="pic_3_trash" src="/imgs/trash.svg" class="account_pics_trash1" onclick="removePic(3);">
				<input style="margin-left: <?php echo $margin ?>;" class="pic_deposit1" type="file" id="pic_3" onchange="upload_pic(3);" onmouseover="srcOverPic(3);" onmouseout="srcOutPic(3);" />
				<img style="display: <?php echo $before ?>;" id="pic_3_before" src="/imgs/plus.svg" class="account_pics_before">
				<img style="display: <?php echo $after ?>;" id="pic_3_after" src="<?php echo $src ?>" class="account_pics_after">
			</div>
			<div class="account_pic2">
				<?php
				$src = "";
				$before = "initial";
				$after = "none";
				$margin = "-15px";
				if (!empty($data['pic_4']))
				{
					$src = $data['pic_4'];
					$before = "none";
					$after = "initial";
					$margin = "0px";
				}
				?>
				<img style="display: <?php echo $after ?>;" id="pic_4_trash" src="/imgs/trash.svg" class="account_pics_trash1" onclick="removePic(4);">
				<input style="margin-left: <?php echo $margin ?>;" class="pic_deposit2" type="file" id="pic_4" onchange="upload_pic(4);" onmouseover="srcOverPic(4);" onmouseout="srcOutPic(4);" />
				<img style="display: <?php echo $before ?>;" id="pic_4_before" src="/imgs/plus.svg" class="account_pics_before">
				<img style="display: <?php echo $after ?>;" id="pic_4_after" src="<?php echo $src ?>" class="account_pics_after">
			</div>
		</div>
		<br />
		<br />
		<input class="submit" type="submit" value="Save modification" onclick="saveInfo(event);" />
	</div>
	<a class="account" href="/history.php"><div class="account">History</div></a>
	<a class="account" href="/block_list.php"><div class="account">Block list</div></a>
	<a class="account" href="/modify_email.php"><div class="account">Modify email</div></a>
	<a class="account" href="/modify_login.php"><div class="account">Modify login</div></a>
	<a class="account" href="/modify_password.php"><div class="account">Modify password</div></a>
	<a class="account" href="/delete_account.php"><div style="margin-bottom: 50px;" class="account">Delete Account</div></a>
</body>
</html>
