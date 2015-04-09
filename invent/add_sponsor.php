<?php 
	$page_menu = "invent_add_sponsor";
	$page_name = "สปอนเซอร์";
	$id_tab = 24;
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
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_sponsor'])){
		    echo"
		   <li><a href='index.php?content=add_sponsor' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link' onclick='validdate()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=add_sponsor' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'  onclick='validdate()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	  		}else if(isset($_GET['view_detail'])){
		   echo"
		   <li><a href='index.php?content=add_sponsor' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=add_sponsor&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
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
?>
<?php 

if(isset($_GET['add'])){
	/****************************** start เพิ่มรายชื่อสปอนเซอร์ **********************************/
	echo"
	<form id='add_sponsor_form' action='controller/sponsorController.php?add_member=y' method='post'>
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อ :&nbsp;</td><input type='hidden' name='id_customer' id='id_customer' />
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='name' id='name' class='form-control input-sm' required='required' /></td>
		<td style='vertical-align:text-top; padding-left:15px;'>
			<div class='row'><div class='col-lg-1' ><span style='color:red;'>*</span><span>&nbsp;&nbsp;หรือ&nbsp;&nbsp;</span></div><div class='col-lg-7'>&nbsp;&nbsp;<a href='index.php?content=customer&add=y' target='_blank'><button type='button' class='btn btn-default btn-sm'>เพิ่มรายชื่อ</button></a></div></div>
		</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>อ้างอิง :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='reference' id='reference' class='form-control input-sm' />
		<span class='help-block'>เลขที่เอกสารอ้างอิง/สัญญา/อื่นๆ</span>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>วงเงิน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='limit_amount' id='limit_amount' class='form-control input-sm' pattern='[0-9_.]{1,20}' title='ตัวเลขเท่านั้น'  required='required'/>
		<span class='help-block'>จำกัดวงเงินในการให้สปอนเซอร์สำหรับรายการนี้</span>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;' >เริ่ม :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<div class='row'>
				<div class='col-lg-5'><input type='text' name='start_date' id='start_date' class='form-control input-sm' required='required'/></div>
				<div class='col-lg-2'><p class='pull-right' style='margin-right:-25px;'>สิ้นสุด :</p></div><div class='col-lg-5'><input type='text' name='end_date' id='end_date' class='form-control input-sm' required='required' /></div>	
			</div>	
			<span class='help-block'>จำกัดระยะเวลาในการให้สปอนเซอร์นี้</span>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>งบประมาณปี :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
		<select id='year' name='year' class='form-control'>";
			  for($i = date("Y")-5; $i < date("Y")+10; $i++){
				  echo '<option value="'.$i.'"';if(date("Y") =="$i"){echo  'selected="selected"';}echo '>'.$i.'</option>';
			  }
			echo "
		</select>
		<span class='help-block'>ใส่ปีงบประมาณที่ใช้</span>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>สถานะ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>			
				<input type='radio' name='active' id='yes' value='1' checked='checked' />
				<label for='yes' style='margin-left:5px; margin-right:25px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>
				<input type='radio' name='active' id='no' value='0' />
				<label for='no' style='margin-left:5px; margin-right:25px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label>
			</td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>หมายเหตุ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><textarea name='remark' id='remark' class='form-control input-sm' ></textarea>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'><button type='submit' id='add_btn' style='display:none;'>บันทึก</button></td>
	</tr>
	</table>	
	</form>
	";
	/****************************** end เพิ่มรายชื่อสปอนเซอร์ **********************************/
}else if(isset($_GET['edit'])&&isset($_GET['id_sponsor'])){
	/****************************** start แก้ไขรายชื่อสปอนเซอร์ **********************************/
	$id_sponsor = $_GET['id_sponsor'];
	$sql = dbQuery("SELECT * FROM tbl_sponsor WHERE id_sponsor = $id_sponsor");
	$data = dbFetchArray($sql);
	$customer = new customer($data['id_customer']);
	$id_customer = $customer->id_customer;
	$customer_name = $customer->full_name;
	$reference = $data['reference'];
	$limit_amount = $data['limit_amount'];
	$start_date = thaiDate($data['start']);
	$end_date = thaiDate($data['end']);
	$remark = $data['remark'];
	$active = $data['active'];
	$year = $data['year'];
	if($active == 1){ 
		$yes = "checked='checked'";
		$no = "";
		}else{ 
		$yes = "";
		$no = "checked='checked'";
		 }
	echo"
	<form id='edit_sponsor_form' action='controller/sponsorController.php?edit_member=y&id_sponsor=$id_sponsor' method='post'>
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อ :&nbsp;</td><input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='name' id='name' class='form-control input-sm' required='required'  value='$customer_name' /></td>
		<td style='vertical-align:text-top; padding-left:15px;'><span style='color:red;'>*</span></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>อ้างอิง :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='reference' id='reference' class='form-control input-sm' value='$reference' />
		<span class='help-block'>เลขที่เอกสารอ้างอิง/สัญญา/อื่นๆ</span>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>วงเงิน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='limit_amount' id='limit_amount' class='form-control input-sm' value='$limit_amount' pattern='[0-9_.]{1,20}' title='ตัวเลขเท่านั้น'  required='required'/>
		<span class='help-block'>จำกัดวงเงินในการให้สปอนเซอร์สำหรับรายการนี้</span>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;' >เริ่ม :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<div class='row'>
				<div class='col-lg-5'><input type='text' name='start_date' id='start_date' class='form-control input-sm' value='$start_date' required='required'/></div>
				<div class='col-lg-2'><p class='pull-right' style='margin-right:-25px;'>สิ้นสุด :</p></div><div class='col-lg-5'><input type='text' name='end_date' id='end_date' class='form-control input-sm' value='$end_date' required='required' /></div>	
			</div>	
			<span class='help-block'>จำกัดระยะเวลาในการให้สปอนเซอร์นี้</span>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>งบประมาณปี :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
		<select id='year' name='year' class='form-control'>";
			  for($i = date("Y")-5; $i < date("Y")+10; $i++){
				  echo '<option value="'.$i.'"';if($year =="$i"){echo  'selected="selected"';}echo '>'.$i.'</option>';
			  }
			echo "
		</select>
		<span class='help-block'>ใส่ปีงบประมาณที่ใช้</span>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>สถานะ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
				<input type='radio' name='active' id='yes' value='1' $yes />
				<label for='yes' style='margin-left:5px; margin-right:25px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>
				<input type='radio' name='active' id='no' value='0' $no />
				<label for='no' style='margin-left:5px; margin-right:25px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label>
			</td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>หมายเหตุ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><textarea name='remark' id='remark' class='form-control input-sm' >$remark </textarea>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'><button type='submit' id='add_btn' style='display:none;'>บันทึก</button></td>
	</tr>
	</table>	
	</form>
	";
	/****************************** end แก้ไขรายชื่อสปอนเซอร์ **********************************/
	
}else if(isset($_GET['view_detail'])&&isset($_GET['id_sponsor'])){
/********************************* start รายละเอียด **************************************/	

/********************************* end รายละเอียด **************************************/
}else{
/*********************************  แสดงรายการ **************************************/	
echo"
	<table class='table table-striped table-hover'>
	<thead style='background-color:#48CFAD;'>
			<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:15%;'>ชื่อ</th><th style='width:10%;'>เลขที่อ้างอิง</th><th style='width:15%; text-align:center;'>วงเงิน</th>
			<th style='width:20%; text-align:center;'>ระยะเวลา</th><th style='text-align:center;'>หมายเหตุ</th><th style='width:15%; text-align:center;'></th>
	</thead>";
	$sql = dbQuery("SELECT * FROM tbl_sponsor ORDER BY active DESC, id_sponsor DESC");
	$row = dbNumRows($sql);
	if($row>0){
		$n =1;
		while($rs=dbFetchArray($sql)){
			$id_sponsor = $rs['id_sponsor'];
			$customer = new customer($rs['id_customer']);
			$sponsor_name = $customer->full_name;
			$reference = $rs['reference'];
			$limit_amount = $rs['limit_amount'];
			$period_time = thaiTextDate($rs['start'])." - ".thaiTextDate($rs['end']);
			$remark = $rs['remark'];
			if($rs['active'] == 1){ $active =""; }else{ $active = "style='display:none;'"; }
	echo"<tr>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=sponsor&view_detail=y&id_sponsor=$id_sponsor'\">$n</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=sponsor&view_detail=y&id_sponsor=$id_sponsor'\">$sponsor_name</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=sponsor&view_detail=y&id_sponsor=$id_sponsor'\">$reference</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=sponsor&view_detail=y&id_sponsor=$id_sponsor'\">$limit_amount</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=sponsor&view_detail=y&id_sponsor=$id_sponsor'\">$period_time</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=sponsor&view_detail=y&id_sponsor=$id_sponsor'\">$remark</td>
				<td align='center' ><p class='pull-right' style='margin:0px;'>
						<a href='index.php?content=add_sponsor&edit=y&id_sponsor=$id_sponsor' $can_edit $active>
								<button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button>
						</a>
						<a href='controller/sponsorController.php?delete=y&id_sponsor=$id_sponsor' $can_delete $active>
							<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $sponsor_name ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button>
						</a></p>
				</td>
			</tr>";
			$n++;
		}
	}else{
		echo"<tr><td colspan='6'><h4 style='text-align:center'>ไม่มีรายการ</h4></td></tr>";
	}
	echo "<table>";
			

}

