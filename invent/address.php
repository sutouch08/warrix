<?php 
	$page_menu = "invent_address";
	$page_name = "ที่อยู่";
	$id_tab = 22;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($view==0){ echo accessDeny(); exit; }
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	?>
<div class="container">
<!-- page place holder -->
<?php if(isset($_GET['edit'])){
	echo"<form id='address_form' action='controller/addressController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form id='address_form' action='controller/addressController.php?add=y' method='post'>";
}
?>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-home"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_address'])){
		    echo"
		   <li><a href='index.php?content=address' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=address' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='validate()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	  		}else if(isset($_GET['view_detail'])){
		   echo"
		   <li><a href='index.php?content=address' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=address&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
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
//*********************************************** เพิ่มที่อยู่ใหม่********************************************************// 
if(isset($_GET['add'])){
	echo"
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp; <input type='hidden' name='id_customer' id='id_customer' /></td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='first_name' id='first_name' class='form-control input-sm' "; if(isset($_GET['first_name'])){echo"value='".$_GET['first_name']."'";} echo" autofocus  /></td>
		<td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='last_name' id='last_name' class='form-control input-sm' "; if(isset($_GET['last_name'])){echo"value='".$_GET['last_name']."'";} echo" /></td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'></sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>บริษัท :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='company' id='company' class='form-control input-sm' "; if(isset($_GET['company'])){echo"value='".$_GET['company']."'";} echo"  /></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เลขประจำตัว :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='id_number' id='id_number' class='form-control input-sm' "; if(isset($_GET['id_number'])){echo"value='".$_GET['id_number']."'";} echo"  />
			<span class='help-block'>เลขประจำตัวผู้เสียภาษี หรือ เลขประจำตัวประชาชน </span>	</td><td style='padding-left:15px;'>&nbsp;</td>
	</tr>

	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ที่อยู่ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='address1' id='address1' class='form-control input-sm' "; if(isset($_GET['address1'])){echo"value='".$_GET['address1']."'";} echo"  /></td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ที่อยู่บรรทัด 2 :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='address2' id='address2' class='form-control input-sm' "; if(isset($_GET['address2'])){echo"value='".$_GET['address2']."'";} echo"  /></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>จังหวัด :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
		<select name='city' id='city' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px'>"; 
		if(isset($_GET['city'])){echo $city = $_GET['city'];}else{ $city ="";}	 selectCity($city); echo"</td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>รหัสไปรษณีย์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='postcode' id='postcode' class='form-control input-sm'  "; if(isset($_GET['postcode'])){echo"value='".$_GET['postcode']."'";} echo" /></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เบอร์โทรศัพท์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='phone' id='phone' class='form-control input-sm' "; if(isset($_GET['phone'])){echo"value='".$_GET['phone']."'";} echo"  />
			<span class='help-block'>ต้องมีอย่างน้อย 1 เบอร์</span></td><td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
		<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ชื่อสำหรับเรียกที่อยู่ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='alias' id='alias' class='form-control input-sm'  "; if(isset($_GET['alias'])){echo"value='".$_GET['alias']."'";} echo"  />
			<span class='help-block'>เช่น ที่ทำงาน, บ้าน, ที่อยู่ของฉัน เป็นต้น</span></td><td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>อื่นๆ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><textarea class='form-control input-sm' name='other' id='other' rows='8'>"; if(isset($_GET['other'])){echo $_GET['other'];} echo"</textarea></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<span class='help-block'><sub style='color:red;'>*</sub> ข้อมูลที่จำเป็นต้องกรอก</span></td><td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	</table></form>
	";
	
//**************************************** จบหน้าเพิ่มที่อยู่ **********************************************//
}else if(isset($_GET['edit'])&&isset($_GET['id_address'])){
//******************************************  หน้าแก้ไขที่อยู่ *****************************************************//
$id_address = $_GET['id_address'];
$sql= dbQuery("SELECT tbl_customer.id_customer, tbl_customer.first_name, tbl_customer.last_name, tbl_customer.email FROM tbl_customer LEFT JOIN tbl_address ON tbl_customer.id_customer = tbl_address.id_customer WHERE id_address = $id_address");
list($id_customer, $first_name, $last_name, $email) = dbFetchArray($sql);
$data = getAddressDetail($id_address);
echo"
<table width='100%' border='0'>
	<tr><td colspan='3'><input type='hidden' id='email' value='$email' /><button type='submit' id='btn_submit' style='display:none'>submit</button></td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='first_name' id='first_name' class='form-control input-sm' value='".$data['firstname']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='last_name' id='last_name' class='form-control input-sm' value='".$data['lastname']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>บริษัท :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='company' id='company' class='form-control input-sm' value='".$data['company']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เลขประจำตัว :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='id_number' id='id_number' class='form-control input-sm' value='".$data['id_number']."' />
			<span class='help-block'>เลขประจำตัวผู้เสียภาษี หรือ เลขประจำตัวประชาชน </span>	</td><td style='padding-left:15px;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ที่อยู่ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='address1' id='address1' class='form-control input-sm' value='".$data['address1']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ที่อยู่บรรทัด 2 :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='address2' id='address2' class='form-control input-sm' value='".$data['address2']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>จังหวัด :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
		<select name='city' id='city' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px'>"; selectCity($data['city']); echo"</td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>รหัสไปรษณีย์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='postcode' id='postcode' class='form-control input-sm' value='".$data['postcode']."'/></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เบอร์โทรศัพท์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='phone' id='phone' class='form-control input-sm' value='".$data['phone']."' />
			<span class='help-block'>ต้องมีอย่างน้อย 1 เบอร์</span></td><td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ชื่อสำหรับเรียกที่อยู่ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='alias' id='alias' class='form-control input-sm' value='".$data['alias']."'  />
			<span class='help-block'>เช่น ที่ทำงาน, บ้าน, ที่อยู่ของฉัน เป็นต้น</span></td><td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>อื่นๆ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><textarea class='form-control input-sm' name='other' id='other' rows='8'>".$data['other']."</textarea></td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<span class='help-block'><sub style='color:red;'>*</sub> ข้อมูลที่จำเป็นต้องกรอก</span></td><td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	</table></form>
	";

//****************************************** จบหน้าแก้ไข ****************************************************//
}else if(isset($_GET['view_detail'])&&isset($_GET['id_address'])){
//////////////////////////////////// แสดงรายละเอียด  //////////////////////////////////
echo"</form>";
	$id_address = $_GET['id_address'];
	$data = getAddressDetail($id_address);
	echo"
	<table width='100%' border='0'>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ชื่อสำหรับเรียกที่อยู่ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$data['alias']."</td><td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$data['firstname']."&nbsp;".$data['lastname']."</td>
		<td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เลขประจำตัว :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$data['id_number']."</td><td style='padding-left:15px;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>บริษัท :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$data['company']."</td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ที่อยู่ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$data['address1']."&nbsp;".$data['address2']."&nbsp;".$data['city']."&nbsp;".$data['postcode']."</td>
		<td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เบอร์โทรศัพท์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$data['phone']."</td><td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>อื่นๆ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$data['other']."</td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	</table>";
	
///////////////////////////////////// จบหน้าแสดงรายละเอียด  //////////////////////////
}else{
echo"</form>";
///////////////////////////////// แสดงรายการ ////////////////////////////////
echo "<div class='col-sm-12'><form action='' method='post' >
				<div class='col-lg-4 col-md-4 col-sm-8 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-2'>
				<div class='input-group'>
					<span class='input-group-addon'> ค้นหา</span>
						<input type='text' name='search' class='form-control' >
					</div>
				</div>
				</form></div><div class='col-sm-12'>
				";	
$paginator = new paginator();
if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
if(isset($_POST['search'])){
	$search = $_POST['search'];
	$sql = dbQuery("SELECT id_address, firstname, lastname, address1, address2, city, postcode FROM tbl_address WHERE firstname LIKE '%$search%' OR address1 LIKE '%$search%' OR address2 LIKE '%$search%' OR city LIKE '%$search%' OR postcode LIKE '%$search%'");
}else{
$paginator->Per_Page("tbl_address","",$get_rows);
$paginator->display($get_rows,"index.php?content=address");
$Page_Start = $paginator->Page_Start;
$Per_Page = $paginator->Per_Page;
$sql = dbQuery("SELECT id_address, firstname, lastname, address1, address2, city, postcode FROM tbl_address LIMIT $Page_Start , $Per_Page");
}
echo"
</div><div class='row'>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
	<thead style='background-color:#48CFAD;'>
		<th style='width:5%; text-align:center;'>ID</th><th style='width:20%;'>ชื่อ</th><th style='width:40%;'>ที่อยู่</th>
		<th style='width:15%;'>จังหวัด</th><th style='width:10%;'>รหัสไปรษณีย์</th>
		<th colspan='2' style='text-align:center;'>การกระทำ</th>
	</thead>";
	
	$row = dbNumRows($sql);
	$i=0;
	if($row>0){
		while($i<$row){
			list($id_address, $first_name, $last_name, $address1, $address2, $city, $post_code) = dbFetchArray($sql);
			echo" <tr>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=address&view_detail=y&id_address=$id_address'\">$id_address</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=address&view_detail=y&id_address=$id_address'\">$first_name &nbsp; $last_name</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=address&view_detail=y&id_address=$id_address'\">$address1 &nbsp; $address2</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=address&view_detail=y&id_address=$id_address'\">$city</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=address&view_detail=y&id_address=$id_address'\">$post_code</td>
					<td align='center'>
						<a href='index.php?content=address&edit=y&id_address=$id_address' $can_edit>
							<button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button>
						</a>
					</td>
					<td align='center' >
						<a href='controller/addressController.php?delete=y&id_address=$id_address' $can_delete>
							<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบที่อยู่นี้');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button>
						</a>
					</td>
					</tr>";
					$i++;
		}
	}else if($row<1){
		echo"<tr><td colspan='7' align='center'><h4>ยังไม่มีรายการ</h4></td></tr>";
	}
	echo"</table></div></div>";
	echo $paginator->display_pages();
		echo "<br><br>";
//////////////////////////////// จบแสดงรายการ //////////////////////////////////////////
}
?>
</div>
<script>
$("#first_name").keyup(function(e) {
	var comp = $("#company").val();
    if(comp !=""){
		$(this).autocomplete({ disabled:true });
	}else{
    	$("#first_name").autocomplete({ 
			disabled: false,	source:"controller/addressController.php?first_name",
			autoFocus: true,
			close: function(event,ui){
			$("#id_customer").val('');
			var data = $("#first_name").val();
			if(data !=""){
			var arr = data.split(':');
			var id = arr[0];
			$.ajax({
				url:"controller/addressController.php?get_data&id_customer="+id, type:"GET", 
				cache:false, success: function(datas){
					if(datas !="no_data"){
					var ar = datas.split(":");
					var company = ar[0];
					var first_name = ar[1];
					var last_name = ar[2];
					$("#id_customer").val(id);
					$("#first_name").val(first_name);
					$("#last_name").val(last_name);
					$("#company").val(company);
					}
				}
			});	
			}
		}
	});
	}
 });

$("#company").keyup(function(e) {
	var c_name = $("#first_name").val();
    if(c_name !=""){
		$(this).autocomplete({ disabled:true });
	}else{
    	$("#company").autocomplete({ 
			disabled: false,	source:"controller/addressController.php?first_name",
			autoFocus: true,
			close: function(event,ui){
			$("#id_customer").val('');
			var data = $("#company").val();
			if(data !=""){
			var arr = data.split(':');
			var id = arr[0];
			$.ajax({
				url:"controller/addressController.php?get_data&id_customer="+id, type:"GET", 
				cache:false, success: function(datas){
					if(datas !="no_data"){
					var ar = datas.split(":");
					var company = ar[0];
					var first_name = ar[1];
					var last_name = ar[2];
					$("#id_customer").val(id);
					$("#first_name").val(first_name);
					$("#last_name").val(last_name);
					$("#company").val(company);
					}
				}
			});	
			}
		}
	});
	}
 });

function validate(){
	var id_customer = $("#id_customer").val();
	var alias = $("#alias").val();
	var first_name = $("#first_name").val();
	var address1 = $("#address1").val();
	var company = $("#company").val();
	var city = $("#city").val();
	var phone = $("#phone").val();
	if(first_name ==""){
		if(company ==""){
			alert("กรุณาระบุชื่อ หรือ บริษัท อย่างน้อยอย่างใดอย่างหนึ่ง");
			$("#first_name").focus();
		}else if(alias ==""){
		alert("กรุณาระบุชื่อสำหรับเรียกที่อยู่");
		$("#alias").focus();
		}else  if( address1 == ""){
		alert("กรุณาใส่ที่อยู่");
		$("#address1").focus();
		}else if(city ==""){
		alert("กรุณาเลือกจังหวัด");
		$("#city").focus();
		}else if(phone ==""){
		alert("กรุณาใส่เบอร์โทรอย่างน้อย 1 เบอร์");
		$("#phone").focus();
		}else if(id_customer ==""){
			alert("ไม่พบ ID ลูกค้า กรุณาเลือกลูกค้าอีกครั้ง");
			$("#first_name").focus();
		}else{
			$("#address_form").submit();
		}
	}else if(alias ==""){
		alert("กรุณาระบุชื่อสำหรับเรียกที่อยู่");
		$("#alias").focus();
		}else  if( address1 == ""){
		alert("กรุณาใส่ที่อยู่");
		$("#address1").focus();
		}else if(city ==""){
		alert("กรุณาเลือกจังหวัด");
		$("#city").focus();
		}else if(phone ==""){
		alert("กรุณาใส่เบอร์โทรอย่างน้อย 1 เบอร์");
		$("#phone").focus();
		}else if(id_customer ==""){
			alert("ไม่พบ ID ลูกค้า กรุณาเลือกลูกค้าอีกครั้ง");
			$("#first_name").focus();
		}else{
			$("#address_form").submit();
		}
		
}
 function get_row(){
	$("#rows").submit();
}		
</script>