<?php include($_SERVER['DOCUMENT_ROOT'].'/database.php'); ?>
<?php
require_once 'vendor/autoload.php';

$faker = Faker\Factory::create();
$j = 0;
while ($j < 1000)
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
	$req = $bdd->prepare('INSERT INTO users (user_id, email, login, last_name, first_name, password, gender, orientation, bio, popularity, last_log, latitude, longitude, fake_latitude, fake_longitude, auto_loc, confirmed, forgot, pic_0, pic_1, pic_2, pic_3, pic_4, age) VALUES (:user_id, :email, :login, :last_name, :first_name, :password, :gender, :orientation, :bio, :popularity, :last_log, :latitude, :longitude, :fake_latitude, :fake_longitude, 0, 1, 0, :pic_0, :pic_1, :pic_2, :pic_3, :pic_4, :age)');
	$req->execute(array(
	'user_id' => $randstring,
	'email' => $faker->email,
	'login' => $faker->userName,
	'last_name' => $faker->lastName,
	'first_name' => $faker->firstName,
	'password' => $hash,
	'gender' => rand(1, 2),
	'orientation' => rand(1, 3),
	'bio' => $faker->text(999),
	'popularity' => rand(0, 99),
	'last_log' => time(),
	'latitude' => $faker->latitude,
	'longitude' => $faker->longitude,
	'fake_latitude' => $faker->latitude,
	'fake_longitude' => $faker->longitude,
	'pic_0' => get_fake_image(),
	'pic_1' => "",
	'pic_2' => "",
	'pic_3' => "",
	'pic_4' => "",
	'age' => rand(18, 40)
	));
	$j++;
}
?>
<?php
function get_fake_image()
{
	$link = "imgs/";
	$name = ['aquaman', 'arrow', 'atom', 'batman', 'cyborg', 'deathstroke', 'flash', 'hal', 'john', 'red', 'superman', 'wonder'];
	$x = rand(0, count($name) - 1);
	$link .= $name[$x].".png";
	return ($link);
}
?>
