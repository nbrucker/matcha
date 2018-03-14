<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
function get_fake_image($men, $women, $gender)
{
	$link = "imgs/";
	if ($gender == 1)
	{
		$link .= "fake_men/";
		$x = rand(0, $men - 1);
	}
	else
	{
		$link .= "fake_women/";
		$x = rand(0, $women - 1);
	}
	$link .= $x.".jpg";
	return ($link);
}
?>
<?php
function get_fake_tag($used)
{
	$tags = ['#42', '#travel', '#cooking', '#shopping', '#family', '#work', '#sport', '#share', '#tech', '#children', '#food', '#museum'];
	$tag = $tags[rand(0, count($tags) - 1)];
	while (in_array($tag, $used))
		$tag = $tags[rand(0, count($tags) - 1)];
	return $tag;
}
?>
<?php
require_once 'vendor/autoload.php';

$min_lat = 41;
$max_lat = 51;
$min_lon = 0;
$max_lon = 7.5;

$faker = Faker\Factory::create();
$j = 0;
$files = glob("imgs/fake_men/*");
$men = count($files) - 1;
$files = glob("imgs/fake_women/*");
$women = count($files) - 1;
while ($j < 500)
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
	$hash = hash('whirlpool', $faker->password);
	$gender = rand(1, 2);
	$req = $bdd->prepare('INSERT INTO users (user_id, email, login, last_name, first_name, password, gender, orientation, bio, popularity, last_log, latitude, longitude, fake_latitude, fake_longitude, auto_loc, confirmed, forgot, pic_0, pic_1, pic_2, pic_3, pic_4, age, token) VALUES (:user_id, :email, :login, :last_name, :first_name, :password, :gender, :orientation, :bio, :popularity, :last_log, :latitude, :longitude, :fake_latitude, :fake_longitude, 0, 1, 0, :pic_0, :pic_1, :pic_2, :pic_3, :pic_4, :age, :token)');
	$req->execute(array(
	'user_id' => $randstring,
	'email' => $faker->email,
	'login' => $faker->userName,
	'last_name' => $faker->lastName,
	'first_name' => $faker->firstName,
	'password' => $hash,
	'gender' => $gender,
	'orientation' => rand(1, 3),
	'bio' => $faker->text(999),
	'popularity' => rand(0, 99),
	'last_log' => time(),
	'latitude' => rand($min_lat * pow(10, 6), $max_lat * pow(10, 6)) / pow(10, 6),
	'longitude' => rand($min_lon * pow(10, 6), $max_lon * pow(10, 6)) / pow(10, 6),
	'fake_latitude' => rand($min_lat * pow(10, 6), $max_lat * pow(10, 6)) / pow(10, 6),
	'fake_longitude' => rand($min_lon * pow(10, 6), $max_lon * pow(10, 6)) / pow(10, 6),
	'pic_0' => get_fake_image($men, $women, $gender),
	'pic_1' => "",
	'pic_2' => "",
	'pic_3' => "",
	'pic_4' => "",
	'age' => rand(18, 40),
	'token' => ""
	));
	$fake_id = $bdd->lastInsertId();
	$i = 0;
	$x = rand(1, 3);
	$tags = [];
	while ($i < $x)
	{
		$tag = htmlspecialchars(get_fake_tag($tags));
		$tags[] = $tag;
		$req = $bdd->prepare('SELECT id FROM tags WHERE tag = ?');
		$req->execute(array(htmlspecialchars($tag)));
		if ($req->rowCount() == 0)
		{
			$req = $bdd->prepare('INSERT INTO tags (tag) VALUES (:tag)');
			$req->execute(array(
			'tag' => htmlspecialchars($tag)
			));
		}
		$req = $bdd->prepare('SELECT id FROM tags WHERE tag = ?');
		$req->execute(array(htmlspecialchars($tag)));
		$data = $req->fetch();
		$id = $data['id'];
		$req = $bdd->prepare('INSERT INTO links (tag_id, user_id) VALUES (:tag_id, :user_id)');
		$req->execute(array(
		'tag_id' => $id,
		'user_id' => $fake_id
		));
		$i++;
	}
	$j++;
}
header('Location: /');
exit;
?>
