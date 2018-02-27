<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
	exit;
$req = $bdd->prepare('SELECT tag FROM tags WHERE user_id = ? ORDER BY id');
$req->execute(array($_SESSION['id']));
$tags = [];
while ($data = $req->fetch())
	array_push($tags, $data['tag']);
echo json_encode($tags);
?>