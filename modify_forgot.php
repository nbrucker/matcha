<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] != '-42')
{
	header('Location: /signed_in.php');
	exit;
}
if (!check_get('u'))
{
	header('Location: /');
	exit;
}
else
{
	$req = $bdd->prepare('SELECT forgot FROM users WHERE user_id = ?');
	$req->execute(array($_GET['u']));
	if ($req->rowCount() != 1)
	{
		header('Location: /');
		exit;
	}
	$data = $req->fetch();
	if ($data['forgot'] != 1)
	{
		header('Location: /');
		exit;
	}
}
if (check_post('new_password') && check_post('password_conf'))
{
	if ($_POST['new_password'] != $_POST['password_conf'])
		echo "<style>#pass_match { display: block; } </style>";
	else if (strlen($_POST['new_password']) < 8)
		echo "<style>#short_pass { display: block; } </style>";
	else if (!preg_match("#[0-9]+#", $_POST['new_password']))
		echo "<style>#number_pass { display: block; } </style>";
	else if (!preg_match("#[a-zA-Z]+#", $_POST['new_password']))
		echo "<style>#letter_pass { display: block; } </style>";
	else
	{
		$hash = hash('whirlpool', $_POST['new_password']);
		$req = $bdd->prepare('UPDATE users SET password = ?, forgot = 0 WHERE user_id = ?');
		$req->execute(array(htmlspecialchars($hash), $_GET['u']));
		header('Location: /password_redirect.php');
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
		<span id="pass_match" class="error_msg">Passwords don't match</span>
		<span id="short_pass" class="error_msg">Password too short</span>
		<span id="number_pass" class="error_msg">Password must include a number</span>
		<span id="letter_pass" class="error_msg">Password must include a letter</span>
		<form action="/modify_forgot.php?u=<?php echo $_GET['u'] ?>" method="post">
			<input class="login" type="password" name="new_password" placeholder="New Password" required />
			<br />
			<input class="login" type="password" name="password_conf" placeholder="Confirmation" required />
			<br />
			<br />
			<input class="submit" type="submit" value="Modify" />
		</form>
	</div>
</body>
</html>
