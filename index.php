<html>
<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body >

<div class="container-fluid">

<form class="navbar-form navbar-right" action="index.php?action=add" method="post">
  <div class="form-group">
    <input type="text" class="form-control" placeholder="Pridėti naują prekę" name="url">
  </div>
  <button type="submit" class="btn btn-default">Pridėti</button>
</form>

<?php
include "simplehtmldom_1_5\simple_html_dom.php";
include "db.php";
include "get_web_page.php";

$items = $db->query("SELECT i.*, p1.price FROM items i JOIN prices p1 ON i.id = p1.items_id left outer join prices p2 on i.id = p2.items_id and (p1.id < p2.id) where p2.id is null;");
echo('<table class="table table-hover table-bordered table-condensed table-striped">');
echo("<th>Pavadinimas</th><th>Dabartinė kaina</th><th>Pradinė kaina</th><th>Pokytis</th><th></th>");
foreach($items as $item) {
	$diff = $item["start_price"] - $item["current_price"];
	echo("<tr><td>".$item["name"]."</td><td>".$item["current_price"]."</td><td>".$item["start_price"]."</td><td>".$diff."</td><td><a href=\"index.php?action=delete&id=".$item["id"]."\">Trinti</a></td></tr>");
}
echo("</table>");

if(isset($_GET["action"])){
	if($_GET["action"] == "add" and isset($_POST["url"])) {
		$url = htmlentities($_POST["url"]);
		$html = str_get_html(get_web_page($url)["content"]);
		foreach($html->find('div[class=product-name] h1') as $e)
       		$name = $e->innertext;
       		$name = addslashes($name);  
		foreach($html->find('span[class=num]') as $e)
       		$prices = $e->innertext;  
			$price = explode("€", $prices, 2)[0];
			$price = explode(">", $price, 2)[1];
		$items = $db->query("select * from items where url =\"".$url."\"");
		if($items->rowCount() == 0) {						
			$db->query("INSERT INTO items (name, url) values (\"".$name."\",\"".$url."\")");
			$items_id = $db->lastInsertId();
			$db->query("INSERT INTO prices (items_id, price) values (\"".$items_id."\",\"".$price."\")");
			header('Location: index.php');
		} else {	
			header('Location: index.php');
		}
	}


	if($_GET["action"] == "delete" and isset($_GET["id"])) {
		$db->query("DELETE FROM items WHERE id = ".$_GET["id"]);
		$db->query("DELETE FROM prices WHERE items_id = ".$_GET["id"]);
		header('Location: index.php');
	}

	if($_GET["action"] == "history" and isset($_GET["id"])) {
		$items = $db->query("select * from prices where items_id = ".$_GET["id"]." order by date desc");
		echo('<table class="table table-hover table-bordered table-condensed table-striped">');
		echo("<th>Data</th><th>Kaina</th>");
		foreach($items as $item) {
			echo("<tr><td>".$item["date"]."</td><td>".$item["price"]."</td></tr>");
		}
		echo("</table>");
	}
}


?>
</div>