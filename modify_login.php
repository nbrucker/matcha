<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] == '-42')
{
	header('Location: /not_signed_in.php');
	exit;
}
if (check_post('password') && check_post('login'))
{
	$req = $bdd->prepare('SELECT id FROM users WHERE login = ?');
	$req->execute(array($_POST["login"]));
	if ($req->rowCount() > 0)
		echo "<style>#login_used { display: block; } </style>";
	else
	{
		$hash = hash('whirlpool', $_POST['password']);
		$req = $bdd->prepare('SELECT id FROM users WHERE id = ? AND password = ?');
		$req->execute(array($_SESSION["id"], $hash));
		if($req->rowCount() == 1)
		{
			$req = $bdd->prepare('UPDATE users SET login = ? WHERE id = ?');
			$req->execute(array(htmlspecialchars($_POST['login']), $_SESSION["id"]));
			header('Location: /login_redirect.php');
			exit;
		}
		else
			echo "<style>#wrong_pass { display: block; } </style>";
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
		<span id="login_used" class="error_msg">Login already used</span>
		<span id="wrong_pass" class="error_msg">Wrong password</span>
		<form action="/modify_login.php" method="post">
			<input class="login" type="text" name="login" placeholder="New Login" required />
			<br />
			<input class="login" type="password" name="password" placeholder="Password" required />
			<br />
			<br />
			<input class="submit" type="submit" value="Modify" />
		</form>
	</div>
</body>
</html>
