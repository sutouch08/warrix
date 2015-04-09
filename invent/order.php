<?php 
	$page_menu = "invent_order";
	$page_name = "ออเดอร์";
	$id_tab = 14;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($view==0){ echo accessDeny(); exit; }
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	
	?>
    
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_order'])){
		   $id_order = $_GET['id_order'];
		   $order= new order($id_order);
		   if($order->valid==1 || $order->current_state !=1 && $order->current_state !=3){ $active = "style='display:none;'";}else{$active = ""; }
		    echo"
		   <li><a href='index.php?content=order' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li $can_edit ><a href='index.php?content=order&add=y&id_order=$id_order' style='text-align:center; background-color:transparent;' $active ><button type='button' class='btn btn-link'  $active><span class='glyphicon glyphicon-pencil' style='color:#5cb85c; font-size:30px;'></span><br />แก้ไข</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add']) || isset($_GET['view_stock'])){
				 echo "<li><a href='index.php?content=order' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
			 if(isset($_GET['id_order'])){$id_order = $_GET['id_order'];
		  echo"	<li><a href='controller/orderController.php?save_order&id_order=$id_order' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='edit_stock()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";}
	  		}else{
		   echo"
		   <li $can_add><a href='controller/orderController.php?check_add' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>
		   <li ><a href='index.php?content=order&view_stock' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-search' style='color:#5cb85c; font-size:30px;'></span><br />ดูสต็อกคงเหลือ</button></a></li>";
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div id='error' class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
if(isset($_GET['missing'])){
	$missing = $_GET['missing'];
	echo"<div id='error' class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$missing</div>";
}
if(isset($_GET['message1'])){
	$message1 = $_GET['message1'];
echo "<div class='alert alert-warning' role='alert'>$message1</div>";
}
function payment_method($selected="เครดิต"){
	$sql=dbQuery("SELECT * FROM tbl_payment");
	while($row = dbFetchArray($sql)){
		$name = $row['payment_name'];
		if($selected==$name){ $default ="selected='selected'";}else{ $default = "";}
		echo "<option value='$name' $default>$name</option>";
	}
}
//***************************** ข้อมูลลูกค้า ****************************//
echo"<button data-toggle='modal' data-target='#myModal' id='info' style='display:none;'>xxx</button>
<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog' style='width:600px;'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'></h4>
									  </div>
									  <div class='modal-body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>รับทราบ</button>
									  </div>
									</div>
							 </div>
								</div>";
//*********************************************** เพิ่มออเดอร์ใหม่ ********************************************************// 
if(isset($_GET['add'])){ 
$user_id = $_COOKIE['user_id'];
if(isset($_GET['id_order'])){ 
$id_order = $_GET['id_order'];
$active = "disabled='disabled'"; 
$add = "style='display:none;'";
$edit = "";
$order = new order($id_order);
$new_ref = $order->reference;
$customer = new customer($order->id_customer);
$id_customer = $customer->id_customer;
$customer_name = $customer->full_name; 
$comment = $order->comment;
$payment = $order->payment;
}else{ 
	$id_order="";
	$new_ref = get_max_role_reference("PREFIX_ORDER",1);
	$active = "";
	$add="";
	$edit = "style='display:none;'";
	$id_customer = "";
	$customer_name = "";
	$comment = "";
	$payment = "เครดิต";
}
echo"<form id='add_order_form' action='controller/orderController.php?add=y' method='post'>
	<div class='row'><input type='hidden' name='id_employee' value='$user_id' /><input type='hidden' name='id_order' id='id_order' value='$id_order' />
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'><div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='doc_id' class='form-control' value='$new_ref' disabled='disabled'/></div> </div> 
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date' name='doc_date' class='form-control' value='".date('d-m-Y')."' $active/></div> </div>
	<div class='col-lg-4 col-md-4 col-sm-4 col-sx-4'><div class='input-group'><span class='input-group-addon'>ชื่อลูกค้า</span><input type='text' id='customer_name' class='form-control' value='$customer_name' autocomplete='off' $active/></div> </div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'><button class='btn btn-default' type='button' id='get_info' >&nbsp&nbsp;ดูข้อมูล&nbsp;&nbsp</button></div>
	</div>
	<div class='row' style='margin-top:15px;'><input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'><div class='input-group'><span class='input-group-addon'>การชำระเงิน</span><select name='payment' id='payment' class='form-control' $active>"; payment_method($payment); echo"</select></div> </div>
	<div class='col-lg-6 col-md-6 col-sm-6 col-sx-6 '><div class='input-group'><span class='input-group-addon'>หมายเหตุ</span><input type='text' id='comment' name='comment' class='form-control' value='$comment' autocomplete='off' $active/></div> </div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<button class='btn btn-default' type='button' id='add_order' $add $can_add>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button>
		<button class='btn btn-default' type='button' id='edit_order' $edit $can_edit>&nbsp&nbsp;แก้ไข&nbsp;&nbsp</button></div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	if(isset($_GET['id_order'])){ 
		echo"<form id='add_detail_form' action='controller/orderController.php?add&insert_detail' method='post'>
			<div class='row'><input type='hidden' name='id_order' id='id_order' value='$id_order' />
			<input type='hidden' name='stock_qty' id='stock_qty' /><input name='id_product_attribute' id='id_product_attribute' type='hidden' />
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ด</span><input type='text' id='barcode' class='form-control' autocomplete='off' autofocus /></div> </div> 
	<div class='col-lg-4 col-md-4 col-sm-4 col-sx-4'><div class='input-group'><span class='input-group-addon'>สินค้า</span><input type='text' id='product_code' class='form-control' autocomplete='off' /></div> </div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'><div class='input-group'><span class='input-group-addon'>ในสต็อก</span><input type='text' id='stock_label' class='form-control' disabled='disabled' /></div> </div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' id='qty' name='qty' class='form-control' autocomplete='off' autofocus /></div> </div>
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'><button class='btn btn-default' type='button' id='add_detail' onclick='submit_detail()'>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button></div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	//*********************************  เริ่ม ORDER GRID ******************************************//
	echo"
	<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
	<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>";
	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				if($rs<1){
					echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
				}else{				
				echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$n++;
				}
				echo"</ul></li>";
				}	
				echo "</li>";
				$i++;
				}
	echo"
	</ul>
	</div>
	</div>
<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
<div class='tab-content'>";	
$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	$r =0;
	while($c = dbFetchArray($query)){
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane"; if($r==0){ echo" active";} echo"' id='cat-$id_category'>";	
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0){
			$i=0;
			while($i<$row){
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);
				
		 echo"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>
			<div class='product' style='padding:5px;'>
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' style='font-size:10px; min-height:50px;'><a href='#'  onclick='getData(".$product->id_product.")'>".$product->product_code."</a></div>
			  </div></div>";
				$i++;
				$r++;
			}
		}else{ 
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		}
		echo "</div>";
	}	
	echo"</div> <button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
