<?php 
//************************************* รายงานสินค้าคงเหลือแยกตามโซน  *******************************///
if(isset($_GET['stock_zone_report'])&&isset($_GET['product'])&&isset($_GET['warehouse'])&&isset($_GET['zone'])){
	$product_rank = $_GET['product'];
	$warehouse_rank = $_GET['warehouse'];
	$zone_rank = $_GET['zone'];
	$date = date('Y-m-d');
	if(isset($_GET['product_from'])&&isset($_GET['product_to'])){ // *** เรียงลำดับ id_product จากน้อยไปมาก
		$p_from  = $_GET['product_from'];
		$p_to = $_GET['product_to'];
			if($p_to < $p_from){
				$product_from = $p_to;
				$product_to = $p_from;
			}else{
				$product_from = $p_from;
				$product_to = $p_to;
			}
	}else{ 
		$product_from =""; $product_to = "";
	}
	if(isset($_GET['product_selected'])){ $product_selected = $_GET['product_selected'];}else{ $product_selected="";}
	if($product_rank==0){  //// product
		$product ="id_product !=''";
		}else if($product_rank==1){ 
			$product ="(id_product BETWEEN '$product_from' AND '$product_to' )";
		}else if($product_rank ==2){
			$product ="id_product = '$product_selected'";
		}
	if(isset($_GET['warehouse_selected'])){ $warehouse_selected = $_GET['warehouse_selected'];}else{ $warehouse_selected="";}
	if($warehouse_rank==0){  //// customer
		$warehouse ="id_warehouse !='-1'";
		$id_warehouse = "";
		}else if($warehouse_rank ==1){
				$warehouse ="id_warehouse = '$warehouse_selected'";	
				$id_warehouse = $warehouse_selected;
		}
	if(isset($_GET['zone_selected'])){ $zone_selected = $_GET['zone_selected'];}else{ $zone_selected="";}
	if($zone_rank ==0){
		$zone ="tbl_zone.id_zone !='-1'";
	}else if($zone_rank ==1){
		$zone_selected = get_id_zone($zone_selected);
		$zone ="tbl_zone.id_zone = $zone_selected";
	}
		$report_date = "  ณ วันที่ ".thaiTextDate($date);
		if($warehouse_rank==0){ $report_warehouse = "รวมทุกคลัง";}else{ $report_warehouse = "   คลัง ".getWarehouseName($warehouse_selected);}
		$report_title = "รายงานสินค้าคงเหลือแยกตามโซน "."  $report_date";
		$html ="<h4 align='center'>$report_title</h4><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
				<table class='table table-striped'>
				<thead><tr style='font-size:14px;'>
				<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:15%; text-align:center;'>โซน</th><th style='width:10%;'>บาร์โค้ด</th><th style='width:15%; '>รหัส</th>
				<th style='width:20%; '>ชื่อสินค้า</th><th style='width:10%; text-align: right;'>ทุน</th><th style='width:10%; text-align: right;'>คงเหลือ</th><th style='width:15%; text-align: right;'>มูลค่า</th>
				</tr></thead>";
		$qr = dbQuery("SELECT tbl_stock.id_product_attribute, qty, tbl_zone.id_zone FROM tbl_stock LEFT JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE $product AND $warehouse AND $zone ORDER BY barcode ASC");
		$row = dbNumRows($qr); 
		if($row>0){
			$i = 0;
			$n = 1;
			$total_qty = 0;
			$total_cost = 0;
			while($i<$row){
				list($id_product_attribute, $qty, $id_zone) = dbFetchArray($qr);
				$product = new product();
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product);
				$product->product_attribute_detail($id_product_attribute);
				$zone_name = get_zone($id_zone);
				$cost = $product->product_cost;
				$cost_amount = $qty*$cost;
				$html .="<tr style='font-size: 12px;'><td align='center'>".$n."</td><td>".$zone_name."</td><td>".$product->barcode."</td><td>".$product->reference."</td><td>".$product->product_name."</td>";
				$html .="<td align='right'>".$cost."</td><td align='right'>".number_format($qty)."</td><td align='right'>".number_format($cost_amount,2)."</td></tr>";
				$total_qty += $qty;
				$total_cost += $cost_amount;
				$i++; $n++;
			}
		$html .="<tr><td colspan='6' align='right'><h4>รวม</h4></td><td align='right'><h4>".number_format($total_qty)."</h4></td><td align='right'><h4>".number_format($total_cost,2)."</h4></td></tr>";
		}else{
			$html .="<tr><td colspan='8'><h4 align='center'>------------------  ไม่มีรายการตามเงื่อนไขที่เลือก  --------------------------</h4></td></tr>";
		}
		$html .="</table>";
		echo $html;
}
//************************************* รายงานสินค้าคงเหลือแยกตามโซน  export to excel *******************************///
if(isset($_GET['export_stock_zone_report'])&&isset($_GET['product'])&&isset($_GET['warehouse'])&&isset($_GET['zone'])){
	$product_rank = $_GET['product'];
	$warehouse_rank = $_GET['warehouse'];
	$zone_rank = $_GET['zone'];
	$date = date('Y-m-d');
	if(isset($_GET['product_from'])&&isset($_GET['product_to'])){ // *** เรียงลำดับ id_product จากน้อยไปมาก
		$p_from  = $_GET['product_from'];
		$p_to = $_GET['product_to'];
			if($p_to < $p_from){
				$product_from = $p_to;
				$product_to = $p_from;
			}else{
				$product_from = $p_from;
				$product_to = $p_to;
			}
	}else{ 
		$product_from =""; $product_to = "";
	}
	if(isset($_GET['product_selected'])){ $product_selected = $_GET['product_selected'];}else{ $product_selected="";}
	if($product_rank==0){  //// product
		$product ="id_product !=''";
		}else if($product_rank==1){ 
			$product ="(id_product BETWEEN '$product_from' AND '$product_to' )";
		}else if($product_rank ==2){
			$product ="id_product = '$product_selected'";
		}
	if(isset($_GET['warehouse_selected'])){ $warehouse_selected = $_GET['warehouse_selected'];}else{ $warehouse_selected="";}
	if($warehouse_rank==0){  //// customer
		$warehouse ="id_warehouse !='-1'";
		$id_warehouse = "";
		}else if($warehouse_rank ==1){
				$warehouse ="id_warehouse = '$warehouse_selected'";	
				$id_warehouse = $warehouse_selected;
		}
	if(isset($_GET['zone_selected'])){ $zone_selected = $_GET['zone_selected'];}else{ $zone_selected="";}
	if($zone_rank ==0){
		$zone ="tbl_zone.id_zone !='-1'";
	}else if($zone_rank ==1){
		$zone_selected = get_id_zone($zone_selected);
		$zone ="tbl_zone.id_zone = $zone_selected";
	}
		$report_date = "  ณ วันที่ ".thaiTextDate($date);
		if($warehouse_rank==0){ $report_warehouse = "รวมทุกคลัง";}else{ $report_warehouse = "   คลัง ".getWarehouseName($warehouse_selected);}
		$report_title = "รายงานสินค้าคงเหลือแยกตามโซน "."  $report_date";
		$title = array(1=>array($report_title));
		$line = array(1=>array("---------------------------------------------------------------------------------------------------------------------"));
		$body = array();
		$sub_header = array(1=>array("ลำดับ","โซน", "บาร์โค้ด", "รหัสสินค้า", "ชื่อสินค้า", "ทุน", "คงเหลือ", "มูลค่า"));
		$qr = dbQuery("SELECT tbl_stock.id_product_attribute, qty, tbl_zone.id_zone FROM tbl_stock LEFT JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE $product AND $warehouse AND $zone ORDER BY barcode ASC");
		$row = dbNumRows($qr); 
		if($row>0){
			$i = 0;
			$n = 1;
			$total_qty = 0;
			$total_cost = 0;
			while($i<$row){
				list($id_product_attribute, $qty, $id_zone) = dbFetchArray($qr);
				$product = new product();
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product);
				$product->product_attribute_detail($id_product_attribute);
				$zone_name = get_zone($id_zone);
				$cost = $product->product_cost;
				$cost_amount = $qty*$cost;
				$arr = array($n, $zone_name, $product->barcode, $product->reference, $product->product_name, $cost, number_format($qty), number_format($cost_amount,2));
				array_push($body, $arr);
				$total_qty += $qty;
				$total_cost += $cost_amount;
				$i++; $n++;
			}
		$arr = array("", "", "", "", "", "รวม", number_format($total_qty), number_format($total_cost,2));
		array_push($body, $arr);
		}else{
			$arr = array("-------------------------------------  ไม่มีรายการตามเงื่อนไขที่เลือก  -----------------------------------");
			array_push($body, $arr);
		}
		$sheet_name = "Stock_BY_Zone";
		$xls = new Excel_XML('UTF-8', false, $sheet_name); 
		$xls->addArray($title);
		$xls->addArray($line);
		$xls->addArray($sub_header);
		$xls->addArray ($body ); 
		$xls->generateXML( "Stock_zone_report" );
}

