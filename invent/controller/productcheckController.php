<?php 
require "../../library/config.php";
$id_employee = $_COOKIE['user_id'];
	if(isset($_GET['editqty'])){
		//ini_set('max_input_vars', 3000);
		$id_zone = $_POST['id_zone'];
		$qty = $_POST['qty'];
		$qty_check = $_POST['qty_check'];
		$id_product_attribute = $_POST['id_product_attribute'];
		$i =0;
		foreach($id_product_attribute as $id){
			$diff = $qty_check[$i] - $qty[$i];
			if($diff >0){
				list($qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff where id_zone = '$id_zone' and id_product_attribute = $id and status_diff = '0'"));
				if($qty_add == ""){
					dbQuery("INSERT INTO tbl_diff(id_zone,id_product_attribute,qty_add,qty_minus,id_employee) VALUES ('$id_zone','$id','$diff','0','$id_employee')");
				}else{
					$diff1 = ($qty_add-$qty_minus) + $diff;
					if($diff1 > "0"){
						dbQuery("UPDATE tbl_diff SET qty_add = '$diff1' , qty_minus = '0' where id_zone = '$id_zone' and id_product_attribute = '$id' and status_diff = '0'");
					}else if($diff1 < "0"){
						$diff2 = $diff1*-1;
						dbQuery("UPDATE tbl_diff SET qty_add = '0' , qty_minus = '$diff2' where id_zone = '$id_zone' and id_product_attribute = '$id' and status_diff = '0'");
					}else if($diff1 == "0"){
						dbQuery("DELETE FROM tbl_diff where id_zone = '$id_zone' and id_product_attribute = '$id' and status_diff = '0'");
					}
				}
			}else if($diff < "0"){
				$diff2 = $diff * -1;
				list($qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff where id_zone = '$id_zone' and id_product_attribute = '$id' and status_diff = '0'"));
				if($qty_minus == ""){
					dbQuery("INSERT INTO tbl_diff(id_zone,id_product_attribute,qty_add,qty_minus,id_employee) VALUES ('$id_zone','$id','','$diff2','$id_employee')");
				}else{
					$diff1 = ($qty_add-$qty_minus) + $diff;
					if($diff1 > "0"){
						dbQuery("UPDATE tbl_diff  SET qty_add = '$diff1' , qty_minus = '0' where id_zone = '$id_zone' and id_product_attribute = '$id' and status_diff = '0'");
					}else if($diff1 < "0"){
						$diff2 = $diff1*-1;
						dbQuery("UPDATE tbl_diff SET qty_add = '0' , qty_minus = '$diff2' where id_zone = '$id_zone' and id_product_attribute = '$id' and status_diff = '0'");
					}else if($diff1 == "0"){
						dbQuery("DELETE FROM tbl_diff where id_zone = '$id_zone' and id_product_attribute = '$id' and status_diff = '0'");
					}
				}
			}
			$i++;
		}
		header("location: ../index.php?content=ProductCheck&id_zone=$id_zone");
	}
	if(isset($_GET['add'])){
		$number = $_POST['number'];
		$barcode_item = $_POST['barcode_item'];
		$id_zone = $_POST['id_zone'];
		list($id_product_attribute) = dbFetchArray(dbQuery("select id_product_attribute from tbl_product_attribute where barcode = '$barcode_item'"));
		if($id_product_attribute != ""){
			list($id_stock,$qty) = dbFetchArray(dbQuery("select id_stock,qty from tbl_stock where id_product_attribute = '$id_product_attribute' and id_zone = '$id_zone'"));
			if($id_stock != ""){
				list($qty_add1,$qty_minus1) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'  and status_diff = '0'"));
				$diff = $number - ($qty+($qty_add1 - $qty_minus1));
				if($diff > "0"){
					list($qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'  and status_diff = '0'"));
					if($qty_add == ""){
						dbQuery("INSERT INTO tbl_diff(id_zone,id_product_attribute,qty_add,qty_minus,id_employee) VALUES ('$id_zone','$id_product_attribute','$diff','0','$id_employee')");
					}else{
						$diff1 = ($qty_add-$qty_minus) + $diff;
						if($diff1 > "0"){
							dbQuery("UPDATE tbl_diff SET qty_add = '$diff1' , qty_minus = '0' where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'  and status_diff = '0'");
						}else if($diff1 < "0"){
							$diff2 = $diff1*-1;
							dbQuery("UPDATE tbl_diff SET qty_add = '0' , qty_minus = '$diff2' where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute'  and status_diff = '0'");
						}
					}
				}else if($diff < "0"){
					$diff2 = $diff * -1;
					list($qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute' and status_diff = '0'"));
					if($qty_minus == ""){
						dbQuery("INSERT INTO tbl_diff(id_zone,id_product_attribute,qty_add,qty_minus,id_employee) VALUES ('$id_zone','$id_product_attribute','','$diff2','$id_employee')");
					}else{
						$diff1 = ($qty_add-$qty_minus) + $diff;
						if($diff1 > "0"){
							dbQuery("UPDATE tbl_diff  SET qty_add = '$diff1' , qty_minus = '0' where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute' and status_diff = '0'");
						}else if($diff1 < "0"){
							$diff2 = $diff1*-1;
							dbQuery("UPDATE tbl_diff SET qty_add = '0' , qty_minus = '$diff2' where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute' and status_diff = '0'");
						}
					}
				}
			}else{
				dbQuery("INSERT INTO tbl_stock (id_zone,id_product_attribute,qty) VALUES ('$id_zone', '$id_product_attribute', '0')");
				dbQuery("INSERT INTO tbl_diff(id_zone,id_product_attribute,qty_add,qty_minus,id_employee) VALUES ('$id_zone','$id_product_attribute','$number','','$id_employee')");
			}
			header("location: ../index.php?content=ProductCheck&id_zone=$id_zone");
		}else{
			header("location: ../index.php?content=ProductCheck&add=y&id_zone=$id_zone&not=N");
		}		
	}
	
?>