</div></div>";	
//************************************ จบ ORDER GRID **********************************************//		
echo"			
	<form action='controller/orderController.php?add_to_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'>title</h4><input type='hidden' name='id_order' value='$id_order'/>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div></form>";
//	order_grid($customer->id_customer, $order->id_order);	
	
	echo"<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<div class='row'><div class='col-lg-12'>
	<table class='table' id='order_detail'>
	<thead><tr style='font-size: 12px;'>
				<th stype='width:5%; text-align:center;'>ลำดับ</th><th style='width:5%; text-align:center;'>รูป</th><th style='width:10%;'>บาร์โค้ด</th><th style='width:30%;'>สินค้า</th>
			   <th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th>
			   <th style='width:10%; text-align:center;'>ส่วนลด</th><th style='width:10%; text-align:center;'>มูลค่า</th><th style='text-align:center;'>การกระทำ</th>
	</tr></thead>";
	$order = new order($id_order);
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order  ORDER BY id_order_detail DESC");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	$sumproduct_qty = 0;
	if($row>0){
	while($i<$row){
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $total_amount)= dbFetchArray($sql);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, $order->id_customer);
		$product->product_attribute_detail($id_product_attribute);
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%";}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;}
		echo"<tr style='font-size: 12px;'><td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' width='35px' height='35px' /> </td>
		<td style='vertical-align:middle;'>$barcode</td>
		<td style='vertical-align:middle;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($product_qty)."</td>
		<td style='text-align:center; vertical-align:middle;'>$discount</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($total_amount,2)."</td>
		<td style='text-align:center; vertical-align:middle;'><a href='controller/orderController.php?delete=y&id_order_detail=$id_order_detail' >
				<button type='button' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $product_reference : $product_name'); \" >
				<span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
				</td></tr>";
				$sumproduct_qty += $product_qty;
				$i++;
				$n++;
	}
	echo "<tr>
	<td colspan='6'></td><td><h4>จำนวน</h4></td><td style='text-align:center; vertical-align:middle;'><h4>$sumproduct_qty</h4></td><td><h4>ชิ้น<h4></td>
	</tr>		";
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo"
		
	</table>	</div></div>
	";
	
	 }