//************************************  รายงานสินค้าไม่เคลื่อนไหว  ************************************//
if(isset($_GET['stock_non_move'])&&isset($_GET['id_warehouse'])&&isset($_GET['from_date'])&&isset($_GET['to_date'])){
	$id_warehouse = $_GET['id_warehouse'];
	$from_date = dbDate($_GET['from_date'])." 00:00:00";
	$to_date = dbDate($_GET['to_date'])." 23:59:59";
	if($from_date == '1970-01-01 00:00:00'){ $from_date = date('Y-m-d 00:00:00'); }
	if($to_date == "1970-01-01 23:59:59"){ $to_date = date("Y-m-d 23:59:59"); }
	if($id_warehouse ==0){ $warehouse = "รวมทุกคลัง"; }else{ $warehouse_name = get_warehouse_name_by_id($id_warehouse); }
	$title = "รายงานสินค้าไม่เคลื่อนไหว &nbsp; ช่วงวันที่ &nbsp; ".thaiDate($from_date)."&nbsp;&nbsp; ถึง &nbsp;&nbsp;".thaiDate($to_date)."&nbsp;&nbsp;".$warehouse_name;
	$html ="<h4 align='center'>".$title."</h4><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
	<table class='table table-striped'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:10%;'>บาร์โค้ด</th><th style='width:15%;'>รหัสสินค้า</th><th style='widht:20%;'>ชื่อสินค้า</th><th style='width:10%; text-align: right;'>ราคาทุน</th>
		<th style='width:10%; text-align: right;'>คงเหลือ</th><th style='width:15%; text-align: right;'>มูลค่า</th><th style='width:15%; text-align:center;'>เคลื่อนไหวล่าสุด</th>
	</thead>";
	//เอารายการที่อยู่ในสต็อกออกมาตั้งต้นเพื่อวนลูปหาว่ารายการไหนมีอยู่ใน temp แล้ว status = เปิดบิลแล้ว ไม่เอาสต็อกที่อยู่ใน Buffer 
	if($id_warehouse == "0" ){ $warehouse = "id_warehouse !=''";  $wh = ""; }else{ $warehouse = "id_warehouse = $id_warehouse";  $wh = $id_warehouse; }
		$qr = dbQuery("SELECT id_product_attribute, SUM(qty) FROM tbl_stock LEFT JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE $warehouse AND tbl_stock.id_zone !=0  GROUP BY tbl_stock.id_product_attribute");
		//echo "SELECT id_product_attribute, SUM(qty) FROM tbl_stock LEFT JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE $warehouse AND tbl_stock.id_zone !=0  GROUP BY tbl_stock.id_product_attribute";
		$row = dbNumRows($qr); 
	if($row>0){
		$i = 0;
		$n = 1;
		while($i<$row){
			list($id_product_attribute, $qty) = dbFetchArray($qr);
			$stock_moveing = stock_moveing($id_product_attribute);
			$sql = dbQuery("SELECT id_product_attribute FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute AND $warehouse AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') AND id_reason = 3");
			//echo "SELECT id_product_attribute FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute AND $warehouse AND (date_upd BETWEEN '".$from_date."' AND '".$to_date."') AND id_reason = 3";
			$r = dbNumRows($sql);  // หาก $r มากกว่า 0 แสดงว่า มีการเคลื่อนไหว
			if($r<1){   //หากน้อยกว่า 1 แสดงว่าไม่มีการเคลื่อนไหว ต้องดึงข้อมูลการเคลื่อนไหวล่าสุดมาแสดง
			list($move) = dbFetchArray(dbQuery("SELECT MAX(date_upd) FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute AND $warehouse AND id_reason = 3 AND date_upd < '".$to_date."'"));
			if($move !=""){ $last_move = thaiDate($move); }else{ $last_move = "ไม่เคยเคลื่อนไหว"; }
				$product = new product();
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product);
				$product->product_attribute_detail($id_product_attribute);
				$total_amount = $qty*$product->product_cost;
				$html .="<tr style='font-size: 12px;'><td align='center'>$n</td><td>".$product->barcode."</td><td>".$product->reference."</td><td>".$product->product_name."</td><td align='right'>".$product->product_cost."</td>
							<td align='right'>".$qty."</td><td align='right' >".number_format($total_amount,2)."</td><td align='center'>".$last_move."</td></tr>";
							
				$n++;
			}	
			$i++;
		}
	}else{
		$html .= "<tr><td colspan='7'><h4 align='center'>-------------------  ไม่มีรายการตามช่วงเวลาที่เลือก  ------------------------</h4></td></tr>";
	}
		$html .="</table>"; 
		echo $html;
}

