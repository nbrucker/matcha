<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
if ($_SESSION['id'] == '-42')
{
	header('Location: /not_signed_in.php');
	exit;
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
	<div style="margin: 0 auto; text-align: center; margin-top: 50px;">
		<?php
		$req = $bdd->prepare('SELECT users.first_name, users.last_name, users.user_id FROM users INNER JOIN blocks ON users.id = blocks.blocked_id WHERE blocks.blocking_id = ? ORDER BY blocks.id');
		$req->execute(array($_SESSION['id']));
		while ($data = $req->fetch())
		{
			?>
			<div id="<?php echo $data['user_id'] ?>" class="block_list">
				<span onclick="unblock('<?php echo $data['user_id'] ?>');" class="block_list">Unblock <?php echo $data['first_name']." ".$data['last_name'] ?></span>
			</div>
			<?php
		}
		?>
	</div>
</body>
</html>
