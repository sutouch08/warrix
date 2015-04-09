<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
if(isset($_GET['confirm_order'])&&isset($_GET['id_order'])){ //ยืนยันว่าเปิดบิลแล้ว ตัดยอดออกจาก Buffer แล้วเพิ่มรายการลง stock movement
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$id_employee = $_GET['id_employee'];
	$state = $order->current_state;
	if( $state == 6 || $state == 7 || $state == 8 || $state == 9){
		header("location: ../index.php?content=bill&id_order=$id_order&view_detail=y");
		exit;
	}else{
	if($order->role == 5){ /// ถ้าเป็นฝากขาย ลบยอดใน temp แล้วไปเพิ่มใน โซน ฝากขาย
		list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_order_consignment WHERE id_order = $id_order")); /// หา id_zone จากคลังฝากขาย
		$sql = dbQuery("SELECT * FROM tbl_temp WHERE id_order = $id_order");
		while($row = dbFetchArray($sql)){
			$id_product_attribute = $row['id_product_attribute'];
			$qty = $row['qty'];
			$date_upd = date("Y-m-d");
			$new_qty = $qty*(-1);  //ทำยอดให้ติดลบเพื่อนำไปบวกกับยอดเดิมจะได้ลดลง
			update_buffer_zone($new_qty, $id_product_attribute);
			stock_movement("out", 2, $id_product_attribute, $row['id_warehouse'], $row['qty'], $order->reference, $date_upd);
			update_stock_zone($qty, $id_zone, $id_product_attribute);
			stock_movement("in", 1, $row['id_product_attribute'], 2, $row['qty'], $order->reference, date('Y-m-d'));
		}
	}else{
		$sql = dbQuery("SELECT * FROM tbl_temp WHERE id_order = $id_order AND status = 3 ");
		while($row = dbFetchArray($sql)){
			$id_product_attribute = $row['id_product_attribute']; 
			$qty = $row['qty'];
			$date_upd = date("Y-m-d");
			$new_qty = $qty*(-1);
			$id_temp = $row['id_temp'];
			dbQuery("UPDATE tbl_temp SET status = 4  WHERE id_temp = $id_temp"); 
			stock_movement("out", 3, $row['id_product_attribute'], $row['id_warehouse'], $row['qty'], $order->reference, date('Y-m-d'));
			update_buffer_zone($new_qty, $id_product_attribute);
		}
		order_sold($id_order);
	}
	order_state_change($id_order, 9, $id_employee);
	header("location: ../index.php?content=bill&id_order=$id_order&view_detail=y");
	}
}
	
	//// ปริ๊นใบกำกับ