//************************************  รายงานสินค้าไม่เคลื่อนไหว  Export to excel ************************************//
if(isset($_GET['export_stock_non_move'])&&isset($_GET['id_warehouse'])&&isset($_GET['from_date'])&&isset($_GET['to_date'])){
	$id_warehouse = $_GET['id_warehouse'];
	$from_date = dbDate($_GET['from_date'])." 00:00:00";
	$to_date = dbDate($_GET['to_date'])." 23:59:59";
	if($from_date == '1970-01-01 00:00:00'){ $from_date = date('Y-m-d 00:00:00'); }
	if($to_date == "1970-01-01 23:59:59"){ $to_date = date("Y-m-d 23:59:59"); }
	if($id_warehouse ==0){ $warehouse = "รวมทุกคลัง"; }else{ $warehouse_name = get_warehouse_name_by_id($id_warehouse); }
	$report_title  = "รายงานสินค้าไม่เคลื่อนไหว "." ช่วงวันที่ "."  ".thaiDate($from_date)." "." ถึง "." ".thaiDate($to_date)." ".$warehouse_name;
	$title= array(1=>array($report_title));
	$sub_header = array(1=>array("ลำดับ", "บาร์โค้ด", "รหัสสินค้า", "ชื่อสินค้า", "ราคาทุน", "คงเหลือ", "มูลค่า", "เคลื่อนไหวล่าสุด"));
	$line = array(1=>array("--------------------------------------------------------------------------------------------------------------------------"));
	$body = array();
	//เอารายการที่อยู่ในสต็อกออกมาตั้งต้นเพื่อวนลูปหาว่ารายการไหนมีอยู่ใน temp แล้ว status = เปิดบิลแล้ว ไม่เอาสต็อกที่อยู่ใน Buffer 
	if($id_warehouse == "0" ){ $warehouse = "id_warehouse !=''";  $wh = ""; }else{ $warehouse = "tbl_zone.id_warehouse = $id_warehouse";  $wh = $id_warehouse; }
		$qr = dbQuery("SELECT id_product_attribute FROM tbl_stock LEFT JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE $warehouse AND tbl_stock.id_zone !=0  GROUP BY tbl_stock.id_product_attribute");
		$row = dbNumRows($qr); 
	if($row>0){
		$i = 0;
		$n = 1;
		while($i<$row){
			list($id_product_attribute) = dbFetchArray($qr);
			$stock_moveing = stock_moveing($id_product_attribute);
			//นำ รายการที่ได้จากข้างบนมา ดึงรายการใน temp และ order_detail เพื่อดูว่ามีใน temp มั้ย แล้วเปิดบิลหรือยัง โดยใน order_detail
			$sql = dbQuery("SELECT tbl_temp.id_product_attribute FROM tbl_temp LEFT JOIN tbl_order_detail ON tbl_temp.id_product_attribute = tbl_order_detail.id_product_attribute AND tbl_temp.id_order = tbl_order_detail.id_order WHERE tbl_temp.id_product_attribute = $id_product_attribute AND ( tbl_order_detail.date_upd BETWEEN '".$from_date."' AND '".$to_date."' ) AND tbl_temp.status = 4");
			$r = dbNumRows($sql);  // หาก $r มากกว่า 0 แสดงว่า มีการเคลื่อนไหว
			if($r<1){   //หากน้อยกว่า 1 แสดงว่าไม่มีการเคลื่อนไหว ต้องดึงข้อมูลการเคลื่อนไหวล่าสุดมาแสดง
			list($move) = dbFetchArray(dbQuery("SELECT MAX(date_upd) FROM tbl_temp LEFT JOIN tbl_order_detail ON tbl_temp.id_product_attribute = tbl_order_detail.id_product_attribute AND tbl_temp.id_order = tbl_order_detail.id_order WHERE tbl_temp.id_product_attribute = $id_product_attribute AND tbl_temp.status = 4 AND date_upd < '".$to_date."'"));
			if($move !=""){ $last_move = thaiDate($move); }else{ $last_move = "ไม่เคยเคลื่อนไหว"; }
				$product = new product();
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product);
				$product->product_attribute_detail($id_product_attribute);
				$available_qty = $product->available_qty($product->id_product_attribute, $wh ) + $stock_moveing;
				$total_amount = $available_qty*$product->product_cost;
				$arr = array($n, $product->barcode, $product->reference, $product->product_name, $product->product_cost, $available_qty, number_format($total_amount,2), $last_move);
				array_push($body, $arr);
				$n++;
			}	
			$i++;
		}
	}else{
		$arr = array("------------------------------------  ไม่มีรายการตามช่วงเวลาที่เลือก  -----------------------------------------");
		array_push($body, $arr);
	}
		$sheet_name = "Stock_non_move";
		$xls = new Excel_XML('UTF-8', false, $sheet_name); 
		$xls->addArray($title);
		$xls->addArray($line);
		$xls->addArray($sub_header);
		$xls->addArray ($body ); 
		$xls->generateXML("Stock_non_move");
}

//***********************************************  รายงานสินค้าคงเหลือปัจุบัน  *****************************************************//
if(isset($_GET['get_stock'])&&isset($_GET['option'])){
	$option = $_GET['option'];
	$html = "
	<div class='row'>
	<div class='col-lg-12'>
	<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>
	<li class='active'><a href='#all' role='tab' data-toggle='tab'>ทั้งหมด</a></li>";
	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				if($rs<1){
					$html .="<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
				}else{				
				$html .="<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				$html .="<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				$html .="<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				$html .=" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$n++;
				}
				$html .="</ul></li>";
				}	
				$html .= "</li>";
				$i++;
				}
	$html .= "</ul></div></div><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' />";
	
	$html .="<div class='row'><div class='col-lg-12'><div class='tab-content'><div class='tab-pane active' id='all'>";

	$qs = dbQuery("SELECT id_product FROM tbl_product");
	$total_qty = "0";
	$total_cost = "0.00";
	$product = new product();
	while($r = dbFetchArray($qs)){
		$id_product = $r['id_product'];
		$data = $product->get_current_stock($id_product);  //ได้ค่ากลังมาเป็น array("total_qty"=>value, "total_cost"=>value)
		$total_qty += $data['total_qty'];
		$total_cost += $data['total_cost'];
	}
