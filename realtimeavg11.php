<?php

/*	realtimeavg11.php
	IKYTraderFramework.
	Trend detector based on the average value of ask rate of the current date.
	Prints --- if the average value of the rate is downtrend.
	Prints +++ if the average value of the rate is uptrend.
	Assumes that only one type of value is in database (here : DAX30).
*/

define("MYSQL_SERVER", "localhost");
define("MYSQL_USER", "root");
define("MYSQL_PASSWORD", "11121975");
define("MYSQL_TRD_DB", "TRD");
define("MYSQL_TRD_MAIN_TABLE", "history");

$connloc = new mysqli(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD);
$r = mysqli_query($connloc, "CREATE DATABASE IF NOT EXISTS " . MYSQL_TRD_DB);
$sql = "CREATE TABLE IF NOT EXISTS " . MYSQL_TRD_DB . "." . MYSQL_TRD_MAIN_TABLE . " (`id` bigint(20) NOT NULL AUTO_INCREMENT, `datetime` datetime, `name` varchar(64), `bid` float, `ask` float, `diff` float, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
$r = mysqli_query($connloc, $sql);

$objDateTime = new DateTime('NOW');
$strdt = $objDateTime->format(DateTime::ISO8601);
$strdtday = substr($strdt, 0, 10);
//echo $strdt . "\r\n";

$previous_avg_ask = "";
while(true){
	$sql = "SELECT avg(ask) FROM " . MYSQL_TRD_DB . "." . MYSQL_TRD_MAIN_TABLE . " WHERE datetime like '" . $strdtday . "%'";
	$r = mysqli_query($connloc, $sql);
	//echo mysqli_error($connloc) . "\r\n";
	if ($r->num_rows>0){
		while($row = $r->fetch_assoc()){
			$avg_ask = $row["avg(ask)"];
			// echo $avg_ask . " : ";
			if ($previous_avg_ask != ""){

				$objDateTime = new DateTime('NOW');
				$strdt = $objDateTime->format(DateTime::ISO8601);

				if ($avg_ask>$previous_avg_ask){
					echo $strdt . " " . $avg_ask . " avg ask +++" . "\r\n";
				} else if ($avg_ask<$previous_avg_ask){
					echo $strdt . " " . $avg_ask . " avg ask ---" . "\r\n";
				} else {
					//echo "\r\n";//echo "0" . "\r\n";
				}
			} else {
				//echo "\r\n";
			}
			$previous_avg_ask = $avg_ask;
		}
	}
	sleep(1);
}

