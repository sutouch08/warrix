<?php
require "../../library/config.php";
require "../function/tools.php";
if(isset($_GET['add'])&&isset($_POST['id_employee'])){
	$id_employee = $_POST['id_employee'];
	$id_group = $_POST['id_group'];
	$row = dbNumRows("SELECT id_employee FROM tbl_sale WHERE id_employee = $id_employee");
	if($row>0){
		$message = "พนักงานคนนี้เป็นพนักงานขายอยู่แล้ว";
		header("location: ../index.php?content=sale&error=$message");
	}else{
	if(dbQuery("INSERT INTO tbl_sale(id_employee, id_group) VALUES ( $id_employee, $id_group)")){
		$message = "เพิ่มพนักงานขายเรียบร้อยแล้ว";
		header("location: ../index.php?content=sale&message=$message");
		}
	}
}

if(isset($_GET['edit'])&&isset($_POST['id_sale'])){
	$id_sale = $_POST['id_sale'];
	$id_group = $_POST['id_group'];
	$sql = dbQuery("UPDATE tbl_sale SET id_group = $id_group WHERE id_sale = $id_sale");
	if(!$sql){
		$message= "แก้ไขข้อมูลไม่สำเร็จ";
		header("location: ../index.php?content=sale&error=$message");
	}else if($sql){
		$message = "ปรับปรุงข้อมูลพนักงานขายเรียบร้อยแล้ว";
		header("location: ../index.php?content=sale&message=$message");
	}
}

if(isset($_GET['delete'])&&isset($_GET['id_sale'])){
	$id_sale = $_GET['id_sale'];
	if(dbQuery("DELETE FROM tbl_sale WHERE id_sale = $id_sale")){
		$message = "ลบพนักงานขายเรียบร้อยแล้ว";
		header("location: ../index.php?content=sale&message=$message");
	}else{
		$message= "ลบพนักงานขายไม่สำเร็จ";
		header("location: ../index.php?content=sale&error=$message");
	}
}
	
?>