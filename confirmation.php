<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] != '-42')
{
	header('Location: /signed_in.php');
	exit;
}
$text = "This account could not be confirmed.";
if (check_get('u'))
{
	$req = $bdd->prepare('SELECT id FROM users WHERE user_id = ? AND confirmed = 0');
	$req->execute(array($_GET['u']));
	if($req->rowCount() == 1)
	{
		$req = $bdd->prepare('UPDATE users SET confirmed = 1 WHERE user_id = ?');
		$req->execute(array($_GET['u']));
		$text = "Your account is now confirmed, you may now sign in.";
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
	<div class="box_big_message">
		<?php echo $text; ?>
	</div>
</body>
</html>
