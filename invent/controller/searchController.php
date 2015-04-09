<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";

$header = "
	<table class='table table-bordered'>
		<thead>
			<th style='width:15%; text-align:center;'>รูปภาพ</th><th style='width:45%; text-align:center;'>สินค้า</th><th style='width:15%; text-align:center;'>จำนวน</th><th style='width:25%; text-align:center;'>สถานที่</th>
		</thead>";
		
if(isset($_GET['text'])){
	$text = trim($_GET['text']);
	$fillter = $_GET['fillter'];
	$html = $header;
	if($text ==""){ echo ""; exit; }
	//echo "SELECT distinct id_product_attribute, reference, product_name FROM product_search WHERE $fillter LIKE'%".$text."%'  GROUP BY id_product_attribute ";
	$sql = dbQuery("SELECT distinct id_product_attribute, reference, product_name FROM product_search WHERE $fillter LIKE'%".$text."%' GROUP BY id_product_attribute ");
	$row =dbNumRows($sql);
	if($row>0){
		while($data = dbFetchArray($sql)){
			$id_product_attribute = $data['id_product_attribute'];
			$sqr = dbQuery("SELECT distinct qty,zone_name FROM product_search WHERE id_product_attribute = $id_product_attribute");
			$qty1 =0;
			while($r = dbFetchArray($sqr)){
				
			$qty1 = $qty1+ $r['qty'];
			}list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
			$qty = $qty1+$qty_moveing;
			$product = new product();
			$id_product = $product->getProductId($id_product_attribute);
			$product->product_detail($id_product);
			$product->product_attribute_detail($id_product_attribute);	
			$reference = $product->reference;
			$product_name = $product->product_name;
			$html .= "<tr><td align='center'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /></td>
			<td style='vertical-align:middle;'>".$reference." : ".$product_name."</td><td align='center' style='vertical-align:middle;'>$qty</td><td align='center' style='vertical-align:middle;'><button type='button' id='$id_product_attribute' class='btn btn-default' data-container='body' data-toggle='popover' data-html='true' data-placement='right' data-content='".$product->stock_in_zone($id_product_attribute,true)."'>แสดงที่เก็บ</button></td>
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
	}else{
		$html .= "<tr><td colspan='4'><h4 style='text-align:center'>ไม่พบรายการที่ค้นหา</h4></td></tr>";
	}		
		$html .="</table>";
		echo $html;
	}

?>

			