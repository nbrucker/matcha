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
	<div style="margin-top: 50px;" id="index_box" class="index_box">
		<span class="list_title">Profile who visited you</span>
		<?php
		$req = $bdd->prepare('SELECT DISTINCT users.last_name, users.first_name, users.user_id, users.pic_0 FROM users INNER JOIN visits ON visits.visiting_id = users.id WHERE visits.visited_id = ?');
		$req->execute(array($_SESSION['id']));
		while ($data = $req->fetch())
		{
			?>
			<a class="index_profile" href="/profile.php?u=<?php echo $data['user_id'] ?>">
				<div class="index_profile">
					<img src="<?php echo $data['pic_0'] ?>" class="index_profile">
					<span class="index_profile"><?php echo $data['first_name']." ".$data['last_name'] ?></span>
				</div>
			</a>
			<?php
		}
		?>
	</div>
</body>
</html>
