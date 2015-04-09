<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
$id_employee = $_COOKIE['user_id'];
///เพิ่มการปรับยอด///
	if(isset($_GET['add'])){
		$adjust_reference = $_POST['adjust_reference'];
		$note = $_POST['note'];
		$adjust_no = newAdjustNO();
		$adjust_date = dbDate($_POST['adjust_date']);
		dbQuery("INSERT INTO tbl_adjust(adjust_no,adjust_reference,adjust_date,adjust_note,id_employee) VALUES ('$adjust_no','$adjust_reference','$adjust_date','$note','$id_employee')");
		list($id_adjust) = dbFetchArray(dbQuery("SELECT id_adjust from tbl_adjust where adjust_no = '$adjust_no'"));
		header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
	}
	//เพิ่มสินค้าที่จะปรับยอด
	if(isset($_GET['add_detail'])){
		$barcode = $_POST['barcode'];
		$barcode_zone = $_POST['barcode_zone'];
		$zone_name = $_POST['zone_name'];
		if($barcode_zone !=""){ $zone = $barcode_zone; }else if($zone_name !=""){ $zone = $zone_name; }else{ $zone =""; }
		$adjust_qty_add = $_POST['adjust_qty_add'];
		$adjust_qty_minus = $_POST['adjust_qty_minus'];
		$id_adjust = $_POST['id_adjust'];
		$id_warehouse = $_POST['id_warehouse'];
		$adjust_no = $_POST['adjust_no'];
		list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute where barcode = '$barcode'"));
		list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE (barcode_zone = '$zone' or zone_name = '$zone') and id_warehouse = '$id_warehouse'"));
		if($id_product_attribute == ""){
			$message = "ไม่มีสินค้านี้กรุณาตรวจสอบบาร์โค้ดสินค้าใหม่";
			header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust&message=$message");
		}else if($id_zone == ""){
			$message = "ไม่มีโซนนี้กรุณาตรวจสอบ คลัง บาร์โค้ดหรือชื่อโซน";
			header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust&message=$message");
		}else{
			list($id_adjust_detail,$qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT id_adjust_detail,adjust_qty_add,adjust_qty_minus FROM tbl_adjust_detail WHERE id_adjust = '$id_adjust' and id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute' and status_up = '0'"));
			if($id_adjust_detail == ""){
				if(allow_under_zero() == false ){
					list($qty_stock) = dbFetchArray(dbQuery("select qty from tbl_stock where id_product_attribute = '$id_product_attribute' and id_zone = '$id_zone'"));
					$qty2 = $adjust_qty_add - $adjust_qty_minus;
					if($qty2 < "0"){
						$qty3 = $qty2 * (-1);
					}else{
						$qty3 = "";
					}
						if($qty_stock >= "$qty3"){
							dbQuery("INSERT INTO tbl_adjust_detail(id_adjust,id_product_attribute,id_zone,adjust_qty_add,adjust_qty_minus)VALUES('$id_adjust','$id_product_attribute','$id_zone','$adjust_qty_add','$adjust_qty_minus')");
							dbQuery("UPDATE tbl_adjust SET adjust_status = '0'  WHERE id_adjust = '$id_adjust'");
							header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
						}else{
							if($qty_stock == ""){
								$qty_stock = 0;
							}
							$message = "ระบบไม่สามารถไห้ยอดสินค้าติดลบได้ ยอดสินค้าในโซนนี้คงเหลือ $qty_stock ชิ้น";
							header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust&message=$message");				
						}
					}else{
				dbQuery("INSERT INTO tbl_adjust_detail(id_adjust,id_product_attribute,id_zone,adjust_qty_add,adjust_qty_minus)VALUES('$id_adjust','$id_product_attribute','$id_zone','$adjust_qty_add','$adjust_qty_minus')");
				dbQuery("UPDATE tbl_adjust SET adjust_status = '0'  WHERE id_adjust = '$id_adjust'");
				header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
					}
			}else{
				$qty1 = $qty_add - $qty_minus;
				$qty2 = $adjust_qty_add - $adjust_qty_minus;
				$sumqty = $qty1 + $qty2;
				if($sumqty > "0"){
					dbQuery("UPDATE tbl_adjust_detail SET adjust_qty_add = '$sumqty' , adjust_qty_minus = '0' WHERE id_adjust_detail = '$id_adjust_detail'");
					dbQuery("UPDATE tbl_adjust SET adjust_status = '0'  WHERE id_adjust = '$id_adjust'");
					header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
				}else{
					$sumqty1 = $sumqty * (-1);
					if(allow_under_zero() == true ){
						list($qty_stock) = dbFetchArray(dbQuery("select qty from tbl_stock where id_product_attribute = '$id_product_attribute' and id_zone = '$id_zone'"));
						if($qty_stock >= "$sumqty1"){
							dbQuery("UPDATE tbl_adjust_detail SET adjust_qty_add = '0' , adjust_qty_minus = '$sumqty1' WHERE id_adjust_detail = '$id_adjust_detail'");
							dbQuery("UPDATE tbl_adjust SET adjust_status = '0'  WHERE id_adjust = '$id_adjust'");
							header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
						}else{
							if($qty_stock == ""){
								$qty_stock = 0;
							}
							$message = "ระบบไม่สามารถไห้ยอดสินค้าติดลบได้ ยอดสินค้าในโซนนี้คงเหลือ $qty_stock ชิ้น";
							header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust&message=$message");
						}	
					}else{
						dbQuery("UPDATE tbl_adjust_detail SET adjust_qty_add = '0' , adjust_qty_minus = '$sumqty1' WHERE id_adjust_detail = '$id_adjust_detail'");
						dbQuery("UPDATE tbl_adjust SET adjust_status = '0'  WHERE id_adjust = '$id_adjust'");
						header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
					}
				}
			}
		}
	}
	//โหลดยอด diff
	if(isset($_GET['loaddiff'])){
		$id_adjust = $_POST['id_adjust'];
		for($loop=1;$loop<=$_POST["hdnCount"];$loop++){
			if(isset($_POST["chkDel$loop"])){
			$id_diff = $_POST["chkDel$loop"];
				if($id_diff != ""){
					dbQuery("UPDATE tbl_diff SET status_diff = '1' where id_diff = '$id_diff'");
					list($id_zone,$id_product_attribute,$qty_add,$qty_minus) = dbFetchArray(dbQuery("select id_zone,id_product_attribute,qty_add,qty_minus from tbl_diff where id_diff = '$id_diff'"));
					dbQuery("INSERT INTO tbl_adjust_detail(id_adjust,id_product_attribute,id_zone,adjust_qty_add,adjust_qty_minus,status_adjust)VALUES('$id_adjust','$id_product_attribute','$id_zone','$qty_add','$qty_minus','$id_diff')");
				}
			}
		}
		dbQuery("UPDATE tbl_adjust SET adjust_status = '0'  WHERE id_adjust = '$id_adjust'");
		header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
	}
	//ลบรายการสินค้าที่ปรับยอด
	if(isset($_GET['drop_adjust'])){
		$id_adjust = $_GET['id_adjust'];
		$id_adjust_detail = $_GET['id_adjust_detail'];
		list($adjust_no) = dbFetchArray(dbQuery("SELECT adjust_no from tbl_adjust where id_adjust = '$id_adjust'"));
		list($status_adjust,$status_up,$id_zone,$id_product_attribute,$adjust_qty_add,$adjust_qty_minus) = dbFetchArray(dbQuery("select status_adjust,status_up,id_zone,id_product_attribute,adjust_qty_add,adjust_qty_minus from tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'"));
		list($id_warehouse) = dbFetchArray(dbQuery("select id_warehouse from tbl_zone where id_zone = '$id_zone'"));
		list($adjust_date) = dbFetchArray(dbQuery("select adjust_date from tbl_adjust where id_adjust = '$id_adjust'"));
		if($status_adjust == "0"){
			if($status_up == "0"){
				dbQuery("DELETE FROM tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'");
			}else{
				$sum_qty_adjust = $adjust_qty_add - $adjust_qty_minus;
				list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'"));
				$sumqty = $qty - $sum_qty_adjust;
				dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = '$id_stock'");
				if($sum_qty_adjust < "0" ){
					$sum_qty_adjust1 = $sum_qty_adjust * (-1);
					stock_movement("in",7,$id_product_attribute,$id_warehouse,$sum_qty_adjust1, $adjust_no,$adjust_date);
				}else if($sum_qty_adjust > "0"){
					stock_movement("out",8,$id_product_attribute,$id_warehouse,$sum_qty_adjust, $adjust_no,$adjust_date);
				}
				dbQuery("DELETE FROM tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'");
			}
		}else{
			if($status_up == "0"){
				dbQuery("DELETE FROM tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'");
			}else{
				$sum_qty_adjust = $adjust_qty_add - $adjust_qty_minus;
				list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'"));
				$sumqty = $qty - $sum_qty_adjust;
				dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = '$id_stock'");
				if($sum_qty_adjust < "0" ){
					$sum_qty_adjust1 = $sum_qty_adjust * (-1);
					stock_movement("in",7,$id_product_attribute,$id_warehouse,$sum_qty_adjust1, $adjust_no,$adjust_date);
				}else if($sum_qty_adjust > "0"){
					stock_movement("out",8,$id_product_attribute,$id_warehouse,$sum_qty_adjust, $adjust_no,$adjust_date);
				}
				dbQuery("DELETE FROM tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'");
			}
			dbQuery("UPDATE tbl_diff SET status_diff = '0' where id_diff = '$status_adjust'");
		}
		dropstockZero($id_product_attribute,$id_zone);
		header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
	}
	//แก้ไขรายการสินค้าที่ปรับยอด
	if(isset($_GET['edit_detail'])){
		$id_adjust = $_POST['id_adjust'];
		$id_adjust_detail = $_POST['id_adjust_detail'];
		$barcode = $_POST['barcode'];
		$barcode_zone = $_POST['barcode_zone'];
		$zone_name = $_POST['zone_name'];
		$adjust_qty_add = $_POST['adjust_qty_add'];
		$adjust_qty_minus = $_POST['adjust_qty_minus'];
		$id_warehouse = $_POST['id_warehouse'];
		list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute where barcode = '$barcode'"));
		list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE (barcode_zone = '$barcode_zone' or zone_name = '$zone_name') and id_warehouse = '$id_warehouse'"));
		if($id_product_attribute == ""){
			$message = "ไม่มีสินค้านี้กรุณาตรวจสอบบาร์โค้ดสินค้าใหม่";
			header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust&message=$message");
		}else if($id_zone == ""){
			$message = "ไม่มีโซนนี้กรุณาตรวจสอบ คลัง บาร์โค้ดหรือชื่อโซน";
			header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust&message=$message");
		}else{
			dbQuery("UPDATE tbl_adjust_detail SET id_product_attribute = '$id_product_attribute' , id_zone = '$id_zone' , adjust_qty_add = '$adjust_qty_add' , adjust_qty_minus = '$adjust_qty_minus' WHERE id_adjust_detail = '$id_adjust_detail'");
		}
		header("location: ../index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust");
	}
	//ปรับยอด
	if(isset($_GET['adjust'])){
		$id_adjust = $_GET['id_adjust'];
		list($adjust_date) = dbFetchArray(dbQuery("select adjust_date from tbl_adjust where id_adjust = '$id_adjust'"));
		list($adjust_no) = dbFetchArray(dbQuery("SELECT adjust_no FROM tbl_adjust WHERE id_adjust = '$id_adjust'"));
		$result = dbQuery("SELECT id_adjust_detail,id_product_attribute,barcode,reference,id_warehouse,warehouse_name,id_zone,barcode_zone,zone_name,adjust_qty_add,adjust_qty_minus,status_adjust FROM adjust_datail_table where id_adjust = '$id_adjust' and status_up = '0'");
		$i=0;
		$row = dbNumRows($result);
		while($i<$row){
		list($id_adjust_detail, $id_product_attribute, $barcode, $reference, $id_warehouse, $warehouse_name, $id_zone ,$barcode_zone ,$zone_name ,$adjust_qty_add ,$adjust_qty_minus ,$status_adjust) = dbFetchArray($result);
		$sum_qty_adjust = $adjust_qty_add - $adjust_qty_minus;
		list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'"));
		if($id_stock != ""){
		$sumqty = $qty + $sum_qty_adjust;
		dbQuery("UPDATE tbl_stock SET qty = '$sumqty' where id_stock = '$id_stock'");
		dbQuery("UPDATE tbl_diff SET status_diff = '2' where id_diff = '$status_adjust'");
		if($sum_qty_adjust > "0" ){
			stock_movement("in",7,$id_product_attribute,$id_warehouse,$sum_qty_adjust, $adjust_no,$adjust_date);
		}else if($sum_qty_adjust < "0"){
			$sum_qty_adjust1 = $sum_qty_adjust * (-1);
			stock_movement("out",8,$id_product_attribute,$id_warehouse,$sum_qty_adjust1, $adjust_no,$adjust_date);
		}
		}else{
			dbQuery("INSERT INTO tbl_stock (id_zone,id_product_attribute,qty) VALUES ('$id_zone','$id_product_attribute','$sum_qty_adjust')");
			if($sum_qty_adjust > "0" ){
				stock_movement("in",7,$id_product_attribute,$id_warehouse,$sum_qty_adjust, $adjust_no,$adjust_date);
			}else if($sum_qty_adjust < "0"){
				$sum_qty_adjust1 = $sum_qty_adjust * (-1);
				stock_movement("out",8,$id_product_attribute,$id_warehouse,$sum_qty_adjust1, $adjust_no,$adjust_date);
			}
		}
		dropstockZero($id_product_attribute,$id_zone);
		$i++;
		}
		dbQuery("UPDATE tbl_adjust SET adjust_status = '1' where id_adjust = '$id_adjust'");
		dbQuery("UPDATE tbl_adjust_detail SET status_up = '1' where id_adjust = '$id_adjust'");
		header("location: ../index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust");
	}
	//ลบการปรับยอด
	if(isset($_GET['drop'])){
		$id_adjust = $_GET['id_adjust'];
		list($adjust_date) = dbFetchArray(dbQuery("select adjust_date from tbl_adjust where id_adjust = '$id_adjust'"));
		list($adjust_no) = dbFetchArray(dbQuery("SELECT adjust_no from tbl_adjust where id_adjust = '$id_adjust'"));
		$result = dbQuery("SELECT id_adjust_detail,status_adjust,status_up,id_zone,id_product_attribute,adjust_qty_add,adjust_qty_minus FROM adjust_datail_table where id_adjust = '$id_adjust'");
		$i=0;
		$row = dbNumRows($result);
		while($i<$row){
		list($id_adjust_detail, $status_adjust,$status_up,$id_zone,$id_product_attribute,$adjust_qty_add,$adjust_qty_minus) = dbFetchArray($result);
		list($id_warehouse) = dbFetchArray(dbQuery("select id_warehouse from tbl_zone where id_zone = '$id_zone'"));
			if($status_adjust == "0"){
			if($status_up == "0"){
				dbQuery("DELETE FROM tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'");
			}else{
				$sum_qty_adjust = $adjust_qty_add - $adjust_qty_minus;
				list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'"));
				$sumqty = $qty - $sum_qty_adjust;
				dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = '$id_stock'");
				if($sum_qty_adjust < "0" ){
					$sum_qty_adjust1 = $sum_qty_adjust * (-1);
					stock_movement("in",7,$id_product_attribute,$id_warehouse,$sum_qty_adjust1, $adjust_no,$adjust_date);
				}else if($sum_qty_adjust > "0"){
					stock_movement("out",8,$id_product_attribute,$id_warehouse,$sum_qty_adjust, $adjust_no,$adjust_date);
				}
				dbQuery("DELETE FROM tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'");
			}
		}else{
			if($status_up == "0"){
				dbQuery("DELETE FROM tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'");
			}else{
				$sum_qty_adjust = $adjust_qty_add - $adjust_qty_minus;
				list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'"));
				$sumqty = $qty - $sum_qty_adjust;
				dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = '$id_stock'");
				if($sum_qty_adjust < "0" ){
					$sum_qty_adjust1 = $sum_qty_adjust * (-1);
					stock_movement("in",7,$id_product_attribute,$id_warehouse,$sum_qty_adjust1, $adjust_no,$adjust_date);
				}else if($sum_qty_adjust > "0"){
					stock_movement("out",8,$id_product_attribute,$id_warehouse,$sum_qty_adjust, $adjust_no,$adjust_date);
				}
				dbQuery("DELETE FROM tbl_adjust_detail where id_adjust_detail = '$id_adjust_detail'");
			}
			dbQuery("UPDATE tbl_diff SET status_diff = '0' where id_diff = '$status_adjust'");
		}
		dropstockZero($id_product_attribute,$id_zone);
		$i++;
		}
		dbQuery("DELETE FROM tbl_adjust where id_adjust = '$id_adjust'");
		
		header("location: ../index.php?content=ProductAdjust");
	}
?>