//**************************************  แถบ ทั้งหมด ไม่แยกตามหมวดหมู่ *************************************//	
	$html .= "<h4 style='margin-top:0px; margin-bottom:15px;'>ทั้งหมด &nbsp;<span style='color:red;'>".number_format($total_qty)." </span>&nbsp;หน่วย &nbsp;&nbsp;  มูลค่า &nbsp; 
	<span style='color:blue;'>".number_format($total_cost,2)."</span> &nbsp;บาท  </h4>";
	$html .="<div class='row'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></div>";
	$sql = dbQuery("SELECT id_product, product_code FROM  tbl_product ORDER BY product_code ASC");
	$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		while($i<$row){
			list($id_product, $product_code) = dbFetchArray($sql);
			$product = new product();
			$data = $product->get_current_stock($id_product); //ได้ค่ากลังมาเป็น array("total_qty"=>value, "total_cost"=>value)
			$total_qty = $data['total_qty'];
			$total_cost = $data['total_cost'];
			//echo "$id_product => $total_qty : ";
			if($total_qty>0 &&$option=="in_stock"){ 
				$html .="<div class='item2 col-lg-2 col-md-2 col-sm-3 col-xs-4' style='text-align:center; margin-bottom:15px;'><div class='product' style='padding:5px;'>
			<div class='image' style='text-align:center; height:125px; width:125px;'><a href='#' onclick='getData(".$id_product.")'>".$product->getCoverImage($id_product,2,"")."</a></div>
			<div class='description' style='min-height:50px; font-size:16px;' align='center'>
				<a href='#' onclick='getData(".$id_product.")'>".$product_code."</a>	
			</div>
			<div class='price' style='text-align:center;'><span style='color:red;'>".number_format($total_qty)." </span>:  ".number_format(($total_cost),2)." </div>
			</div></div>";
			}else if($total_qty<1 && $option =="non_stock"){

				// หากเรียกดูเฉพาะสินค้าที่ไม่มีสต็อก แสดงเฉพาะที่ไม่มียอด
				//echo "$id_product => $total_qty : ";
				$html .="<div class='item2 col-lg-2 col-md-2 col-sm-3 col-xs-4' style='text-align:center; margin-bottom:15px;'><div class='product' style='padding:5px;'>
			<div class='image' style='text-align:center; height:125px; width:125px;'><a href='#' onclick='getData(".$id_product.")'>".$product->getCoverImage($id_product,2,"")."</a></div>
			<div class='description' style='min-height:50px; font-size:16px;' align='center'>
				<a href='#' onclick='getData(".$id_product.")'>".$product_code."</a>	
			</div>
			<div class='price' style='text-align:center;'><span style='color:red;'>".number_format($total_qty)." </span>:  ".number_format(($total_cost),2)." </div>
			</div></div>";
			}else if($option=="show_all"){
				// ถ้าเรียกดูทั้งหมด แสดงทุกรายการสินค้า
				//echo "$id_product => $total_qty : ";
			$html .="<div class='item2 col-lg-2 col-md-2 col-sm-3 col-xs-4' style='text-align:center; margin-bottom:15px;'><div class='product' style='padding:5px;'>
			<div class='image' style='text-align:center; height:125px; width:125px;'><a href='#' onclick='getData(".$id_product.")'>".$product->getCoverImage($id_product,2,"")."</a></div>
			<div class='description' style='min-height:50px; font-size:16px;' align='center'>
				<a href='#' onclick='getData(".$id_product.")'>".$product_code."</a>	
			</div>
			<div class='price' style='text-align:center;'><span style='color:red;'>".number_format($total_qty)." </span>:  ".number_format(($total_cost),2)." </div>
			</div></div>";
			}
			$i++;
		}
	}else{ 
		$html .="<h4 style='align:center;'>ยังไม่มีรายการสินค้า</h4>";
	}	
	$html .="</div>";
 //**************************************************** จบ แถบ ทั้งหมด  ***********************************//
 
 //************************************  เริ่ม แถบอื่นๆ แยกตามหมวดหมู่  ********************************//
	$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	while($c = dbFetchArray($query)){
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		$html .="<div class='tab-pane' id='cat-$id_category'>";
	$qs = dbQuery("SELECT tbl_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_product.id_product = tbl_category_product.id_product  WHERE id_category = $id_category");
	$total_qty = 0;
	$total_cost = 0;
	while($r = dbFetchArray($qs)){
		$product = new product();
		$data = $product->get_current_stock($r['id_product']);
		$total_cost += $data['total_cost'];
		$total_qty += $data['total_qty'];
	}
	
	$html .="<h4 style='margin-top:0px; margin-bottom:15px;'>$cate_name &nbsp;&nbsp;<span style='color:red;'>".number_format($total_qty)." </span>&nbsp;&nbsp; หน่วย   มูลค่า &nbsp;&nbsp;<span style='color:blue;'>".number_format($total_cost,2)." </span>&nbsp;&nbsp; บาท </h4>";
	$html .="<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />";
		$sql = dbQuery("SELECT tbl_category_product.id_product,product_code FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0){
			$i=0;
			while($i<$row){
				list($id_product,$product_code) = dbFetchArray($sql);
				$product = new product();
				$data = $product->get_current_stock($id_product);
				$qty = $data['total_qty'];
				$cost = $data['total_cost'];
				if($qty>0 &&$option=="in_stock"){ 
				// หากเรียกแบบเฉพาะที่มีสต็อก ไม่ต้องแสดงอะไรถ้าไม่มีสต็อก
				$html .="<div class='item2 col-lg-2 col-md-2 col-sm-3 col-xs-4' style='text-align:center; margin-bottom:15px;'><div class='product' style='padding:5px;'>
			<div class='image' style='text-align:center; height:125px; width:125px;'><a href='#' onclick='getData(".$id_product.")'>".$product->getCoverImage($id_product,2,"")."</a></div>
			<div class='description' style='min-height:50px; font-size:16px;' align='center'>
				<a href='#' onclick='getData(".$id_product.")'>".$product_code."</a>	
			</div>
			<div class='price' style='text-align:center;'><span style='color:red;'>".number_format($qty)." </span>:  ".number_format(($cost),2)." </div>
			</div></div>";
			}else if($qty<1 && $option =="non_stock"){
				// หากเรียกดูเฉพาะสินค้าที่ไม่มีสต็อก แสดงเฉพาะที่ไม่มียอด
			$html .="<div class='item2 col-lg-2 col-md-2 col-sm-3 col-xs-4' style='text-align:center; margin-bottom:15px;'><div class='product' style='padding:5px;'>
			<div class='image' style='text-align:center; height:125px; width:125px;'><a href='#' onclick='getData(".$id_product.")'>".$product->getCoverImage($id_product,2,"")."</a></div>
			<div class='description' style='min-height:50px; font-size:16px;' align='center'>
				<a href='#' onclick='getData(".$id_product.")'>".$product_code."</a>	
			</div>
			<div class='price' style='text-align:center;'><span style='color:red;'>".number_format($qty)." </span>:  ".number_format(($cost),2)." </div>
			</div></div>";
			}else if($option=="show_all"){
			$html .="<div class='item2 col-lg-2 col-md-2 col-sm-3 col-xs-4' style='text-align:center; margin-bottom:15px;'><div class='product' style='padding:5px;'>
			<div class='image' style='text-align:center; height:125px; width:125px;'><a href='#' onclick='getData(".$id_product.")'>".$product->getCoverImage($id_product,2,"")."</a></div>
			<div class='description' style='min-height:50px; font-size:16px;' align='center'>
				<a href='#' onclick='getData(".$id_product.")'>".$product_code."</a>	
			</div>
			<div class='price' style='text-align:center;'><span style='color:red;'>".number_format($qty)." </span>:  ".number_format(($cost),2)." </div>
			</div></div>";
			}
				$i++;
			}	
		
		}else{ 
			$html .="<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		}
	$html .="</div>";
	}
	$html .="		
			<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'>title</h4>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
									  </div>
									</div>
								  </div>
								</div>
<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>";
$html .= "</div></div></div>";
echo $html;
}
//-----------------------------รายงานยอดขายแยกตามพื้นที่ขาย------------------------------//
if(isset($_GET['SaleReportZone'])){
	$from_date = $_GET['from_date'];
	$to_date = $_GET['to_date'];
	if($from_date !=="เลือกวัน" || $to_date !=="เลือกวัน"){
		$from = dbDate($from_date);
		$to = dbDate($to_date); 
	}else{
		$rang = getMonth();
		$to = $rang['to'];
		$from = $rang['from'];
	}
	$html = " 
	<h4>รายงานยอดขายแยกตามพื้นที่การขาย วันที่ &nbsp;".thaiTextDate($from)." &nbsp; ถึง &nbsp; ".thaiTextDate($to)."</h4>
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
	<table class='table table-bordered table-striped'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:75%; text-align:center;'>พื้นที่การขาย</th><th style='widht:20%; text-align:center;'>ยอดขาย</th>
	</thead>";
	//ถ้าไม่ได้เลือกวันที่ จะกำหนดช่วงให้เป็นเดือนปัจจุบัน
		$sale = new sale();
		$result= $sale->groupLeaderBoard($from, $to);
		$n = 1;
		$total_amount = 0;
		foreach($result as $data){
			$zone_name = $data['zone_name'];
			$amount = $data['sale_amount'];
			$html .="<tr><td align='center'>$n</td><td style='padding:10px;'>$zone_name</td><td style='text-align:right; padding:10px;'>".number_format($amount,2)."</td></tr>";
			$total_amount = $total_amount+$amount;
			$n++;
		}
		$html .="<tr><td colspan='2' align='right' style='padding:10px;'>รวมทั้งหมด</td><td style='text-align:right; padding:10px;'>".number_format($total_amount,2)."</td></tr>";
		$html .="</table>";
		echo $html;
}
//-----------------------------รายงานยอดขายแยกตามพนักงานขาย-----------------------------------------//
if(isset($_GET['SaleReportEmployee'])){
	$from_date = $_GET['from_date'];
	$to_date = $_GET['to_date'];
if($from_date !=="เลือกวัน" || $to_date !=="เลือกวัน"){
		$from = dbDate($from_date);
		$to = dbDate($to_date); 
	}else{
		$rang = getMonth();
		$to = $rang['to'];
		$from = $rang['from'];
	}
	$html =" 
	<h4>รายงานยอดขายแยกตามพนักงานขาย วันที่ &nbsp;".thaiTextDate($from)." &nbsp; ถึง &nbsp; ".thaiTextDate($to)."</h4>
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
	<table class='table table-bordered table-striped'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:45%; text-align:center;'>พนักงานขาย</th><th style='width:30%; text-align:center;'>พื้นที่การขาย</th><th style='widht:20%; text-align:center;'>ยอดขาย</th>
	</thead>";
	//ถ้าไม่ได้เลือกวันที่ จะกำหนดช่วงให้เป็นเดือนปัจจุบัน
		$sale = new sale();
		$qr = $sale->saleLeaderBoard($from, $to); /// ได้ค่ากลับมาเป็น Array ( [full_name]=>ชื่อเต็มพนักงาน , [zone_name]=>ชื่อพี้นที่การขาย , [sale_amount]=> ยอดขาย )
		$n = 1;
		$total_amount = 0;
		foreach($qr as $data){
			$salex = new sale($data['id']);
			$sale_name = $salex->full_name;
			$zone_name = $salex->group_name;
			$amount = $data['sale_amount'];
			$html .="<tr><td align='center'>$n</td><td style='padding:10px;'>$sale_name</td><td style='padding:10px;'>$zone_name</td><td style='text-align:right; padding:10px;'>".number_format($amount,2)."</td></tr>";
			$total_amount = $total_amount+$amount;
			$n++;
		}
		$html .="<tr><td colspan='3' align='right' style='padding:10px;'>รวมทั้งหมด</td><td style='text-align:right; padding:10px;'>".number_format($total_amount,2)."</td></tr>";
		$html .="</table>";
		echo $html;
}
//-----------------------------------------------รายงานยอดขายเเยกตามรายการสินค้า---------------------------//
if(isset($_GET['SaleReportProduct'])){
	$from_date = $_GET['from_date'];
	$to_date = $_GET['to_date'];
if($from_date !=="เลือกวัน" || $to_date !=="เลือกวัน"){
		$from = dbDate($from_date);
		$to = dbDate($to_date); 
	}else{
		$rang = getMonth();
		$to = $rang['to'];
		$from = $rang['from'];
	}
	$html =" 
	<h4>รายงานยอดขายแยกตามสินค้า วันที่ &nbsp;".thaiTextDate($from)." &nbsp; ถึง &nbsp; ".thaiTextDate($to)."</h4>
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
	<table class='table table-bordered table-striped'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:45%; text-align:center;'>ชื่อสินค้า</th><th style='width:15%; text-align:center;'>จำนวน</th><th style='widht:35%; text-align:center;'>ยอดขาย</th>
	</thead>";
	//ถ้าไม่ได้เลือกวันที่ จะกำหนดช่วงให้เป็นเดือนปัจจุบัน
		$qr = dbQuery("SELECT id_product FROM tbl_product");
		$n = 1;
		$grand_amount = 0;
		$sumqty = 0;
		while($data=dbFetchArray($qr)){
			$id_product = $data['id_product'];
			$product = new product($id_product);
			$product->product_detail($id_product);
			$sold = dbNumRows(dbQuery("SELECT id_product FROM tbl_order_detail_sold WHERE id_product = $id_product AND id_role IN (1,5) AND (date_upd BETWEEN '$from 00:00:00.000000' AND '$to 23:59:59.000000')"));
			if($sold>0){
			$sqr = dbQuery("SELECT SUM(sold_qty),SUM(total_amount) FROM tbl_order_detail_sold WHERE id_product = $id_product AND id_role IN (1,5) AND (date_upd BETWEEN '$from 00:00:00.000000' AND '$to 23:59:59.000000')");
			list($qty,$amount) = dbFetchArray($sqr);
			$total_amount = $amount;
			$html .="<tr><td align='center'>$n</td><td style='padding:10px;'>".$product->product_code." : ".$product->product_name."</td><td style='text-align:right; padding:10px;'>".number_format($qty)."</td><td style='text-align:right; padding:10px;'>".number_format($total_amount,2)."</td></tr>";
			$grand_amount = $grand_amount+$total_amount;
			$sumqty = $sumqty + $qty;
			$n++;
			}
		}
		$html .="<tr><td colspan='2' align='right' style='padding:10px;'>รวมทั้งหมด</td><td style='text-align:right; padding:10px;'>".number_format($sumqty)."</td><td style='text-align:right; padding:10px;'>".number_format($grand_amount,2)."</td></tr>";
		$html .="</table>";
		echo $html;
}
//*************************************ตารางรายงานยอดขาย**************************************//
if(isset($_GET['sale_table'])){
	$view = $_GET['view'];
	if($view == 1){
		$view_selected = $_GET["view_selected"];
			if($view_selected =="month_1"){
				$month_1 = date("Y-m" ,strtotime("-1 month")); 
				$month = date("m" ,strtotime($month_1)); 
				$year = date("Y" ,strtotime($month_1)); 
				$date = date_in_month($month, $year);
			}else if($view_selected == "7"){
				$date = date_back(7);
			}else if($view_selected == "15"){
				$date = date_back(15);
			}else{
				$month = date("m"); 
				$year= date("Y");
				$date = date_in_month($month, $year);
			}
	}else if($view == 2){
		$from = $_GET["from_date"];
		$to = $_GET["to_date"];
		 $from = dbDate($from);
		 $to = dbDate($to);
		$date = date_from_to($from,$to);
	}
				$bg_color_total_amount = "#FFFFCC";
				$f_color_total_amount = "#0033FF";
				$bg_color_total_cost = "#FAF0E6";
				$f_color_total_cost = "#007700";
				$bg_color_profit = "#F0FFF0";
				$f_color_profit = "#CC0000";
				$sql_group = "SELECT id_group,group_name FROM tbl_group ORDER BY id_group ASC";
				$query_group = dbQuery($sql_group);
				$row_group = dbNumRows($query_group);
				$total_consumption = get_amount_consumption();
				$total = 0;
				$consumption = 0;
				$cost = 0;
				echo "<div class='row'><div class='col-xs-5 '><table width='40%' class='table table-bordered table-striped' ><tr><td width='30%'>ต้นทุนคงที่/วัน</td><td>".number_format($total_consumption)."
				<span style='float: right'><a href='#' data-toggle='modal' data-target='.bs-example-modal-lg'>
				  รายระเอียด
				</a></span>
				</td></tr></table></div>
				<div class='col-xs-7 '>
				<table width='40%' class='table table-bordered table-striped' ><tr>
				<td width='20%' style='background-color:$bg_color_total_amount; color:$f_color_total_amount;' align='center'>ยอดขาย</td>
				<td width='20%' style='background-color:$bg_color_total_cost; color:$f_color_total_cost; ' align='center'>ทุนสินค้า</td>
				<td width='20%' style='background-color:$bg_color_profit; color:$f_color_profit;' align='center'>กำไร</td>
				</tr></table>
				</div></div>";
				echo "<table class='table-bordered table-striped' width='100%' ><thead style='font-size:12px;'>
				<th style='width:90px; text-align:center;' height='50px'>วันที่</th>";
					$n = 0;
					$arr_id_group = "";
					while($n<$row_group){
						$group = dbFetchArray($query_group);
						$arr_id_group[] = $group["id_group"];
						echo "<th style='width:70px; text-align:center;'>".$group["group_name"]."</th>";
						$n++;
					}
				echo "<th style='width:70px; text-align:center;'>รวม</th>
				<th style='width:70px; text-align:center;'>กำไรสุทธิ</th>
				</thead>";
				foreach( $date as $d){
					if(date("Y-m-d") < $d){
						$total_consumption = 0;
					}
					$d_start = $d." 00:00:00.000000";
					$d_end = $d." 23:59:59.000000";
					$day = date('d', strtotime($d));
					echo "<tr><td stye='vertical-align: bottom;' align='center'>".thai_date($d)."</td>";
					$sumtotal_amount = 0;
					$sumtotal_cost = 0;
					foreach( $arr_id_group as $id_group){
						$sqr = dbQuery("SELECT SUM(total_amount),SUM(total_cost) FROM tbl_order_detail_sold LEFT JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer WHERE id_default_group = $id_group AND id_role IN(1,5) AND (tbl_order_detail_sold.date_upd LIKE '%$d%')");
						list($total_amount,$total_cost) = dbFetchArray($sqr);
						$profit = $total_amount - $total_cost;
						echo "<td align='right'><div style='background-color:$bg_color_total_amount; color:$f_color_total_amount;'>".number_format($total_amount)."&nbsp;</div><div style='background-color:$bg_color_total_cost; color:$f_color_total_cost; '>".number_format($total_cost)."&nbsp;</div><div style='background-color:$bg_color_profit; color:$f_color_profit;'>".number_format($profit)."&nbsp;</div></td>";
						$sumtotal_amount = $sumtotal_amount + $total_amount;
						$sumtotal_cost = $sumtotal_cost + $total_cost;
					}
					
					$sumprofit = $sumtotal_amount - $sumtotal_cost;
					$total_profit = $sumprofit - $total_consumption;
					$total = $total + $sumtotal_amount;
					$cost = $cost + $sumtotal_cost;
					$consumption = $consumption + $total_consumption;
					$profit_balance = $total - $cost - $consumption;
					echo "<td align='right'><div style='background-color:$bg_color_total_amount; color:$f_color_total_amount;' >".number_format($sumtotal_amount)."&nbsp;</div><div style='background-color:$bg_color_total_cost; color:$f_color_total_cost; '>".number_format($sumtotal_cost)."&nbsp;</div><div style='background-color:$bg_color_profit; color:$f_color_profit;'>".number_format($sumprofit)."&nbsp;</div></td><td align='right' ";if($total_profit < 0 ){echo " style='color:#FF0000'";}echo ">".number_format($total_profit)."&nbsp;</td></tr>";
					if(date("Y-m-d") <= $d){
						break;
					}
				}
				echo "
				<tr style='font-size:16px;' height='20px'><td colspan='".($row_group+3)."'></td></tr>
				<tr  height='30px' style='font-size:16px;'><td rowspan='4' colspan='".($row_group-1)."'></td><td colspan='2' style='background-color:$bg_color_total_amount; color:$f_color_total_amount;' >&nbsp;&nbsp;ยอดขายรวม</td><td colspan='2' align='right' style='background-color:$bg_color_total_amount; color:$f_color_total_amount;' >".number_format($total)."&nbsp;</td></tr>
				<tr height='30px' style='font-size:16px; background-color:$bg_color_total_cost; color:$f_color_total_cost;'><td colspan='2'>&nbsp;&nbsp;ต้นทุนสินค้ารวม</td><td colspan='2' align='right'>".number_format($cost)."&nbsp;</td></tr>
				<tr height='30px' style='font-size:16px;'><td colspan='2'>&nbsp;&nbsp;ต้นทุนคงที่รวม</td><td colspan='2' align='right'>".number_format($consumption)."&nbsp;</td></tr>
				<tr height='30px' style='font-size:16px; background-color:$bg_color_profit; color:$f_color_profit;'><td colspan='2'>&nbsp;&nbsp;กำไรสุทธิ</td><td colspan='2' align='right'>".number_format($profit_balance)."&nbsp;</td></tr></table><br>";
}
if(isset($_GET['chart_table'])){
	$view = $_GET['view'];
	if($view == 1){
		$view_selected = $_GET["view_selected"];
			if($view_selected =="month_1"){
				$month_1 = date("Y-m" ,strtotime("-1 month")); 
				$month = date("m" ,strtotime($month_1)); 
				$year = date("Y" ,strtotime($month_1)); 
				$date = date_in_month($month, $year);
			}else if($view_selected == "7"){
				$date = date_back(7);
			}else if($view_selected == "15"){
				$date = date_back(15);
			}else{
				$month = date("m"); 
				$year= date("Y");
				$date = date_in_month($month, $year);
			}
	}else if($view == 2){
		$from = $_GET["from_date"];
		$to = $_GET["to_date"];
		 $from = dbDate($from);
		 $to = dbDate($to);
		$date = date_from_to($from,$to);
	}
				$bg_color_total_amount = "#FFFFCC";
				$f_color_total_amount = "#0033FF";
				$bg_color_total_cost = "#FAF0E6";
				$f_color_total_cost = "#007700";
				$bg_color_profit = "#F0FFF0";
				$f_color_profit = "#CC0000";
				$total_consumption = get_amount_consumption();
				$total = 0;
				$consumption = 0;
				$cost = 0;
				$total_profit = 0;
				$chart = "";
				$table = "";
				echo "<div class='row'><div class='col-xs-5 '><table width='40%' class='table table-bordered table-striped' ><tr><td width='30%'>ต้นทุนคงที่/วัน</td><td>".number_format($total_consumption)."
				<span style='float: right'><a href='#' data-toggle='modal' data-target='.bs-example-modal-lg'>
				  รายระเอียด
				</a></span>
				</td></tr></table></div>
				<div class='col-xs-7 '>
				</div></div>";
				$table .= "<table class='table table-bordered table-striped' width='100%' ><thead>
				<th style='width:20%; text-align:center;' height='50px'>วันที่</th>
				<th style='width:20%; text-align:center;'>ยอดขาย</th>
				<th style='width:20%; text-align:center;'>ต้นทุนสินค้า</th>
				<th style='width:20%; text-align:center;'>ค่าใช้จ่ายคงที่</th>
				<th style='width:20%; text-align:center;'>กำไรสุทธิ</th>
				</thead>";
				foreach( $date as $d){
					if(date("Y-m-d") < $d){
						$total_consumption = 0;
					}
					$d_start = $d." 00:00:00.000000";
					$d_end = $d." 23:59:59.000000";
					$day = date('d', strtotime($d));
					$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount,SUM(total_cost) FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND (tbl_order_detail_sold.date_upd LIKE '%$d%')");
						list($total_amount,$total_cost) = dbFetchArray($sqr);
						$profit = $total_amount - $total_cost - $total_consumption;
						
					$table .= "<tr><td stye='vertical-align: bottom;' align='center'>".thai_date($d)."</td>
					<td align='right'>".number_format($total_amount)."</td>
					<td align='right'>".number_format($total_cost)."</td>
					<td align='right'>".number_format($total_consumption)."</td>
					<td align='right'";if($profit < 0 ){$table .= " style='color:#FF0000'";}$table .= ">".number_format($profit)."</td>";
					if($total_amount == ""){
						$total_amount = 0;
					}
					$sumcost = $total_cost + $total_consumption;
					$total = $total + $total_amount;
					$cost = $cost + $total_cost;
					$consumption = $consumption + $total_consumption;
					$sum_consumption = $cost + $consumption;
					$total_profit = $total_profit + $profit;
					
					$chart .= "{ d: '".date("d-m", strtotime(showDate($d)))."', total_amount: '$total_amount', concumption: '$sumcost', profit: '$profit'},";
					if(date("Y-m-d") <= $d){
						break;
					}
				}
			
				$table .= "
				<tr style='font-size:16px;' height='20px'><td colspan='5'></td></tr>
				<tr  height='30px' style='font-size:16px;'><td rowspan='4' colspan='3'></td><td  style='background-color:$bg_color_total_amount; color:$f_color_total_amount;' >&nbsp;&nbsp;ยอดขายรวม</td><td  align='right' style='background-color:$bg_color_total_amount; color:$f_color_total_amount;' >".number_format($total)."&nbsp;</td></tr>
				<tr height='30px' style='font-size:16px; background-color:$bg_color_total_cost; color:$f_color_total_cost;'><td>&nbsp;&nbsp;ต้นทุนสินค้ารวม</td><td align='right'>".number_format($cost)."&nbsp;</td></tr>
				<tr height='30px' style='font-size:16px;'><td>&nbsp;&nbsp;ต้นทุนคงที่รวม</td><td align='right'>".number_format($consumption)."&nbsp;</td></tr>
				<tr height='30px' style='font-size:16px; background-color:$bg_color_profit; color:$f_color_profit;'><td>&nbsp;&nbsp;กำไรสุทธิ</td><td align='right'>".number_format($total_profit)."&nbsp;</td></tr></table><br>	";
	echo "<div class='row'>
          <div class='col-lg-12'>
            <div class='panel panel-primary' id='qty' style='position:relative;'>
              <div class='panel-heading'><h3 class='panel-title'><i class='fa fa-line-chart'></i>กราฟรายงานวิเคราะห์การขาย</h3></div>
              <div class='panel-body'> <div id='morris-chart-line'></div></div> 
              <div class='panel-footer'><h3 class='panel-title' id='footer'>ยอดขายรวม : ".number_format($total)." ต้นทุนรวม : ".number_format($cost)." กำไรสุทธิ : ".number_format($total_profit)."</h3></div>
            </div>
        </div>
 </div>";
 echo $table;
?>
<script>
var line = new Morris.Line({
  element: 'morris-chart-line',
  data: [	
	<?php echo $chart;?>
  ],
  xkey: 'd' ,
  ykeys:['total_amount','concumption','profit'],
  labels: ['ยอดขาย(บาท)','ทุน(บาท)','กำไร(บาท)'],
  smooth: false, 
  parseTime: false,
  yLabelFormat: function(y){ return y = Math.round(y); },
  xLabelMargin:5
  
});
</script>
                <?php
				}
