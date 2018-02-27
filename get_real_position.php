<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if ($_SESSION['id'] == '-42')
	exit;
$req = $bdd->prepare('SELECT latitude, longitude FROM users WHERE id = ?');
$req->execute(array($_SESSION['id']));
$data = $req->fetch();
$array = [];
array_push($array, $data['latitude']);
array_push($array, $data['longitude']);
echo json_encode($array);
?>
