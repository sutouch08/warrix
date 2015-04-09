<?php 
include "../../library/config.php";
include "../../library/functions.php";
include "../function/tools.php";

if(isset($_GET['prepared'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_employee = $_POST['id_user'];
	$id_zone = $_POST['id_zone'];
	$barcode = $_POST['barcode_item'];
	$input_qty = $_POST['qty'];
	$product = new product();
	$arr = $product->check_barcode($barcode);
	$id_product_attribute = $arr['id_product_attribute'];
	$qty = $input_qty * $arr['qty'];
	list($id_warehouse) = dbFetchArray(dbQuery("SELECT id_warehouse FROM tbl_zone WHERE id_zone = $id_zone"));
	if(isset($_POST['barcode'])){ $barcode_zone = $_POST['barcode_zone']; }else{ list($barcode_zone) = dbFetchArray(dbQuery("SELECT barcode_zone FROM tbl_zone WHERE id_zone = $id_zone")); }
	if(check_product_in_order($id_product_attribute, $id_order)){
		$checked = true;
	}else{
		$message = "คุณจัดสินค้าผิด";
		header("location: ../index.php?content=prepare&process&id_order=$id_order&id_zone=$id_zone&barcode_zone=$barcode_zone&error=$message");
		exit;
	}
	$check = check_current_qty($id_order, $id_product_attribute, $qty); ///ตรวจสอบยอดใน temp กับยอด order
	$order_qty = $check['order_qty'];
	$current_qty = $check['current'];
	if($current_qty+$qty > $order_qty){
		$err_qty =  ($current_qty+$qty)-$order_qty;
		$message = "สินค้าเกิน $err_qty ตัว กรุณาคืนที่เดิม $qty ตัว แล้วค่อยจัดใหม่";
		header("location: ../index.php?content=prepare&process&id_order=$id_order&id_zone=$id_zone&barcode_zone=$barcode_zone&error=$message");
		exit;
	}
	list($old_qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute"));
	if($old_qty ==""){
		$message = "ไม่มีสินค้าที่เลือกในโซนนี้ กรุณาตรวจสอบ";
		header("location: ../index.php?content=prepare&process&id_order=$id_order&id_zone=$id_zone&barcode_zone=$barcode_zone&error=$message");
		exit;
	}else if($old_qty<$qty){
		$message = "สินค้าในโซนนี้ มีน้อยกว่ายอดที่คุณป้อน กรุณาตรวจสอบ";
		header("location: ../index.php?content=prepare&process&id_order=$id_order&id_zone=$id_zone&barcode_zone=$barcode_zone&error=$message");
		exit;
	}
	
	if(insert_to_temp($id_order, $id_product_attribute, $qty, $id_warehouse, $id_zone, 1, $id_employee)){
		update_stock_zone((-1*$qty), $id_zone, $id_product_attribute);
		update_buffer_zone($qty, $id_product_attribute);
	}else{
		$message = "ทำรายการไม่สำเร็จ";
		header("location: ../index.php?content=prepare&process&id_order=$id_order&id_zone=$id_zone&barcode_zone=$barcode_zone&error=$message");
		exit;
	}
	if(($current_qty+$qty) == $order_qty){
		dbQuery("UPDATE tbl_order_detail SET valid_detail = 1 WHERE id_order =$id_order AND id_product_attribute = $id_product_attribute");
	}
	header("location: ../index.php?content=prepare&process&id_order=$id_order&id_zone=$id_zone&barcode_zone=$barcode_zone");
}

///// ปิดการจัดเมื่อจัดสินค้าครบแล้ว //////
if(isset($_GET['close_job'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_employee = $_GET['id_employee'];
	$order = new order($id_order);
	dbQuery("UPDATE tbl_prepare SET end = NOW() WHERE id_order = $id_order");
/*	if($order->role == 5){ // เปลี่ยนไปย้ายสต็อกตอนเปิดบิลแล้ว
		list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_order_consignment WHERE id_order = $id_order"));
		$sql = dbQuery("SELECT * FROM tbl_temp WHERE id_order = $id_order");
		while($row = dbFetchArray($sql)){
			$id_product_attribute = $row['id_product_attribute'];
			$qty = $row['qty'];
			$date_upd = date("Y-m-d");
			$new_qty = $qty*(-1);
			dbQuery("UPDATE tbl_temp SET status = 1  WHERE id_temp =".$row['id_temp']);
			stock_movement("out", 2, $row['id_product_attribute'], $row['id_warehouse'], $row['qty'], $order->reference, date('Y-m-d'));
			dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty, date_upd) VALUES ($id_zone, $id_product_attribute, $qty, $date_upd)");
			stock_movement("in", 1, $row['id_product_attribute'], 2, $row['qty'], $order->reference, date('Y-m-d'));
			update_buffer_zone($new_qty, $id_product_attribute);
		}
	}*/
			
	if($order->state_change($order->id_order, 5, $id_employee)){
		dbQuery("UPDATE tbl_order_detail SET valid_detail = 1 WHERE id_order = '$id_order'");
		header("location: ../index.php?content=prepare");
		}else{
		$message = "ปิดการจัดไม่สำเร็จ";
		header("location: ../index.php?content=prepare&process&id_order=$id_order&id_zone=$id_zone&barcode_zone=$barcode_zone&error=$message");
	}
}

if(isset($_GET['getData'])&&isset($_GET['id_product_attribute'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$sql = dbQuery("SELECT * FROM tbl_temp WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
	$order_qty = check_qty_in_order($id_product_attribute, $id_order);
	$product = new product();
	$product->product_attribute_detail($id_product_attribute);
	$header = "<table class='table table-striped'><tr><td colspan='3' >".$product->reference." จำนวนสั่ง | $order_qty ตัว </td></tr>
				<tr><td style='width:50%; text-align:center;'>โซน</td><td style='width:25%; text-align:center;'>จำนวนที่จัด</td><td style='width:25%; text-align:center;'>จำนวนที่เอาออก</td></tr>";
	$body = "";
	$end = "</table>";
	while($row = dbFetchArray($sql)){
		$id_temp = $row['id_temp'];
		$id_zone = $row['id_zone'];
		$qty = $row['qty'];
		$zone = get_zone($id_zone);
		$body .="<tr><td>$zone</td><td align='center'>$qty</td>
		<td align='center'>
		<input type='hidden' name='id_temp[]'  value='$id_temp' />
		<input type='hidden' name='order_qty[$id_temp]' id='order_qty$id_temp' value='$qty' />

		<input type='text' class='from-control' name='edit[$id_temp]' id='edit$id_temp'  value='0' onkeyup='check_qty($id_temp)' /></td>";
	}
	$result = $header.$body.$end;
	echo $result;
}
if(isset($_GET['edit_temp'])&&isset($_POST['id_temp'])){
	$id_temp = $_POST['id_temp'];
	$edit_qty = $_POST['edit'];	
	$order_qty = $_POST['order_qty'];
	$id_order = $_POST['id_order'];
	foreach($id_temp as $id){
		if($edit_qty[$id]<1){ $edit = 0; }else if($edit_qty[$id] > $order_qty[$id]){ $edit = $order_qty[$id]; }else{ $edit = $edit_qty[$id]; }
		$qty = $order_qty[$id];
		$new = $qty - $edit;
		if($new<1){ 
		dbQuery("DELETE FROM tbl_temp WHERE id_temp = $id");
		}else{
		dbQuery("UPDATE tbl_temp SET qty = $new WHERE id_temp = $id");
		}
	}
	header("location: ../index.php?content=prepare&process=y&id_order=$id_order");
}
if(isset($_GET['reload'])){
	echo "<table class='table' >
		<thead>
			<th style='width: 5%; text-align:center;'>ลำดับ</th><th style='width: 20%; text-align:center;'>เลขที่เอกสาร</th>
			<th style='width: 15%; text-align:center;'>ลูกค้า</th><th style='width: 15%; text-align:center;'>รูปแบบ</th><th style='width: 15%; text-align:center;'>วันที่สั่ง</th><th style='width: 5%; text-align:center;'>&nbsp;</th>
		</thead>";
		$sql = dbQuery("SELECT id_order FROM tbl_order WHERE current_state = 3  AND order_status = 1");
		$n = 1;
		while($row = dbFetchArray($sql)){
			$order = new order($row['id_order']);
			$customer = new customer($order->id_customer);
			echo"
			<tr>
					<td align='center'>$n</td>
					<td align='center'>".$order->reference."</td>
					<td align='center'>".$customer->full_name."</td>
					<td align='center'>".$order->role_name."</td>
					<td align='center'>".thaiDate($order->date_add)."</td>
					<td align='center'><a href='index.php?content=prepare&process=y&id_order=".$order->id_order."'><span class='btn btn-default'>จัดสินค้า</span></a></td>
			</tr>";
			$n++;
		}
echo"		</table>";
}
//// function ///////
if(isset($_GET['delete'])){
	$id_product_attribute = $_GET['id_product_attribute'];
	$id_order = $_GET['id_order'];
	dbQuery("DELETE FROM tbl_temp WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
	header("location: ../index.php?content=prepare&process=y&id_order=$id_order");
}
		 

?>