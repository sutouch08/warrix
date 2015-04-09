<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
$user_id = $_COOKIE['user_id'];
//****************************  เพิ่มพนักงานใหม่ ******************************//
if(isset($_GET['add'])){
	$data = array($_POST['id_profile'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['s_key'], $_POST['active']);
	$employee = new employee();
	if($employee->addEmployee($data)){
		$message = "เพิ่มพนักงานเรียบร้อย";
		header("location: ../index.php?content=Employee&message=$message");
	}else{
		$message = $employee->error_message;
		header("location: ../index.php?content=Employee&add=y&error=$message");
	}
}

//****************************  เแก้ไขข้อมูลพนักงาน ******************************//
if(isset($_GET['edit'])){
	$id_employee = $_POST['id_employee'];
	$data = array($id_employee, $_POST['id_profile'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['s_key'], $_POST['active']);
	$employee = new employee();
	if($employee->editEmployee($data)){
		$message = "แก้ไขข้อมูลเรียบร้อยแล้ว";
		header("location: ../index.php?content=Employee&edit=y&id_employee=$id_employee&message=$message");
	}else{
		$message = $employee->error_message;
		header("location: ../index.php?content=Employee&edit=y&id_employee=$id_employee&error=$message");
	}
}

//****************************  ลบพนักงาน ******************************//
if(isset($_GET['drop'])){
	$id_employee = $_GET['id_employee'];
	$employee = new employee();
	if($employee->deleteEmployee($id_employee)){
		$message = "ลบพนักงานเรียบร้อยแล้ว";
		header("location: ../index.php?content=Employee&message=$message");
	}else{
		$message = $employee->error_message;
		header("location: ../index.php?content=Employee&error=$message");
	}
}


//***********************  reset password  ************************************//
if(isset($_GET['reset_password'])){
	$id_employee = $_POST['id_employee'];
	$data = array($id_employee, $_POST['email'], $_POST['password']);
	$employee = new employee();
	if($employee->reset_password($data)){
		$message = "แก้ไขข้อมูลเรียบร้อยแล้ว";
		header("location: ../index.php?content=Employee&reset_password=y&id_employee=$id_employee&message=$message");
	}else{
		$message = $employee->error_message;
		header("location: ../index.php?content=Employee&reset_password=y&id_employee=$id_employee&error=$message");
	}
}

//*******************  Active / Disactive พนักงาน  ************************//
if(isset($_GET['active'])&&isset($_GET['id_employee'])){
	$employee = new employee();
	if($employee->change_status($_GET['id_employee'], $_GET['active'])){
		header("location: ../index.php?content=Employee");	
	}else{
		$message = $employee->error_message;
		header("location: ../index.php?content=Employee&error=$message");
	}
}
?>