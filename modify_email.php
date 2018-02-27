<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] == '-42')
{
	header('Location: /not_signed_in.php');
	exit;
}
if (check_post('password') && check_post('email'))
{
	$req = $bdd->prepare('SELECT id FROM users WHERE email = ?');
	$req->execute(array($_POST["email"]));
	if ($req->rowCount() > 0)
		echo "<style>#email_used { display: block; } </style>";
	else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		echo "<style>#email_format { display: block; } </style>";
	else
	{
		$hash = hash('whirlpool', $_POST['password']);
		$req = $bdd->prepare('SELECT id FROM users WHERE id = ? AND password = ?');
		$req->execute(array($_SESSION["id"], $hash));
		if ($req->rowCount() == 1)
		{
			$req = $bdd->prepare('UPDATE users SET email = ? WHERE id = ?');
			$req->execute(array(htmlspecialchars($_POST['email']), $_SESSION["id"]));
			header('Location: /email_redirect.php');
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
		<span id="wrong_pass" class="error_msg">Wrong password</span>
		<span id="email_used" class="error_msg">Email already used</span>
		<span id="email_format" class="error_msg">Not a valid email</span>
		<form action="/modify_email.php" method="post">
			<input class="login" type="email" name="email" placeholder="New Email" required />
			<br />
			<input class="login" type="password" name="password" placeholder="Password" required />
			<br />
			<br />
			<input class="submit" type="submit" value="Modify" />
		</form>
	</div>
</body>
</html>
