<?php 
require "../../library/config.php";
require "../../library/functions.php";
	$id_employee = $_COOKIE['user_id'];
	if(isset($_GET['moveout'])){
		$barcode = $_POST['barcode'];
		$qty_move = $_POST['qty'];
		$id_zone = $_POST['id_zone'];
		$id_product_attribute = checkProduct($barcode);
		if($id_product_attribute == ""){
			$message = "ไม่มีสินค้านี้กรุณาตรวจสอบบาร์โค้ด";
			header("location: ../index.php?content=ProductMove&id_zone=$id_zone&message=$message");
		}else{
			list($qty,$id_stock) = dbFetchArray(dbQuery("SELECT qty,id_stock FROM tbl_stock WHERE id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'"));
			list($qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff WHERE id_product_attribute = '$id_product_attribute' and id_zone = '$id_zone' and status_diff = '0'"));
			$sumqty = $qty +($qty_add - $qty_minus);
			list($id_warehouse) = dbFetchArray(dbQuery("SELECT id_warehouse FROM tbl_zone WHERE id_zone = $id_zone"));
			if($id_stock == ""){
				$message = "ไม่มีสินค้าในโซนนี้";
				header("location: ../index.php?content=ProductMove&id_zone=$id_zone&message=$message");
			}else if($sumqty < "$qty_move"){
				$message = "จำนวนสินค้ามี $sumqty เท่านั้นกรุณาตรวจสอบจำนวนที่จะย้าย";
				header("location: ../index.php?content=ProductMove&id_zone=$id_zone&message=$message");
			}else{
				$summove = $qty - $qty_move;
				dbQuery("UPDATE tbl_stock SET qty = '$summove' WHERE id_stock = '$id_stock'");
				list($id_move,$qtymove) = dbFetchArray(dbQuery("SELECT id_move,qty_move FROM tbl_move where id_product_attribute = '$id_product_attribute' and id_warehouse = '$id_warehouse'"));
				if($id_move == ""){
					dbQuery("INSERT INTO tbl_move (id_product_attribute,qty_move,id_employee,id_warehouse,id_zone) VALUES ('$id_product_attribute','$qty_move','$id_employee','$id_warehouse','$id_zone')");
				}else{
					$sumqtymove = $qty_move + $qtymove;
					dbQuery("UPDATE tbl_move SET qty_move = '$sumqtymove' WHERE id_move = '$id_move'");
				}
				dropstockZero($id_product_attribute,$id_zone);
				header("location: ../index.php?content=ProductMove&id_zone=$id_zone");
			}	
		}
		
	}
	if(isset($_GET['movein'])){
		$id_move = $_POST['id_move'];
		$zone = $_POST['zone'];
		$qty = $_POST['qty'];
		$qty_move = $_POST['qty_move'];
		$id_product_attribute = $_POST['id_product_attribute'];
		list($reference) = dbFetchArray(dbQuery("SELECT reference FROM tbl_product_attribute where id_product_attribute = '$id_product_attribute'"));
		list($id_zone,$zone_name,$id_warehouse) = dbFetchArray(dbQuery("SELECT id_zone,zone_name,id_warehouse FROM tbl_zone where barcode_zone = '$zone' or zone_name = '$zone'"));
		if($id_zone == ""){
			$message = "ไม่มีโซนนี้กรุณาตรวจสอบ";
			header("location: ../index.php?content=ProductMove&productMove=y&in=in&id=$id_move&error=$message");
		}else{
			list($id_warehouse_move) = dbFetchArray(dbQuery("SELECT id_warehouse FROM tbl_move WHERE id_move = '$id_move'"));
			if($id_warehouse != "$id_warehouse_move"){
				$message = "ไม่สามารถย้ายสินค้าข้ามคลังได้";
				header("location: ../index.php?content=ProductMove&productMove=y&in=in&id=$id_move&error=$message");
			}else{
				$qtysum = $qty - $qty_move;
				if($qtysum < "0"){
					$message = "สินค้าย้ายมา $qty คุณใส่จำนวนเกินกรุณาตรวจสอบจำนวน";
					header("location: ../index.php?content=ProductMove&productMove=y&in=in&id=$id_move&message=$message");
				}else{
					list($qty_stock,$id_stock) = dbFetchArray(dbQuery("SELECT qty,id_stock from tbl_stock where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'"));
					if($id_stock == ""){
						dbQuery("INSERT INTO tbl_stock (id_zone,id_product_attribute,qty) VALUES ('$id_zone','$id_product_attribute','$qty_move')");
						dbQuery("UPDATE tbl_move SET qty_move = '$qtysum' where id_move = '$id_move'");
						dbQuery("DELETE FROM tbl_move where id_move = '$id_move' and qty_move = '0'");
					}else{
						$qty_sum_in = $qty_stock + $qty_move;
						dbQuery("UPDATE tbl_stock SET qty = '$qty_sum_in' where id_stock = '$id_stock'");
						dbQuery("UPDATE tbl_move SET qty_move = '$qtysum' where id_move = '$id_move'");
						dbQuery("DELETE FROM tbl_move where id_move = '$id_move' and qty_move = '0'");
					}
					$message = "ย้าย $reference จำนวน $qty_move เรียบร้อย";
					header("location: ../index.php?content=ProductMove&productMove=y&message=$message");
				}
			}
				
		}
	}
?>