<?php 
	$page_menu = "invent_order_bill";
	$page_name = "รายการรอเปิดบิล";
	$id_tab = 19;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($view==0){ echo accessDeny(); exit; }
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	?>
<div class="container"><audio id="sound1"><source src="../library/beep.mp3" type="audio/mpeg"></audio>
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-list-alt"></span>&nbsp;<?php echo $page_name; ?></h3>
  </div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	 if(isset($_GET['view_detail'])&&isset($_GET['id_order'])){
		   echo"
		   <li><a href='index.php?content=bill' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
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
///************************** แสดงรายละเอียด ****************************//
if(isset($_GET['view_detail'])&&isset($_GET['id_order'])){
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$sale = new sale($order->id_sale);
	if($order->current_state != 9){ $active = "style='display:none;'";}else{ $active = ""; }
	if($order->current_state == 10 ){ $confirm = ""; }else{ $confirm = "style='display:none;'";}
	echo "
        <div class='row'>
        	<div class='col-lg-12'><h4>".$order->reference." - ";if($order->id_customer != "0"){echo $customer->full_name;}echo "<p class='pull-right'>พนักงานขาย : &nbsp;".$sale->full_name."</p></h4></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-lg-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".thaiDate($order->date_add)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_product)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_qty)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_amount,2)."&nbsp;฿</dd> </dt></dl>
		<p class='pull-right' $active >
			<a href='controller/billController.php?print_order&id_order=$id_order' >
			<button type='button' class='btn btn-primary'><span class='glyphicon glyphicon-print' style='color:#FFF; font-size:30px;'></span></button>
			</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</p>
		<p class='pull-right' $confirm>
			
			
			<button type='button' class='btn btn-primary' id='iv_button' onclick='save_iv($id_order, $id_employee)'>เปิดใบกำกับภาษี</button>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</p>
		</div></div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />";
		echo "	
		<div class='row'><div class='col-lg-12'>"; echo $order->order_detail_qc_table(); echo "</div></div>";
	
}else{
/// ***************************  แสดงรายการรอตรวจ ****************************////
echo"
<div class='row'>
<div class='col-sm-12'>
	<table class='table table-striped'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ลำดับ</th>
			<th style='width:10%;'>เลขที่อ้างอิง</th><th style='width:20%;'>ลูกค้า</th>
            <th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:15%; text-align:center;'>เงื่อนไข</th>
			<th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%;'>พนักงาน</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
			<th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
        </thead>";
		$result = dbQuery("SELECT id_order,reference,date_add,date_upd,payment,id_customer,id_employee,current_state FROM tbl_order WHERE current_state = 10  ORDER BY id_order DESC ");
		$i=0;
		$n = 1;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order,$reference,$date_add,$date_upd,$payment,$id_customer,$id_employee,$current_state) = dbFetchArray($result);
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = '$id_order'"));
			list($cus_first_name,$cus_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			list($em_first_name,$em_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_employee WHERE id_employee = '$id_employee'"));
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = '$current_state'"));
			$customer_name = "$cus_first_name $cus_last_name";
			$employee_name = "$em_first_name $em_last_name";	
	echo"<tr>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$n</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$customer_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$payment</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$status</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$employee_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_upd)."</td>
			</tr>";
			$i++; $n++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
}
?>
<script>
$("#iv_button").dblclick(function(e) {
    alert("คลิกทีเดียวพอ");
});
function save_iv(id_order, id_employee){
	$("#iv_button").attr("disabled","disabled");
	window.location.href = "controller/billController.php?confirm_order&id_order="+id_order+"&id_employee="+id_employee;
}
</script>