<?php 
	$page_menu = "invent_sale";
	$page_name = "ตรวจนับสินค้า";
	$id_tab = 30;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($view==0){ echo accessDeny(); exit; }
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	if(isset($_POST['from_date'])){
		$from_date = $_POST['from_date'];
		$to_date = $_POST['to_date'];
		 $from= date('Y-m-d',strtotime($from_date));
		 $to = date('Y-m-d',strtotime($to_date));
	}else{
		$month = getMonth();
		$from = $month['from'];
		$to = $month['to'];
	}
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-eye-close"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['open']) || isset($_GET['close'])){
		
	   }else{
		 
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php
if(!isset($_GET['id_zone'])){
		if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
}
$checkstock = new checkstock();
if($checkstock->check_open() == true){
	$id_user = $_COOKIE['user_id'];
	if(isset($_GET['barcode_zone'])){ $barcode_zone = $_GET['barcode_zone']; }else{ $barcode_zone = "";}
	if(isset($_GET['id_zone'])){ $id_zone = $_GET['id_zone']; }else{ $id_zone = "";}
	if($id_zone !=""){ 
			$active = "disabled=disabled"; 
			$actived = "";
			$autofocus_zone = "";
			$autofocus_item = "autofocus='autofocus'";
		}else{ 
			$active = "";
			$actived = "disabled=disabled"; 
			$autofocus_zone = "autofocus='autofocus'";
			$autofocus_item = "";
		}
		if(isset($_GET['id_zone'])){
			list($barcode_zone) = dbFetchArray(dbQuery("SELECT barcode_zone FROM tbl_zone WHERE id_zone = '".$_GET['id_zone']."'"));
		}else{
			echo "<form id='stock_order_from' action='controller/checkstockController.php?check_zone' method='post'>";
		}
	echo "<div class='row'><input type='hidden' name='id_zone' id='id_zone' value='$id_zone' /><input type='hidden' id='id_employee' name='id_employee' value='$id_user' />
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ดโซน</span><input type='text' id='barcode_zone' name='barcode_zone' class='form-control' value='$barcode_zone' $autofocus_zone autocomplete='off' $active value='$barcode_zone' /></div> </div>
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' id='qty' name='qty' class='form-control' value='1' autocomplete='off' $actived' /></div> </div> 
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ดสินค้า</span><input type='text' id='barcode_item' name='barcode_item' class='form-control'  $autofocus_item autocomplete='off' $actived  /></div> </div>
	<div class='col-xs-2' id='load'><button type='button' class='btn btn-default' id='add' onclick='check_process()' $actived >ตกลง</button></div>
	<div class='col-xs-2'><button type='button' class='btn btn-default' id='change_zone' onclick='reset_zone()' $actived >เปลี่ยนโซน(F2)</button></div>
	</div></form>
	<br>
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />";
	if(isset($_GET['id_zone'])){
		$zone_name = get_zone($id_zone);
		echo "<br><div align='center'><h4>โซน&nbsp;&nbsp;$zone_name</h4></div>";
		echo "
		<form id='edit_order_form' action='controller/checkstockController.php?edit'  method='post' autocomplete='off'>
		<input type='hidden' name='id_zone' value='$id_zone' >
		<div id='value'>";
		if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}echo "
	<table class='table table-bordered table-striped'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:75%;'>รหัสสินค้า</th>
		<th style='width:10%; text-align:right;'>จำนวน</th><th style='width:10%; text-align:center;'>การกระทำ</th>
	</thead>";
	$id_check = $checkstock->get_id_check();
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
		echo "</table></div>";
		echo "<input type='hidden' name='new_qty' id='new_qty' /><input type='hidden' name='id_stock_check' id='id_stock_check' /></form>";
	}else{
		echo "<br><div align='center'><h2><span class='glyphicon glyphicon-info-sign'></span>&nbsp;&nbsp;ใส่บาร์โค้ดโซน</h2></div>";
	}
}else{
	echo "<br><div align='center'><h1><span class='glyphicon glyphicon-info-sign'></span>&nbsp;&nbsp;ยังไม่ได้เปิดการตรวจนับ</h1></div>";
}
?>

</div>
<script>
$("#barcode_zone").bind("enterKey",function(){
	var barcode = $(this).val();
	if(barcode !=""){
		$("#stock_order_from").submit();
	}
});
$("#barcode_zone").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});

$("#qty").bind("enterKey",function(){
	qty = $(this).val();
	if(qty ==""){
		alert("ยังไม่ได้ใส่จำนวน");
		$("#qty").focus();
	}else if(qty > 9999){
		alert("จำนวนสินค้าผิดปกติ");
		$("#qty").focus();
	}else{
		$("#barcode_item").focus();
	}
});
$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});		
///// กด shift เพื่อสลับระหว่างช่องจำนวนกับบาร์โค้ดสินค้า
$("#barcode_item").bind("spaceKey",function(){
	$("#qty").focus();
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 17)
    {
        $(this).trigger("spaceKey");
    }
});		
$("#qty").bind("spaceKey",function(){
	$("#barcode_item").focus();
});
$("#qty").keyup(function(e){
    if(e.keyCode == 17)
    {
        $(this).trigger("spaceKey");
    }
});		
//// เปลี่ยนโซน///
$(document).bind("F2",function(){
	$("#change_zone").click();
});
$(document).keyup(function(e){
	if(e.keyCode == 113)
	{
		$(this).trigger("F2");
	}
});
/// เพิ่มสินค้าเข้าไปในตะกร้าจัดสินค้าและตัดออกจากโซน	
function reset_zone(id_order){
	window.location.href="index.php?content=checkstock";
}
$(document).ready(function(e) {
    if($("#error").length){
		document.getElementById("sound1").play();
		alert($("#error").text());
	}
});
function edit_product(id_stock_check){
	var qty = parseInt($("#qty"+id_stock_check).text());
	$("#edit"+id_stock_check).css("display","none");
	$("#delete"+id_stock_check).css("display","none");
	$("#update"+id_stock_check).css("display","");
	$("#edit_qty"+id_stock_check).val(qty);
	$("#qty"+id_stock_check).css("display","none");
	$("#edit_qty"+id_stock_check).css("display","");	
}
function update(id_stock_check){
	var qty = $("#edit_qty"+id_stock_check).val();
		$("#new_qty").val(qty);
		$("#id_stock_check").val(id_stock_check);
		$("#edit_order_form").submit();
}
$("#barcode_item").bind("enterKey",function(){
	//alert("123");
	check_process();
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});		
function check_process(){
	var barcode_item = $("#barcode_item").val();
	var qty = $("#qty").val();
	var id_zone = $("#id_zone").val();
	var id_employee = $("#id_employee");
	if(barcode_item != ""){
	$("#barcode_item").val('');
	$("#add").focus();
	$("#load").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
	$.ajax({
		url:"controller/checkstockController.php?add=y&id_zone="+id_zone+"&id_employee="+id_employee+"&barcode_item="+barcode_item+"&qty="+qty,
		type:"GET", cache:false, 
		success: function(dataset){
			$("#barcode_item").focus();
		$("#value").html(dataset);
		$("#load").html("<button type='button' class='btn btn-default' id='add' onclick='check_process()'>ตกลง</button>");
		}
	});
	}else{
		alert("ยังไม่ได้ใส่บาร์โค้ดสินค้า");
	}
}
</script>