<?php 
require "../../library/config.php";
require "../../library/functions.php";
	$id_employee = $_COOKIE['user_id'];
	$id_profile = $_POST['id_profile'];	
	for($loop=1;$loop<=$_POST["loop"];$loop++){
		$id_tab = $_POST["id_tab$loop"];
		if(isset($_POST["view$loop"])){
			$view = $_POST["view$loop"];
		}else{
			$view = "0";
		}
		if(isset($_POST["add$loop"])){
			$add = $_POST["add$loop"];
		}else{
			$add = "0";
		}
		if(isset($_POST["edit$loop"])){
			$edit = $_POST["edit$loop"];
		}else{
			$edit = "0";
		}
		if(isset($_POST["delete$loop"])){
			$delete = $_POST["delete$loop"];
		}else{
			$delete = "0";
		}
		dbQuery("UPDATE tbl_access SET `view` = '$view',`add` = '$add',`edit` = '$edit',`delete` = '$delete' WHERE id_profile = '$id_profile' and id_tab = '$id_tab'");
		$message = "แก้ไขข้อมูลการกำหนดสิทธิ์เรียบร้อย";
		header("location: ../index.php?content=securable&message=$message");
	}

?>