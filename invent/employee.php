<?php 
	$page_menu = "invent_sale";
	$page_name = "พนักงาน";
	$id_tab = 26;
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
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['reset_password'])){
		   echo"<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='reset_password();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=Employee' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='submit_add();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=Employee&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
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
	echo "<form method='post' name='add_employee' action='controller/employeeController.php?add=y' >";
	$id_profile = "";
}else if(isset($_GET['edit'])){
	echo "<form method='post' name='add_employee' action='controller/employeeController.php?edit=y'>";
	$id_employee = $_GET['id_employee'];
	$employee = new employee($id_employee);
	$id_profile = $employee->id_profile;
	echo "<input type='hidden' name='id_employee' id='id_employee' value='".$id_employee."'/>";
}else if(isset($_GET['reset_password'])){
	echo "<form method='post' name='add_employee' action='controller/employeeController.php?reset_password=y'>";
}
if(isset($_GET['edit']) || isset($_GET['add'])){
	echo"
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<input type='text' name='first_name' id='first_name' value='";if(isset($_GET['edit'])){echo $employee->first_name;}echo "' class='form-control input-sm' required='required' autofocus />
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='last_name' id='last_name' value='";if(isset($_GET['edit'])){echo $employee->last_name;}echo "' class='form-control input-sm' required='required'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>อีเมล์/User name :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='email' id='email' value='";if(isset($_GET['edit'])){echo $employee->email;}echo "' class='form-control input-sm' required='required' autocomplete='off'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัสผ่าน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='password' name='password' id='password' class='form-control input-sm'"; if(isset($_GET['add'])){ echo" required='required'";} echo"/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>* </td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>โปรไฟล์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='id_profile' id='id_profile' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectEmployeeGroup($id_profile); echo"</select>
			<span class='help-block'></span></td>
			<td style='padding-bottom:10px; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัสลับ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='password' name='s_key' id='s_key' class='form-control input-sm' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'> </td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>สถานะ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><div class='row'>&nbsp;&nbsp;<input type='radio' name='active' id='yes' value='1' "; if(isset($_GET['edit'])&&$employee->active==1){ echo "checked='checked'" ;} echo" style='margin-left:15px;' /><label for='yes' style='margin-left:5px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='active' id='no' value='0' "; if(isset($_GET['edit'])&&$employee->active==0){ echo "checked='checked'" ;} echo"style='margin-left:15px;' /><label for='no' style='margin-left:5px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label></div></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	</table></form>";
}else if(isset($_GET['reset_password'])&&isset($_GET['id_employee'])){
	$employee = new employee($_GET['id_employee']);
	$id_employee = $employee->id_employee;
	$first_name = $employee->first_name;
	$last_name = $employee->last_name;
	$email = $employee->email;
	echo"
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp; <input type='hidden' name='id_employee' id='id_employee' value='$id_employee' /></td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' value='$first_name' class='form-control input-sm' disabled /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' value='$last_name' class='form-control input-sm' disabled /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>อีเมล์/User name :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='email' id='email' value='$email' class='form-control input-sm' required='required' autocomplete='off'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัสผ่าน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='password' name='password' id='password' class='form-control input-sm' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>* </td>
	</tr>
	<tr><td colspan='3'><button type='submit' id='reset' style='display:none;'>reset</button>
	</table></form>";
	
}else{
	echo "<div class='row'>
	<div class='col-sm-12'>
		<table class='table table-striped table-hover'>
			<thead style='background-color:#48CFAD;'>
				<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:30%;'>ชื่อ</th>
				<th style='width:20%; text-align:center;'>ที่อยู่อีเมล์</th><th style='width:20%; text-align:center;'>โปรไฟล์</th><th style='width:10%; text-align:center;'>สถานะ</th><th style='width:15%; text-align:center;'>การกระทำ</th>
			</thead>";
			$result = dbQuery("SELECT tbl_employee.id_profile,id_employee,first_name,last_name,email,profile_name, active FROM tbl_employee LEFT JOIN tbl_profile ON tbl_employee.id_profile = tbl_profile.id_profile ");
			$i=0;
			$n=1;
			$row = dbNumRows($result);
			while($i<$row){
				list($profile ,$id_employees, $first_name, $last_name, $email, $profile_name, $active) = dbFetchArray($result);
				echo "<tr>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=Employee&edit=y&id_employees=$id_employees'\">$n</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=Employee&edit=y&id_employee=$id_employees'\">$first_name $last_name</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=Employee&edit=y&id_employee=$id_employees'\">$email</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=Employee&edit=y&id_employee=$id_employees'\">$profile_name</td>
				<td align='center' style='vertical-align:middle;'>"; if($active ==1){ if($edit==1){echo "<a href='controller/employeeController.php?active=$active&id_employee=$id_employees'>";} echo"<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span>"; if($edit==1){echo "</a>";} }else{ if($edit==1){echo "<a href='controller/employeeController.php?active=$active&id_employee=$id_employees'>"; } echo"<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span>"; if($edit==1){echo "</a>";} } echo"</td>
				<td align='center'>";
				if($id_profile <= "$profile"){
					echo "
					<a href='index.php?content=Employee&edit=y&id_employee=$id_employees' $can_edit>
						<button class='btn btn-warning btn-sx'>
							<span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>
						</button>
					</a>&nbsp;
					<a href='controller/employeeController.php?drop=y&id_employee=$id_employees'  $can_delete>
						<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $first_name $last_name ? ');\">
							<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
						</button>
					</a>";
				}echo "
				</td>
				</tr>";
				$i++;
				$n++;
			}
			echo"       
		</table>
	</div> </div>";
}
	?>
</div>
<script>
function submit_add(){
	var first_name = $("#first_name").val();
	var last_name = $("#last_name").val();
	var email = $("#email").val();
	var password = $("#password").val();
	var id_profile = $("#id_profile").val();
	if(first_name == ""){
		alert("ยังไม่ได้ใส่ชื่อ");
		$("#first_name").focus();
	}else if(last_name == ""){
		alert("ยังไม่ได้ใส่นามสกุล");
		$("#last_name").focus();
	}else if(email == ""){
		alert("ยังไม่ได้ใส่อีเมล์");
		$("#email").focus();
	}else if(id_profile == ""){
		alert("ยังไม่ได้เลือกโปรไฟร์");
		$("#id_profile").focus();
	}else{
		document.add_employee.submit();
	}
}

function reset_password(){
	var id = $("#id_employee").val();
	if(id==""){
		alert("ไม่พบตัวแปร id_employee กรุณาติดต่อผู้ดูแลระบบ");
	}else{
		$("#reset").click();
	}
}
</script>