//*********************************************** จบหน้าเพิ่มออเดอร์ ****************************************************//
}else if(isset($_GET['edit'])&&isset($_GET['id_order'])){
//*********************************************** แก้ไขออเดอร์ **************************************************************//
	//echo"<form id='state_change' action='controller/orderController.php?edit&state_change' method='post'>";
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	if($order->id_customer != "0"){
	$customer = new customer($order->id_customer);
	$customer->customer_stat();
	$customer->sponsor_detail();
	}
	$sale = new sale($order->id_sale);
	$state = $order->orderState();
	$role = $order->role;
	echo"		
        <div class='row'>
        	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'><h4>".$order->reference." - ";if($order->id_customer != "0"){echo $customer->full_name;}echo "<p class='pull-right'>พนักงาน : &nbsp;".$sale->full_name."</p></h4></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".thaiDate($order->date_add)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_product)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_qty)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_amount,2)."&nbsp;฿</dd> </dt></dl>"; if($order->current_state ==3){
			echo"	<p class='pull-right'><a href='controller/orderController.php?print_prepare&id_order=$id_order' ><button type='button' class='btn btn-success'>ปริ๊นใบสั่งจัด</button></a></p>"; }
		echo"
		<p class='pull-right'><a href='controller/orderController.php?print_order&id_order=$id_order' ><span class='glyphicon glyphicon-print' style='color:#5cb85c; font-size:30px;'></span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
		</div></div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'><form id='state_change' action='controller/orderController.php?edit&state_change' method='post'>
		<div class='col-lg-6 col-md-6 col-sm-6 col-sx-6'>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'><tr><input type='hidden' name='id_order' value='".$order->id_order."' /><input type='hidden' name='id_employee' value='$id_employee' />
		<td style='width:25%; text-align:right; vertical-align:middle;'>สถานะ :&nbsp; </td><td style='width:40%; padding-right:10px;'>
		<select name='order_state' id='order_state' class='form-control input-sm' $can_edit>"; orderStateList($order->id_order); echo"</select></td><td style='padding-right:10px;'><button class='btn btn-default' type='button' onclick='state_change()' $can_edit>เพิ่ม</button></td></tr>";
		$row = dbNumRows($state);
		$i=0;
		if($row>0){
			while($i<$row){
				list($id_order_state, $state_name, $first_name, $last_name, $date_add)=dbFetchArray($state);
			echo"
			<tr  style='background-color:".state_color($id_order_state).";'><td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'>$state_name</td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'>$first_name  $last_name</td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'>".date('d-m-Y H:i:s', strtotime($date_add))."</td></tr>";
			$i++;
			}
		}else{
		echo"<tr><td style='padding-top:10px; padding-bottom:10px; text-align:center;'>".$order->currentState()."</td>
		<td style='padding-top:10px; padding-bottom:10px; text-align:right;'></td>
		<td style='padding-top:10px; padding-bottom:10px; text-align:center;'>".date('d-m-Y H:i:s', strtotime($order->date_upd))."</td></tr>";
		}
		echo"
		</table></div></form><div class='col-lg-6 col-md-6 col-sm-6 col-sx-6'>";
		if($order->id_customer != "0"){
			if($role == 4){
				echo"
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<tr><td colspan='3' >ข้อมูลสปอนเซอร์</td></tr>
		<tr><input type='hidden' id='id_customer' value='".$customer->id_customer."' />
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; ".$customer->full_name."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เลขที่เอกสาร : &nbsp;".$customer->sponsor_reference."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp;".$customer->email."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงิน :&nbsp;".number_format($customer->sponsor_amount,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อายุ :&nbsp;"; if($customer->birthday !="0000-00-00"){ echo round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{echo "-";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ใช้ไป :&nbsp;".number_format($customer->sponsor_used,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เพศ : &nbsp;"; if($customer->id_gender==1){ echo"ไม่ระบุ";}else if($customer->id_gender==2){echo"ชาย";}else{echo"หญิง";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>คงเหลือ : &nbsp;".number_format($customer->sponsor_balance,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp;".thaiTextDate($customer->date_add)."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>จำนวนครั้งที่เบิก : &nbsp;".$customer->total_sponsor_place." ครั้ง</td>
		</tr><tr>
		<td colspan='2' style='width:100%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ระยะสัญญา : &nbsp;".thaiTextDate($customer->sponsor_start)." ถึง ".thaiTextDate($customer->sponsor_end)."</td></tr>
		</table>";
			}else{
		echo "
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<tr><td colspan='3' >ข้อมูลลูกค้า</td></tr>
		<tr><input type='hidden' id='id_customer' value='".$customer->id_customer."' />
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; ".$customer->full_name."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตเทอม : &nbsp;".$customer->credit_term."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp;".$customer->email."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงินเครดิต :&nbsp;".number_format($customer->credit_amount,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อายุ :&nbsp;"; if($customer->birthday !="0000-00-00"){ echo round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{echo "-";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตใช้ไป :&nbsp;".number_format($customer->credit_used,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เพศ : &nbsp;"; if($customer->id_gender==1){ echo"ไม่ระบุ";}else if($customer->id_gender==2){echo"ชาย";}else{echo"หญิง";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตคงเหลือ : &nbsp;".number_format($customer->credit_balance,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp;".thaiTextDate($customer->date_add)."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ยอดเงินตั้งแต่เป็นสมาชิก : &nbsp;".number_format($customer->total_spent,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>&nbsp;</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ออเดอร์ตั้งแต่เป็นสมาชิก : &nbsp;".$customer->total_order_place."</td></tr>
		</table>";
		}
		}
		echo "</div><!--col --></div><!--row-->
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
		<form id='edit_order_form' action='controller/orderController.php?edit_order&add_detail' method='post' autocomplete='off'>
		<div class='row'><div class='col-lg-12'>"; echo $order->orderProductTable($can_edit, $can_delete); echo "</div></div><div class='row'><div class='col-lg-12'><p><h4>ข้อความ :  "; if($order->comment ==""){ echo"ไม่มีข้อความ";}else{ echo $order->comment; }echo "</h4></p></div></div><h4></h4></form>
		
<div class='modal fade' id='ModalLogin' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจการแก้ไขส่วนลด </h4>
			</div>
			<input type='hidden' id='id_employee' name='id_employee'>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required'>
				</div>
				<input id='login' class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='check_password()' >
				<!--userForm--> 
			</div>
			<p style='text-align:center; color:red;' id='message'></p>
			<div class='modal-footer'>
			</div>
		</div>
		<!-- /.modal-content --> 
	</div>
	<!-- /.modal-dialog --> 
</div>
<!-- /.Modal Login --> 
		";
//*********************************************** จบหน้าแก้ไข ****************************************************//
}else if(isset($_GET['view_stock'])){
//*********************************************** ดูยอดสต็อกคงเหลือนำยอดที่สั่งมาคำนวนแล้ว *********************************//	
	
	echo"
<div class='row'>
<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
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
					echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
				}else{				
				echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$n++;
				}
				echo"</ul></li>";
				}	
				echo "</li>";
				$i++;
				}
echo"
</ul>
</div>
</div>
<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'></h4>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
									  </div>
									</div>
							 </div>
								</div>
<div class='tab-content'>
<div class='tab-pane active' id='all'>";
	$sql = dbQuery("SELECT id_product FROM tbl_product ORDER BY product_code ASC");
	$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product);
			
			echo"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>			
						<div class='product' style='padding:5px;'>
							<div class='image' style='text-align:center;'><a href='#' onclick='view_data(".$product->id_product.")' >".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
							<div class='description' style='min-height:50px; font-size:10px;'><a href='#' onclick='view_data(".$product->id_product.")'>".$product->product_code."</a>	</div>
						</div>
					</div>";
			$i++;
		}
		//echo"</div>";
	}else{ 
		echo"<h4 style='align:center;'>ยังไม่มีรายการสินค้า</h4>";
	}
echo"</div>";
	$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	while($c = dbFetchArray($query)){
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane' id='cat-$id_category'>";	
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0){
			$i=0;
			while($i<$row){
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);
				
				echo"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>			
				<div class='product' style='padding:5px;'>
				<div class='image' style='text-align:center;'><a href='#' onclick='view_data(".$product->id_product.")' >".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
				<div class='description' style='min-height:50px; font-size:10px;'><a href='#' onclick='view_data(".$product->id_product.")' >".$product->product_code."</a></div>
				</div>
				</div>";
				$i++;
			}
			//echo"</div>";
		}else{ 
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		}
		echo "</div>";
	}
	echo" <button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button></div>"; 
