<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
///////////////////  AutoComplete //////////////////////
if(isset($_GET['customer_name'])&&isset($_REQUEST['term'])){
	$qstring = "SELECT id_sponsor, tbl_sponsor.id_customer, first_name, last_name FROM tbl_customer LEFT JOIN tbl_sponsor ON tbl_customer.id_customer = tbl_sponsor.id_customer WHERE tbl_sponsor.active =1 AND (first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%')";
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{ 
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['id_sponsor'].":".$row['id_customer'].":".$row['first_name']." ".$row['last_name'];
		}
		echo  json_encode($data);//format the array into json data
	}else {
		echo "error";
	}
}
/***********************  add member ********************************/
if(isset($_GET['add_member'])&&isset($_POST['id_customer'])){
	$id_customer = $_POST['id_customer'];
	$customer = new customer($id_customer);
	$reference = $_POST['reference'];
	$limit_amount = $_POST['limit_amount'];
	$start_date = dbDate($_POST['start_date']);
	$end_date = dbDate($_POST['end_date']);
	$remark = $_POST['remark'];
	$active = $_POST['active'];
	$year = $_POST['year'];
	$checked = dbNumRows(dbQuery("SELECT reference FROM tbl_sponsor WHERE id_customer = $id_customer AND active = 1"));
	if($checked >0){
		$message = "มีรายชื่อนี้อยู่แล้วและยังอยู่ในระยะสัญญาไม่สามารถเพิ่มใหม่ได้หากยังอยู่ในระยะสัญญา";
		header("location: ../index.php?content=add_sponsor&add=y&error=$message");
		exit;
	}
	$sql = "INSERT INTO tbl_sponsor(reference, id_customer, limit_amount, start, end, remark, active,year) VALUES ('$reference', $id_customer, $limit_amount, '$start_date', '$end_date','$remark','$active','$year')";
	if(dbQuery($sql)){
		$message = "เพิ่มรายชื่อเรียบร้อยแล้ว";
		header("location: ../index.php?content=add_sponsor&message=$message");
	}else{
		$message = "เพิ่มรายชื่อไม่สำเร็จ";
		header("location: ../index.php?content=add_sponsor&add=y&error=$message");
	}
}

/***************************************** edit member  ****************************************/
if(isset($_GET['edit_member'])&&isset($_GET['id_sponsor'])){
	$id_sponsor = $_GET['id_sponsor'];
	$id_customer = $_POST['id_customer'];
	$customer = new customer($id_customer);
	$reference = $_POST['reference'];
	$limit_amount = $_POST['limit_amount'];
	$start_date = dbDate($_POST['start_date']);
	$end_date = dbDate($_POST['end_date']);
	$remark = $_POST['remark'];
	$active = $_POST['active'];
	$checked = dbNumRows(dbQuery("SELECT reference FROM tbl_sponsor WHERE id_customer = $id_customer AND active = 1 AND id_sponsor != $id_sponsor"));
	if($checked >0){
		$message = "มีรายชื่อนี้อยู่แล้วและยังอยู่ในระยะสัญญาไม่สามารถเพิ่มใหม่ได้หากยังอยู่ในระยะสัญญา";
		header("location: ../index.php?content=add_sponsor&edit=y&id_sponsor=$id_sponsor&error=$message");
		exit;
	}
	$sql = "UPDATE tbl_sponsor SET reference = '$reference', id_customer = $id_customer, limit_amount = $limit_amount, start = '$start_date', end = '$end_date', remark = '$remark', active = $active WHERE id_sponsor = $id_sponsor";
	if(dbQuery($sql)){
		$message = "แก้ไขข้อมูลเรียบร้อยแล้ว";
		header("location: ../index.php?content=add_sponsor&edit=y&id_sponsor=$id_sponsor&message=$message");
	}else{
		$message = "แก้ไขข้อมูลไม่สำเร็จ";
		header("location: ../index.php?content=add_sponsor&edit=y&id_sponsor=$id_sponsor&error=$message");
	}
}

//// add order
if(isset($_GET['add'])&&isset($_POST['id_customer'])){
	$id_customer = $_POST['id_customer'];
	$customer = new customer($id_customer);
	$reference = get_max_role_reference("PREFIX_SPONSOR",4);
	$payment = "สปอนเซอร์";
	$role = 4; 
	$id_employee = $_POST['id_employee'];
	$id_cart = 0;
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
	if(dbQuery("INSERT INTO tbl_order(reference, id_customer, id_employee, id_cart, id_address_delivery, current_state, payment, shipping_number, invoice_number, delivery_number, delivery_date, comment, valid, role, date_add, date_upd,order_status) VALUES ('$reference', $id_customer, $id_employee, $id_cart, $id_address, $current_state, '$payment', $shipping_no, $invoice_no, $delivery_no, '$delivery_date', '$comment', $valid, $role, '$date_add', NOW(),0)")){
		list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE reference = '$reference' AND id_customer = $id_customer"));
		header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&id_customer=$id_customer");
	}else{
		$message = "ไม่สามารถเพิ่มออเดอร์ใหม่ในฐานข้อมูลได้";
		header("location: ../index.php?content=sponsor&add=y&error=$message");
	}
	
}
if(isset($_GET['edit_order'])&&isset($_POST['new_qty'])&&$_POST['new_qty'] !=""){
	$id_order = $_POST['id_order'];
	$id_product_attribute = $_POST['id_product_attribute'];
	$qty = $_POST['new_qty'];
	list($old_qty, $old_total_amount) = dbFetchArray(dbQuery("SELECT product_qty, total_amount FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute"));
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	if($customer->credit_amount != 0.00){
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, $order->id_customer);
		$product->product_attribute_detail($id_product_attribute);
		$total_amount = $qty * $product->product_sell; 
		$new_total_amount = $total_amount - $old_total_amount;
		if($qty<$old_qty){
			if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
		}else{
			if($order->check_credit($new_total_amount)){
				if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
			}else{
				$message = "แก้ไขไม่สำเร็จ เนื่องจากเคดิตไม่พอ เครดิตคงเหลือ : ".$customer->credit_balance;
				header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
		}
		}else if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
}

