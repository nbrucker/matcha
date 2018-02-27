<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] != '-42')
{
	header('Location: /signed_in.php');
	exit;
}
if (check_post('email'))
{
	$req = $bdd->prepare('SELECT user_id FROM users WHERE email = ?');
	$req->execute(array($_POST["email"]));
	if($req->rowCount() == 1)
	{
		$data = $req->fetch();
		$msg = 'Click on this link to modify your password : http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/modify_forgot.php?u='.$data['user_id'];
		mail($_POST['email'], 'Password reset', $msg);
		$req = $bdd->prepare('UPDATE users SET forgot = 1 WHERE email = ?');
		$req->execute(array($_POST["email"]));
		header('Location: /forgot_redirect.php');
		exit;
	}
	else
		echo "<style>#wrong_email { display: block; } </style>";
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
		<span id="wrong_email" class="error_msg">Wrong email</span>
		<form action="/forgot.php" method="post">
			<input class="login" type="email" name="email" placeholder="Email" required />
			<br />
			<br />
			<input class="submit" type="submit" value="Reset My Password" />
		</form>
	</div>
</body>
</html>