?>

<?php
/***********************************************  Consign by zone  ********************************/

if(isset($_GET['consign_by_zone']) && isset($_GET['from_date']) && isset($_GET['to_date']) )
{
	$from 		= date("Y-m-d 00:00:00",strtotime($_GET['from_date']));
	$to 			= date("Y-m-d 23:59:59", strtotime($_GET['to_date']));
	$zn			= $_GET['zone'];
	if(isset($_GET['zone_selected'])){ $zone_selected = trim($_GET['zone_selected']);}else{ $zone_selected="";}
	switch($zn)
	{
		case "0" :
		$zone_query = "id_zone >0";
		break;
		case "1" :
		$zone_query = "id_zone = ".$zone_selected;
		break;
		default :
		$zone_query = "id_zone >0";
		break;
	}
	$html = "<h4 style='text-align:center'>รายงานเอกสารตัดยอดฝากขาย แยกตามโซน เรียงตามเอกสาร วันที่ ".thaiDate($from)." ถึง ".thaiDate($to)." </h4><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<table class='table table-striped'>	
<thead>
<th style='width:10%; text-align:center'>ลำดับ</th><th style='width:15%; text-align:center;'>วันที่</th>
<th style='width:15%;'>เลขที่เอกสาร</th><th>โซน</th><th style='width:10%; text-align:right;'>จำนวน</th><th style='width:15%; text-align:right;'>มูลค่า</th>
</thead>";
	$sql = dbQuery("SELECT id_order_consign, reference, id_zone, date_add FROM tbl_order_consign WHERE ".$zone_query." AND (date_add BETWEEN '$from' AND '$to' ) ORDER BY date_add DESC");
	$row = dbNumRows($sql);
	if($row>0){
		$i = 0;
		$o = 1;
		$sum_qty = 0; $sum_amount = 0;
		while($i<$row){
			$rs = dbFetchArray($sql);
			$id_order_consign = $rs['id_order_consign'];
			$reference = $rs['reference'];
			$date_add = $rs['date_add'];
			$zone = get_zone($rs['id_zone']);
			$qr = dbQuery("SELECT * FROM tbl_order_consign_detail WHERE id_order_consign = $id_order_consign");
			$ro = dbNumRows($qr);
			if($ro > 0){
				$total_qty = 0; $total_amount = 0; $n = 0;
				while($n < $ro){
				$ra = dbFetchArray($qr);
				$amount = $ra['product_price']*$ra['qty'];
				$total_qty += $ra['qty'];
				$total_amount += $amount;
				$n++;
				}
			}
			$html .= "<tr><td align='center'>$o</td><td align='center'>".thaiDate($date_add)."</td><td>$reference</td><td>$zone</td>
			<td align='right'>".number_format($total_qty)."</td><td align='right'>".number_format($total_amount,2)."</td></tr>";
			$sum_qty += $total_qty; $sum_amount += $total_amount;
			$i++; $o++;
		}
		$html .="<tr><td colspan='4' align='right'><strong>รวม</strong></td><td align='right'>".number_format($sum_qty)."</td><td align='right'>".number_format($sum_amount,2)."</td></tr>";
	}else{
		$html .= "<tr><td colspan='10'><h4 style='text-align:center'>----------- ไม่มีรายการ  ----------</h4></td></tr>";
	}
	$html .="</table>";
	echo $html;	
}

