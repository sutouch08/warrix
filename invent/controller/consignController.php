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
	$order_consign = $_POST['order_consign'];
	$id_cart = 0;
	$date_add = dbDate($_POST['doc_date']);
	$reference = get_max_role_reference_consign("PREFIX_CONSIGN",$role,$date_add);
	$payment = "ตัดยอดฝากขาย";
	if($customer->id_address !=""){ $id_address = $customer->id_address; }else{ $id_address = 0; } 
	$current_state = 1;
	$shipping_no = 0;
	$invoice_no = 0;
	$delivery_no = 0;
	$delivery_date = "";
	$comment = $_POST['comment'];
	$valid = 0;
	$date_upd = date('Y-m-d');
	if(isset($_POST['auto_zone'])){
		$sql = dbQuery("SELECT id_zone FROM tbl_order_consignment WHERE id_customer ='$id_customer'");
		$row = dbNumRows($sql);  
		if($row<1){
			$message = "การเลือกอัตโนมัติไม่พบโซนที่ต้องการกรุณาระบุโซน";
			header("location: ../index.php?content=consignment&add=y&error=$message");
			exit;
		}else{
			list($id) = dbFetchArray($sql);
			$id_zone = $id;
		}
	}
	if(isset($_POST['zone_id'])){ $id_zone = $_POST['zone_id']; }
	//ต่อ
	if($order_consign !=""){
		list($id_zone_retrun) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_order_consign WHERE id_order_consign = '$order_consign'"));
		$sql = dbQuery("SELECT id_product_attribute,qty FROM tbl_order_consign_detail WHERE id_order_consign = '$order_consign'");
	$rs = dbNumRows($sql);
	$i = 0;
	while($i<$rs){
		list($id_product_attribute,$qty)= dbFetchArray($sql);
			list($id_stock,$qty_stock) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = $id_zone_retrun AND id_product_attribute = $id_product_attribute"));
			if($id_stock != ""){
				$sumqty = $qty + $qty_stock;
				dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = $id_stock");
			}else{
				dbQuery("INSERT INTO tbl_stock(id_zone,id_product_attribute,qty)VALUES('$id_zone_retrun',$id_product_attribute,'$qty')");
			}
		$i++;
	}
	dbQuery("DELETE FROM tbl_order_consign_detail WHERE id_order_consign = $order_consign");
	dbQuery("UPDATE tbl_order_consign SET id_customer = $id_customer ,id_zone = $id_zone , comment = '$comment' WHERE id_order_consign = $order_consign");
	header("location: ../index.php?content=consign&add=y&id_order_consign=$order_consign");
	}else{
	if(dbQuery("INSERT INTO tbl_order_consign(reference, id_customer, date_add,comment,id_zone) VALUES ('$reference','$id_customer','$date_add','$comment','$id_zone')")){
	list($id_consign) = dbFetchArray(dbQuery("SELECT id_order_consign FROM tbl_order_consign WHERE reference = '$reference'"));
		header("location: ../index.php?content=consign&add=y&id_order_consign=$id_consign");
	
	}else{
		$message = "ไม่สามารถเพิ่มการตัดยอดฝากขายใหม่ในฐานข้อมูลได้";
		header("location: ../index.php?content=consign&add=y&error=$message");
	}
	}	
}