echo"
</div>
</div></div></div>";
}else{
//************************************************ แสดงรายการ *************************************************//
$paginator = new paginator();
echo"<form  method='post' id='form'>
		<div class='row'>
			<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เงื่อนไข</span>
						<select class='form-control' name='filter' id='filter'>
							<option value='customer'>ลูกค้า</option>
							<option value='sale'>พนักงานขาย</option>
							<option value='reference'>เลขที่เอกสาร</option>
						</select>
				</div>		
			</div>	
			<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
				<div class='input-group'>
					<span class='input-group-addon'>ค้นหา</span>
						<input class='form-control' type='text' name='search-text' id='search-text' />
					<span class='input-group-btn'><button class='btn btn-default' id='search-btn' type='button'><span id='load'><span class='glyphicon glyphicon-search'></span></span></button>
				</div>		
			</div>	
			<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date'  value='";
					 if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['from_date']));} else { echo "เลือกวัน";} 
					 echo "'/>
				</div>		
			</div>	
			<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' value='";
				  if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['to_date']));} else{ echo "เลือกวัน";}  echo"' />
				</div>
			</div>
			<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>	
			<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
					<button type='button' class='btn btn-default' onclick='window.location.href=\"index.php?content=order\"'><i class='fa fa-refresh'></i></button>
			</div>
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />";
				$view = "";
		if(isset($_POST['from_date'])){	$from = date('Y-m-d',strtotime($_POST['from_date'])); }else{ $from = "";} if(isset($_POST['to_date'])){  $to =date('Y-m-d',strtotime($_POST['to_date'])); }else{ $to = "";}
		if($from==""){
			if($to==""){
				$view = getConfig("VIEW_ORDER_IN_DAYS");
			}
		}
		if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_order","WHERE (tbl_order.date_add BETWEEN '$from' AND '$to') AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC",$get_rows);
		//$paginator->display($get_rows,"index.php?content=order");