/***********************************************  Consign by zone Export to Excel ********************************/

if(isset($_GET['export_consign_by_zone']) && isset($_GET['from_date']) && isset($_GET['to_date']) )
{
	$from 		= date("Y-m-d 00:00:00",strtotime($_GET['from_date']));
	$to 			= date("Y-m-d 23:59:59", strtotime($_GET['to_date']));
	$zn			= $_GET['zone'];
	if(isset($_GET['zone_selected'])){ $zone_selected = trim($_GET['zone_selected']);}else{ $zone_selected="";}
	switch($zn)
	{
		case "0" :
		$zone_query = "id_zone >0";
		break;
		case "1" :
		$zone_query = "id_zone = ".$zone_selected;
		break;
		default :
		$zone_query = "id_zone >0";
		break;
	}
	$report_title = "รายงานเอกสารตัดยอดฝากขาย แยกตามโซน เรียงตามเอกสาร วันที่ ".thaiDate($from)." ถึง ".thaiDate($to);
	$title = array(1=>array($report_title));
	$line = array(1=>array("-------------------------------------------------------------------------------------"));
	$header = array("ลำดับ", "วันที่", "เลขที่เอกสาร", "โซน", "จำนวน", "มูลค่า");
	$body = array();
	array_push($body, $header);

	$sql = dbQuery("SELECT id_order_consign, reference, id_zone, date_add FROM tbl_order_consign WHERE ".$zone_query." AND (date_add BETWEEN '$from' AND '$to' ) ORDER BY date_add DESC");
	$row = dbNumRows($sql);
	if($row>0){
		$i = 0;
		$o = 1;
		$sum_qty = 0; $sum_amount = 0;
		while($i<$row){
			$rs = dbFetchArray($sql);
			$id_order_consign = $rs['id_order_consign'];
			$reference = $rs['reference'];
			$date_add = $rs['date_add'];
			$zone = get_zone($rs['id_zone']);
			$qr = dbQuery("SELECT * FROM tbl_order_consign_detail WHERE id_order_consign = $id_order_consign");
			$ro = dbNumRows($qr);
			if($ro > 0){
				$total_qty = 0; $total_amount = 0; $n = 0;
				while($n < $ro){
				$ra = dbFetchArray($qr);
				$amount = $ra['product_price']*$ra['qty'];
				$total_qty += $ra['qty'];
				$total_amount += $amount;
				$n++;
				}
			}
			$arr = array($o, thaiDate($date_add), $reference, $zone, number_format($total_qty), number_format($total_amount,2) );
			array_push($body, $arr);
			$sum_qty += $total_qty; $sum_amount += $total_amount;
			$i++; $o++;
		}
		$arr = array("", "", "", "รวม", number_format($sum_qty), number_format($sum_amount,2) );
		array_push($body, $arr);
	}else{
		$arr = array( "-------------------- ไม่มีรายการ  --------------------");
		array_push($body, $arr);
	}
	$sheet_name = "Consign_by_zone";
	$xls = new Excel_XML('UTF-8', false, $sheet_name); 
	$xls->addArray($title);
	$xls->addArray($line);
	$xls->addArray ( $body ); 
	$xls->generateXML("Consign_by_zone"); 
}


