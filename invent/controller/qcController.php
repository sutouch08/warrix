<?php 
include "../../library/config.php";
include "../../library/functions.php";
include "../function/tools.php";
function checked_qty($id_order, $id_product_attribute){
		 $sql = dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1");
		 $sqr =dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND status IN(1,2,3,6)");
		 $sqm = dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
		 list($re) = dbFetchArray($sql);
		 list($rs) = dbFetchArray($sqr);
		 list($rm) = dbFetchArray($sqm);
		 if($re <1){ $result['current'] = 0; }else{ $result['current'] = $re; } // ยอด qc
		$result['prepare_qty'] = $rs; // ยอดจัด
		$result['order_qty'] = $rm; //ยอดสั่ง
		return $result;
	 }
if(isset($_GET['checked'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_employee = $_GET['id_user'];
	$barcode = trim($_GET['barcode_item']);
	$product = new product();
	$arr = $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$id_product_attribute = $arr['id_product_attribute'];
	$qty = $arr['qty'];
	if($id_product_attribute==""){
		$message = "บาร์โค้ดผิด หรือ ไม่มีรายการสินค้านี้ในระบบ";
		echo "erreo :".$message;
	}else if(check_product_in_order($id_product_attribute, $id_order)){ ///ถ้ายอดจัดผิด บันทึก error ลง tbl_qc แล้วส่ง error กลับ
	$check = checked_qty($id_order, $id_product_attribute); ///ตรวจสอบยอดจัด กับยอดที่ qc แล้ว
	$order_qty = $check['order_qty']; // ยอดสั่ง
	$current_qty = $check['current'];// ยอด qc
	$prepare_qty = $check['prepare_qty'];	// ยอดจัด
	if($current_qty+$qty > $prepare_qty || $current_qty+$qty >$order_qty){ ///ถ้ายอดจัดเกิน บันทึก error ลง tbl_qc แล้วส่ง error กลับ
		$sqm = dbQuery("SELECT id_zone FROM tbl_temp  WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
		list($id_zone) = dbFetchArray($sqm);
		$message = "สินค้าเกิน";
		dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 0, 2)");
		echo "erreo :".$message;
		}else{
		// ถ้าไม่มีอะไรผิดพลาด บันทึกรายการปกติ
		dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 1, 0)");
		dbQuery("UPDATE tbl_order_detail SET date_upd = NOW() WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
		dbQuery("UPDATE tbl_temp SET status = 6 WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
		if(($current_qty+$qty) == $order_qty){ /// ถ้ายอดตรวจครบตามจำนวนที่จัดมา อัพเดตสถานะใน tbl_temp ให้เป็น QC แล้ว
			dbQuery("UPDATE tbl_temp SET status = 2 WHERE id_order =$id_order AND id_product_attribute = $id_product_attribute");
		}
		$qc_qty = $current_qty+$qty;
		echo "ok:".$id_product_attribute.":".$qc_qty;
		}
	}else{
		$message = "จัดสินค้าผิด";
		dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 0, 1)");
		echo "erreo :".$message;
	}

}
/*if(isset($_GET['checked'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_employee = $_GET['id_user'];
	$barcode = trim($_GET['barcode_item']);
	$product = new product();
	$arr = $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$id_product_attribute = $arr['id_product_attribute'];
	$qty = $arr['qty'];
	if($id_product_attribute==""){
		$message = "บาร์โค้ดผิด หรือ ไม่มีรายการสินค้านี้ในระบบ";
		//header("location: ../index.php?content=qc&process&id_order=$id_order&error=$message");
		//exit;
	}else if(check_product_in_order($id_product_attribute, $id_order)){ ///ถ้ายอดจัดผิด บันทึก error ลง tbl_qc แล้วส่ง error กลับ
		//$checked = true;
		$check = checked_qty($id_order, $id_product_attribute); ///ตรวจสอบยอดจัด กับยอดที่ qc แล้ว
	$order_qty = $check['order_qty']; // ยอดสั่ง
	$current_qty = $check['current'];// ยอด qc
	$prepare_qty = $check['prepare_qty'];	// ยอดจัด
	if($current_qty+$qty > $prepare_qty || $current_qty+$qty >$order_qty){ ///ถ้ายอดจัดเกิน บันทึก error ลง tbl_qc แล้วส่ง error กลับ
		$sqm = dbQuery("SELECT id_zone FROM tbl_temp  WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
		list($id_zone) = dbFetchArray($sqm);
		$message = "สินค้าเกิน";
		dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 0, 2)");
	//	header("location: ../index.php?content=qc&process&id_order=$id_order&confirm_error=$message&id_zone=$id_zone&id_product_attribute=$id_product_attribute");
		//exit;
	}else{
	// ถ้าไม่มีอะไรผิดพลาด บันทึกรายการปกติ
	dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 1, 0)");
	dbQuery("UPDATE tbl_order_detail SET date_upd = NOW() WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
	dbQuery("UPDATE tbl_temp SET status = 6 WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
	if(($current_qty+$qty) == $order_qty){ /// ถ้ายอดตรวจครบตามจำนวนที่จัดมา อัพเดตสถานะใน tbl_temp ให้เป็น QC แล้ว
		dbQuery("UPDATE tbl_temp SET status = 2 WHERE id_order =$id_order AND id_product_attribute = $id_product_attribute");
	}
	//header("location: ../index.php?content=qc&process&id_order=$id_order");
	$message = "";
	}
	}else{
		$message = "จัดสินค้าผิด";
		dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 0, 1)");
	//	header("location: ../index.php?content=qc&process&id_order=$id_order&error=$message");
		//exit;
	}
	
	
	function product_from_zone($id_order, $id_product_attribute){
	$sql = dbQuery("SELECT zone_name, SUM(qty) AS qty FROM tbl_temp LEFT JOIN tbl_zone ON tbl_temp.id_zone = tbl_zone.id_zone WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute GROUP BY tbl_temp.id_zone");
	$result = "";
		while($row = dbFetchArray($sql)){
			$zone = $row['zone_name'];
			$qty = $row['qty'];
			$result = $result." ".$zone." : ".$qty."<br/>";
		}
		return $result;
	}

	$order = new order($id_order);
if($message == "สินค้าเกิน"){
	$confirm_error = $message;
	//$id_zone = $_GET['id_zone'];
	//$id_product_attribute = $_GET['id_product_attribute'];
	$zone = product_from_zone($id_order, $id_product_attribute);
	$arr = explode(":",$zone);
	$zone_name = $arr[0];
	echo"<div id='confirm_error' class='alert alert-danger alert-dismissible' role='alert' > <b>มีบางอย่างผิดพลาด&nbsp;</b>$confirm_error</div>";
	echo "<a href='controller/qcController.php?over_order&id_order=$id_order&id_product_attribute=$id_product_attribute&id_zone=$id_zone' >
	<button type='button' id='move_zone' style='display:none;' onclick=\"return confirm('สินค้าเกิน ต้องการย้ายสต็อกจากโซน $zone_name ไปยัง Buffer หรือไม่'); \">
	click</button></a>";
}else if($message != ""){
	echo"<div id='error' class='alert alert-danger alert-dismissible' role='alert' >
	 <b>มีบางอย่างผิดพลาด&nbsp;</b>$message</div>";
}else{
	$confirm_error = "";
	$id_zone = "";
	$id_product_attribute ="";
	$zone_name = "";
}

	echo "<table class='table'>
	<thead>
	
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:20%; text-align:center;'>บาร์โค้ด</th><th style='width:35%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวนที่สั่ง</th><th style='width:10%; text-align:center;'>จำนวนที่จัด</th>	<th style='width:10%; text-align:center;'>ตรวจแล้ว</th><th style='width:10%; text-align:center;'>จากโซน</th>
	</thead>";
	if($order->current_state == 5){ $order->state_change($order->id_order, 11, $id_user);}
	$sql = dbQuery("SELECT tbl_order_detail.id_product_attribute, product_qty FROM tbl_order_detail WHERE tbl_order_detail.id_order = $id_order ORDER BY date_upd DESC");
	$row = dbNumRows($sql);
	$n = 1;
	$row1 = 0;
	while($list = dbFetchArray($sql)){
			$id_product_attribute  = $list['id_product_attribute'];
			$order_qty = $list['product_qty'];
			//$checked  = $list['qty'];
			list($prepare_qty) = dbFetchArray(dbQuery("SELECT  SUM(qty) AS qty FROM tbl_temp WHERE id_order = $id_order  AND id_product_attribute = $id_product_attribute"));
			list($checked) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1"));
			$balance_qty = $prepare_qty - $checked;
			if($order_qty == "$checked"){
			}else{
				$row1++;
				list($id_product_attribute,$reference,$barcode) = dbFetchArray(dbQuery("SELECT id_product_attribute,reference,barcode FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute"));
		//	$product = new product();
			//$product->product_attribute_detail($id_product_attribute);
			//$product->product_detail($product->id_product);
			//$barcode = $product->barcode;
			$product_code = $reference;
		echo"
			<tr ";if($order_qty > "$prepare_qty"){echo "style='color:#FF0000'";} echo ">
				<td align='center'>$row1</td><td align='center'>$barcode</td><td> $product_code </td><td align='center'> ".number_format($order_qty)." </td>
				<td align='center'>".number_format($prepare_qty)."</td><td align='center'> ".number_format($checked)." </td>
				<td align='center'>"; if($checked>$order_qty){ echo" <a href='#'  onclick='edit_qc(".$id_product_attribute.",".$id_order.")'><button type='button' class='btn btn-default'>แก้ไข</button></a>
  <form action='controller/qcController.php?edit_qc' method='post'>
	<div class='modal fade' id='edit_qc' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'></h4><input type='hidden' name='id_order' value='$id_order'/>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>แก้ไขรายการ</button>
									  </div>
									</div>
								  </div>
								</div></form>";}else{
								echo" <button type='button' id='$id_product_attribute' class='btn btn-default' data-container='body' data-toggle='popover' data-html='true' data-placement='right' data-content='".product_from_zone($id_order,$id_product_attribute)."'>จากโซน</button></td>
			</tr>
			<script>
			$('#$id_product_attribute').mouseenter(function(){
				$(this).popover('show');
			});
			$('#$id_product_attribute').mouseleave(function(){
				$(this).popover('hide');
			});
			</script>";}
			}
			$n++;
	}
	if($row1 == "0"){
		echo"<tr><td colspan='7' align='center'><a href='controller/qcController.php?close_job&id_order=$id_order&id_employee=$id_employee'><button type='button' class='btn btn-success'>ตรวจเสร็จแล้ว</button></a></td></tr>";
	}else{
	echo "<tr><td colspan='7' align='center'>
<input type='checkbox' id='checkboxes'  onclick='getcondition()' />
สินค้ามีไม่ครบ
<br />
<br />
<div id='continue_bt'></div>
";
echo "</td></tr>";
	}
	echo"<button data-toggle='modal' data-target='#edit_qc' id='btn_toggle' style='display:none;'>toggle</button>
			</table>
			<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
			<div class='row'><div class='col-xs-12'><h4 style='text-align:center;'>รายการที่ครบแล้ว</h4></div></div>
			<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
		<table class='table'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:20%; text-align:center;'>บาร์โค้ด</th><th style='width:35%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวนที่สั่ง</th><th style='width:10%; text-align:center;'>จำนวนที่จัด</th>	<th style='width:10%; text-align:center;'>ตรวจแล้ว</th><th style='width:10%; text-align:center;'>จากโซน</th>
	</thead>";
	$sql = dbQuery("SELECT id_product_attribute, product_qty FROM tbl_order_detail WHERE id_order = $id_order  ORDER BY barcode ASC");
	$row = dbNumRows($sql);
	if($row>0){
	$n = 1;
	$row1 = 0;
	while($list = dbFetchArray($sql)){
			$id_product_attribute  = $list['id_product_attribute'];
			$order_qty = $list['product_qty'];
			list($prepare_qty) = dbFetchArray(dbQuery("SELECT  SUM(qty) AS qty FROM tbl_temp WHERE id_order = $id_order  AND id_product_attribute = $id_product_attribute"));
			list($checked) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1"));
			$balance_qty = $prepare_qty - $checked;
			if($order_qty == "$checked"){
				list($id_product_attribute,$reference,$barcode) = dbFetchArray(dbQuery("SELECT id_product_attribute,reference,barcode FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute"));
		//	$product = new product();
			//$product->product_attribute_detail($id_product_attribute);
			//$product->product_detail($product->id_product);
			//$barcode = $product->barcode;
			$product_code = $reference;
			$row1++;
	echo"
			<tr ";if($order_qty > "$prepare_qty"){echo "style='color:#FF0000'";} echo ">
				<td align='center'>$row1</td><td align='center'>$barcode</td><td> $product_code </td><td align='center'> ".number_format($order_qty)." </td>
				<td align='center'>".number_format($prepare_qty)."</td><td align='center'> ".number_format($checked)." </td>
				<td align='center'><button type='button' id='$id_product_attribute' class='btn btn-default' data-container='body' data-toggle='popover' data-html='true' data-placement='right' data-content='".product_from_zone($id_order,$id_product_attribute)."'>จากโซน</button></td>
			</tr>
			<script>
			$('#$id_product_attribute').mouseenter(function(){
				$(this).popover('show');
			});
			$('#$id_product_attribute').mouseleave(function(){
				$(this).popover('hide');
			});
			</script>";
			}
			$n++;
	}
	}
	echo"
			</table>";
			?>
            <script>
			$(document).ready(function(e) {
    if($("#error").length){
		document.getElementById("sound1").play();
		alert($("#error").text());
	}
});
$(document).ready(function(e) {
    if($("#confirm_error").length){
		$("#move_zone").click();
	}
});
</script>
            <?php
}*/

////// ปิดการตรวจเมื่อตรวจสินค้าครบแล้ว ///////
if(isset($_GET['close_job'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_employee = $_GET['id_employee'];
	$order = new order($id_order);
	dbQuery("UPDATE tbl_temp SET status = 3 WHERE id_order = $id_order AND (status = 2 OR status = 6)");	
	if($order->state_change($order->id_order, 10, $id_employee)){
		header("location: ../index.php?content=qc");
	}else{
		$message = "ปิดการจัดไม่สำเร็จ";
		header("location: ../index.php?content=qc&process&id_order=$id_order&error=$message");
	}
}
if(isset($_GET['over_order'])&&isset($_GET['id_zone'])&&isset($_GET['id_product_attribute'])){
	$id_zone = $_GET['id_zone'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$id_order = $_GET['id_order'];
	list($old_qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute"));
	$new_qty = -1;
	$qty = 1;
	//echo $new_qty;
	update_buffer_zone($qty, $id_product_attribute);
	update_stock_zone($new_qty, $id_zone, $id_product_attribute);
	header("location: ../index.php?content=qc&process&id_order=$id_order");
}

if(isset($_GET['getData'])&&isset($_GET['id_product_attribute'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$sql = dbQuery("SELECT id_qc, id_product_attribute, qty FROM tbl_qc WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order AND valid =1");
	$order_qty = check_qty_in_order($id_product_attribute, $id_order);
	$product = new product();
	$product->product_attribute_detail($id_product_attribute);
	$header = "<table class='table table-striped'><tr><td colspan='3' >".$product->reference." จำนวนสั่ง |  $order_qty  ตัว </td></tr>
				<tr><td style='width:60%; text-align:center;'>สินค้า</td><td style='width:20%; text-align:center;'>จำนวน</td><td style='width:20%; text-align:center;'>จำนวนที่เอาออก</td></tr>";
	$body = "";
	$end = "</table>";
	while($row = dbFetchArray($sql)){
		$id_qc = $row['id_qc'];
		$qty = $row['qty'];
		$body .="<tr><td>".$product->reference."</td><td align='center'>$qty</td>
		<td align='center'>
		<input type='hidden' name='id_qc[]' value='$id_qc' />
		<input type='hidden' name='order_qty[$id_qc]' value='$qty' />
		<input type='checkbox' class='from-control' name='edit[$id_qc]' /></td>";
	}
	$result = $header.$body.$end;
	echo $result;
}
if(isset($_GET['edit_qc'])&&isset($_POST['id_qc'])){
	$id_qc = $_POST['id_qc'];
	$edit_qty = $_POST['edit'];	
	$id_order = $_POST['id_order'];
	foreach($id_qc as $id){
		$edit = $edit_qty[$id];
		if($edit =="on"){ 
		dbQuery("DELETE FROM tbl_qc WHERE id_qc = $id");
		}
	}
	header("location: ../index.php?content=qc&process=y&id_order=$id_order");
}
if(isset($_GET['reload'])){
	echo "<table class='table'>
		<thead>
			<th style='width: 5%; text-align:center;'>ลำดับ</th><th style='width: 20%; text-align:center;'>เลขที่เอกสาร</th>
			<th style='width: 15%; text-align:center;'>ลูกค้า</th><th style='width: 15%; text-align:center;'>รูปแบบ</th><th style='width: 15%; text-align:center;'>วันที่สั่ง</th>
			<th style='width: 15%; text-align:center;'>พนักงานจัด</th><th style='width: 15%; text-align:center;'>&nbsp;</th>
		</thead>";
		$sql = dbQuery("SELECT id_order FROM tbl_order WHERE current_state = 5");
		$n = 1;
		while($row = dbFetchArray($sql)){
			$order = new order($row['id_order']);
			$customer = new customer($order->id_customer);
			list($id_employee) = dbFetchArray(dbQuery("SELECT id_employee FROM tbl_temp WHERE id_order =".$order->id_order." AND status = 1 GROUP BY id_employee"));
			$employee = new employee($id_employee);
			echo"
			<tr>
					<td align='center'>$n</td>
					<td align='center'>".$order->reference."</td>
					<td align='center'>".$customer->full_name."</td>
					<td align='center'>".$order->role_name."</td>
					<td align='center'>".thaiDate($order->date_add)."</td>
					<td align='center'>".$employee->full_name."</td>
					<td align='center'><a href='index.php?content=qc&process=y&id_order=".$order->id_order."'><span class='btn btn-default'>ตรวจสินค้า</span></a></td>
			</tr>";
			$n++;
		}
echo"		</table>";
}
if(isset($_GET['delete'])){
	$id_product_attribute = $_GET['id_product_attribute'];
	$id_order = $_GET['id_order'];
	dbQuery("DELETE FROM tbl_qc WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
	header("location: ../index.php?content=qc&process=y&id_order=$id_order");
}
?>