?>
</div>
<script>
$(document).ready(function(e) {
    $("#name").autocomplete({
		source:"controller/orderController.php?customer_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#name").val();
			var arr = data.split(':');
			var id = arr[2];
			var name = arr[1];
			$("#id_customer").val(id);
			$(this).val(name);
		}
	});			
});

$(function() {
    $("#start_date").datepicker({
      dateFormat: 'dd-mm-yy', changeYear: true, onClose: function( selectedDate ) {
        $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
	$("#year").datepicker({
      dateFormat: 'yy',showButtonPanel: true, changeYear: true, onClose: function( selectedDate ) {
		   var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
        $( "#year" ).datepicker('setDate', new Date(year, 1));
      }
    });
    $( "#end_date" ).datepicker({
      dateFormat: 'dd-mm-yy', changeYear: true,  onClose: function( selectedDate ) {
        $( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
 function validdate(){
	  var id_customer = $("#id_customer").val();
	  var name = $("#name").val();
	  var limit = $("#limit_amount").val();
	  var start = $("#start_date").val();
	  var end = $("#end_date").val();
	  if(id_customer ==""){
		  alert("ไม่พบ id_customer โปรดตรวจสอบ");
	  }else if(name == ""){
		  alert("ชื่อไม่ถูกต้อง");
	  }else if(limit ==""){
		  alert("ยังไม่ได้ระบุวงเงิน");
	  }else if(start==""){
		  alert("ไม่ได้ใส่วันที่เริ่ม");
	  }else if(end ==""){
		  alert("ไม่ได้ใส่วันที่สิ้นสุด");
	  }else{
		  $("#add_btn").click();
	  }
 }
</script>