/***********************************************  Consignment by zone  ********************************/

if(isset($_GET['consignment_by_zone']) && isset($_GET['from_date']) && isset($_GET['to_date']) )
{
	$from 		= date("Y-m-d 00:00:00",strtotime($_GET['from_date']));
	$to 			= date("Y-m-d 23:59:59", strtotime($_GET['to_date']));
	$zn			= $_GET['zone'];
	if(isset($_GET['zone_selected'])){ $zone_selected = trim($_GET['zone_selected']);}else{ $zone_selected="";}
	switch($zn)
	{
		case "0" :
		$zone_query = "id_zone >0";
		break;
		case "1" :
		$zone_query = "id_zone = ".$zone_selected;
		break;
		default :
		$zone_query = "id_zone >0";
		break;
	}
	$html = "<h4 style='text-align:center'>รายงานบิลส่งสินค้าไปฝากขาย แยกตามโซน แยกตามเลขที่เอกสาร วันที่ ".thaiDate($from)." ถึง ".thaiDate($to)." </h4><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<table class='table table-striped'>	
<thead>
<th style='width:10%; text-align:center'>ลำดับ</th><th style='width:15%; text-align:center;'>วันที่</th>
<th style='width:15%;'>เลขที่เอกสาร</th><th>โซน</th><th style='width:10%; text-align:right;'>จำนวน</th><th style='width:15%; text-align:right;'>มูลค่า</th>
</thead>";
	$sql = dbQuery("SELECT id_order_consignment, tbl_order_consignment.id_order, reference, id_zone, date_upd FROM tbl_order_consignment LEFT JOIN tbl_order ON tbl_order_consignment.id_order = tbl_order.id_order WHERE ".$zone_query." AND (date_upd BETWEEN '$from' AND '$to' ) ORDER BY date_upd DESC");
	$row = dbNumRows($sql);
	if($row>0){
		$i = 0;
		$o = 1;
		$sum_qty = 0; $sum_amount = 0;
		while($i<$row){
			$rs = dbFetchArray($sql);
			$id_order_consign = $rs['id_order_consignment'];
			$id_order = $rs['id_order'];
			$reference = $rs['reference'];
			$date_add = $rs['date_upd'];
			$zone = get_zone($rs['id_zone']);
			//$qr = dbQuery("SELECT SUM(product_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail WHERE id_order = $id_order");
			$qr = dbQuery("SELECT id_product_attribute, SUM(qty) AS qty FROM tbl_qc WHERE id_order = $id_order AND valid = 1 GROUP BY id_product_attribute");
			//list($qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order AND valid = 1"));
			$ro = dbNumRows($qr);
			if($ro > 0){
				$total_qty = 0; $total_amount = 0; $n = 0;
				while($n < $ro){
				$ra = dbFetchArray($qr);
				$id_product_attribute = $ra['id_product_attribute'];
				$product = new product();
				$product->product_attribute_detail($id_product_attribute);
				$total_qty += $ra['qty'];
				$total_amount += $ra['qty']*$product->product_price;
				$n++;
				}
			}
			$html .= "<tr><td align='center'>$o</td><td align='center'>".thaiDate($date_add)."</td><td>$reference</td><td>$zone</td>
			<td align='right'>".number_format($total_qty)."</td><td align='right'>".number_format($total_amount,2)."</td></tr>";
			$sum_qty += $total_qty; $sum_amount += $total_amount;
			$i++; $o++;
		}
		$html .="<tr><td colspan='4' align='right'><strong>รวม</strong></td><td align='right'>".number_format($sum_qty)."</td><td align='right'>".number_format($sum_amount,2)."</td></tr>";
	}else{
		$html .= "<tr><td colspan='10'><h4 style='text-align:center'>----------- ไม่มีรายการ  ----------</h4></td></tr>";
	}
	$html .="</table>";
	echo $html;	
}
/***********************************************  Consignment by zone Export to Excel ********************************/

