<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";

/**************************  เพิ่มออเดอร์ใหม่  ***************************/
if(isset($_GET['add'])&&isset($_POST['id_customer'])){
	$id_customer = $_POST['id_customer'];
	$customer = new customer($id_customer);
	$id_employee = $_POST['id_employee'];
	$role = $_POST['role'];
	$id_cart = 0;
	$reference = get_max_role_reference("PREFIX_CONSIGNMENT",$role);
	$payment = "ฝากขาย";
	if($customer->id_address !=""){ $id_address = $customer->id_address; }else{ $id_address = 0; } 
	$current_state = 3;
	$shipping_no = 0;
	$invoice_no = 0;
	$delivery_no = 0;
	$delivery_date = "";
	$comment = $_POST['comment'];
	$valid = 0;
	$date_add = dbDate($_POST['doc_date']);
	$date_upd = date('Y-m-d');
	if(isset($_POST['auto_zone'])){
		$zone_name = $customer->full_name;
		$sql = dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name ='$zone_name' AND id_warehouse = 2");
		$row = dbNumRows($sql);  
		if($row<1){
			dbQuery("INSERT INTO tbl_zone (id_warehouse, barcode_zone, zone_name) VALUES (2, 0, '$zone_name')");
			list($id) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name ='$zone_name' AND id_warehouse = 2"));
			$id_zone = $id; // echo "id_zone = ".$id_zone;
		}else{
			list($id) = dbFetchArray($sql);
			$id_zone = $id;   // echo "else id_zone = ".$id_zone;
		}
	}
	if(isset($_POST['zone_id'])){ $id_zone = $_POST['zone_id']; }
	if(dbQuery("INSERT INTO tbl_order(reference, id_customer, id_employee, id_cart, id_address_delivery, current_state, payment, shipping_number, invoice_number, delivery_number, delivery_date, comment, valid, role, date_add, date_upd,order_status) VALUES ('$reference', $id_customer, $id_employee, $id_cart, $id_address, $current_state, '$payment', $shipping_no, $invoice_no, $delivery_no, '$delivery_date', '$comment', $valid, $role, '$date_add', NOW(),0)")){
	list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE reference = '$reference' AND id_customer = $id_customer"));
		dbQuery("INSERT INTO tbl_order_consignment (id_order, id_customer, id_zone, status) VALUES ( $id_order, $id_customer, $id_zone, 1)");
		header("location: ../index.php?content=consignment&add=y&id_order=$id_order&id_customer=$id_customer");
	}else{
		$message = "ไม่สามารถเพิ่มออเดอร์ใหม่ในฐานข้อมูลได้";  echo $message;
		header("location: ../index.php?content=consignment&add=y&error=$message");
	}
		
}
//*********************************** เพิ่มสินค้าในออเดอร์ (add order detail ) ******************************************//
if(isset($_GET['add_to_order'])){
	$id_order= $_POST['id_order'];
	$order= new order($id_order);
	$id_customer = $order->id_customer;
	$order_qty = $_POST['order_qty'];
	$id_product_attribute = $_POST['id_product_attribute'];
	$i = 0;
	$n = 1;
	foreach ($id_product_attribute as $id ){
		if($order_qty[$i] !=""){
			$product = new product();
			$customer = new customer($id_customer);
			$id_product = $product->getProductId($id_product_attribute);
			$product->product_detail($id_product, $order->id_customer);
			$product->product_attribute_detail($id);
			$total_amount = $order_qty[$i]*$product->product_sell;
				if($order->insertDetail($id, $order_qty[$i])){
						$message = "เพิ่ม $n รายการเรียบร้อย";
					$i++;
					$n++;
						}else{
					$message = "ทำรายการสำเร็จ $n รายการแรกเท่านั้น";
					header("location: ../index.php?content=consignment&add=y&id_order=$id_order&id_customer=$id_customer&error=$message");
					exit;
						}
		}else{
			$i++;
		}
	}
	header("location: ../index.php?content=consignment&add=y&id_order=$id_order&id_customer=$id_customer&message=$message");
}

/************************ state change ****************************/
if(isset($_GET['edit'])&&isset($_GET['state_change'])){
	$id_order = $_POST['id_order'];
	$id_employee = $_POST['id_employee'];
	$id_order_state = $_POST['order_state'];
	if($id_order_state != 0){
		if(order_state_change($id_order, $id_order_state, $id_employee)){
			$message = "เปลี่ยนสถานะเรียบร้อยแล้ว";
			header("location: ../index.php?content=consignment&id_order=$id_order&view_detail=y&message=$message");
		}else{
			$message = "เปลี่ยนสถานะไม่สำเร็จ";
			header("location: ../index.php?content=consignment&id_order=$id_order&view_detail=y&error=$message");
		}
		}else{
			$message = "คุณไม่ได้เลือกสถานะ";
			header("location: ../index.php?content=consignment&id_order=$id_order&view_detail=y&error=$message");
		}
}
if(isset($_GET['save_order'])){
	$id_order = $_GET['id_order'];
	$now = date("Y-m-d H:i:s");
	dbQuery("UPDATE tbl_order SET order_status = 1, date_add='$now' WHERE id_order = $id_order");
	$message = "บันทึกเรียบร้อยเเล้ว";
	header("location: ../index.php?content=consignment&message=$message");
}
if(isset($_GET['check_add'])){
	$user_id = $_COOKIE['user_id'];
	list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE id_employee = $user_id AND order_status = 0 AND role = 5"));
	if($id_order == ""){
		header("location: ../index.php?content=consignment&add=y");
	}else{
		$message = "ยังไม่ได้บันทึกออร์เดอร์นี้";
		header("location: ../index.php?content=consignment&add=y&id_order=$id_order&message1=$message");
	}
}
?>