if(isset($_GET['print_order'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$company = new company();
	$customer = new customer($order->id_customer);
	$remark = $order->comment;
	$role = $order->role;
	switch($role){
		case 1 :
			$content="order";
			$title = "Packing List";
			break;
		case 2 : 
			$content = "requisition";
			$title = "ใบเบิกสินค้า / Requisition Product";
			break;
		case 3 :
			$content = "lend";
			$title = "ใบยืมสินค้า / Lend Product";
			break;
		case 4 :
			$content = "sponsor";
			$title = "รายการอภินันทนาการ / Sponsor Order";
			break;
		case 5 :
			$content = "consignment";
			$title = "ใบส่งของ / ใบแจ้งหนี้";
			break;
		case 6 :
			$content = "requisition";
			$title = "ใบส่งของ / ใบเบิกสินค้าเพื่อแปรรูป";
			break;
		default :
			$content = "order";
			$title = "ใบส่งของ / ใบแจ้งหนี้";
			break;
	}
	$total_qty = ""; /// เก็บยอดสินค้าตอนวนลูป
	$total_all_qty =""; ///วนเสร็จแล้วเอาค่ามาใส่ตัวนี้
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$row = 22;
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, final_price, total_amount FROM tbl_order_detail WHERE id_order = $id_order  ORDER BY barcode ASC");
	$rs = dbNumRows($sql);
	$total_page = ceil($rs/$row);
	$page = 1;
	$count = 1;
	$n = 1;
	$i = 0;
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
				<a href='../index.php?content=order_closed&id_order=$id_order&view_detail=y' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> ";
				function doc_head($order,$company, $customer, $title, $page, $total_page){
					list($employee_first_name,$employee_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_employee WHERE id_employee = '".$order->id_employee."'"));
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
				<tr><td style='width:20%; padding:10px; height:5mm; vertical-align:text-top;'>";if($order->role == 3){$customer_name = "$employee_first_name $employee_last_name";$result .= "ผู้ยืม";}else{$customer_name = $customer->full_name;$result .= "ลูกค้า";}$result .= " :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer_name."</td></tr>
				<!--<tr><td style='width:20%; padding:10px; vertical-align:text-top;'>ที่อยู่ :</td>
				<td style='padding:10px; height:30mm; vertical-align:text-top;'>".$customer->address1." ".$customer->address2." ".$customer->city."<br/>เบอร์โทร ".$customer->phone."</td></tr>-->
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:20mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".thaiDate($order->date_add)."</td></tr>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$order->reference."</td></tr>
				<!--<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เครดิตเทอม :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->credit_term." วัน</td></tr>-->
				</table>	</div></td></tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px; ' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td><td style='text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:30%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ราคา</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>มูลค่า</td>
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
	function page_summary($total_order_amount, $total_discount_amount, $net_total, $remark, $total_qty=""){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		if($total_discount_amount !=""){ $total_discount_amount = number_format($total_discount_amount,2); }
		if($net_total !=""){ $net_total = number_format($net_total,2); }
		echo"	<tr style='height:9mm;'><td colspan='7' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top; text-align:right;'>รวม $total_qty หน่วย</td></tr>
				<tr style='height:9mm;'><td rowspan='3' colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
					<td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_order_amount."</td></tr>
				<tr style='height:9mm;'><td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ส่วนลด</td>
					<td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_discount_amount."</td></tr>
				<tr style='height:9mm;'><td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ยอดเงินสุทธิ</td>
					<td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$net_total."</td></tr>
				</table>";
	}
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($order, $company, $customer, $title,$page, $total_page);
	while($i<$rs){
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $final_price, $total_amount)= dbFetchArray($sql);
		list($qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order  AND valid = 1 "));
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		$total = $product_price * $qty;
		$total_price = $qty * $final_price;
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%"; $total_discount_amount1 = ($total/100)*$discount_percent;}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;$total_discount_amount1 = $total-($discount_percent*$qty);}
		if($count+1 >$row){  $css_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_row ="border-top: 0px;";}
		if($qty>0){
		echo"<tr style='height:9mm;'>
		<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>$n</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>$barcode</td>
		<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".number_format($qty)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>$discount</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'>".number_format($total_price,2)."</td>
				</tr>"; }
				$total_qty += $qty;
				$total_order += $total;
				$total_discount_order += $total_discount_amount1;
				$i++; $count++;
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
				$total_all_qty = $total_qty;
				$total_order_amount = $total_order;
				$total_discount_amount = $total_discount_order;
				$net_total = $total_order_amount - $total_discount_amount;
				page_summary($total_order_amount, $total_discount_amount, $net_total, $remark, $total_all_qty);
				echo footer($total_all_qty);
				}else{
				if($count>$row){  $page++; echo "</table><div style='page-break-after:always;'></div>".doc_head($order, $company, $customer, $title, $page, $total_page); $count = 1;  }
				}
				$n++; 
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo "</div></body></html>";
	 }
if(isset($_GET['repay'])){
	$id_order = $_GET['id_order'];
	$order_valid = $_POST['order_valid'];
	if($order_valid == "1"){
		dbQuery("UPDATE tbl_order SET valid = 1 WHERE id_order = $id_order");
		$message = "บันทึกการชำระเงินเรียบร้อยแล้ว";
		header("location: ../index.php?content=repay&id_order=$id_order&view_detail=y&message=$message");
	}else{
		$message = "บันทึกการชำระเงินไม่สำเร็จ";
		header("location: ../index.php?content=repay&id_order=$id_order&view_detail=y&error=$message");
	}
}


?>
