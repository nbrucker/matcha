<?php include($_SERVER['DOCUMENT_ROOT']."/database.php") ?>
<?php include($_SERVER['DOCUMENT_ROOT']."/check_ajax.php") ?>
<?php
if (isset($_POST['age_min']) && isset($_POST['age_max']) && isset($_POST['popularity_min']) && isset($_POST['popularity_max']) && isset($_POST['latitude']) && isset($_POST['longitude']) && isset($_POST['distance_min']) && isset($_POST['distance_max']) && isset($_POST['tags']) && isset($_POST['order']) && isset($_POST['limit']))
{
	$users = [];
	$req = $bdd->prepare('SELECT age, pic_0, popularity, latitude, longitude, fake_latitude, fake_longitude, auto_loc, id, first_name, last_name, user_id FROM users WHERE age != "" AND pic_0 != "" AND confirmed = 1 AND gender != 0 AND orientation != 0');
	$req->execute(array());
	while ($data = $req->fetch())
		$users[] = $data;
	$tmp = [];
	$tags = json_decode($_POST['tags']);
	foreach ($users as $key => $el)
	{
		if (blocked($_SESSION['id'], $el['id'], $bdd))
			continue ;
		$latitude = $el['latitude'];
		$longitude = $el['longitude'];
		if ($el['auto_loc'] == 0)
		{
			$latitude = $el['fake_latitude'];
			$longitude = $el['fake_longitude'];
		}
		$el['distance'] = distance($latitude, $longitude, $_POST['latitude'], $_POST['longitude']);
		$nbr_tags = 0;
		if (count($tags) > 0)
		{
			$i = 0;
			$sql = "SELECT tags.id as id FROM tags INNER JOIN links ON links.tag_id = tags.id WHERE links.user_id = ? AND tags.tag IN (";
			foreach ($tags as $tag)
			{
				if ($i > 0)
					$sql .= ", ";
				$sql .= "?";
				$i++;
			}
			$sql .= ")";
			$req = $bdd->prepare($sql);
			array_unshift($tags, $el['id']);
			$req->execute($tags);
			$nbr_tags = $req->rowCount();
		}
		$el['tags'] = $nbr_tags;
		if ($_POST['age_min'] != "" && $el['age'] < intval($_POST['age_min']))
			continue ;
		if ($_POST['age_max'] != "" && $el['age'] > intval($_POST['age_max']))
			continue ;
		if ($_POST['popularity_min'] != "" && $el['popularity'] < intval($_POST['popularity_min']))
			continue ;
		if ($_POST['popularity_max'] != "" && $el['popularity'] > intval($_POST['popularity_max']))
			continue ;
		if ($_POST['distance_min'] != "" && $el['distance'] < intval($_POST['distance_min']))
			continue ;
		if ($_POST['distance_max'] != "" && $el['distance'] > intval($_POST['distance_max']))
			continue ;
		if (count($tags) > 0 && $nbr_tags <= 0)
			continue ;
		else
			$tmp[] = $el;
	}
	if ($_POST['order'] == 1)
		$users = sort_by($tmp, 'popularity', 1);
	else if ($_POST['order'] == 2)
		$users = sort_by($tmp, 'age', 0);
	else if ($_POST['order'] == 3)
		$users = sort_by($tmp, 'age', 1);
	else if ($_POST['order'] == 4)
		$users = sort_by($tmp, 'distance', 0);
	else if ($_POST['order'] == 5)
		$users = sort_by($tmp, 'distance', 1);
	else if ($_POST['order'] == 6)
		$users = sort_by($tmp, 'tags', 0);
	else if ($_POST['order'] == 7)
		$users = sort_by($tmp, 'tags', 1);
	else
		$users = sort_by($tmp, 'popularity', 0);
	$i = 0;
	while ($i < intval($_POST['limit']))
	{
		array_shift($users);
		$i++;
	}
	$i = 0;
	foreach ($users as $el)
	{
		if ($i >= 20)
		{
			?>
			<div id="more" onclick="loadMore();" class="more">MORE</div>
			<?php
			break ;
		}
		?>
		<a class="index_profile" href="/profile.php?u=<?php echo $el['user_id'] ?>">
			<div class="index_profile">
				<img src="<?php echo $el['pic_0'] ?>" class="index_profile">
				<span class="index_profile"><?php echo $el['first_name']." ".$el['last_name'] ?></span>
			</div>
		</a>
		<?php
		$i++;
	}
}
else
	echo "error";
?>