if(isset($_GET['add_to_order'])){
	$id_order= $_POST['id_order'];
	$order= new order($id_order);
	$id_customer = $order->id_customer;
	$order_qty = $_POST['order_qty'];
	$id_product_attribute = $_POST['id_product_attribute'];
	$i = 0;
	$n = 0;
	$missing="";
	foreach ($id_product_attribute as $id ){
		if($order_qty[$i] !=""){
			$product = new product();
			$customer = new customer($id_customer);
			$id_product = $product->getProductId($id);
			$product->product_detail($id_product, $order->id_customer);
			$product->product_attribute_detail($id);
			$total_amount = $order_qty[$i]*$product->product_sell;
				if(!ALLOW_UNDER_ZERO){
				$instock = $product->available_qty() - $product->order_qty(); 
				if($order_qty[$i]>$instock){
					$missing .= $product->reference." มียอดคงเหลือไม่เพียงพอ &nbsp;<br/>";
					$i++;
				}else{
					if($order->insertDetail($id, $order_qty[$i])){
						$i++;
						$n++;
							}else{
						$message = $order->error_message;
						header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&id_customer=$id_customer&error=$message");
						exit;
							}
					}
				}else{
					if($order->insertDetail($id, $order_qty[$i])){
					$i++;
					$n++;
						}else{
					$message = $order->error_message;
					header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&id_customer=$id_customer&error=$message");
					exit;
						}
				}
		}else{
			$i++;
		}
	}
	if($missing ==""){
	$message = "เพิ่ม $n รายการเรียบร้อย";
	header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&id_customer=$id_customer&message=$message");
	}else{
	$message = "เพิ่ม $n รายการเรียบร้อย";
	header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&id_customer=$id_customer&message=$message&missing=$missing");	
	}
}
/// เพิ่มรายการสินค้าในออเดอร์แบบคีย์มือ
if(isset($_GET['add'])&&isset($_GET['insert_detail'])){
	$id_order = $_POST['id_order'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$order = new order($id_order);
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, $order->id_customer);
	$product->product_attribute_detail($id_product_attribute);
	if($order->insertDetail($id_product_attribute, $qty)){
		$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&message=$message");
	}else{
		$message = "เพิ่มสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&error=$message");
	}	
}

if(isset($_GET['edit_order'])&&isset($_GET['add_detail'])&& $_POST['qty']!=""){
	$id_order = $_POST['id_order'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product, $order->id_customer);
	$product->product_attribute_detail($id_product_attribute);
	$total_amount = $qty * $product->product_sell;
	if($customer->credit_amount != 0.00){
		if($order->check_credit($total_amount)){
			if($order->insertDetail($id_product_attribute, $qty)){
				$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
				header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&message=$message");
		
			}else{
				$message = "เพิ่มสินค้าไม่สำเร็จ";
				header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
		}else{
			$message = "ไม่สามารถเพิ่มรายการได้เนื่องจากเครดิตคงเหลือไม่พอ  เครดิตคงเหลือ : ".$customer->credit_balance." ฿";
			header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&error=$message");
			exit;
		 }
	}else{
		if($order->insertDetail($id_product_attribute, $qty)){
				$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
				header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&message=$message");
		
			}else{
				$message = "เพิ่มสินค้าไม่สำเร็จ";
				header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
	}
		
}

/// ลบในหน้า แก้ไข
if(isset($_GET['delete'])&&isset($_GET['id_order'])&&isset($_GET['id_product_attribute'])){
	$id_order = $_GET['id_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	if(dbQuery("DELETE FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute")){
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&message=$message");
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=sponsor&edit=y&id_order=$id_order&view_detail=y&error=$message");
	}	
}

/// ลบในหน้า เพิ่ม //
if(isset($_GET['delete'])&&isset($_GET['id_order_detail'])){
	$id_order_detail = $_GET['id_order_detail'];
	list($id_order)=dbFetchArray(dbQuery("SELECT id_order FROM tbl_order_detail WHERE id_order_detail = $id_order_detail"));
	if(dbQuery("DELETE FROM tbl_order_detail WHERE id_order_detail = $id_order_detail")){
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&message=$message");	
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&error=$message");
	}
}
if(isset($_GET['save_order'])){
	$id_order = $_GET['id_order'];
	$now = date("Y-m-d H:i:s");
	dbQuery("UPDATE tbl_order SET order_status = 1, date_add='$now' WHERE id_order = $id_order");
	$message = "บันทึกเรียบร้อยเเล้ว";
	header("location: ../index.php?content=sponsor&message=$message");
}
if(isset($_GET['check_add'])){
	$user_id = $_COOKIE['user_id'];
	list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE id_employee = $user_id AND order_status = 0 AND role = 4"));
	if($id_order == ""){
		header("location: ../index.php?content=sponsor&add=y");
	}else{
		$message = "ยังไม่ได้บันทึกออร์เดอร์นี้";
		header("location: ../index.php?content=sponsor&add=y&id_order=$id_order&message1=$message");
	}
}
?>