if(isset($_GET['export_consignment_by_zone']) && isset($_GET['from_date']) && isset($_GET['to_date']) )
{
	$from 		= date("Y-m-d 00:00:00",strtotime($_GET['from_date']));
	$to 			= date("Y-m-d 23:59:59", strtotime($_GET['to_date']));
	$zn			= $_GET['zone'];
	if(isset($_GET['zone_selected'])){ $zone_selected = trim($_GET['zone_selected']);}else{ $zone_selected="";}
	switch($zn)
	{
		case "0" :
		$zone_query = "id_zone >0";
		break;
		case "1" :
		$zone_query = "id_zone = ".$zone_selected;
		break;
		default :
		$zone_query = "id_zone >0";
		break;
	}
	$report_title = "รายงานบิลส่งสินค้าไปฝากขาย แยกตามโซน แยกตามเลขที่เอกสาร วันที่ ".thaiDate($from)." ถึง ".thaiDate($to);
	$title = array(1=>array($report_title));
	$line = array(1=>array("------------------------------------------------------------------------------------------------------------------------------------"));
	$sub_header = array("ลำดับ","วันที","เลขที่เอกสาร","โซน","จำนวน","มูลค่า");
	$body = array();
	array_push($body, $sub_header);
	$space = array("----------------------------------------------------------------------------------------------------------------------------------");
	$sql = dbQuery("SELECT id_order_consignment, tbl_order_consignment.id_order, reference, id_zone, date_upd FROM tbl_order_consignment 
						 LEFT JOIN tbl_order ON tbl_order_consignment.id_order = tbl_order.id_order WHERE ".$zone_query." AND (date_upd BETWEEN '$from' AND '$to' ) 
						 ORDER BY date_upd DESC");
	$row = dbNumRows($sql);
	if($row>0){
		$i = 0;
		$o = 1;
		$sum_qty = 0; $sum_amount = 0;
		while($i<$row){
			$rs = dbFetchArray($sql);
			$id_order_consign = $rs['id_order_consignment'];
			$id_order = $rs['id_order'];
			$reference = $rs['reference'];
			$date_add = $rs['date_upd'];
			$zone = get_zone($rs['id_zone']);
			$qr = dbQuery("SELECT SUM(product_qty) AS qty, SUM(total_amount) AS amount FROM tbl_order_detail WHERE id_order = $id_order");
			$ro = dbNumRows($qr);
			if($ro > 0){
				$total_qty = 0; $total_amount = 0; $n = 0;
				while($n < $ro){
				$ra = dbFetchArray($qr);
				$total_qty += $ra['qty'];
				$total_amount += $ra['amount'];
				$n++;
				}
			}
			$arr = array($o, thaiDate($date_add), $reference, $zone, number_format($total_qty), number_format($total_amount,2));
			array_push($body, $arr);
			$sum_qty += $total_qty; $sum_amount += $total_amount;
			$i++; $o++;
		}
		$arr = array("","","","รวม", number_format($sum_qty), number_format($sum_amount,2) );
		array_push($body, $arr);
	}else{
		$arr = array( "----------- ไม่มีรายการ  ----------"); 
		array_push($body, $arr);
	}
	$sheet_name = "Consignment_by_zone";
	$xls = new Excel_XML('UTF-8', false, $sheet_name); 
	$xls->addArray($title);
	$xls->addArray($line);
	$xls->addArray ( $body ); 
	$xls->generateXML("Consignment_by_zone"); 
	
}

?>
