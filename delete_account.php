<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] == '-42')
{
	header('Location: /not_signed_in.php');
	exit;
}
if (check_post('password'))
{
	$hash = hash('whirlpool', $_POST['password']);
	$req = $bdd->prepare('SELECT pic_0, pic_1, pic_2, pic_3, pic_4 FROM users WHERE id = ? AND password = ?');
	$req->execute(array($_SESSION["id"], $hash));
	if ($req->rowCount() != 1)
		echo "<style>#wrong_pass { display: block; } </style>";
	else
	{
		$user = $req->fetch();
		$req = $bdd->prepare('DELETE FROM users WHERE id = ?');
		$req->execute(array($_SESSION["id"]));
		$req = $bdd->prepare('DELETE FROM tags WHERE user_id = ?');
		$req->execute(array($_SESSION["id"]));
		$req = $bdd->prepare('DELETE FROM blocks WHERE blocked_id = ? OR blocking_id = ?');
		$req->execute(array($_SESSION["id"], $_SESSION["id"]));
		$req = $bdd->prepare('DELETE FROM likes WHERE liked_id = ? OR liking_id = ?');
		$req->execute(array($_SESSION["id"], $_SESSION["id"]));
		$req = $bdd->prepare('DELETE FROM reports WHERE reported_id = ? OR reporting_id = ?');
		$req->execute(array($_SESSION["id"], $_SESSION["id"]));
		$req = $bdd->prepare('DELETE FROM visits WHERE visiting_id = ? OR visited_id = ?');
		$req->execute(array($_SESSION["id"], $_SESSION["id"]));
		$req = $bdd->prepare('DELETE FROM messages WHERE to_id = ? OR from_id = ?');
		$req->execute(array($_SESSION["id"], $_SESSION["id"]));
		$req = $bdd->prepare('DELETE FROM notifications WHERE user_id = ?');
		$req->execute(array($_SESSION["id"]));
		if (file_exists($user['pic_0']))
			unlink($user['pic_0']);
		if (file_exists($user['pic_1']))
			unlink($user['pic_1']);
		if (file_exists($user['pic_2']))
			unlink($user['pic_2']);
		if (file_exists($user['pic_3']))
			unlink($user['pic_3']);
		if (file_exists($user['pic_4']))
			unlink($user['pic_4']);
		header('Location: /delete_redirect.php');
		exit;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Matcha</title>
	<script src="/jquery.js"></script>
	<script src="/js.js"></script>
	<link rel="stylesheet" type="text/css" href="/css.css">
	<link rel="icon" type="image/png" href="/imgs/42.png" />
</head>
<body>
	<?php include($_SERVER['DOCUMENT_ROOT'].'/header.php'); ?>
	<div class="login_box">
		<span id="wrong_pass" class="error_msg">Wrong password</span>
		<form action="/delete_account.php" method="post">
			<input class="login" type="password" name="password" placeholder="Password" required />
			<br />
			<br />
			<input class="submit" type="submit" value="Delete" />
		</form>
	</div>
</body>
</html>
