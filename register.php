<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] != '-42')
{
	header('Location: /signed_in.php');
	exit;
}
if (check_post('email') && check_post('login') && check_post('last_name') && check_post('first_name') && check_post('password') && check_post('password_conf'))
{
	if ($_POST['password'] != $_POST['password_conf'])
		echo "<style>#pass_match { display: block; } </style>";
	else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		echo "<style>#email_format { display: block; } </style>";
	else if (strlen($_POST['password']) < 8)
		echo "<style>#short_pass { display: block; } </style>";
	else if (!preg_match("#[0-9]+#", $_POST['password']))
		echo "<style>#number_pass { display: block; } </style>";
	else if (!preg_match("#[a-zA-Z]+#", $_POST['password']))
		echo "<style>#letter_pass { display: block; } </style>";
	else
	{
		$req = $bdd->prepare('SELECT id FROM users WHERE login = ?');
		$req->execute(array($_POST["login"]));
		$reqb = $bdd->prepare('SELECT id FROM users WHERE email = ?');
		$reqb->execute(array($_POST["email"]));
		if ($req->rowCount() > 0)
			echo "<style>#login_used { display: block; } </style>";
		else if ($reqb->rowCount() > 0)
			echo "<style>#email_used { display: block; } </style>";
		else
		{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randstring = 'u';
			for ($i = 0; $i < 30; $i++)
			{
				$randstring .= $characters[rand(0, strlen($characters)-1)];
			}
			$req = $bdd->prepare('SELECT id FROM users WHERE user_id = ?');
			$req->execute(array($randstring));
			while ($req->rowCount() != 0)
			{
				$randstring = 'u';
				for ($i = 0; $i < 30; $i++)
				{
					$randstring .= $characters[rand(0, strlen($characters)-1)];
				}
				$req = $bdd->prepare('SELECT id FROM users WHERE user_id = ?');
				$req->execute(array($randstring));
			}
			$hash = hash('whirlpool', $_POST['password']);
			$req = $bdd->prepare('INSERT INTO users (user_id, email, login, last_name, first_name, password, bio, pic_0, pic_1, pic_2, pic_3, pic_4, age) VALUES (:user_id, :email, :login, :last_name, :first_name, :password, :bio, :pic_0, :pic_1, :pic_2, :pic_3, :pic_4, :age)');
			$req->execute(array(
			'user_id' => htmlspecialchars($randstring),
			'email' => htmlspecialchars($_POST['email']),
			'login' => htmlspecialchars($_POST['login']),
			'last_name' => ucfirst(htmlspecialchars($_POST['last_name'])),
			'first_name' => ucfirst(htmlspecialchars($_POST['first_name'])),
			'password' => htmlspecialchars($hash),
			'bio' => htmlspecialchars(""),
			'pic_0' => htmlspecialchars(""),
			'pic_1' => htmlspecialchars(""),
			'pic_2' => htmlspecialchars(""),
			'pic_3' => htmlspecialchars(""),
			'pic_4' => htmlspecialchars(""),
			'age' => htmlspecialchars("")
			));
			$msg = 'To validate your account please click on the following link : http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/confirmation.php?u='.$randstring;
			mail($_POST['email'], 'Account confirmation', $msg);
			header('Location: /register_redirect.php');
			exit;
		}
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
		<span id="email_used" class="error_msg">Email already used</span>
		<span id="pass_match" class="error_msg">Passwords don't match</span>
		<span id="email_format" class="error_msg">Not a valid email</span>
		<span id="short_pass" class="error_msg">Password too short</span>
		<span id="number_pass" class="error_msg">Password must include a number</span>
		<span id="letter_pass" class="error_msg">Password must include a letter</span>
		<form action="/register.php" method="post">
			<input class="login" type="email" name="email" placeholder="Email" required />
			<br />
			<input class="login" type="text" name="login" placeholder="Login" required />
			<br />
			<input class="login" type="text" name="last_name" placeholder="Last name" required />
			<br />
			<input class="login" type="text" name="first_name" placeholder="First name" required />
			<br />
			<input class="login" type="password" name="password" placeholder="Password" required />
			<br />
			<input class="login" type="password" name="password_conf" placeholder="Confirmation" required />
			<br />
			<br />
			<input class="submit" type="submit" value="Register" />
		</form>
	</div>
</body>
</html>
