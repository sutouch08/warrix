<?php 
	$page_menu = "invent_order_qc";
	$page_name = "ตรวจสินค้า";
	$id_tab = 18;
	$id_profile = $_COOKIE['profile_id'];
	ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($view==0){ echo accessDeny(); exit; }
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
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
	
    ?>
<div class="container"><audio id="sound1"><source src="../library/beep.mp3" type="audio/mpeg"></audio>
<!-- page place holder -->
<div class="row">
	<div class="col-xs-6"><h3><span class="glyphicon glyphicon-eye-open"></span>&nbsp;<?php echo $page_name; ?></h3>
  </div>
    <div class="col-xs-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	 
	  if(!isset($_GET['id_order'])&&!isset($_GET['process'])){
		
	
		   echo"
		   <li><a href='index.php?content=qc&process' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-forward' style='color:#5cb85c; font-size:30px;'></span><br />กำลังตรวจ</button></a></li>";
	   }else if(isset($_GET['id_order'])&&isset($_GET['process'])){
		     $id_order = $_GET['id_order'];
			 list($sumqty_order) = dbFetchArray(dbQuery("SELECT SUM(product_qty) FROM tbl_order_detail WHERE id_order = $id_order"));
			 list($sumqty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = $id_order"));
		   echo"
		   <li><a href='index.php?content=qc' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
		   if($sumqty_order != "$sumqty"){
		   echo "<li $can_add><a href='index.php?content=prepare&process=y&id_order=$id_order' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-repeat' style='color:#5cb85c; font-size:30px;'></span><br />จัดสินค้าใหม่</button></a></li>";
		   }
		   
	   }else{
		   echo"
		   <li><a href='index.php?content=qc' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
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
	 echo"<div id='error' class='alert alert-danger alert-dismissible' role='alert' >
	 
	 <b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}


if(isset($_GET['id_order'])){ 
 
	$id_order = $_GET['id_order'];
	$id_user = $_COOKIE['user_id'];
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
if(isset($_GET['confirm_error'])&&isset($_GET['id_zone'])&&isset($_GET['id_product_attribute'])){
	$confirm_error = $_GET['confirm_error'];
	$id_zone = $_GET['id_zone'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$zone = product_from_zone($id_order, $id_product_attribute);
	$arr = explode(":",$zone);
	$zone_name = $arr[0];
	echo"<div id='confirm_error' class='alert alert-danger alert-dismissible' role='alert' > <b>มีบางอย่างผิดพลาด&nbsp;</b>$confirm_error</div>";
}else{
	$confirm_error = "";
	$id_zone = "";
	$id_product_attribute ="";
	$zone_name = "";
}
	$id_order_state_change = dbNumRows(dbQuery("SELECT id_order_state_change FROM tbl_order_state_change WHERE id_order = $id_order AND id_order_state = 11 AND id_employee != $id_user"));
	if($id_order_state_change > 0){ ?>
    <script type="text/javascript">
		window.location = "index.php?content=qc&error=เออเดอร์นี้มีคนตรวจแล้ว";	
	</script>
    <?php 
	}	
echo"
	<div class='row'>
		<div class='col-xs-2'>เลขที่ : ".$order->reference."</div><div class='col-xs-2'>ลูกค้า : ".$customer->full_name."</div><div class='col-xs-2'>วันที่สั่ง : ".thaiDate($order->date_add)."</div>	
		<div class='col-xs-2'>จำนวนรายการ : ".$order->total_product." รายการ </div><div class='col-xs-2'>จำนวนตัว : ".$order->total_qty." ตัว </div><div class='col-xs-2'>มูลค่า : ".number_format($order->total_amount,2)." </div>
		<div class='col-xs-12'>&nbsp;</div>
		<div class='col-xs-12'>หมายเหตุ : ".$order->comment."</div>
	</div>
	
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<div class='row'><input type='hidden' id='id_user' name='id_user' value='$id_user'/>
	<div class='col-xs-3 col-xs-offset-4'><div class='input-group'><span class='input-group-addon'>บาร์โค้ดสินค้า</span><input type='text' id='barcode_item' name='barcode_item' class='form-control' autofocus  autocomplete='off' /></div> </div>
	<div class='col-xs-2' id='load'><button type='button' class='btn btn-default' id='add' onclick='qc_process()' >ตกลง</button></div>
	</div>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />	
	
	
	<div id='value'>
	<a href='controller/qcController.php?over_order&id_order=$id_order&id_product_attribute=$id_product_attribute&id_zone=$id_zone' >
	<button type='button' id='move_zone' style='display:none;' onclick=\"return confirm('สินค้าเกิน ต้องการย้ายสต็อกจากโซน $zone_name ไปยัง Buffer หรือไม่'); \">
	click</button></a>
	<table class='table' id='table1'>
	<thead id='head'>
	
		<th style='width:20%; text-align:center;'>บาร์โค้ด</th><th style='width:35%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวนที่สั่ง</th><th style='width:10%; text-align:center;'>จำนวนที่จัด</th>	<th style='width:10%; text-align:center;'>ตรวจแล้ว</th><th style='width:10%; text-align:center;'>จากโซน</th>
	</thead>";
	if($order->current_state == 5){ if($id_order_state_change > 0){}else{$order->state_change($order->id_order, 11, $id_user);}}
	$sql = dbQuery("SELECT tbl_order_detail.id_product_attribute, product_qty FROM tbl_order_detail WHERE tbl_order_detail.id_order = $id_order ORDER BY date_upd DESC");
	$row = dbNumRows($sql);
	$n = 1;
	$row1 = 0;
	while($list = dbFetchArray($sql)){
			$id_product_attribute  = $list['id_product_attribute'];
			$product = new product();
			$product->product_attribute_detail($id_product_attribute);
			$product->product_detail($product->id_product);
			$barcode = $product->barcode;
			$product_code = $product->reference." : ".$product->product_name;
			$order_qty = $list['product_qty'];
			//$checked  = $list['qty'];
			list($prepare_qty) = dbFetchArray(dbQuery("SELECT  SUM(qty) AS qty FROM tbl_temp WHERE id_order = $id_order  AND id_product_attribute = $id_product_attribute"));
			list($checked) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1"));
			$balance_qty = $prepare_qty - $checked;
			if($order_qty == "$checked"){
				
			}else{
				$row1++;
		echo"
			<tr id='row".$id_product_attribute."' ";if($order_qty > "$prepare_qty"){echo "style='color:#FF0000'";} echo ">
				<td align='center'>$barcode</td><td> $product_code </td><td align='center'> ".number_format($order_qty)." </td>
				<td align='center' ><p id='prepare".$id_product_attribute."'>".number_format($prepare_qty)."</p></td><td align='center' id='checked".$id_product_attribute."'> ".number_format($checked)." </td>
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
	}
//	if($row1 == "0"){
		echo"<tr id='finish' style='display:none;'><td colspan='6' align='center'><a href='controller/qcController.php?close_job&id_order=$id_order&id_employee=$id_user'><button type='button' class='btn btn-success' onclick=\"return check_cancal() \">ตรวจเสร็จแล้ว</button></a></td></tr>";
//	}else{
	echo "<tr><td id='force_close' colspan='6' align='center'>
<input type='checkbox' id='checkboxes'  onclick='getcondition()' />
สินค้ามีไม่ครบ
<br />
<br />
<div id='continue_bt'></div>
";
echo "</td></tr>";
//	}
	echo"<button data-toggle='modal' data-target='#edit_qc' id='btn_toggle' style='display:none;'>toggle</button>
			</table>
			<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
			<div class='row'><div class='col-xs-12'><h4 style='text-align:center;'>รายการที่ครบแล้ว</h4></div></div>
			<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
		<table class='table'>
	<thead id='head2'>
		<th style='width:20%; text-align:center;'>บาร์โค้ด</th><th style='width:35%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวนที่สั่ง</th><th style='width:10%; text-align:center;'>จำนวนที่จัด</th>	<th style='width:10%; text-align:center;'>ตรวจแล้ว</th><th style='width:10%; text-align:center;'>จากโซน</th>
	</thead>";
	$sql = dbQuery("SELECT SUM(qty)AS qty,id_product_attribute FROM tbl_qc WHERE id_order = $id_order GROUP BY id_product_attribute ORDER BY id_product_attribute ASC ");
	$row = dbNumRows($sql);
	$n=0;
	if($row>0){
	while($list = dbFetchArray($sql)){
			$id_product_attribute  = $list['id_product_attribute'];
			list($order_qty) = dbFetchArray(dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order"));
			$product = new product();
			$product->product_attribute_detail($id_product_attribute);
			$product->product_detail($product->id_product);
			$barcode = $product->barcode;
			$product_code = $product->reference." : ".$product->product_name;
			$prepare_qty = $list['qty'];
			list($checked) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1"));
			$balance_qty = $prepare_qty - $checked;
			if($order_qty == "$checked"){
			$row1++;
	echo"
			<tr ";if($order_qty > "$prepare_qty"){echo "style='color:#FF0000'";} echo ">
				<td align='center'>$barcode</td><td> $product_code </td><td align='center'> ".number_format($order_qty)." </td>
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
			}else if(number_format($order_qty) == 0 ){
				$n++;
				echo"
			<tr ";if($order_qty > "$prepare_qty"){echo "style='color:#FF0000'";} echo ">
				<td align='center'>$barcode</td><td> $product_code </td><td align='center'> ".number_format($order_qty)." </td>
				<td align='center'>".number_format($prepare_qty)."</td><td align='center'> ".number_format($checked)." </td>
				<td align='center'><a href='controller/qcController.php?delete&id_product_attribute=$id_product_attribute&id_order=$id_order'><button type='button' class='btn btn-danger'>ยกเลิก</button></a></td>
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
	}
	}
	echo"
			</table>	<div><input type='hidden' id='loop_cancal' value='$n' >
	";
		
	
}else if(isset($_GET['process'])){ ///// รายการระหว่างจัด
	echo"
<div class='row'>
	<div class='col-xs-12'>
		<table class='table'>
		<thead>
			<th style='width: 5%; text-align:center;'>ลำดับ</th><th style='width: 20%; text-align:center;'>เลขที่เอกสาร</th>
			<th style='width: 15%; text-align:center;'>ลูกค้า</th><th style='width: 15%; text-align:center;'>รูปแบบ</th><th style='width: 15%; text-align:center;'>วันที่สั่ง</th><th style='width: 20%; text-align:center;'>พนักงานจัด</th><th style='width: 5%; text-align:center;'>&nbsp;</th>
		</thead>";
		//$user_id = $_COOKIE['user_id'];
		$sql = dbQuery("SELECT tbl_order.id_order, tbl_temp.id_employee FROM tbl_order LEFT JOIN tbl_temp ON tbl_order.id_order = tbl_temp.id_order WHERE current_state = 11  GROUP BY tbl_order.id_order");
		$n = 1;
		while($row = dbFetchArray($sql)){
			$order = new order($row['id_order']);
			$customer = new customer($order->id_customer);
			$employee = new employee($row['id_employee']);
			echo"
			<tr>
					<td align='center'>$n</td>
					<td align='center'>".$order->reference."</td>
					<td align='center'>".$customer->full_name."</td>
					<td align='center'>".$order->role_name."</td>
					<td align='center'>".thaiDate($order->date_add)."</td>
					<td align='center'>".$employee->full_name."</td>
					<td align='center'><a href='index.php?content=qc&process=y&id_order=".$order->id_order."'><span class='btn btn-default'>ตรวจสินค้าต่อ</span></a></td>
			</tr>";
			$n++;
		}
echo"		</table>
	</div> <!-- col-xs-12 -->
</div> <!--  row -->
";	
}else{
/// ***************************  แสดงรายการรอตรวจ ****************************////
echo"
<div class='row'>
	<div class='col-xs-12' id='reload'>
		<table class='table'>
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
echo"		</table>
	</div> <!-- col-xs-12 -->
</div> <!--  row -->
";	
?>
<script>
setInterval(function() {
    $.get('controller/qcController.php?reload', function(data) {
      $("#reload").html(data);
    });
}, 60000);
</script>
<?php
}
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
function edit_qc(id_product_attribute, id_order){
	$.ajax({
		url:"controller/qcController.php?getData&id_product_attribute="+id_product_attribute+"&id_order="+id_order,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				$("#modal").css("width","500px");
				$("#modal_title").html("แก้ไข QC เกิน");
				$("#modal_body").html(dataset);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
function getcondition(){
	if(checkboxes.checked){
		$("#continue_bt").html("<a href='controller/qcController.php?close_job&id_order=<?php echo $id_order;?>&id_employee=<?php echo $id_user;?>'><button type='button' class='btn btn-success' onclick=\"return check_cancal() \">บังคับจบ</button></a>");
	}else{
		$("#continue_bt").html("");
	}
}
$("#barcode_item").bind("enterKey",function(){
	//alert("123");
	qc_process();
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});		
function qc_process(){
	var barcode_item = $("#barcode_item").val();
	if(barcode_item != ""){
	$("#barcode_item").val('');
	$("#add").focus();
	$("#load").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
	$.ajax({
		url:"controller/qcController.php?checked=y&id_order=<?php echo $id_order;?>&id_user=<?php echo $id_user;?>&barcode_item="+barcode_item,
		type:"GET", cache:false, 
		success: function(dataset){
			$("#barcode_item").focus();
			arr = dataset.split(":");
			
			if(arr[0].trim()=="ok"){
				id_product_attribute = arr[1];
				qc_qty = parseInt(arr[2]);
				pre_qty = parseInt($("#prepare"+id_product_attribute).html());
				if(qc_qty == pre_qty){
				$("#checked"+id_product_attribute).html(qc_qty);
				$("#row"+id_product_attribute).insertAfter($("#head2"));
				}else{
				$("#checked"+id_product_attribute).html(qc_qty);
				$("#row"+id_product_attribute).insertAfter($("#head"));
				}
			}else{
				error = arr[1];
				alert(error);
			}
			$("#load").html("<button type='button' class='btn btn-default' id='add' onclick='qc_process()' >ตกลง</button>");
			count = $("#table1 td").size();
			if(count<3){
				$("#force_close").css("display","none");
				$("#finish").css("display", "");
			}
		}
	});
	}else{
		alert("คุณยังไม่ได้ใส่บาร์โค้ด");
	}
	
}

count = $("#table1 td").size();
	if(count<3){
		$("#force_close").css("display","none");
		$("#finish").css("display", "");
	}
function check_cancal(){
	var loop_cancal = $("#loop_cancal").val();
	if(loop_cancal > 0){
		alert("มีรายการสินค้าที่ยกเลิกกรุณายกเลิกรายรายสินค้าก่อน");
		return false;
	}else{
		return true;
	}
}
</script>
