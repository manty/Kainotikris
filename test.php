<?php
include "simplehtmldom_1_5\simple_html_dom.php";
include "db.php";
include "get_web_page.php";

$latest_prices = $db->query("SELECT i.*, p1.price as current_price, p1.date
FROM items i
JOIN prices p1 ON i.id = p1.items_id
LEFT OUTER
JOIN prices p2 ON i.id = p2.items_id AND p1.id < p2.id
WHERE p2.id IS NULL");
foreach ($latest_prices as $item) {
	//print_r($item["current_price"]."<br>");
}
$first_prices = $db->query("SELECT i.*, p1.price as first_price, p1.date
FROM items i
JOIN prices p1 ON i.id = p1.items_id
LEFT OUTER
JOIN prices p2 ON i.id = p2.items_id AND p1.id > p2.id
WHERE p2.id IS NULL");
foreach ($first_prices as $item) {
	//print_r($item["first_price"]."<br>");
}
//$items = array_merge($latest_prices, $first_prices["firts_price"]);
echo "<pre>";
print_r($latest_prices["0"]);