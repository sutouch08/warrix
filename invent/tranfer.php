<?php 
	$page_menu = "invent_product_in";
	$page_name = "โอนคลัง";
	$id_tab = 6;
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
	<div class="col-xs-6"><h3><span class="glyphicon glyphicon-export"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-xs-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_recieved_product'])){
		    echo"
		   <li><a href='index.php?content=product_in' style='text-align:center; background-color:transparent; '><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='edit_stock()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
				if(isset($_GET['id_tranfer'])){
					$id_tranfer = $_GET['id_tranfer'];	
				}
		   echo"
		   <li><a href='index.php?content=tranfer' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
		if(isset($_GET['id_tranfer'])){
			$id_tranfer = $_GET['id_tranfer'];	
       		echo "<li><a href='controller/tranferController.php?print=y&id_tranfer=$id_tranfer' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-print' style='color:#5cb85c; font-size:30px;'></span><br />print</button></a></li>";}
	  		}else if(isset($_GET['view_detail'])){
		   echo"
		   <li><a href='index.php?content=tranfer' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=tranfer&add=y' style='text-align:center; background-color:transparent; padding-top:0px; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
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
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
} 
if(isset($_GET['add'])){
		if(!isset($_GET['id_tranfer'])){ 
			$reference = get_max_role_reference_tranfer("PREFIX_TRANFER");
			$user_id = $_COOKIE['user_id'];
			$active = "";
			$customer_name = "";
			$comment = "";
			$warehouse_from = "";
			$warehouse_to = "";
		}else{
			$id_tranfer = $_GET['id_tranfer'];
			list($reference,$warehouse_from,$warehouse_to,$date_add,$comment) = dbFetchArray(dbQuery("SELECT reference,warehouse_from,warehouse_to,date_add,comment FROM tbl_tranfer WHERE id_tranfer = $id_tranfer"));
			$active = "disabled='disabled'";
		}
function select_zone_consign($selected=""){
	echo"<option value='0'>-------เลือกพื้นที่--------</option>";
	$sql = dbQuery("SELECT * FROM tbl_zone WHERE id_warehouse = 2");
	while($rs = dbFetchArray($sql)){
		$id_zone = $rs['id_zone'];
		$zone_name = $rs['zone_name'];
		if($selected==$id_zone){ $select = "selected='selected'";}else{ $select = "";}
		echo"<option value='$id_zone' $select>$zone_name</option>";
	}
}
	/////////  เพิ่มออเดอร์ ID ใหม่
echo "<form id='add_order_form' action='controller/tranferController.php?add=y' method='post'>
	<div class='row'><input type='hidden' name='id_employee' value='$user_id' />
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='doc_id' class='form-control' value='$reference' disabled='disabled'/></div> </div> 
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date' name='doc_date' class='form-control' value='".date('d-m-Y')."' $active/></div> </div>

	<div class='col-xs-3'><div class='input-group'>
						<span class='input-group-addon'>ย้ายจาก</span>";
						echo"
						<select class='form-control' name='warehouse_from' $active>"; warehouseList($warehouse_from); echo"</select>
						</div></div>
	<div class='col-xs-3'><div class='input-group'>
						<span class='input-group-addon'>ไปที่</span>";
						echo"
						<select class='form-control' name='warehouse_to' $active>"; warehouseList($warehouse_to); echo"</select>
						</div></div></div>
						<div class='row' style='margin-top:15px;'>
	<div class='col-xs-6'><div class='input-group'><span class='input-group-addon'>หมายเหตุ</span><input type='text' id='comment' name='comment' class='form-control' value='$comment' autocomplete='off' $active/></div> </div>
	<div class='col-xs-2'><button class='btn btn-default' type='submit' id='add_order' $active>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button></div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	if(isset($id_tranfer)){
	echo "<div class='row'><div class='col-xs-10'><div id='from_zone' style='display:none;'>
		<table border='0' width='80%' align='center' id='from_con'>
			<tr>
				<td width='65%'>
					<div class='input-group'>
						<span class='input-group-addon'>ชื่อโซน,บาร์โค้ดโซน ที่ออก</span>
						<input type='text' name='zone' id='zone' class='form-control' placeholder='' required autofocus >
					</div>
				</td>
				<td width='15%' align='center'><div class='input-group' id='load'><input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='check_zone()' /></div></td>
				<td width='20%' align='right'></td>
			</tr>
		</table>
	</div>
	<div id='from_move_all' style='display:none;'>
		<table border='0' width='80%' align='center' id='from_con'>
			<tr>
				<td width='65%'>
					<div class='input-group'>
						<span class='input-group-addon'>ชื่อโซน,บาร์โค้ดโซน ที่เข้า</span>
						<input type='text' name='zone_in_all' id='zone_in_all' class='form-control' placeholder='' required autofocus >
					</div>
				</td>
				<td width='15%' align='center'><div class='input-group' id='load4'><input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='move_all()' /></div></td>
				<td width='20%' align='right'></td>
			</tr>
		</table>
	</div>
	<div id='from_move_in' style='display:none;'>
		<table border='0' width='100%' align='center' id='from_con'>
			<tr>
				<td width='35%'>
					<div class='input-group'>
						<span class='input-group-addon'>สินค้า</span>
						<input type='text' id='name_item' class='form-control' placeholder='' disabled='disabled' >
					</div>
					</td>
					<td width='5%' align='center'></td>
				<td width='50%' align='center'><div class='input-group'>
						<span class='input-group-addon'>เข้าโซน ชื่อโซน,บาร์โค้ดโซน</span>
						<input type='text' name='zone' id='zone_in' class='form-control' placeholder='' required autofocus >
					</div></td>
				<td width='15%' align='right'><div class='input-group' id='load2'><input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='move_in()' /></div></td>
			</tr>
		</table>
	</div>
	<div id='from_out' style='display:none;' >
		<table border='0' width='100%' align='center' id='from_con'>
			<tr><td width='15%' align='center'><div class='input-group'><input type='checkbox' id='checkboxes' onclick='check_import()' /><label for='auto_zone' style='margin-left:10px;'>ย้ายทั้งหมด</label></div></td><td width='75%' align='center'>
			<div id='move_out1'><table border='0' width='100%' align='center' id='from_con'>
			<tr><td width='20%'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' name='qty' id='qty' class='form-control' placeholder='' value='1'  ></div></td><td width='5%'></td><td width='40%'><div class='input-group'><span class='input-group-addon'>บาร์โค้ด</span><input type='text' name='barcode_item' id='barcode_item' class='form-control' ></div></td><td width='10%' align='center'><div id='load1'><input type='submit' class='btn btn-primary' onclick='moveout()' value='ย้าย' /></div></td></tr></table></div>
			<div id='move_out2' style='display:none;' ><table border='0' width='100%' align='center' id='from_con'>
			<tr><td width='20%'><div id='load3'><input type='submit' class='btn btn-primary' onclick='moveout_all()' value='ดำเนินการย้าย' /></div></td><td width='5%'></td><td width='40%'></td><td width='10%' align='center'></td></tr></table></div>
			</td>
			<td width='10%' align='right'><input type='submit' id='change_zone' class='btn btn-default' onclick='new_zone()' value='โซนไหม่ (F2)' /></td></tr>
		</table>
	</div>
	<div id='menu_main' style='' >
		<input type='submit' id='item_move' class='btn btn-default' onclick='menu_main()' value='ย้ายสินค้าออก' />&nbsp;&nbsp;<input type='submit' id='item_move' class='btn btn-default' onclick='click_move_all()' value='ย้ายสินค้าเข้าทั้งหมด' />
	</div>
	</div>
	<div class='col-xs-2'>
		<input type='submit' id='item_move' class='btn btn-default' onclick='item_move()' value='สินค้าที่ย้าย' />
	</div></div>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<input type='hidden' id='id_zone'  ><input type='hidden' id='id_tranfer' value='$id_tranfer' ><input type='hidden' id='id_product_attribute'  ><input type='hidden' id='qty_in'  >
	<div id='tables'>
	<table class='table table-bordered'><thead id='head'><th style='width:5%;' >ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th><th style='width:10%;text-align:center;'>ย้ายจาก</th><th style='width:10%;text-align:center;'>ไปที่</th><th style='width:10%;text-align:center;'>จำนวน</th><th  style='width:10%; text-align:center;'>การกระทำ</th></thead>";
				$sql = dbQuery("SELECT id_tranfer_detail,id_product_attribute,id_zone_from,id_zone_to,tranfer_qty,valid FROM tbl_tranfer_detail WHERE id_tranfer = '$id_tranfer' ORDER BY id_product_attribute ASC");
				$row = dbNumRows($sql);
				$n = 1;
				$i = 0;
				if($row>0){
				while($i<$row){
					list($id_tranfer_detail,$id_product_attribute,$id_zone_from,$id_zone_to,$tranfer_qty,$valid)= dbFetchArray($sql);
					$product = new product();
					$product->product_attribute_detail($id_product_attribute);
					$reference = $product->reference;
					$barcode = $product->barcode;
					list($name_zone_from) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_from"));
					if($valid == "0"){
						$name_zone_to = "<button type='button' class='btn btn-link' onclick=\"click_move_in($id_product_attribute,'$reference',$tranfer_qty)\"><span class='glyphicon glyphicon-log-in' style='color:#5cb85c; font-size:16px;'></span></button>";	
					}else{
						list($name_zone_to) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_to"));
					}
					echo"<tr id='row$id_product_attribute'><td style='text-align:center; vertical-align:middle;'>$n</td><td style='vertical-align:middle;'>$barcode</td><td style='vertical-align:middle;'>$reference</td><td style='text-align:center; vertical-align:middle;'>$name_zone_from</td><td style='text-align:center; vertical-align:middle;'>$name_zone_to</td><td style='text-align:center; vertical-align:middle;'>$tranfer_qty</td>
					
			<td align='center'>
					<button class='btn btn-danger btn-xs' onclick='delete_detail($id_tranfer_detail)'>
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
			</td></tr>";
					$i++;
					$n++;	}
				}else{
					echo"<tr id='row'><td style='text-align:center; vertical-align:middle;' colspan='7' align='center'><h4>ไม่มีรายการสินค้าที่ย้าย</h4></td></tr>";
				}echo "<div></div>";
	}
}else{
		echo"<form  method='post' id='form'>
		<div class='row'>
			<div class='col-xs-2 col-xs-offset-4'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date'  value='";
					 if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['from_date']));} else { echo "เลือกวัน";} 
					 echo "'/>
				</div>		
			</div>	
			<div class='col-xs-2 '>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' value='";
				  if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['to_date']));} else{ echo "เลือกวัน";}  echo"' />
				</div>
			</div>
			<div class='col-xs-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>	
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<div class='row'>
<div class='col-xs-12'>
	<table class='table table-striped table-hover'>
    	<thead style='background-color:#48CFAD;'>
        	<th style='width:10%; text-align:center;'>ลำดับ</th><th style='width:20%;'>อ้างอิง</th>
            <th style='width:10%; text-align:center;'>ย้ายจาก</th><th style='width:10%; text-align:center;'>ไปที่</th>
			<th style='width:10%; text-align:center;'>พนักงาน</th>
			<th style='width:10%; text-align:center;'>วันที่</th>
			<th colspan='2' style='width:10%; text-align:center;'>การกระทำ</th>
        </thead>";
		$view = "";
		if(isset($_POST['from_date'])){	$from = date('Y-m-d',strtotime($_POST['from_date'])); }else{ $from = "";} if(isset($_POST['to_date'])){  $to =date('Y-m-d',strtotime($_POST['to_date'])); }else{ $to = "";}
		if($from==""){
			if($to==""){
				$view = "week";
				$date = getLastDays($view);
				$from = $date['from'];
				$to = $date['to'];
			}
		}
		$result = dbQuery("SELECT id_tranfer,id_employee,warehouse_from,warehouse_to,reference,date_add FROM tbl_tranfer WHERE (date_add BETWEEN '$from' AND '$to') ORDER BY id_tranfer DESC");
		$i=0;
		$n=1;
		$row = dbNumRows($result);
		if($row<1){ echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}else{
		while($i<$row){
			list($id,$id_employee,$warehouse_from,$warehouse_to,$reference,$date_add) = dbFetchArray($result);
			list($from) = dbFetchArray(dbQuery("SELECT warehouse_name FROM tbl_warehouse WHERE id_warehouse = $warehouse_from"));
			list($to) = dbFetchArray(dbQuery("SELECT warehouse_name FROM tbl_warehouse WHERE id_warehouse = $warehouse_to"));
			$employee = new employee($id_employee);
			echo "<tr>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=tranfer&add=y&id_tranfer=$id'\">$n</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=tranfer&add=y&id_tranfer=$id'\">$reference</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=tranfer&add=y&id_tranfer=$id'\">$from</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=tranfer&add=y&id_tranfer=$id'\">$to</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=tranfer&add=y&id_tranfer=$id'\">".$employee->first_name."</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=tranfer&add=y&id_tranfer=$id'\">".showDate($date_add)."</td>
			<td align='center'>
				<a href='index.php?content=tranfer&add=y&id_tranfer=$id'' $can_edit>
					<button class='btn btn-warning btn-sx'>
						<span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>
					</button>
				</a>
			</td>
			<td align='center'>
					<button class='btn btn-danger btn-sx' onclick='delete_tranfer($id)'>
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
			</td>
			</tr>";
			$i++;
			$n++;
		}
		echo"       
    </table>
</div> </div>";
}
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
    $("#doc_date").datepicker({
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
//// เมื่อยิงบาร์โค้ด หรือ ใส่รหัสด้วยมือแล้ว enter////
///************  บาร์โค้ดโซน **********************//
$("#zone").bind("enterKey",function(){
	if($("#zone").val() != ""){
		check_zone();
	}else{
		alert("ยังไม่ได้ใส่ชื่อหรือบาร์โค้ดโซน");
	}
});
$("#zone").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function check_zone(){
	var zone = $("#zone").val();
	var id_tranfer = $("#id_tranfer").val();
	$("#submit").focus();
	$("#load").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
	$.ajax({
		url:"controller/tranferController.php?check_zone&zone="+zone+"&id_tranfer="+id_tranfer,
		type:"GET", cache:false, 
		success: function(data){
			var arr = data.split('!');
			var status = arr[0];
			if(status == "ok"){
				var id_zone = arr[1];
				var table = arr[2];
				///alert(table);
				$("#tables").html(table);
				$("#id_zone").val(id_zone);
				$("#zone").val("");
				$("#from_zone").css("display","none");
				$("#from_out").css("display","");
				$("#menu_main").css("display","none");	
				$("#from_move_in").css("display","none");	
				$("#qty").focus();
			}else{
				var mes = arr[1];
				$("#tables").html("");
				alert(mes);
				
			}
			$("#load").html("<button class='btn btn-default' type='button' id='add_detail' onclick='check_zone()'>&nbsp&nbsp;ตกลง&nbsp;&nbsp</button>");
		}
	});
}
///***************** รหัสโซน *********************///
$("#barcode_item").bind("enterKey",function(){
	if($("#barcode_item").val() != ""){
	moveout();
	}
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
///********************* จำนวนสินค้า ***********************///
$("#qty").bind("enterKey",function(){
	if($("#qty").val() != ""){
	$("#barcode_item").focus();
	}
});
$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function moveout(){
	var barcode_item = $("#barcode_item").val();
	var qty = $("#qty").val();
	var id_zone = $("#id_zone").val();
	var id_tranfer = $("#id_tranfer").val();
	if(qty != ""){
		if(barcode_item != ""){
			$("#submit").focus();
			$("#load1").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
			$.ajax({
				url:"controller/tranferController.php?moveout&id_zone="+id_zone+"&id_tranfer="+id_tranfer+"&barcode_item="+barcode_item+"&qty="+qty,
				type:"GET", cache:false, 
				success: function(data){
					var arr = data.split('!');
					var status = arr[0];
					if(status == "ok"){
						var id_zone = arr[1];
						var table = arr[2];
						$("#tables").html(table);
						$("#barcode_item").val("");
						$("#qty").focus();
					}else{
						var mess = arr[1];
						alert(mess);
					}
					$("#load1").html("<input type='submit' class='btn btn-primary' onclick='moveout()' value='ย้าย' />");
				}
			});
		}else{
			alert("ยังไม่ได้ใส่บาร์โค้ด");
		}
	}else{
		alert("ยังไม่ได้ใส่จำนวน");
	}
}
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
function new_zone(){
	$("#from_zone").css("display","");
	$("#from_out").css("display","none");	
	$("#from_move_in").css("display","none");	
	$("#tables").html("");
	$("#zone").focus();
}
function item_move(){
	var id_tranfer = $("#id_tranfer").val();
	$.ajax({
		url:"controller/tranferController.php?item_move&id_tranfer="+id_tranfer,
		type:"GET", cache:false, 
		success: function(table){
			$("#tables").html(table);
			$("#from_zone").css("display","none");
			$("#from_out").css("display","none");	
			$("#menu_main").css("display","");	
			$("#from_move_in").css("display","none");	
			$("#from_move_all").css("display","none");
		}
	});
}
function menu_main(){
	$("#tables").html("");
	$("#from_zone").css("display","");
	$("#from_out").css("display","none");	
	$("#menu_main").css("display","none");	
	$("#from_move_all").css("display","none");
	$("#zone").focus();
}
function click_move_in(id_product_attribute,reference,qty){
	$("#id_product_attribute").val(id_product_attribute);
	$("#name_item").val(reference);
	$("#qty_in").val(qty);
	$("#from_zone").css("display","none");
	$("#from_out").css("display","none");	
	$("#menu_main").css("display","none");
	$("#from_move_in").css("display","");	
	$("#from_move_all").css("display","none");
	$("#zone_in").focus();
}
$("#zone_in").bind("enterKey",function(){
	if($("#zone_in").val() != ""){
		move_in();
	}else{
		alert("ยังไม่ได้ใส่บาร์โค้ดโซน");
	}
});
$("#zone_in").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function move_in(){
	var id_product_attribute = $("#id_product_attribute").val();
	var zone_in = $("#zone_in").val();
	var id_tranfer = $("#id_tranfer").val();
	var qty_in = $("#qty_in").val();
	if(zone_in == ""){
		alert("ยังไม่ได้ใส่ชื่อหรือบาร์โค้ดโซน");
	}else{
		$("#load2").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
		$.ajax({
			url:"controller/tranferController.php?move_in&id_tranfer="+id_tranfer+"&zone_in="+zone_in+"&id_product_attribute="+id_product_attribute+"&qty_in="+qty_in,
			type:"GET", cache:false, 
			success: function(data){
				var arr = data.split('!');
				var status = arr[0];
				//alert(status);
				if(status == "ok"){
					var table = arr[1];
					$("#tables").html(table);
					$("#id_product_attribute").val("");
					$("#name_item").val("");
					$("#qty_in").val("");
					$("#from_zone").css("display","none");
					$("#from_out").css("display","none");	
					$("#menu_main").css("display","");
					$("#from_move_in").css("display","none");	
					$("#from_move_all").css("display","none");
				}else{
					var mess = arr[1];
					alert(mess);
					$("#zone_in").val("");
					$("#zone_in").focus();
				}
				$("#load2").html("<input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='move_in()' />");
			}
		});
	}
}
$("#zone_in_all").bind("enterKey",function(){
	if($("#zone_in_all").val() != ""){
		move_all();
	}else{
		alert("ยังไม่ได้ใส่บาร์โค้ดโซน");
	}
});
$("#zone_in_all").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function move_all(){
	var id_tranfer = $("#id_tranfer").val();
	var zone_in_all = $("#zone_in_all").val();
	if(zone_in_all == ""){
		alert("ยังไม่ได้ใส่ชื่อหรือบาร์โค้ดโซน");
	}else{
		$("#load4").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
		$.ajax({
			url:"controller/tranferController.php?move_in_all&id_tranfer="+id_tranfer+"&zone_in_all="+zone_in_all,
			type:"GET", cache:false, 
			success: function(data){
				var arr = data.split('!');
				var status = arr[0];
				if(status == "ok"){
					var table = arr[1];
					$("#tables").html(table);
					$("#id_product_attribute").val("");
					$("#name_item").val("");
					$("#qty_in").val("");
					$("#from_zone").css("display","none");
					$("#from_out").css("display","none");	
					$("#menu_main").css("display","");
					$("#from_move_in").css("display","none");	
					$("#from_move_all").css("display","none");	
				}else{
					var mess = arr[1];
					alert(mess);
					$("#zone_in").val("");
					$("#zone_in").focus();
				}
				$("#load4").html("<input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='move_in()' />");
			}
		});
	}
}
function click_move_all(){
	$("#menu_main").css("display","none");
	$("#from_move_all").css("display","");
	$("#zone_in_all").focus();
}
function check_import(){
	if(checkboxes.checked){
		$("#move_out1").css("display","none");
		$("#move_out2").css("display","");
	}else{
		$("#move_out2").css("display","none");
		$("#move_out1").css("display","");
	}
}
function moveout_all(){
	var id_tranfer = $("#id_tranfer").val();
	var id_zone = $("#id_zone").val();
	$("#load3").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
			$.ajax({
				url:"controller/tranferController.php?moveout_all&id_zone="+id_zone+"&id_tranfer="+id_tranfer,
				type:"GET", cache:false, 
				success: function(data){
					var arr = data.split('!');
					var status = arr[0];
					if(status == "ok"){
						var table = arr[1];
						$("#tables").html(table);
					}else{
						var mess = arr[1];
						alert(mess);
					}
					$("#load3").html("<input type='submit' class='btn btn-primary' onclick='moveout_all()' value='ดำเนินการย้าย' />");
				}
			});
}
function delete_detail(id_tranfer_detail){
	var id_tranfer = $("#id_tranfer").val();
	if(confirm('ต้องการลบหรือไม่') == true){	
	$.ajax({
				url:"controller/tranferController.php?delete&id_tranfer_detail="+id_tranfer_detail+"&id_tranfer="+id_tranfer,
				type:"GET", cache:false, 
				success: function(data){
					var arr = data.split('!');
					var status = arr[0];
					if(status == "ok"){
						var table = arr[1];
						$("#tables").html(table);
					}else{
						var mess = arr[1];
						alert(mess);
					}
				}
		});
	}
}
function delete_tranfer(id_tranfer){
	if(confirm('ต้องการลบหรือไม่') == true){	
	$.ajax({
				url:"controller/tranferController.php?delete_tranfer&id_tranfer="+id_tranfer,
				type:"GET", cache:false, 
				success: function(data){
					var arr = data.split('!');
					var status = arr[0];
					if(status == "ok"){
						location.reload();
					}else{
						var mess = arr[1];
						alert(mess);
					}
				}
		});
	}
}
$(document).ready(function(e) {
    $("#zone_name").autocomplete({
		source:"controller/tranferController.php?autozone&zone_name="+zone_name,
		autoFocus: true,
		close: function(event,ui){
			var data = $("#zone_name").val();
		}
	});			
});
      </script>