if(isset($_GET['update_order'])&&isset($_GET['id_order_consign']) ){
	$id_order_consign = $_GET['id_order_consign'];
	$id_customer = $_GET['id_customer'];
	$id_zone = $_GET['id_zone'];
	$comment = $_GET['comment'];
	$date_add = dbDate($_GET['date_add']);
	$sql = "UPDATE tbl_order_consign SET id_customer = '$id_customer', id_zone = '$id_zone', comment = '$comment',  date_add = '$date_add' WHERE id_order_consign = '$id_order_consign'";
	if(dbQuery($sql)){
		echo 1;
	}else{
		echo 0;
	}	
}
//*********************************** เพิ่มสินค้าในออเดอร์ (add order detail ) ******************************************//
if(isset($_GET['add_to_order'])){
	$id_order_consign= $_POST['id_order_consign'];
	$consign= new consign($id_order_consign);
	$id_customer = $consign->id_customer;
	$date_add = date("Y-m-d",strtotime($consign->date_add));
	$order_qty = $_POST['order_qty'];
	$id_product_attribute = $_POST['id_product_attribute'];
	$i = 0;
	$n = 0;
	foreach ($id_product_attribute as $id ){//echo $order_qty[$i];
		if($order_qty[$i] !=""){
			$product = new product();
			$customer = new customer($id_customer);
			$id_product = $product->getProductId($id);
			$product->product_detail($id_product, $consign->id_customer);
			$product->product_attribute_detail($id);
			$product_price = $product->product_price;
			list($id_order_consign_detail,$qty) = dbFetchArray(dbQuery("SELECT id_order_consign_detail,qty FROM tbl_order_consign_detail WHERE id_order_consign = $id_order_consign AND id_product_attribute = $id"));
			list($id_zone,$id_consign_check) = dbFetchArray(dbQuery("SELECT id_zone,id_consign_check FROM tbl_order_consign WHERE id_order_consign = '$id_order_consign'"));
			list($stock_qty,$id_stock) = dbFetchArray(dbQuery("SELECT qty,id_stock FROM tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id'"));
			$stock = $stock_qty - $order_qty[$i];
			if($id_order_consign_detail != ""){
				$sumqty = $qty + $order_qty[$i];
				dbQuery("UPDATE tbl_order_consign_detail SET qty = $sumqty WHERE id_order_consign_detail = $id_order_consign_detail");	
			}else{
				$time = date("H:i:s");
				$date_upd = $date_add." ".$time;
				$sumqty = $order_qty[$i];
				dbQuery("INSERT INTO tbl_order_consign_detail (id_order_consign,id_product_attribute,product_price,reduction_percent,reduction_amount,qty,date_upd)VALUES('$id_order_consign','$id','$product_price',0,0,'$sumqty','$date_upd')");
			}
		dbQuery("UPDATE tbl_stock SET qty = '$stock' WHERE id_stock = $id_stock");
			dbQuery("DELETE FROM tbl_stock WHERE id_stock = $id_stock AND qty = 0");
			if($id_consign_check != 0){
			list($id_consign_check_detail,$qty_check) = dbFetchArray(dbQuery("SELECT id_consign_check_detail,qty_check FROM tbl_consign_check_detail WHERE id_consign_check = $id_consign_check AND id_product_attribute = $id"));
			$sum = $qty_check - $order_qty[$i];
			//echo $sum;
			dbQuery("UPDATE tbl_consign_check_detail SET qty_check = '$sum' WHERE id_consign_check_detail = $id_consign_check_detail");
		}
			echo $stock_qty;
			$n++;
		}
			$i++;
	}
	$message = "เพิ่ม $n รายการเรียบร้อย";
header("location: ../index.php?content=consign&add=y&id_order_consign=$id_order_consign&id_customer=$id_customer&message=$message");
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
if(isset($_GET['check_stock'])&&isset($_GET['barcode'])){
	$reference = $_GET['barcode'];
	$id_zone = $_GET['id_zone'];
	list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE barcode = '$reference'"));
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product);
	$product->product_attribute_detail($id_product_attribute);
	$product_code = $product->reference;
	$consign = new consign();
	$qty = $consign->order_qty($id_product_attribute,$id_zone);
	$data = trim($id_product_attribute).":".$qty.":".$product_code;
	echo $data;
}
if(isset($_GET['check_stock'])&&isset($_GET['product_code'])){
	$reference = $_GET['product_code'];
	$id_zone = $_GET['id_zone'];
	list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE reference = '$reference'"));
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product);
	$product->product_attribute_detail($id_product_attribute);
	$product_code = $product->reference;
	$consign = new consign();
	$qty = $consign->order_qty($id_product_attribute,$id_zone);
	if($qty == ""){
		$qty = 0;
	}
	$data = trim($id_product_attribute).":".$qty.":".$product_code;
	echo $data;
}
if(isset($_GET['insert_detail'])){
	$id_order_consign = $_POST['id_order_consign'];
	$consign= new consign($id_order_consign);
	$date_add = date("Y-m-d",strtotime($consign->date_add));
	$id = trim($_POST['id_product_attribute']);
	$order_qty = $_POST['qty'];
	$id_customer = $_POST['id_customer'];
	list($product_price) = dbFetchArray(dbQuery("SELECT product_price FROM tbl_product_attribute LEFT JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product WHERE id_product_attribute = $id"));
	list($id_order_consign_detail,$qty) = dbFetchArray(dbQuery("SELECT id_order_consign,qty FROM tbl_order_consign_detail WHERE id_order_consign = $id_order_consign AND id_product_attribute = $id"));
	list($id_zone,$id_consign_check) = dbFetchArray(dbQuery("SELECT id_zone,id_consign_check FROM tbl_order_consign WHERE id_order_consign = '$id_order_consign'"));
	list($stock_qty,$id_stock) = dbFetchArray(dbQuery("SELECT qty,id_stock FROM tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id'"));
	$stock = $stock_qty - $order_qty;
	if($id_order_consign_detail != ""){
		$sumqty = $qty + $order_qty;
		dbQuery("UPDATE tbl_order_consign_detail SET qty = $sumqty WHERE id_order_consign_detail = $id_order_consign_detail");	
	}else{
		$sumqty = $order_qty;
		$time = date("H:i:s");
		$date_upd = $date_add." ".$time;
		dbQuery("INSERT INTO tbl_order_consign_detail (id_order_consign,id_product_attribute,product_price,reduction_percent,reduction_amount,qty,date_upd)VALUES('$id_order_consign','$id','$product_price',0,0,'$sumqty','$date_upd')");
		echo $product_price;
	}
	dbQuery("UPDATE tbl_stock SET qty = '$stock' WHERE id_stock = $id_stock");
	dbQuery("DELETE FROM tbl_stock WHERE id_stock = $id_stock AND qty = 0");
	if($id_consign_check != 0){
			list($id_consign_check_detail,$qty_check) = dbFetchArray(dbQuery("SELECT id_consign_check_detail,qty_check FROM tbl_consign_check_detail WHERE id_consign_check = $id_consign_check AND id_product_attribute = $id"));
			$sum = $qty_check - $order_qty;
			dbQuery("UPDATE tbl_consign_check_detail SET qty_check = '$sum' WHERE id_consign_check_detail = $id_consign_check_detail");
		}
	$message = "เพิ่มรายการเรียบร้อย";
	header("location: ../index.php?content=consign&add=y&id_order_consign=$id_order_consign&id_customer=$id_customer&message=$message");
}
/// ลบในหน้า แก้ไข
if(isset($_GET['delete'])&&isset($_GET['id_order_consign'])&&isset($_GET['id_order_consign'])){
	$id_order_consign = $_GET['id_order_consign'];
	$id_order_consign_detail = $_GET['id_order_consign_detail'];
	$id_customer = $_GET['id_customer'];
	list($qty,$id_product_attribute) = dbFetchArray(dbQuery("SELECT qty,id_product_attribute FROM tbl_order_consign_detail WHERE id_order_consign_detail = '$id_order_consign_detail'"));
	list($id_zone,$id_consign_check) = dbFetchArray(dbQuery("SELECT id_zone,id_consign_check FROM tbl_order_consign WHERE id_order_consign = '$id_order_consign'"));
	list($stock_qty,$id_stock) = dbFetchArray(dbQuery("SELECT qty,id_stock FROM tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
	if($id_stock == ""){
		dbQuery("INSERT INTO tbl_stock (id_zone,id_product_attribute,qty)VALUES('$id_zone','$id_product_attribute','$qty')");
	}else{
		$sumqty = $qty + $stock_qty;
		dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = $id_stock");
	}
		if($id_consign_check != 0){
			list($id_consign_check_detail,$qty_check) = dbFetchArray(dbQuery("SELECT id_consign_check_detail,qty_check FROM tbl_consign_check_detail WHERE id_consign_check = $id_consign_check AND id_product_attribute = $id_product_attribute"));
			$sum = $qty_check + $qty;
			echo $sum;
			dbQuery("UPDATE tbl_consign_check_detail SET qty_check = '$sum' WHERE id_consign_check_detail = $id_consign_check_detail");
		}
	if(dbQuery("DELETE FROM tbl_order_consign_detail WHERE id_order_consign_detail = $id_order_consign_detail")){
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=consign&add=y&id_order_consign=$id_order_consign&id_customer=$id_customer&message=$message");
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=consign&add=y&id_order_consign=$id_order_consign&id_customer=$id_customer&error=$message");
	}	
}
if(isset($_GET['print_order'])&&isset($_GET['id_order_consign'])){
	$id_order_consign = $_GET['id_order_consign'];
	$company = new company();
	$consign = new consign($id_order_consign);
	$customer = new customer($consign->id_customer);
	$id_customer = $consign->id_customer;
	$product_price = "";
	$discount_percent = "";
	$remark = $consign->comment;
	//$role = $order->role;
			$content="consign";
			$title = "ใบส่งของ / ใบแจ้งหนี้";
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$html = "	<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>ออเดอร์</title>
					<!-- Core CSS - Include with every page -->
					<link href='../../library/css/bootstrap.css' rel='stylesheet'>
					<link href='../../library/css/font-awesome.css' rel='stylesheet'>
					<link href='../../library/css/bootflat.min.css' rel='stylesheet'>
					 <link rel='stylesheet' href='../../library/css/jquery-ui-1.10.4.custom.min.css' />
					 <script src='../../library/js/jquery.min.js'></script>
					<script src='../../library/js/jquery-ui-1.10.4.custom.min.js'></script>
					<script src='../../library/js/bootstrap.min.js'></script>  
					<!-- SB Admin CSS - Include with every page -->
					<link href='../../library/css/sb-admin.css' rel='stylesheet'>
					<link href='../../library/css/template.css' rel='stylesheet'>
				</head>";
				$doc_body_top = "<body style='padding-top:10px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px'>
				<div class=\"hidden-print\" style='margin-bottom:25px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=$content&id_order_consign=$id_order_consign&view_detail=y&id_customer=$id_customer' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> ";
				$doc_head = "
	<div style='width:100%; height:40mm; margin-right:0.5%;'>
		<table width='100%' border='0px'><tr>
			<td style='width:20%; padding:10px; text-align:center; vertical-align:top;'><img src='../../img/company/logo.png' style='width:100px; padding-right:10px;' /></td>
			<td style='width:40%; padding:10px; vertical-align:text-top;'>
				<h4 style='margin-top:0px; margin-bottom:5px;'>".$company->full_name."</h4>
				<p style='font-size:12px'>".$company->address." &nbsp; ".$company->post_code."</p>
				<p style='font-size:12px'>โทร. ".$company->phone." &nbsp;แฟกซ์. ".$company->fax."</p>
				<p style='font-size:12px'>เลขประจำตัวผู้เสียภาษี ".$company->tax_id."</p></td>
				<td style='vertical-align:text-top; text-align:right; padding-bottom:10px;'><strong>$title</strong></td></tr>
			</table></div>
	
	<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:40mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:20%; padding:10px; height:5mm; vertical-align:text-top;'>ลูกค้า :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->full_name."</td></tr>
				<tr><td style='width:20%; padding:10px; vertical-align:text-top;'>ที่อยู่ :</td>
				<td style='padding:10px; height:30mm; vertical-align:text-top;'>".$customer->address1." ".$customer->address2." ".$customer->city."<br/>เบอร์โทร ".$customer->phone."</td></tr>
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:40mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".thaiDate($consign->date_add)."</td></tr>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$consign->reference."</td></tr>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เครดิตเทอม :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->credit_term." วัน</td></tr>
				</table>	</div></td></tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px;' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td><td style='text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:30%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ราคา</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>มูลค่า</td>
	</tr>";
	function page_summary($total_order_amount, $total_discount_amount, $net_total, $remark){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		if($total_discount_amount !=""){ $total_discount_amount = number_format($total_discount_amount,2); }
		if($net_total !=""){ $net_total = number_format($net_total,2); }
		echo"	<tr><td rowspan='3' colspan='3' style='border:solid 1px #AAA;  padding:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
				<td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$total_order_amount."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$total_discount_amount."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ยอดเงินสุทธิ</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$net_total."</td></tr>
				</table>
				<div style='page-break-after: always'></div>";
	}
	$row = 13;
	$sql = dbQuery("SELECT id_order_consign_detail,tbl_order_consign_detail.id_product_attribute,qty,tbl_order_consign_detail.date_upd,barcode,reference,product_price,reduction_percent,reduction_amount FROM tbl_order_consign_detail LEFT JOIN tbl_product_attribute ON tbl_order_consign_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_order_consign = $id_order_consign ORDER BY barcode ASC");
	$rs = dbNumRows($sql);
	$count = 1;
	$n = 1;
	$i = 0;
	if($rs>0){
		echo $html.$doc_body_top.$doc_head;
	while($i<$rs){
		list($id_order_consign_detail,$id_product_attribute,$qty,$date_add,$barcode,$product_reference,$product_price,$reduction_percent,$reduction_amount)= dbFetchArray($sql);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		if($reduction_amount == 0){
			$discount = $reduction_percent;
			$unit = "%";
		}else{
			$discount = $reduction_amount;
			$unit = "";
		}
		if($unit == "%"){
			$dis = ($product_price * $discount)/100;
			$total_discount_amount1 = $dis * $qty;
			$price = $product_price - $dis;
			$total = $price * $qty;
			$total1 = $product_price * $qty;
		}else{
			$total_discount_amount1 = $discount * $qty;;
			$price = $product_price - $discount;
			$total = $price * $qty;
			$total1 = $product_price * $qty;
		}
		echo"<tr style='height:9mm;'>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$n</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$barcode</td>
		<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$product_reference : ".$product->product_name."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($qty)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$discount</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($total,2)."</td>
				</tr>";
				$total_order += $total1;
				$total_discount_order += $total_discount_amount1;
				$i++; $count++;
				if($n==$rs){ 
				$total_order_amount = $total_order;
				$total_discount_amount = $total_discount_order;
				$net_total = $total_order_amount - $total_discount_amount;
				page_summary($total_order_amount, $total_discount_amount, $net_total, $remark);
				}else{
				if($count>$row){ page_summary($total_order_amount, $total_discount_amount, $net_total, $remark); echo $doc_head; $count = 1; }
				}
				$n++; 
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo "</div></body></html>";
	 }
	if(isset($_GET['getData'])&&isset($_GET['id_product'])){
			$id_product = $_GET['id_product'];
			$id_cus = $_GET['id_customer'];
			$id_zone = $_GET['id_zone'];
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$sqm = dbQuery("SELECT id_color, id_size, id_attribute FROM tbl_product_attribute WHERE id_product = $id_product LIMIT 1");
			list($co, $si, $at) = dbFetchArray($sqm);
			if($co !=0){ $co =1;}
			if($si !=0){ $si = 1;}
			if($at !=0){ $at = 1;}
			$count = $co+$si+$at;
			if($count >1){	$table_w = (70*($colums+1)+100); }else if($count ==1){ $table_w = 800; }
			$dataset = $product->consign_attribute_grid($product->id_product,$id_cus,$id_zone);
			$dataset .= "|".$table_w;
			$dataset .= "|".$product->product_code;
			echo $dataset;
}
if(isset($_GET['add_consign_check'])){
	$id_customer = $_POST['id_customer'];
	$customer = new customer($id_customer);
	$id_employee = $_POST['id_employee'];
	$reference = get_max_role_reference_consign_check("PREFIX_CONSIGN_CHECK",$role);
	$comment = $_POST['comment'];
	$date_add = dbDate($_POST['doc_date']);
	$date_upd = date('Y-m-d');
	if(isset($_POST['auto_zone'])){
		$sql = dbQuery("SELECT id_zone FROM tbl_order_consignment WHERE id_customer ='$id_customer'");
		$row = dbNumRows($sql);  
		if($row<1){
			$message = "การเลือกอัตโนมัติไม่พบโซนที่ต้องการกรุณาระบุโซน";
			header("location: ../index.php?content=consign&add_consign_check=y&error=$message");
			exit;
		}else{
			list($id) = dbFetchArray($sql);
			$id_zone = $id;
		}
	}
	if(isset($_POST['zone_id'])){ $id_zone = $_POST['zone_id']; }
	if(isset($_POST['edit_consign_check'])){
		$id_consign_check = $_POST['edit_consign_check'];
		dbQuery("UPDATE tbl_consign_check SET id_customer = '$id_customer', id_zone = $id_zone ,comment = '$comment' WHERE id_consign_check = $id_consign_check");
		dbQuery("DELETE FROM tbl_consign_check_detail WHERE id_consign_check = $id_consign_check");
		dbQuery("INSERT INTO tbl_consign_check_detail (id_consign_check,id_product_attribute,qty_stock,qty_check) SELECT $id_consign_check,id_product_attribute,qty,0 FROM tbl_stock WHERE id_zone = $id_zone");
		header("location: ../index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign_check");
	}else{
		if(dbQuery("INSERT INTO tbl_consign_check(reference, id_customer, id_zone, date_add,comment) VALUES ('$reference','$id_customer', '$id_zone',NOW(),'$comment')")){
			list($id_consign) = dbFetchArray(dbQuery("SELECT id_consign_check FROM tbl_consign_check WHERE reference = '$reference'"));
			dbQuery("INSERT INTO tbl_consign_check_detail (id_consign_check,id_product_attribute,qty_stock,qty_check) SELECT $id_consign,id_product_attribute,qty,0 FROM tbl_stock WHERE id_zone = $id_zone");
			header("location: ../index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign");
		}else{
			$message = "ไม่สามารถเพิ่มการกระทบยอดฝากขายใหม่ในฐานข้อมูลได้";
			header("location: ../index.php?content=consign&add_consign_check=y&error=$message");
		}
	}
}
if(isset($_GET['add_stock_consign'])){
	$barcode = $_GET['barcode'];
	$qty = $_GET['qty'];
	$id_consign_check = $_GET['id_consign_check'];
	$product = new product();
	$arr = $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$id_product_attribute = $arr['id_product_attribute'];
	$qty1 = $arr['qty'];
	$sumqty = $qty * $qty1;
	if($id_product_attribute == "" ){
		$message = "บาร์โค้ดผิด หรือ ไม่มีรายการสินค้านี้ในระบบ";
		echo "erreo :".$message;
	}else{
		list($qty_stock,$qty_check) = dbFetchArray(dbQuery("SELECT qty_stock,qty_check FROM tbl_consign_check_detail WHERE id_product_attribute = '$id_product_attribute' AND id_consign_check = '$id_consign_check'"));
		$qty_diff = $qty_stock - $qty_check;
		if($qty_stock == ""){
			$message = "ไม่มีสินค้านี้ในในคลังฝากขายนี้";
			echo "erreo :".$message;
		}else if($sumqty > "$qty_diff"){
			if($sumqty > "1"){
				$message = "จำนวนสินค้าเกินจากจำนวนในคลังฝากขายนี้ กรุณาเช็คจำนวน หรือ ลดจำนวนที่ใส่";
				echo "error :".$message.":".$id_product_attribute;
			}else{
				$message = "จำนวนสินค้าเกินจากจำนวนในคลังฝากขายนี้";
				echo "error :".$message.":".$id_product_attribute;
			}
		}else{
			$qty_upd = $qty_check + $sumqty;
			$diff = $qty_stock -$qty_upd;
			dbQuery("UPDATE tbl_consign_check_detail SET qty_check = '$qty_upd' WHERE id_product_attribute = '$id_product_attribute' AND id_consign_check = '$id_consign_check'");
			echo "ok:".$id_product_attribute.":".$qty_upd.":".$diff;
		}
	}
}
if(isset($_GET['edit_stock_consign'])){
	$id_product_attribute = $_GET['id_product_attribute'];
	$id_consign_check = $_GET['id_consign_check'];
	$qty = $_GET['qty'];
	list($qty_stock,$qty_check) = dbFetchArray(dbQuery("SELECT qty_stock,qty_check FROM tbl_consign_check_detail WHERE id_product_attribute = '$id_product_attribute' AND id_consign_check = '$id_consign_check'"));
	if($qty_stock < "$qty"){
		$message = "จำนวนสินค้าเกินจากจำนวนในคลังฝากขายนี้";
		echo "error :".$message.":".$id_product_attribute.":".$qty_check;
	}else{
		dbQuery("UPDATE tbl_consign_check_detail SET qty_check = '$qty' WHERE id_consign_check = $id_consign_check AND id_product_attribute = $id_product_attribute");
		$diff = $qty_stock - $qty;
		echo "ok:".$id_product_attribute.":".$qty.":".$diff;
	}
}
if(isset($_GET['import_consign_check'])){
	$id_consign_check = $_POST['id_consign_check'];
	//$file = $_POST['file'];
	move_uploaded_file($_FILES["file"]["tmp_name"],$_FILES["file"]["name"]);
	$objCSV = fopen($_FILES["file"]["name"], "r");
	$sum = 0;
	$true = 0;
	$fales = 0;
	while (($objArr = fgetcsv($objCSV, 1000, ",")) !== FALSE) {
		$barcode = $objArr[0];
		$qty = $objArr[1];
		echo "$barcode / $qty <br>";
		$product = new product();
		$arr = $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
		$id_product_attribute = $arr['id_product_attribute'];
		$qty1 = $arr['qty'];
		$sumqty = $qty * $qty1;
		if($id_product_attribute == "" ){
		$message = "บาร์โค้ดผิด หรือ ไม่มีรายการสินค้านี้ในระบบ";
		insert_import_fales($id_consign_check,$barcode,$qty,$message);
		$fales++;
	}else{
		list($qty_stock,$qty_check) = dbFetchArray(dbQuery("SELECT qty_stock,qty_check FROM tbl_consign_check_detail WHERE id_product_attribute = '$id_product_attribute' AND id_consign_check = '$id_consign_check'"));
		$qty_diff = $qty_stock - $qty_check;
			if($qty_stock == ""){
				$message = "ไม่มีสินค้านี้ในในคลังฝากขายนี้";
				insert_import_fales($id_consign_check,$barcode,$qty,$message);
				$fales++;
			}else if($sumqty > "$qty_diff"){
				if($sumqty > "1"){
					$message = "จำนวนสินค้าเกินจากจำนวนในคลังฝากขายนี้ กรุณาเช็คจำนวน";
					insert_import_fales($id_consign_check,$barcode,$qty,$message);
					$fales++;
				}else{
					$message = "จำนวนสินค้าเกินจากจำนวนในคลังฝากขายนี้";
					insert_import_fales($id_consign_check,$barcode,$qty,$message);
					$fales++;
				}
			}else{
				$qty_upd = $qty_check + $sumqty;
				$diff = $qty_stock -$qty_upd;
				dbQuery("UPDATE tbl_consign_check_detail SET qty_check = '$qty_upd' WHERE id_product_attribute = '$id_product_attribute' AND id_consign_check = '$id_consign_check'");
				$true++;
				}
			}
			$sum++;
	}
	fclose($objCSV);
	$messages = "จำนวนทั้งหมด $sum รายการ สำเร็จ $true รายการ ไม่สำเร็จ $fales รายการ";
	header("location: ../index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign_check&message=$messages");
	echo $messages;
}
if(isset($_GET['add_consign_diff'])){
	$id_employee = $_COOKIE['user_id'];
	$id_consign_check = $_GET['id_consign_check'];
	$sql = dbQuery("SELECT tbl_consign_check_detail.id_product_attribute, qty_stock, qty_check FROM tbl_consign_check_detail  WHERE id_consign_check = $id_consign_check AND qty_stock > qty_check");
	$row = dbNumRows($sql);
	if($row > 0){
		list($id_customer,$id_zone) = dbFetchArray(dbQuery("SELECT id_customer,id_zone FROM tbl_consign_check WHERE id_consign_check = $id_consign_check"));
		$customer = new customer($id_customer);
		$reference = get_max_role_reference_consign("PREFIX_CONSIGN",$role);
		$comment = '';
		dbQuery("INSERT INTO tbl_order_consign(reference, id_customer, date_add,comment,id_zone,id_consign_check) VALUES ('$reference','$id_customer',NOW(),'$comment',$id_zone,$id_consign_check)");
		list($id_consign) = dbFetchArray(dbQuery("SELECT id_order_consign FROM tbl_order_consign WHERE reference = '$reference'"));
		$i = 0;
		while($i<$row){
			list($id_product_attribute,$qty_stock,$qty_check)= dbFetchArray($sql);
			list($product_price) = dbFetchArray(dbQuery("SELECT product_price FROM tbl_product_attribute LEFT JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product WHERE id_product_attribute = $id_product_attribute"));
			$diff = $qty_stock - $qty_check;
			dbQuery("UPDATE tbl_stock SET qty = '$qty_check' WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone");
			dbQuery("INSERT INTO tbl_order_consign_detail (id_order_consign,id_product_attribute,product_price,reduction_percent,reduction_amount,qty)VALUES($id_consign,$id_product_attribute,'$product_price','0',0,'$diff')");
			dbquery("DELETE FROM tbl_stock WHERE qty < 1 AND id_product_attribute = $id_product_attribute AND id_zone = $id_zone");
		$i++;
		}
		dbQuery("UPDATE tbl_consign_check SET consign_valid = 1 WHERE id_consign_check = $id_consign_check");
		$messages = "เพิ่มรายการตัดยอดฝากขายเรียบร้อยแล้ว";
		header("location: ../index.php?content=consign&id_order_consign=$id_consign&id_customer=$id_customer&view_detail=y&message=$messages");
	}else{
		$messages = "ไม่สามารถเพิ่มรายการตัดยอดฝากได้เพราะไม่มีรายการสินค้าที่จะนำมาตักยอด";
		header("location: ../index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign_check&message=$messages");
	}
}
if(isset($_GET['edit_discount'])){
	$id_employee = $_POST['id_employee'];
	$id_order_consign_detail_array = $_POST['id_order_consign_detail_array'];
	$id_order_consign = $_POST['id_order_consign'];
	$amount_array = $_POST['amount_array'];
	$percent_array = $_POST['percent_array'];
	list($reference) = dbFetchArray(dbQuery("SELECT reference FROM tbl_order_consign WHERE id_order_consign = $id_order_consign"));
	$i = 0;
	foreach ($id_order_consign_detail_array as $id ){
		
		$amount_dis = $amount_array[$i];
		$percent_dis = $percent_array[$i];
		dbQuery("UPDATE tbl_order_consign_detail SET reduction_percent = '$percent_dis' , reduction_amount = '$amount_dis' WHERE id_order_consign_detail = '$id'");
		//echo "SELECT id_product_attribute,reduction_amount,reduction_amount,product_price,qty FROM tbl_order_consign_detail WHERE id_order_consign_detail = '$id'";
		list($id_product_attribute,$reduction_amount,$reduction_percent,$product_price,$qty) = dbFetchArray(dbQuery("SELECT id_product_attribute,reduction_amount,reduction_percent,product_price,qty FROM tbl_order_consign_detail WHERE id_order_consign_detail = '$id'"));
		if($reduction_amount == 0){
			$discount = $reduction_percent;
			$unit = "%";
		}else{
			$discount = $reduction_amount;
			$unit = "";
		}
		if($unit == "%"){
			$dis = ($product_price * $discount)/100;
			$total_discount_amount1 = $dis * $qty;
			$price = $product_price - $dis;
			$total = $price * $qty;
		}else{
			$dis = $discount;
			$total_discount_amount1 = $discount;
			$price = $product_price - $discount;
			$total = $price * $qty;
		}
		//echo "$dis<br>";
		dbQuery("UPDATE tbl_order_detail_sold SET reduction_percent = '$percent_dis' , reduction_amount = '$amount_dis', discount_amount = '$dis' , final_price = '$price', total_amount = '$total' WHERE reference = '$reference' AND id_product_attribute = '$id_product_attribute' AND id_order = 0");
	$i++;	
	}
	echo 1;
}
if(isset($_GET['confirm_consign'])){
	$id_employee = $_COOKIE['user_id'];
	$id_order_consign = $_GET['id_order_consign'];
	$consign = new consign($id_order_consign);
	$date_upd = $consign->date_add;
	list($reference,$id_customer) = dbFetchArray(dbQuery("SELECT reference,id_customer FROM tbl_order_consign WHERE id_order_consign = $id_order_consign"));
	list($id_sale) = dbFetchArray(dbQuery("SELECT id_sale FROM tbl_customer WHERE id_customer = $id_customer"));
	dbQuery("UPDATE tbl_order_consign SET consign_status = 1 WHERE id_order_consign = $id_order_consign");
	$sql = dbQuery("SELECT id_order_consign_detail,tbl_order_consign_detail.id_product_attribute,qty,tbl_order_consign_detail.date_upd,barcode,reference,product_price,reduction_percent,reduction_amount,id_product FROM tbl_order_consign_detail LEFT JOIN tbl_product_attribute ON tbl_order_consign_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_order_consign = $id_order_consign ORDER BY barcode ASC");
		$row = dbNumRows($sql);
		$i=0;
		$n = 1;
	while($i<$row){
		list($id_order_consign_detail,$id_product_attribute,$qty,$date_add,$barcode,$product_reference,$product_price,$reduction_percent,$reduction_amount,$id_product)= dbFetchArray($sql);
		if($reduction_amount == 0){
			$discount = $reduction_percent;
			$unit = "%";
		}else{
			$discount = $reduction_amount;
			$unit = "";
		}
		if($unit == "%"){
			$dis = ($product_price * $discount)/100;
			$total_discount_amount1 = $dis * $qty;
			$price = $product_price - $dis;
			$total = $price * $qty;
		}else{
			$dis = $discount;
			$total_discount_amount1 = $discount;
			$price = $product_price - $discount;
			$total = $price * $qty;
		}
		list($product_name) = dbFetchArray(dbQuery("SELECT product_name FROM tbl_product WHERE id_product = $id_product"));
		stock_movement("out", 3, $id_product_attribute, 2, $qty, $reference, $date_upd);
		dbQuery("INSERT INTO tbl_order_detail_sold(id_order,reference, id_role, id_customer,id_employee, id_sale, id_product, id_product_attribute, product_name, product_reference, barcode, product_price, order_qty, sold_qty, reduction_percent, reduction_amount, discount_amount, final_price, total_amount) VALUES( '','$reference', 5, $id_customer,$id_employee, $id_sale, $id_product, $id_product_attribute, '$product_name', '$product_reference', '$barcode', $product_price, $qty, $qty, $reduction_percent, $reduction_amount, $dis, $price, $total)");
		$i++; 
	}
		header("location: ../index.php?content=consign&id_order_consign=$id_order_consign&id_customer=$id_customer&view_detail=y");
}
if(isset($_GET['cancel_consign_check'])){
	$id_consign_check = $_GET['id_consign_check'];
	dbQuery("UPDATE tbl_consign_check SET consign_valid = 2 WHERE id_consign_check = $id_consign_check");
	header("location: ../index.php?content=consign&consign_check=y");
}
if(isset($_GET['cancel_consign'])){
	$order_consign = $_GET['id_order_consign'];
	list($id_zone_retrun) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_order_consign WHERE id_order_consign = '$order_consign'"));
		$sql = dbQuery("SELECT id_product_attribute,qty FROM tbl_order_consign_detail WHERE id_order_consign = '$order_consign'");
	$rs = dbNumRows($sql);
	$i = 0;
	while($i<$rs){
		list($id_product_attribute,$qty)= dbFetchArray($sql);
			list($id_stock,$qty_stock) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = $id_zone_retrun AND id_product_attribute = $id_product_attribute"));
			if($id_stock != ""){
				$sumqty = $qty + $qty_stock;
				dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = $id_stock");
			}else{
				dbQuery("INSERT INTO tbl_stock(id_zone,id_product_attribute,qty)VALUES('$id_zone_retrun',$id_product_attribute,'$qty')");
			}
		$i++;
	}
	dbQuery("DELETE FROM tbl_order_consign_detail WHERE id_order_consign = $order_consign");
	dbQuery("UPDATE tbl_order_consign SET consign_status = 2 WHERE id_order_consign = $order_consign"); 
	header("location: ../index.php?content=consign");
}
if(isset($_GET['print_diff'])){
	$id_consign_check = $_GET['id_consign_check'];
	list($reference,$id_customer,$remark,$date_add) = dbFetchArray(dbQuery("SELECT reference,id_customer,comment,date_add FROM tbl_consign_check WHERE id_consign_check = $id_consign_check"));
	$company = new company();
	$customer = new customer($id_customer);
			$title = "ใบแจ้งรายการขายจากการฝากขาย";
	$total_qty = ""; /// เก็บยอดสินค้าตอนวนลูป
	$total_all_qty =""; ///วนเสร็จแล้วเอาค่ามาใส่ตัวนี้
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$row = 22;
	$sql = dbQuery("SELECT tbl_consign_check_detail.id_product_attribute, barcode, reference, qty_stock, qty_check FROM tbl_consign_check_detail LEFT JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = $id_consign_check AND qty_stock > qty_check ORDER BY barcode DESC");
	$rs = dbNumRows($sql);
	
	$total_page = ceil($rs/$row);
	$page = 1;
	$count = 1;
	$n = 1;
	$i = 0;
	$sumdiff = 0;
	$total = 0;
	$html = "	<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>ออเดอร์</title>
					<!-- Core CSS - Include with every page -->
					<link href='../../library/css/bootstrap.css' rel='stylesheet'>
					<link href='../../library/css/font-awesome.css' rel='stylesheet'>
					<link href='../../library/css/bootflat.min.css' rel='stylesheet'>
					 <link rel='stylesheet' href='../../library/css/jquery-ui-1.10.4.custom.min.css' />
					 <script src='../../library/js/jquery.min.js'></script>
					<script src='../../library/js/jquery-ui-1.10.4.custom.min.js'></script>
					<script src='../../library/js/bootstrap.min.js'></script>  
					<!-- SB Admin CSS - Include with every page -->
					<link href='../../library/css/sb-admin.css' rel='stylesheet'>
					<link href='../../library/css/template.css' rel='stylesheet'>
				</head>";
				$doc_body_top = "<body style='padding-top:0px; margin-top:-15px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px'>
				<div class='hidden-print' style='margin-bottom:0px; margin-top:10px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=consign&consign_balance&id_consign_check=$id_consign_check' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> ";
				function doc_head($reference,$company, $customer, $title, $page, $total_page,$date_add){
					$result = "<!--
	<div style='width:100%; height:25mm; margin-right:0.5%;'>
		<table width='100%' border='0px'><tr>
			<td style='width:20%; padding:10px; text-align:center; vertical-align:top;'><img src='../../img/company/logo.png' style='width:100px; padding-right:10px;' /></td>
			<td style='width:40%; padding:10px; vertical-align:text-top;'>
				<h4 style='margin-top:0px; margin-bottom:5px;'>".$company->full_name."</h4>
				<p style='font-size:12px'>".$company->address." &nbsp; ".$company->post_code."</p>
				<p style='font-size:12px'>โทร. ".$company->phone." &nbsp;แฟกซ์. ".$company->fax."</p>
				<p style='font-size:12px'>เลขประจำตัวผู้เสียภาษี ".$company->tax_id."</p></td>
				<td style='vertical-align:text-top; text-align:right; padding-bottom:10px;'><strong>$title</strong><br/> หน้า $page / $total_page</td></tr>
			</table></div>-->
	<h4>$title</h4><p class='pull-right'>หน้า $page / $total_page</p>
	<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:20mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:20%; padding:10px; height:5mm; vertical-align:text-top;'>ลูกค้า :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->full_name."</td></tr>
				<!--<tr><td style='width:20%; padding:10px; vertical-align:text-top;'>ที่อยู่ :</td>
				<td style='padding:10px; height:30mm; vertical-align:text-top;'>".$customer->address1." ".$customer->address2." ".$customer->city."<br/>เบอร์โทร ".$customer->phone."</td></tr>-->
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:20mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".showDate($date_add)."</td></tr>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>$reference</td></tr>
				<!--<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เครดิตเทอม :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->credit_term." วัน</td></tr>-->
				</table>	</div></td></tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px; ' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td><td style='width:20%; text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:35%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ราคา</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>มูลค่า</td>
	</tr>"; return $result; }
	function footer($total_qty=""){
				$result = "</table>
				<div style='page-break-after:always'>
				<table style='width:100%; border:0px;'>
				<tr><td>	<div class='col-lg-12' style='text-align:center;'>ผู้รับของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ส่งของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ตรวจสอบ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้อนุมัติ</div></td>
				</tr>
				<tr><td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>ได้รับสินค้าถูกต้องแล้ว</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div>
				</td></tr></table></div>
				"; return $result; }
	function page_summary($total_order_amount, $remark, $total_all_qty){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		echo"	<tr style='height:9mm;'><td colspan='7' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top; text-align:right;'>รวม $total_all_qty หน่วย</td></tr>
				<tr style='height:9mm;'><td colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
					<td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_order_amount."</td></tr>
				</table>";
	}
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($reference,$company, $customer, $title,$page, $total_page,$date_add);
			while($i<$rs){
				list($id_product_attribute,$barcode,$reference1,$qty_stock,$qty_check)= dbFetchArray($sql);
				$product = new product();
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product);
				$product->product_attribute_detail($id_product_attribute);
				$product_price = $product->product_price;
				$diff = $qty_stock - $qty_check;
				$price = $diff * $product_price;
				$sumdiff = $sumdiff + $diff;
				$total = $total +$price;
				if($count+1 >$row){  $css_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_row ="border-top: 0px;";}
				echo"<tr style='height:9mm;'>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$n</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$barcode</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$reference1 </td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$product_price</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$diff</td>
				<td  style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 10px;'>".number_format($price,2)."</td></tr>";
				$i++;
				$count++;
				if($n==$rs){ 
					$ba_row = $row - $count -7; 
					$ba = 0;
					if($ba_row >0){
						while($ba <= $ba_row){
							if($count+1 >$row){  $css_ba_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_ba_row ="border-top: 0px;";}
							echo"<tr style='height:9mm;'>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'></td>
							</tr>";
							$ba++; $count++;
						}
					}
					$total_all_qty = $sumdiff;
					$total_order_amount = $total;
					page_summary($total_order_amount, $remark, $total_all_qty);
					echo footer($total_all_qty);
				}else{
					if($count>$row){  $page++; echo "</table><div style='page-break-after:always;'></div>".doc_head($reference,$company, $customer, $title,$page, $total_page,$date_add); $count = 1;  }
				}
				$n++;
			}
		echo "</table>	";
	}
	echo "</div></body></html>";
}
if(isset($_GET['print_balance'])){
	$id_consign_check = $_GET['id_consign_check'];
	list($reference,$id_customer,$remark,$date_add) = dbFetchArray(dbQuery("SELECT reference,id_customer,comment,date_add FROM tbl_consign_check WHERE id_consign_check = $id_consign_check"));
	$company = new company();
	$customer = new customer($id_customer);
			$title = "ใบแจ้งรายการคงเหลือจากการฝากขาย";
	$total_qty = ""; /// เก็บยอดสินค้าตอนวนลูป
	$total_all_qty =""; ///วนเสร็จแล้วเอาค่ามาใส่ตัวนี้
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$row = 22;
	$sql = dbQuery("SELECT tbl_consign_check_detail.id_product_attribute, barcode, reference,qty_check FROM tbl_consign_check_detail LEFT JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = $id_consign_check AND qty_check != 0  ORDER BY barcode DESC");
	$rs = dbNumRows($sql);
	
	$total_page = ceil($rs/$row);
	$page = 1;
	$count = 1;
	$n = 1;
	$i = 0;
	$sumdiff = 0;
	$total = 0;
	$html = "	<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>ออเดอร์</title>
					<!-- Core CSS - Include with every page -->
					<link href='../../library/css/bootstrap.css' rel='stylesheet'>
					<link href='../../library/css/font-awesome.css' rel='stylesheet'>
					<link href='../../library/css/bootflat.min.css' rel='stylesheet'>
					 <link rel='stylesheet' href='../../library/css/jquery-ui-1.10.4.custom.min.css' />
					 <script src='../../library/js/jquery.min.js'></script>
					<script src='../../library/js/jquery-ui-1.10.4.custom.min.js'></script>
					<script src='../../library/js/bootstrap.min.js'></script>  
					<!-- SB Admin CSS - Include with every page -->
					<link href='../../library/css/sb-admin.css' rel='stylesheet'>
					<link href='../../library/css/template.css' rel='stylesheet'>
				</head>";
				$doc_body_top = "<body style='padding-top:0px; margin-top:-15px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px'>
				<div class='hidden-print' style='margin-bottom:0px; margin-top:10px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=consign&consign_balance_check&id_consign_check=$id_consign_check' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> ";
				function doc_head($reference,$company, $customer, $title, $page, $total_page,$date_add){
					$result = "<!--
	<div style='width:100%; height:25mm; margin-right:0.5%;'>
		<table width='100%' border='0px'><tr>
			<td style='width:20%; padding:10px; text-align:center; vertical-align:top;'><img src='../../img/company/logo.png' style='width:100px; padding-right:10px;' /></td>
			<td style='width:40%; padding:10px; vertical-align:text-top;'>
				<h4 style='margin-top:0px; margin-bottom:5px;'>".$company->full_name."</h4>
				<p style='font-size:12px'>".$company->address." &nbsp; ".$company->post_code."</p>
				<p style='font-size:12px'>โทร. ".$company->phone." &nbsp;แฟกซ์. ".$company->fax."</p>
				<p style='font-size:12px'>เลขประจำตัวผู้เสียภาษี ".$company->tax_id."</p></td>
				<td style='vertical-align:text-top; text-align:right; padding-bottom:10px;'><strong>$title</strong><br/> หน้า $page / $total_page</td></tr>
			</table></div>-->
	<h4>$title</h4><p class='pull-right'>หน้า $page / $total_page</p>
	<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:20mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:20%; padding:10px; height:5mm; vertical-align:text-top;'>ลูกค้า :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->full_name."</td></tr>
				<!--<tr><td style='width:20%; padding:10px; vertical-align:text-top;'>ที่อยู่ :</td>
				<td style='padding:10px; height:30mm; vertical-align:text-top;'>".$customer->address1." ".$customer->address2." ".$customer->city."<br/>เบอร์โทร ".$customer->phone."</td></tr>-->
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:20mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".showDate($date_add)."</td></tr>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>$reference</td></tr>
				<!--<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เครดิตเทอม :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->credit_term." วัน</td></tr>-->
				</table>	</div></td></tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px; ' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td><td style='width:20%; text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:35%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ราคา</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>มูลค่า</td>
	</tr>"; return $result; }
	function footer($total_qty=""){
				$result = "</table>
				<div style='page-break-after:always'>
				<table style='width:100%; border:0px;'>
				<tr><td>	<div class='col-lg-12' style='text-align:center;'>ผู้รับของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ส่งของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ตรวจสอบ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้อนุมัติ</div></td>
				</tr>
				<tr><td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>ได้รับสินค้าถูกต้องแล้ว</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div>
				</td></tr></table></div>
				"; return $result; }
	function page_summary($total_order_amount, $remark, $total_all_qty){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		echo"	<tr style='height:9mm;'><td colspan='7' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top; text-align:right;'>รวม $total_all_qty หน่วย</td></tr>
				<tr style='height:9mm;'><td colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
					<td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_order_amount."</td></tr>
				</table>";
	}
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($reference,$company, $customer, $title,$page, $total_page,$date_add);
			while($i<$rs){
				list($id_product_attribute,$barcode,$reference1,$qty_check)= dbFetchArray($sql);
				$product = new product();
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product);
				$product->product_attribute_detail($id_product_attribute);
				$product_price = $product->product_price;
				$price = $qty_check * $product_price;
				$sumdiff = $sumdiff + $qty_check;
				$total = $total +$price;
				if($count+1 >$row){  $css_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_row ="border-top: 0px;";}
				echo"<tr style='height:9mm;'>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$n</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$barcode</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$reference1 </td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$product_price</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$qty_check</td>
				<td  style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 10px;'>".number_format($price,2)."</td></tr>";
				$i++;
				$count++;
				if($n==$rs){ 
					$ba_row = $row - $count -7; 
					$ba = 0;
					if($ba_row >0){
						while($ba <= $ba_row){
							if($count+1 >$row){  $css_ba_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_ba_row ="border-top: 0px;";}
							echo"<tr style='height:9mm;'>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'></td>
							</tr>";
							$ba++; $count++;
						}
					}
					$total_all_qty = $sumdiff;
					$total_order_amount = $total;
					page_summary($total_order_amount, $remark, $total_all_qty);
					echo footer($total_all_qty);
				}else{
					if($count>$row){  $page++; echo "</table><div style='page-break-after:always;'></div>".doc_head($reference,$company, $customer, $title,$page, $total_page,$date_add); $count = 1;  }
				}
				$n++;
			}
		echo "</table>	";
	}
	echo "</div></body></html>";
}

if(isset($_GET['edit_price']) && isset($_GET['id_order_consign']) ){
	$id_order_consign = $_GET['id_order_consign'];
	$id_customer = $_GET['id_customer'];
	$data = $_POST['id'];
	foreach($data as $id){
		$price = $_POST['price_'.$id];
		$sql = "UPDATE tbl_order_consign_detail SET product_price = '$price' WHERE id_order_consign = '$id_order_consign' AND id_order_consign_detail = '$id'";
		dbQuery($sql);
	}
	header("location: ../index.php?content=consign&id_order_consign=$id_order_consign&id_customer=$id_customer&view_detail=y");
		
}
?>