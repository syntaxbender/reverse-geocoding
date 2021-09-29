<?php
/*
*
* Author : SyntaxBender
* Description : Reverse Geocoding on epsg 4623 with polygon/multipolygon geographical data
*
* Dont forget to check is defined reverseGeocoding as stored procedure on db
*
* delimiter //
* CREATE PROCEDURE reverseGeocoding (IN point CHAR(255))
* BEGIN
*	set @loc_point = ST_GeomFromText(point,4326);
*	set @region_id=(select id FROM regions where ST_INTERSECTS(geometry, @loc_point) limit 1);
*	set @city_id=(select id FROM cities where ST_INTERSECTS(geometry, @loc_point) and region_id=@region_id limit 1);
*	set @county_id=(select id from counties where ST_INTERSECTS(geometry, @loc_point) and city_id=@city_id limit 1);
*	select @county_id as county,@city_id as city,@region_id as region;
* END//
* delimiter ;
*
*/

class GeoCoder{
	public $db;
	public function __construct($dbname,$user,$pass,$host = "localhost"){
		try {
			$this->db = new PDO("mysql:host=".$host.";dbname=".$dbname, $user, $pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch ( PDOException $e ){
			print $e->getMessage();
			exit();
		}
	}
	public function reverseGeocoding($lat,$lon){
		try {
			$point = "POINT(".$lat." ".$lon.")";
			$sqlquery = $this->db->prepare("CALL reverseGeocoding(?);");
			$sqlquery->execute([$point]);
			$data = $sqlquery->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			print $e->getMessage();
		}
		return $data;
	}
}
$start_time = microtime(true);
$geocode = new GeoCoder("geography","root","12345678");
$data = $geocode->reverseGeocoding($_GET["lat"],$_GET["lon"]);
//echo json_encode($data);
foreach ($data as $key => $value) {
	echo "<b>".$key."</b> : ".$value."<br>";
}
$end_time = microtime(true);
$execution_time = ($end_time - $start_time);
echo " Execution time of script = ".$execution_time." sec";
?>