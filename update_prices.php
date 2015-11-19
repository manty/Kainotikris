<?php
include "simplehtmldom_1_5\simple_html_dom.php";
include "db.php";
include "get_web_page.php";

$items = $db->query("select * from items where updated < now() - 86400");
foreach($items as $item) {
	$url = $item["url"];
	$html = str_get_html(get_web_page($url)["content"]);
	foreach($html->find('div[class=product-name] h1') as $e)
       	$name = $e->innertext;
    $name = addslashes($name);  
	foreach($html->find('span[class=num]') as $e)
       	$prices = $e->innertext;  
	$price = explode("€", $prices, 2)[0];
	$price = explode(">", $price, 2)[1];				
	$db->query("INSERT INTO prices (items_id, price) values (\"".$item["id"]."\",\"".$price."\")");
	$db->query("update items set updated = now() where id = ".$item["id"]);
}