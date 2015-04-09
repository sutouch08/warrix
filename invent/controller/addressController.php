<?php
require "../../library/config.php";
require "../../library/functions.php";
///////////////////  AutoComplete //////////////////////
if(isset($_REQUEST['term'])&&isset($_GET['first_name'])){
	$qstring = "SELECT id_customer, first_name, last_name FROM tbl_customer WHERE first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%'";
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['id_customer'].":".$row['first_name'].":".$row['last_name'];
		}
		echo json_encode($data);//format the array into json data
	}else {
		echo "error";
	}
}
	
if(isset($_REQUEST['term'])&&isset($_GET['company'])){
	$qstring = "SELECT id_customer, company FROM tbl_customer WHERE company LIKE '%".$_REQUEST['term']."%' AND company !=''";
	$result = dbQuery($qstring);//query the database for entries containing the term
	if ($result->num_rows>0)	{
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['id_customer'].":".$row['company'];
		}
		echo json_encode($data);//format the array into json data
	}else {
		echo "error";
	}

}
if(isset($_GET['get_data'])&&isset($_GET['id_customer'])){
	$id_customer = $_GET['id_customer'];
	$sql = dbQuery("SELECT company, first_name, last_name FROM tbl_customer WHERE id_customer = '$id_customer'");
	$row = dbNumRows($sql);
	if($row>0){
	list($company, $first_name, $last_name) = dbFetchArray($sql);
	$data = $company.":".$first_name.":".$last_name;
	}else{
		$data = "no_data";
	}
	echo $data;
		
}

/////****************************************** เพิ่มที่อยู่ใหม่ ***********************************************//

if(isset($_GET['add'])&&isset($_POST['id_customer'])){
	$id_customer = $_POST['id_customer'];
	if(isset($_POST['first_name'])){ $first_name = $_POST['first_name']; }else{ $first_name = ""; }
	if(isset($_POST['last_name'])){ $last_name = $_POST['last_name']; }else{ $last_name = ""; }
	if(isset($_POST['company'])){ $company = $_POST['company'];}else{ $company = "";}
	if(isset($_POST['id_number'])){ $id_number = $_POST['id_number'];}else{ $id_number = "";}
	if(isset($_POST['address2'])){ $address2 = $_POST['address2'];}else{ $address2 = "";}
	if(isset($_POST['postcode'])){ $postcode = $_POST['postcode'];}else{ $postcode = "";}
	if(isset($_POST['other'])){ $other = $_POST['other'];}else{ $other = "";}
	$alias = $_POST['alias'];
	$address1 = $_POST['address1'];
	$city = $_POST['city'];
	$phone = $_POST['phone'];
	$active = 1;
	$date_add = dbDate(date('Y-m-d'));
	$date_upd = dbDate(date('Y-m-d'));
	if(dbQuery("INSERT INTO tbl_address( id_customer, alias, company, firstname, lastname, address1, address2, city, postcode, phone, id_number,active, date_add, date_upd) VALUES ($id_customer, '$alias', '$company', '$first_name', '$last_name', '$address1', '$address2', '$city', '$postcode', '$phone', '$id_number', $active, '$date_add', '$date_upd')")){
		$message = "เพิ่มที่อยู่เรียบร้อยแล้ว";
		header("location: ../index.php?content=address&message=$message");
	}else{
		$message = "เพิ่มที่อยู่ไม่สำเร็จ";
		header("location: ../index.php?content=address&error=$message");
	}
}
///*************************** แก้ไขที่อยู่ *************************************//
if(isset($_GET['edit'])&&isset($_POST['id_address'])){
	$id_address = $_POST['id_address'];
	if(isset($_POST['first_name'])){ $first_name = $_POST['first_name']; }else{ $first_name = ""; }
	if(isset($_POST['last_name'])){ $last_name = $_POST['last_name']; }else{ $last_name = ""; }
	if(isset($_POST['company'])){ $company = $_POST['company'];}else{ $company = "";}
	if(isset($_POST['id_number'])){ $id_number = $_POST['id_number'];}else{ $id_number = "";}
	if(isset($_POST['address2'])){ $address2 = $_POST['address2'];}else{ $address2 = "";}
	if(isset($_POST['postcode'])){ $postcode = $_POST['postcode'];}else{ $postcode = "";}
	if(isset($_POST['other'])){ $other = $_POST['other'];}else{ $other = "";}
	$alias = $_POST['alias'];
	$address1 = $_POST['address1'];
	$city = $_POST['city'];
	$phone = $_POST['phone'];
	$active = 1;
	$date_upd = dbDate(date('Y-m-d'));
	if(dbQuery("UPDATE tbl_address SET alias = '$alias', company ='$company', firstname = '$first_name', lastname = '$last_name', address1 ='$address1', address2 = '$address2', city = '$city', postcode ='$postcode', phone = '$phone', id_number = '$id_number', other = '$other', date_upd= '$date_upd' WHERE id_address = $id_address")){
		$message = "แก้ไขที่อยู่เรียบร้อยแล้ว";
		header("location: ../index.php?content=address&message=$message");
	}else{
		$message ="แก้ไขที่อยู่ไม่สำเร็จ";
		header("location: ../index.php?content=address&error=$message");
	}
}
///*************************** ลบที่อยู่ *************************************//
if(isset($_GET['delete'])&&isset($_GET['id_address'])){
	$id_address = $_GET['id_address'];
	if(dbQuery("DELETE FROM tbl_address WHERE id_address = $id_address")){
		$message = "ลบที่อยู่เรียบร้อยแล้ว";
		header("location: ../index.php?content=address&message=$message");
	}else{
		$message = "ลบที่อยู่ไม่สำเร็จ";
		header("location: ../index.php?content=address&error=$message");
	}
}

?>