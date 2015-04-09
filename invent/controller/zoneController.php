<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
///////////////////  AutoComplete //////////////////////
if(isset($_GET['get_consign_zone'])&&isset($_REQUEST['term'])){
	if($_REQUEST['term'] =="*"){
		$qstring = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE id_warehouse = 2 AND id_zone >0");
	}else{
		$qstring = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE id_warehouse = 2 AND  zone_name LIKE '%".$_REQUEST['term']."%'");
		//echo "SELECT id_zone, zone_name FROM tbl_zone WHERE id_warehouse = 2 AND  zone_name LIKE '%".$_REQUEST['term']."%'";
	}
	$row = dbNumRows($qstring);
	if($row > 0)
	{
		$data=array();
		$i = 0;
		while($i<$row)
		{
			$rs = dbFetchArray($qstring);
			$data[] = $rs['zone_name'].":".$rs['id_zone'];
			$i++;
		}
		echo json_encode($data);
	}else{
		echo "error";
	}
}
//********************* ตรวจสอบชื่อซ้ำกันหรือไม่ ก่อนเพิ่มหรือแก้ไข ********************************//
if(isset($_GET['zone_name'])&&isset($_GET['id_warehouse'])){
	$zone_name = $_GET['zone_name'];
	$id_warehouse = $_GET['id_warehouse'];
	$row =dbNumRows(dbQuery("SELECT zone_name FROM tbl_zone WHERE zone_name='$zone_name' AND id_warehouse=$id_warehouse"));
	if($row >0){
		$message ="1";
		echo $message;
	}else{
		$message ="0";
		echo $message;
	}
}
//********************************** ตรวจสอบบาร์โค้ดซ้ำก่อน เพิ่มหรือแก้ไข **********************************//
if(isset($_GET['barcode_zone'])&&isset($_GET['id_warehouse'])){
	$barcode_zone = $_GET['barcode_zone'];
	$id_warehouse = $_GET['id_warehouse'];
	$row =dbNumRows(dbQuery("SELECT barcode_zone FROM tbl_zone WHERE barcode_zone='$barcode_zone' AND id_warehouse=$id_warehouse"));
	if($row >0){
		$message ="1";
		echo $message;
	}else{
		$message ="0";
		echo $message;
	}
}
//********************************** เพิ่มโซน ***********************************************//
if(isset($_GET['add'])&&isset($_POST['zone_name'])){
	$zone_name = $_POST['zone_name'];
	$barcode_zone = $_POST['barcode_zone'];
	$id_warehouse = $_POST['id_warehouse'];
	dbQuery("INSERT INTO tbl_zone (id_warehouse, barcode_zone, zone_name) VALUES ($id_warehouse,'$barcode_zone','$zone_name')");
	header("location:../index.php?content=zone&add=y&id_warehouse=$id_warehouse");
}
//********************************************* แก้ไขโซน *****************************************//
if(isset($_GET['edit'])&&isset($_POST['id_zone'])){
	$id_zone = $_POST['id_zone'];
	$zone_name = $_POST['zone_name'];
	$barcode_zone = $_POST['barcode_zone'];
	$id_warehouse = $_POST['id_warehouse'];
	dbQuery("UPDATE tbl_zone SET id_warehouse =$id_warehouse, barcode_zone='$barcode_zone', zone_name = '$zone_name' WHERE id_zone = $id_zone");
	header("location: ../index.php?content=zone");
}
//******************************************  ลบโซน *************************************//
if(isset($_GET['delete'])&&isset($_GET['id_zone'])){
	$id_zone = $_GET['id_zone'];
	$check = dbNumRows(dbQuery("SELECT qty FROM product_zone WHERE id_zone = $id_zone AND qty >0")); // ตรวจสอบยอดสินค้าคงเหลือภายในโซนก่อนลบ
	if($check <1 ){																				// ไม่มีสินค้าคงเหลือภายในโซน
	dbQuery("DELETE FROM tbl_zone WHERE id_zone = $id_zone");					// ลบสินค้าได้
	header("location: ../index.php?content=zone");
	}else{
		$error_message = "คุณไม่สามารถลบโซนนี้ได้เนื่องจากมีรายการสินค้าคงเหลือในโซนนี้"; //ถ้ามีสินค้าคงเหลือภายในโซน ให้แจ้งเตือนและยังไม่ลบ
		header("location: ../index.php?content=zone&error=$error_message");
	}
}
if(isset($_GET['check'])&&isset($_GET['barcode_zone'])){
	$barcode_zone = $_GET['barcode_zone'];
	$sql = dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '$barcode_zone'");
	$row = dbNumRows($sql);
	if($row>0){
		list($id_zone) = dbFetchArray($sql);
		echo $id_zone;
	}else{
		echo "0";
	}
}

if( isset($_GET['stock_zone_report']) && isset($_REQUEST['term'])){
	$qstring = "SELECT zone_name FROM tbl_zone WHERE zone_name LIKE '%".$_REQUEST['term']."%'";
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[] = $row['zone_name'];
		}
		echo json_encode($data);//format the array into json data
	}else {
		echo "error";
	}

}
?>