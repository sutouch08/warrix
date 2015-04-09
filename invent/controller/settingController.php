
<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
function updateConfig($config_name, $value="", $id_employee=0){
	if($value !=""){
		dbQuery("UPDATE tbl_config SET value='$value', id_employee='id_employee' WHERE config_name = '$config_name'");
		return true;
	}else{
		return false;
	}
}
if(isset($_GET['config'])&&isset($_GET['em'])){
	$id_employee = $_GET['em'];
	$role = $_POST['form_role'];
	if($role =="general"){
		$company = $_POST['company'];
		$brand = $_POST['brand'];
		$address = $_POST['company_address'];
		$post_code = $_POST['post_code'];
		$phone = $_POST['phone'];
		$fax = $_POST['fax'];
		$tax_id = $_POST['tax_id'];
		$email = $_POST['email'];
		$url = $_POST['home_page'];
		$barcode_type = $_POST['barcode_type'];
		$allow_under_zero = $_POST['allow_under_zero'];
		$view_order_in_days = $_POST['view_order_in_days'];
		$payment_detail = $_POST['payment_detail'];
		$email_to_neworder = $_POST['email_to_neworder'];
			updateConfig("EMAIL_TO_NEW_ORDER",$email_to_neworder, $id_employee);
			updateConfig("COMPANY_FULL_NAME",$company, $id_employee);
			updateConfig("COMPANY_NAME",$brand, $id_employee);
			updateConfig("COMPANY_ADDRESS",$address, $id_employee);
			updateConfig("COMPANY_POST_CODE",$post_code, $id_employee);
			updateConfig("COMPANY_PHONE",$phone, $id_employee);
			updateConfig("COMPANY_FAX_NUMBER",$fax, $id_employee);
			updateConfig("COMPANY_TAX_ID",$tax_id, $id_employee);
			updateConfig("COMPANY_EMAIL",$email, $id_employee);
			updateConfig("HOME_PAGE_URL",$url, $id_employee);
			updateConfig("BARCODE_TYPE",$barcode_type, $id_employee);
			updateConfig("ALLOW_UNDER_ZERO",$allow_under_zero, $id_employee);	
			updateConfig("VIEW_ORDER_IN_DAYS",$view_order_in_days, $id_employee);
			updateConfig("PAYMENT",$payment_detail, $id_employee);
			$message = base64_encode("ปรับปรุงการตั้งค่าแล้ว");
		header("location: ../index.php?content=config&general&message=$message");
	}else if($role =="product"){
		$new_product_date = $_POST['new_product_date'];
		$new_product_qty = $_POST['new_product_qty'];
		$features_product = $_POST['features_product'];
		$max_show_stock = $_POST['max_show_stock'];
		$vertical = $_POST['vertical'];
		$horizontal = $_POST['horizontal'];
		$additional = $_POST['additional'];
		updateConfig("NEW_PRODUCT_DATE", $new_product_date, $id_employee);
		updateConfig("FEATURES_PRODUCT", $features_product, $id_employee);
		updateConfig("MAX_SHOW_STOCK",$max_show_stock , $id_employee); 
		updateConfig("NEW_PRODUCT_QTY",$new_product_qty, $id_employee); 
		updateConfig("ATTRIBUTE_GRID_VERTICAL",$vertical, $id_employee);
		updateConfig("ATTRIBUTE_GRID_HORIZONTAL", $horizontal, $id_employee);
		updateConfig("ATTRIBUTE_GRID_ADDITIONAL", $additional, $id_employee);
		$message = base64_encode("ปรับปรุงการตั้งค่าแล้ว");
		header("location: ../index.php?content=config&product&message=$message");		
	}else if($role =="document"){
		$prefix_order = $_POST['prefix_order'];
		$prefix_recieve = $_POST['prefix_recieve'];
		$prefix_requisition = $_POST['prefix_requisition'];
		$prefix_lend = $_POST['prefix_lend'];
		$prefix_sponsor = $_POST['prefix_sponsor'];
		$prefix_consignment = $_POST['prefix_consignment'];
		$prefix_consign = $_POST['prefix_consign'];
		$prefix_return = $_POST['prefix_return'];
		updateConfig("PREFIX_ORDER",$prefix_order, $id_employee); /// ขาย
		updateConfig("PREFIX_RECIEVE", $prefix_recieve, $id_employee); /// รับสินค้าเข้า
		updateConfig("PREFIX_REQUISITION", $prefix_requisition, $id_employee); //เบิกสินค้า
		updateConfig("PREFIX_LEND", $prefix_lend, $id_employee);//ยืมสินค้า
		updateConfig("PREFIX_SPONSOR", $prefix_sponsor, $id_employee);///สปอนเซอร์
		updateConfig("PREFIX_CONSIGNMENT", $prefix_consignment, $id_employee); //ฝากขาย
		updateConfig("PREFIX_CONSIGN", $prefix_consign, $id_employee); //ตัดยอดฝากขาย
		updateConfig("PREFIX_RETURN", $prefix_return, $id_employee);
		$message = base64_encode("ปรับปรุงการตั้งค่าแล้ว");
		header("location: ../index.php?content=config&document&message=$message");		
	}else if($role=="popup"){
		$pop_on = $_POST['pop_on'];
		$delay = $_POST['loop'];
		$start = dbDate($_POST['from_date'])." 00:00:00";
		if($start =="1970-01-01"){ $start = date('Y-m-d 00:00:00'); }
		$end = dbDate($_POST['to_date'])." 23:59:59";
		if($end =="1970-01-01"){ $end = date('Y-m-d 23:59:59',strtotime("+1 month")); }
		$width = $_POST['width']; if($width =="" || $width ==0){ $width = 600; }
		$height = $_POST['height']; if($height =="" || $width ==0){ $height = 600; }
		$content = $_POST['content'];
		$active = $_POST['active'];
		if(isset($_POST['update_all'])){ 
			$sql = "UPDATE tbl_popup SET delay = $delay, start ='$start', end = '$end', content = '$content', width ='$width', height ='$height', active ='$active'";
		}else{
			$sql = "UPDATE tbl_popup SET delay = $delay, start ='$start', end = '$end', content = '$content', width ='$width', height ='$height', active ='$active' WHERE pop_on = '$pop_on' ";
		}
		if(dbQuery($sql)){
		$message = base64_encode("ปรับปรุงการตั้งค่าแล้ว");
		header("location: ../index.php?content=config&popup=y&pop_on=$pop_on&message=$message");	
		}else{
		$message = base64_encode("ปรับปรุงการตั้งค่าไม่สำเร็จ");
		header("location: ../index.php?content=config&popup=y&pop_on=$pop_on&error=$message");	
		}
			
	}else{
		$message = base64_encode("ไม่สามารถปรับปรุงการตั้งค่าได้"); 
		header("location: ../index.php?content=config&error=$message");	
	}
}
?>