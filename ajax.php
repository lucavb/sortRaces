<?php
$db = new PDO('mysql:host=localhost;dbname=perp;port=3306;', "php", "php", array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_TIMEOUT => 90)); //
$db->query("SET NAMES 'UTF8'");

if (isset($_GET["races"])) {
	$query = $db->query("SELECT ab.*, l.SollStartZeit
							FROM ablauf ab
							LEFT JOIN laeufe l ON l.Regatta_ID = ab.Regatta_ID AND l.Rennen = ab.Rennen AND l.Lauf = ab.Lauf
							INNER JOIN parameter p ON p.Sektion = 'Global'
							AND p.Schluessel = 'AktRegatta'
							AND p.Wert = ab.Regatta_ID
							ORDER BY ab.Order ASC
							");
	if ($query->rowCount() >= 1) {
		$arr = $query->fetchAll(PDO::FETCH_ASSOC);
		print_r(json_encode($arr));
		exit();
	}
	else {
	}
	$query = $db->query("SELECT r.Rennen, l.Lauf, l.`SollStartZeit`
							FROM rennen r
							LEFT JOIN laeufe l ON l.Regatta_ID = r.Regatta_ID
							AND l.Rennen = r.Rennen
							INNER JOIN parameter p ON p.Sektion = 'Global'
							AND p.Schluessel = 'AktRegatta'
							AND p.Wert = r.Regatta_ID
							ORDER BY l.SollStartZeit ASC");
	if ($query->rowCount() >= 1) {
		print_r(json_encode($query->fetchAll(PDO::FETCH_ASSOC)));
		exit();
	}
}
elseif (isset($_POST["list"]) ) {
	$arr = json_decode($_POST["list"]);
	$query = $db->query("SELECT AktRegatta FROM parameter");
	$Regatta_ID = 34;
	$counter = 1;
	$query = $db->prepare("DELETE FROM ablauf WHERE Regatta_ID = :id");
	$query->bindValue(":id", $Regatta_ID);
	$query->execute();
	foreach ($arr as $value) {
		$current_arr = objectToArray($value);
		print_r($current_arr);
		$query = $db->prepare("INSERT INTO ablauf(Regatta_ID, Rennen, Lauf, `Order`, `publish`) VALUES (:regatta, :rennen, :lauf, :order, :publish);");
		$query->bindValue(":regatta", $Regatta_ID);
		$query->bindValue(":rennen", $current_arr["race"]);
		$query->bindValue(":lauf", $current_arr["lauf"]);
		$query->bindValue(":order", $counter);
		$query->bindValue(":publish", $current_arr["publish"]);
		$query->execute();
		$counter++;
	}
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}
 
	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}
?>