echo "<div class='row' id='result'>
			
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12' id='search-table'>";
	$paginator->display($get_rows,"index.php?content=order");
echo"	<table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th><th style='width:20%;'>ลูกค้า</th>
            <th style='width:10%;'>พนักงาน</th><th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:15%; text-align:center;'>การชำระเงิน</th><th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th><th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
        </thead>";
		//$result = getOrderTable($view,$from, $to,$paginator->Page_Start,$paginator->Per_Page);
		$sqm = dbQuery("SELECT id_order,reference,id_customer,id_employee,payment,tbl_order.date_add,current_state,tbl_order.date_upd FROM tbl_order WHERE role IN(1,4) AND order_status = 1 AND current_state =1 ORDER BY id_order DESC");
		$rm = dbNumRows($sqm);
		$m = 0;
		if($rm>0){
		while($m<$rm){
			list($id_order, $reference,$id_customer,$id_employee,  $payment,   $date_add,$current_state,$date_upd)=dbFetchArray($sqm);
			list($cus_first_name, $cus_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			if($id_employee ==""){ $employee_name = ""; }else{ list($employee_name) = dbFetchArray(dbQuery("SELECT first_name FROM tbl_employee WHERE id_employee = '$id_employee'")); }
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = $id_order"));
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = '$current_state'"));
			
	echo "<tr style='color:#FFF; background-color:".state_color($current_state).";'>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$id_order</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$cus_first_name &nbsp; $cus_last_name</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$employee_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$payment</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$status</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_upd)."</td>
			</tr>";
			$m++;
			}
		}
		$result = dbQuery("SELECT id_order,reference,id_customer,id_employee,payment,tbl_order.date_add,current_state,tbl_order.date_upd FROM tbl_order WHERE (tbl_order.date_add BETWEEN '$from' AND '$to') AND current_state !=1 AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page);
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order, $reference,$id_customer,$id_employee,  $payment,   $date_add,$current_state,$date_upd)=dbFetchArray($result);
			list($cus_first_name, $cus_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			if($id_employee ==""){ $employee_name = ""; }else{ list($employee_name) = dbFetchArray(dbQuery("SELECT first_name FROM tbl_employee WHERE id_employee = '$id_employee'")); }
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = $id_order"));
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = '$current_state'"));
	echo"<tr style='color:#FFF; background-color:".state_color($current_state).";'>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$id_order</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$cus_first_name &nbsp; $cus_last_name</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$employee_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$payment</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$status</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_upd)."</td>
			</tr>";
			$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
		echo $paginator->display_pages();
		echo "<br><br>";
		echo "<script> var x = setTimeout(function () { 
      						location.reload();
  							  }, 60 * 5000);
							  </script>";
}
?>
</div>
<script language="javascript">  
$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', onClose: function( selectedDate ) {
        $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to_date" ).datepicker({
      dateFormat: 'dd-mm-yy',   onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  $(function() {
    $("#date").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
   function validate() {
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if(from_date =="เลือกวัน"){	
		alert("คุณยังไม่ได้เลือกช่วงเวลา");
		}else if(to_date ==""){
		alert("คุณยังไม่ได้เลือกวันสุดท้าย");	
	}else{
		$("#form").submit();
	}
}	
	function state_change(){
		var state = $("#order_state").val();
		if(state == 0){
			alert("ยังไม่ได้เลือกสถานะ");
		}else{
			$("#state_change").submit();
		}
	}
$("#add_product").click(function() {
    $("#new_row").css("display","");
	var id_cus = $("#id_customer").val();
	$("#id_cus").val(id_cus);
});	

$(document).ready(function(e) {
	$("#product").autocomplete(
	{
		 source: "controller/orderController.php?product",
		 close: function(event,ui){
			 var ref = $(this).val();
			var id_cus = $("#id_cus").val();	
		$.ajax({ 
			 url: "controller/orderController.php?reference="+ref+"&id_customer="+id_cus,
			 type: "GET", cache:false, 
			 success: function(data){
				 if(data !=""){ 
				 	var arr = data.split(':');
					var id = arr[0];
					var price = arr[1];
					var stock = arr[2];
					 $("#id_product_attribute").val(id);
					 $("#price").val(price);
					 $("#available").text("คงเหลือ : "+stock);
					 $("#stock_qty").val(stock);
				 }
			 }
		});
    }
	});

});
$(document).ready(function(e) {
    $("#customer_name").autocomplete({
		source:"controller/orderController.php?customer_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#customer_name").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[1];
			var id_customer = arr[2];
			$("#id_customer").val(id_customer);
			$(this).val(name);
		}
	});			
});

