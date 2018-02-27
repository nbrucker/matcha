<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
	exit;
$req = $bdd->prepare('SELECT user_id, pic_0, pic_1, pic_2, pic_3, pic_4 FROM users WHERE id = ?');
$req->execute(array($_SESSION['id']));
$user = $req->fetch();
if (check_post('last_name'))
{
	$req = $bdd->prepare('UPDATE users SET last_name = ? WHERE id = ?');
	$req->execute(array(ucfirst(htmlspecialchars($_POST['last_name'])), $_SESSION["id"]));
}
if (check_post('first_name'))
{
	$req = $bdd->prepare('UPDATE users SET first_name = ? WHERE id = ?');
	$req->execute(array(ucfirst(htmlspecialchars($_POST['first_name'])), $_SESSION["id"]));
}
if (isset($_POST['gender']) && ($_POST['gender'] == 1 || $_POST['gender'] == 2))
{
	$req = $bdd->prepare('UPDATE users SET gender = ? WHERE id = ?');
	$req->execute(array(intval(htmlspecialchars($_POST['gender'])), $_SESSION["id"]));
}
if (isset($_POST['men']))
{
	if ($_POST['men'] == 'false')
	{
		$req = $bdd->prepare('UPDATE users SET orientation = orientation & 2 WHERE id = ?');
		$req->execute(array($_SESSION["id"]));
	}
	else if ($_POST['men'] == 'true')
	{
		$req = $bdd->prepare('UPDATE users SET orientation = orientation | 1 WHERE id = ?');
		$req->execute(array($_SESSION["id"]));
	}
}
if (isset($_POST['women']))
{
	if ($_POST['women'] == 'false')
	{
		$req = $bdd->prepare('UPDATE users SET orientation = orientation & 1 WHERE id = ?');
		$req->execute(array($_SESSION["id"]));
	}
	else if ($_POST['women'] == 'true')
	{
		$req = $bdd->prepare('UPDATE users SET orientation = orientation | 2 WHERE id = ?');
		$req->execute(array($_SESSION["id"]));
	}
}
if (check_post('bio'))
{
	$req = $bdd->prepare('UPDATE users SET bio = ? WHERE id = ?');
	$req->execute(array(htmlspecialchars($_POST['bio']), $_SESSION["id"]));
}
if (isset($_POST['tags']))
{
	$tags = json_decode($_POST['tags']);
	$req = $bdd->prepare('SELECT id, tag FROM tags WHERE user_id = ?');
	$req->execute(array($_SESSION["id"]));
	while ($data = $req->fetch())
	{
		if (($key = array_search($data['tag'], $tags)) !== false)
			unset($tags[$key]);
		else
		{
			$reqb = $bdd->prepare('DELETE FROM tags WHERE id = ?');
			$reqb->execute(array($data['id']));
		}
	}
	foreach ($tags as $tag)
	{
		$req = $bdd->prepare('INSERT INTO tags (user_id, tag) VALUES (:user_id, :tag)');
		$req->execute(array(
		'user_id' => htmlspecialchars($_SESSION['id']),
		'tag' => htmlspecialchars($tag)
		));
	}
}
if (check_post('loc') && isset($_POST['latitude']) && isset($_POST['longitude']))
{
	if ($_POST['loc'] == "auto")
	{
		$req = $bdd->prepare('UPDATE users SET auto_loc = 1 WHERE id = ?');
		$req->execute(array($_SESSION["id"]));
	}
	else if ($_POST['loc'] == "nauto")
	{
		$req = $bdd->prepare('UPDATE users SET auto_loc = 0, fake_latitude = ?, fake_longitude = ? WHERE id = ?');
		$req->execute(array(floatval(htmlspecialchars($_POST['latitude'])), floatval(htmlspecialchars($_POST['longitude'])), $_SESSION["id"]));
	}
}
if (check_post('pic_0') && $_POST['pic_0'] == "removed")
{
	if (file_exists($user['pic_0']))
		unlink($user['pic_0']);
	$req = $bdd->prepare('UPDATE users SET pic_0 = ? WHERE id = ?');
	$req->execute(array("", $_SESSION["id"]));
}
if (isset($_FILES['pic_0']))
{
	$fileName = $_FILES['pic_0']['name'];
	$fileLoc = $_FILES['pic_0']['tmp_name'];
	$fileType = $_FILES['pic_0']['type'];
	$fileSize = $_FILES['pic_0']['size'];
	$extension = pathinfo($fileName, PATHINFO_EXTENSION);
	if ((($extension == "png" && $fileType == "image/png") || ($extension == "jpg" && $fileType == "image/jpeg")) && $fileSize <= 1000000)
	{
		$path = 'pictures/'.$user['user_id'].'0.'.$extension;
		if (move_uploaded_file($fileLoc, $path))
		{
			$req = $bdd->prepare('UPDATE users SET pic_0 = ? WHERE id = ?');
			$req->execute(array(htmlspecialchars($path), $_SESSION["id"]));
		}
	}
}
if (check_post('pic_1') && $_POST['pic_1'] == "removed")
{
	if (file_exists($user['pic_1']))
		unlink($user['pic_1']);
	$req = $bdd->prepare('UPDATE users SET pic_1 = ? WHERE id = ?');
	$req->execute(array("", $_SESSION["id"]));
}
if (isset($_FILES['pic_1']))
{
	$fileName = $_FILES['pic_1']['name'];
	$fileLoc = $_FILES['pic_1']['tmp_name'];
	$fileType = $_FILES['pic_1']['type'];
	$fileSize = $_FILES['pic_1']['size'];
	$extension = pathinfo($fileName, PATHINFO_EXTENSION);
	if ((($extension == "png" && $fileType == "image/png") || ($extension == "jpg" && $fileType == "image/jpeg")) && $fileSize <= 1000000)
	{
		$path = 'pictures/'.$user['user_id'].'1.'.$extension;
		if (move_uploaded_file($fileLoc, $path))
		{
			$req = $bdd->prepare('UPDATE users SET pic_1 = ? WHERE id = ?');
			$req->execute(array(htmlspecialchars($path), $_SESSION["id"]));
		}
	}
}
if (check_post('pic_2') && $_POST['pic_2'] == "removed")
{
	if (file_exists($user['pic_2']))
		unlink($user['pic_2']);
	$req = $bdd->prepare('UPDATE users SET pic_2 = ? WHERE id = ?');
	$req->execute(array("", $_SESSION["id"]));
}
if (isset($_FILES['pic_2']))
{
	$fileName = $_FILES['pic_2']['name'];
	$fileLoc = $_FILES['pic_2']['tmp_name'];
	$fileType = $_FILES['pic_2']['type'];
	$fileSize = $_FILES['pic_2']['size'];
	$extension = pathinfo($fileName, PATHINFO_EXTENSION);
	if ((($extension == "png" && $fileType == "image/png") || ($extension == "jpg" && $fileType == "image/jpeg")) && $fileSize <= 1000000)
	{
		$path = 'pictures/'.$user['user_id'].'2.'.$extension;
		if (move_uploaded_file($fileLoc, $path))
		{
			$req = $bdd->prepare('UPDATE users SET pic_2 = ? WHERE id = ?');
			$req->execute(array(htmlspecialchars($path), $_SESSION["id"]));
		}
	}
}
if (check_post('pic_3') && $_POST['pic_3'] == "removed")
{
	if (file_exists($user['pic_3']))
		unlink($user['pic_3']);
	$req = $bdd->prepare('UPDATE users SET pic_3 = ? WHERE id = ?');
	$req->execute(array("", $_SESSION["id"]));
}
if (isset($_FILES['pic_3']))
{
	$fileName = $_FILES['pic_3']['name'];
	$fileLoc = $_FILES['pic_3']['tmp_name'];
	$fileType = $_FILES['pic_3']['type'];
	$fileSize = $_FILES['pic_3']['size'];
	$extension = pathinfo($fileName, PATHINFO_EXTENSION);
	if ((($extension == "png" && $fileType == "image/png") || ($extension == "jpg" && $fileType == "image/jpeg")) && $fileSize <= 1000000)
	{
		$path = 'pictures/'.$user['user_id'].'3.'.$extension;
		if (move_uploaded_file($fileLoc, $path))
		{
			$req = $bdd->prepare('UPDATE users SET pic_3 = ? WHERE id = ?');
			$req->execute(array(htmlspecialchars($path), $_SESSION["id"]));
		}
	}
}
if (check_post('pic_4') && $_POST['pic_4'] == "removed")
{
	if (file_exists($user['pic_4']))
		unlink($user['pic_4']);
	$req = $bdd->prepare('UPDATE users SET pic_4 = ? WHERE id = ?');
	$req->execute(array("", $_SESSION["id"]));
}
if (isset($_FILES['pic_4']))
{
	$fileName = $_FILES['pic_4']['name'];
	$fileLoc = $_FILES['pic_4']['tmp_name'];
	$fileType = $_FILES['pic_4']['type'];
	$fileSize = $_FILES['pic_4']['size'];
	$extension = pathinfo($fileName, PATHINFO_EXTENSION);
	if ((($extension == "png" && $fileType == "image/png") || ($extension == "jpg" && $fileType == "image/jpeg")) && $fileSize <= 1000000)
	{
		$path = 'pictures/'.$user['user_id'].'4.'.$extension;
		if (move_uploaded_file($fileLoc, $path))
		{
			$req = $bdd->prepare('UPDATE users SET pic_4 = ? WHERE id = ?');
			$req->execute(array(htmlspecialchars($path), $_SESSION["id"]));
		}
	}
}
if (check_post('age'))
{
	$req = $bdd->prepare('UPDATE users SET age = ? WHERE id = ?');
	$req->execute(array(htmlspecialchars(intval($_POST['age'])), $_SESSION["id"]));
}
?>
