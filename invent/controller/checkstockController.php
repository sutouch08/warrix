<?php 
include "../../library/config.php";
include "../../library/functions.php";
include "../function/tools.php";
function add_stock($id_zone,$id_product_attribute,$qty,$id_check){
	list($id_stock_check,$qty_stock_check) = dbFetchArray(dbQuery("SELECT id_stock_check,qty_after FROM tbl_stock_check WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute AND id_check = $id_check"));
	if($id_stock_check != ""){
		$up_qty = $qty + $qty_stock_check;
		dbQuery("UPDATE tbl_stock_check SET qty_after = '$up_qty' WHERE id_stock_check = $id_stock_check");
	}else{
		dbQuery("INSERT INTO tbl_stock_check (id_zone,id_check,id_product_attribute,qty_after)VALUES($id_zone,$id_check,$id_product_attribute,$qty)");
	}
}
if(isset($_GET['add'])){
	$product = new product();
	$check_stock = new checkstock();
	$id_check = $check_stock->get_id_check();
	$id_zone = $_GET['id_zone'];
	$barcode_item = $_GET['barcode_item'];
	$id_zone = $_GET['id_zone'];
	$input_qty = $_GET['qty'];
	$id_employee = $_GET['id_employee'];
	$arr = $product->check_barcode($barcode_item);
	$id_product_attribute = $arr['id_product_attribute'];
	$qty = $input_qty * $arr['qty'];
	$zone_name = get_zone($id_zone);
	if($id_product_attribute != ""){
	dbQuery("DELETE FROM tbl_stock_check WHERE id_product_attribute = 0 AND id_zone='$id_zone' AND id_check = $id_check");
	add_stock($id_zone,$id_product_attribute,$qty,$id_check);
	$product->product_attribute_detail($id_product_attribute);
	$message = "เพิ่ม ".$product->reference." จำนวน $qty เรียบร้อยแล้ว";
	echo"<div class='alert alert-success'>$message</div>";
	}else{
		$message = "ไม่มีข้อมูลสินค้านี้กรุณาตรวจสอบบาร์โค้ด"; 
		echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$message</div>";
	}
		
	echo "<table class='table table-bordered table-striped'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:75%;'>รหัสสินค้า</th>
		<th style='width:10%; text-align:right;'>จำนวน</th><th style='width:10%; text-align:center;'>การกระทำ</th>
	</thead>";
		$qr = dbQuery("SELECT id_stock_check,id_product_attribute, Product,id_zone, qty_after FROM stock_check where id_zone = $id_zone AND id_check = $id_check AND qty_after != 0 ORDER BY Product ASC");
		$row = dbNumRows($qr); 
		$i = 0;
		$n = 1;
		if($row == 0){
			echo "<tr><td align='center' colspan='4'><h4>ยังไม่มีสินค้าในโซนนี้</h4></td></tr>";
		}
		while($i<$row){
			list($id_stock_check, $id_product_attribute,  $reference, $id_zone, $qty) = dbFetchArray($qr);
			echo "<tr><td align='center'>$n</td><td>$reference</td><td align='right'><p id='qty$id_stock_check'>".number_format($qty)."</p><input type='text' id='edit_qty$id_stock_check' style='display:none;' /></td><td align='center'>
			<button type='button' id='edit$id_stock_check' class='btn btn-warning btn-xs' onclick='edit_product($id_stock_check) ' >
							<span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>
						</button><button type='button' id='update$id_stock_check' onclick='update($id_stock_check)' class='btn btn-default btn-xs' style='display:none;' >Update</button>
					<a href='controller/checkstockController.php?delete=y&id_stock_check=$id_stock_check&id_zone=$id_zone'>
						<button type='button' id='delete$id_stock_check' class='btn btn-danger btn-xs' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $reference ออกจาก $zone_name หรือไม่'); \">
							<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
						</button>
					</a>
			</td></tr>";
			$i++;
			$n++;
		}
		echo "</table>";
		
}
if(isset($_GET['check_zone'])){
	$barcode_zone = $_POST['barcode_zone'];
	$check_stock = new checkstock();
	$check_stock->detail($check_stock->get_id_check());
	$id_warehouse = $check_stock->id_warehouse;
	list($id_zone)=dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone where barcode_zone = $barcode_zone AND id_warehouse = $id_warehouse"));
	if($id_zone == ""){
		$message = "ไม่มีโซนนี้กรุณาตรวจสอบบาร์โค้ดโซน";
		header("location: ../index.php?content=checkstock&error=$message");
	}else{
		dbQuery("UPDATE tbl_stock_check SET status = 1 WHERE id_zone = '$id_zone' AND id_check = '".$check_stock->get_id_check()."'");
		header("location: ../index.php?content=checkstock&edit=y&id_zone=$id_zone");
	}
}
if(isset($_GET['edit'])){
	$id_stock_check = $_POST['id_stock_check'];
	$id_zone = $_POST['id_zone'];
	$new_qty = $_POST['new_qty'];
	$check_stock = new checkstock();
	$check_stock->get_stock_check($id_stock_check);
	$qty = $check_stock->qty_after;
	$product = new product();
	$product->product_attribute_detail($check_stock->id_product_attribute);
	//echo "UPDATE tbl_stock_check SET qty_after = '$new_qty' WHERE id_stock_check = $id_stock_check";
	dbQuery("UPDATE tbl_stock_check SET qty_after = '$new_qty' WHERE id_stock_check = $id_stock_check");
	$message = "แก้ไข ".$product->reference." จาก $qty เป็น $new_qty เรียบร้อยแล้ว";
	header("location: ../index.php?content=checkstock&id_zone=$id_zone&message=$message");
}
if(isset($_GET['delete'])){
	$id_stock_check = $_GET['id_stock_check'];
	$id_zone = $_GET['id_zone'];
	$check_stock = new checkstock();
	$check_stock->get_stock_check($id_stock_check);
	$qty = $check_stock->qty;
	$product = new product();
	$product->product_attribute_detail($check_stock->id_product_attribute);
	dbQuery("UPDATE tbl_stock_check SET qty_after = 0 WHERE id_stock_check = $id_stock_check");
	$message = "ลบ ".$product->reference." ออกจากโซนนี้เรียบร้อยแล้ว";
	header("location: ../index.php?content=checkstock&id_zone=$id_zone&message=$message");
}
if(isset($_GET['save_diff'])){
	$id_employee = $_GET['id_employee'];
	$id_check = $_GET['id_check'];
	$id_employee = $_COOKIE['user_id'];
			dbQuery("INSERT INTO tbl_diff (id_zone,id_product_attribute,qty_add,qty_minus,id_employee,status_diff) SELECT id_zone,id_product_attribute,qty_after-qty_before,qty_before-qty_after,$id_employee,0 FROM tbl_stock_check WHERE id_check = $id_check");
			dbQuery("UPDATE tbl_diff SET qty_add = '0' WHERE qty_add < 0 ");
			dbQuery("UPDATE tbl_diff SET qty_minus = '0' WHERE qty_minus < 0");
				dbQuery("DELETE FROM tbl_diff WHERE qty_add = 0 AND qty_minus = 0");
	dbQuery("UPDATE tbl_check SET status = 3 WHERE id_check = $id_check");
	$message = "บันทึกยอดต่างเรียบร้อยแล้ว";
	header("location: ../index.php?content=ProductCount&view_stock_diff=y&id_check=$id_check&message=$message");
			//------------------------------- พรุ้งนี้ต่อ ------------------------------------//
}
?>
<?php
	if(isset($_GET['get_data'])&&isset($_GET['id_check'])){
	$id_check = $_GET['id_check'];
	$data = "";
	$sql = dbQuery("SELECT id_zone, status FROM tbl_stock_check WHERE id_check = $id_check GROUP BY id_zone ");
	while($rs=dbFetchArray($sql)){
		$id_zone = $rs['id_zone'];
		$status = $rs['status'];
		switch($status){
		case '-1':
			$class = "zone no-item";
			break;
		case '0':
			$class = "zone before-check";
			break;
		case '1':
			$class = "zone active";
			break;
		default :
			$class = "zone before-check";
			break;	
		}
		$zone_name = get_zone($id_zone);
		$data .="<div class='$class' title='$zone_name'>&nbsp;</div>";
	}
	echo $data;
	}
		
?>