$(document).ready(function(e) {
    $("#product_code").autocomplete({
		source:"controller/orderController.php?product_code",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#product_code").val();
		}
	});			
});
//// product code
$("#product_code").focusout(function(e) {
    var product_code = $(this).val();
	if(product_code !=""){
	$.ajax({
		url:"controller/orderController.php?check_stock&product_code="+product_code
		,type:"GET",cache:false,success: function(stock_qty){
			if(stock_qty !=""){
				var arr = stock_qty.split(":");
				$.trim($("#id_product_attribute").val(arr[0]));
				$("#stock_qty").val(arr[1]);
				$("#stock_label").val(arr[1]);
			}
		}
	});
	}
});
//// barcode 
$("#barcode").focusout(function(e) {
    var barcode = $(this).val();
	if(barcode !=""){
	$.ajax({
		url:"controller/orderController.php?check_stock&barcode="+barcode
		,type:"GET",cache:false,success: function(data){
			if(data !=""){
				var arr = data.split(":");
				$("#id_product_attribute").val(arr[0]);
				$("#stock_qty").val(arr[1]);
				$("#stock_label").val(arr[1]);
				$("#product_code").val(arr[2]);
			}
		}
	});
	}
});
/////
$("#qty").keyup(function(e) {
    var limit = parseInt($("#stock_qty").val());
	var qty = parseInt($("#qty").val());
	if(qty>limit){
		alert("มีสินค้าในสต็อกแค่ "+limit+" ตัวเท่านั้น");
		$("#qty").val(limit);
	}
});
$("#product_code").bind("enterKey",function(){
	if($("#product_code").val() != ""){
	$("#qty").focus();
	}
});
///// ชื่อสินค้า เมื่อกดปุ่ม enter
	$("#product_code").bind("enterKey",function(){
	if($("#product_code").val() != ""){
	$("#qty").focus();
	}
});
$("#product_code").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
/// barcode 
$("#barcode").bind("enterKey",function(){
	if($("#barcode").val() != ""){
		$("#qty").focus();
	}else{
		$("#product_code").focus();
	}
});
$("#barcode").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#qty").bind("enterKey",function(){
	if($("#qty").val() != ""){
		$("#add_detail").click();
	}
});
$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$(document).ready(function(e) {
    $("#qty").keyup(function(e) {
        var qty = parseInt($(this).val());
		var stock = parseInt($("#stock_qty").val());
		var price = $("#price").val();
		if(qty>stock){
			alert("มีสินค้าในสต็อกแค่ "+stock);
			$(this).val(stock);
			var total = price * qty;
			$("#total_amount").val(total);
			$("#total").html(total +" ฿");
		}else{
			var total = price * qty;
		$("#total_amount").val(total);
		$("#total").html(total +" ฿");
		}
    });
});
function edit_product(id_order, id_product_attribute){
	var name = id_order.toString() + id_product_attribute.toString();
	var qty = parseInt($("#qty"+name).text());
	$("#edit"+name).css("display","none");
	$("#delete"+name).css("display","none");
	$("#update"+name).css("display","");
	$("#edit_qty"+name).val(qty);
	$("#qty"+name).css("display","none");
	$("#edit_qty"+name).css("display","");	
}
function update(id_order, id_product_attribute){
	var name = id_order.toString() + id_product_attribute.toString();
	var qty = $("#edit_qty"+name).val();
	var old_qty = parseInt($("#qty"+name).text());
	if(qty<=0){
		alert("จำนวนที่สั่งอย่างน้อย 1 ตัว");
	}else{
	$.ajax({
		url:"controller/orderController.php?check_stock&id_order="+id_order+"&id_product_attribute="+id_product_attribute, 
		cache:false,
		success: function(stock_qty){
			var stock = parseInt(stock_qty);
			if(qty<=old_qty){
				$("#new_qty").val(qty);
				$("#id_order").val(id_order);
				$("#id_product_attribute").val(id_product_attribute);
				$("#edit_order_form").submit();
			}else if(qty>stock){
				alert("มีสินค้าในสต็อกแค่ "+stock+" เท่านั้น");
				$("#edit_qty"+name).val(stock);
				//$("#new_qty").val(stock);
			}else{
				$("#new_qty").val(qty);
				$("#id_order").val(id_order);
				$("#id_product_attribute").val(id_product_attribute);
				$("#edit_order_form").submit();
			}
		}
	});
	}
}
$(document).ready(function(e) {
    $("#doc_date").datepicker({ 
	dateFormat: 'dd-mm-yy'
	});
});
$("#add_order").click(function(e) {
    var date = $("#doc_date").val();
	var cus_name = $("#customer_name").val();
	var cus_id = $("#id_customer").val();
	if(date ==""){
		alert("ยังไม่ได้ระบุวันที่");
	}else if(cus_name == ""){
		alert("ยังไม่ได้เลือกลูกค้า");
	}else if(cus_id ==""){
		alert("ระบบไม่พบ Customer ID ไม่สามารถเพิ่มออเดอร์ได้กรุณาเลือกลูกค้าใหม่หรือติดต่อผู้ดูแลระบบ");
	}else{
		$("#add_order_form").submit();
	}
});
$("#edit_order").click(function(e) {
    $("#doc_date").removeAttr("disabled");
	$("#customer_name").removeAttr("disabled");
	$("#payment").removeAttr("disabled");
	$("#comment").removeAttr("disabled");
	$("#add_order").text("บันทึก");
	$(this).css("display", "none");
	$("#add_order").css("display","");
});
//////// เพิ่มรายการสั่งซื้อสินค้าแต่ไม่เปลียนหน้าใหม่ ///
function submit_detail(){
	var id_order = $("#id_order").val();
	var id_product_attribute =  $("#id_product_attribute").val();
	var order_qty = $("#qty").val();
	var stock_qty = $("#stock_qty").val();
	var id_customer = $("#id_customer").val();
	if(id_order==""){
		alert("ไม่พบตัวแปร id_order ติดต่อผู้ดูแลระบบ");
	}else if(id_product_attribute ==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(id_customer==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(order_qty==""){
		alert("ยังไม่ได้ใส่จำนวนสินค้า");
	}else if(parseInt(order_qty)>parseInt(stock_qty)){
		alert("จำนวนที่สังมากกว่าจำนวนที่มีในสต็อก");
		$("#qty").val(stock_qty);
	}else{
		$("#add_detail_form").submit();
	}
}
//// เพิ่มรายการสั่งซื้อสินค้าแล้วเปลียนหน้าใหม่ ///
function add_detail(){ 
	var id_order = $("#id_order").val();
	var id_product_attribute =  $("#id_product_attribute").val();
	var order_qty = $("#qty").val();
	var stock_qty = $("#stock_qty").val();
	var id_customer = $("#id_customer").val();
	if(id_order==""){
		alert("ไม่พบตัวแปร id_order ติดต่อผู้ดูแลระบบ");
	}else if(id_product_attribute ==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(id_customer==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(order_qty==""){
		alert("ยังไม่ได้ใส่จำนวนสินค้า");
	}else if(parseInt(order_qty)>parseInt(stock_qty)){
		alert("จำนวนที่สังมากกว่าจำนวนที่มีในสต็อก");
		$("#qty").val(stock_qty);
	}else{
		$("#edit_order_form").submit();
	}
}
var no = 0;
//////// เพิ่มรายการสั่งซื้อสินค้าแต่ไม่เปลียนหน้าใหม่ ///
function insert_detail(id_order,id_product_attribute, qty){
	$.ajax({
		url:"controller/orderController.php?insert_detail",
		data: {id_order:id_order, id_product_attribute:id_product_attribute, qty:qty},type:"POST",cache:false,
		success: function(complete){
			if(complete !="error"){
				no = no +1;
				var data = complete.split(":");
				var barcode = data[0];
				var product = data[1];
				var price = data[2];
				var quantity = data[3];
				var discount = data[4];
				var amount = data[5];
				$("#order_detail").append("<tr><td align='center'>"+no+"</td><td>"+barcode+"</td><td>"+product+"</td><td>"+price+"</td><td>"+quantity+"</td><td>"+discount+"</td><td>"+amount+"</td></tr>");
			}else{ 
			alert("เพิ่มข้อมูลไม่ได้");
			}
		}
	});
}
$(document).ready(function(e) {
    if($("#error").length){
		alert($("#error").text());
	}
});
$("#get_info").click(function(e) {
	var cus_name = $("#customer_name").val();
	var cus_id = $("#id_customer").val();
	 if(cus_name == ""){
		alert("ยังไม่ได้เลือกลูกค้า");
	}else if(cus_id ==""){
		alert("ระบบไม่พบ Customer ID ไม่สามารถเพิ่มออเดอร์ได้กรุณาเลือกลูกค้าใหม่หรือติดต่อผู้ดูแลระบบ");
	}else{
		$.ajax({
			url:"controller/customerController.php?get_info&id_customer="+cus_id,
			type:"GET", cache:false, success: function(data){
				$(".modal-title").text("ข้อมูล : "+cus_name);
				$(".modal-body").html(data);
				$("#info").click();
			}
		});
	}
});
function get_row(){
	$("#rows").submit();
}
function getData(id_product){
	var id_cus = $("#id_customer").val();
	$.ajax({
		url:"controller/orderController.php?getData&id_product="+id_product+"&id_customer="+id_cus,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
function view_data(id_product){
	$.ajax({
		url:"controller/orderController.php?view_stock_data&id_product="+id_product,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
$("#edit_reduction").click(function(e) {
    $(".reduction").css("display","none");
	$(".input_reduction").css("display","block");
	$("#save_reduction").removeAttr("disabled");
});
function check_password(){
	var password = $("#password").val();
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			if(data == "0"){
				$("#message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#password").val("");
			}else{
				update_discount(data);
			}
		}
	});
}
function update_discount(id_employee){
	//var reduction = $("#reduction[]").val();
	//alert ("reduction"+id_employee);
	var id_order_detail_array = [];
	var reduction_array = [];
	var id_order = $("#id_order").val();
	//alert(loop);
	for ( i = 0 ;i < $("#loop").val(); i++ ){
		//alert(i);
		var n = i+1;
		var id_order_detail = $("#id_order_detail"+n).val();
		var reduction = $("#reduction"+n).val();
		id_order_detail_array[n] = id_order_detail;
		reduction_array[n] = reduction;
		//alert("id_order_detail"+n);
	}
	$.ajax({
		url:"controller/orderController.php?edit_discount",
		data: {id_employee:id_employee, reduction_array:reduction_array, id_order_detail_array:id_order_detail_array,id_order:id_order},type:"POST",cache:false,
		success: function(complete){
			window.location.reload();
		}
	});
}
$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-text").bind("enterKey",function(){
	if($("#search-text").val() != ""){
		$("#search-btn").click();
	}
});
$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var filter = $("#filter").val();
	if(query_text !=""){
	$("#load").html("<img src='../img/ajax-loader.gif' width='18' height='18' />");
	$.ajax({
		url:"controller/orderController.php?search-text="+query_text+"&filter="+filter , type: "GET", cache:false,
		success: function(result){
			$("#result").html(result);
			clearTimeout(x);
			$("#load").html("<span class='glyphicon glyphicon-search'></span>");
	}
	}); 
	}
});
</script>