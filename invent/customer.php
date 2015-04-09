<?php 
	$page_menu = "invent_customer";
	$page_name = "ลูกค้า";
	$id_tab = 21;
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
	echo"<form id='customer_form' action='controller/customerController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form id='customer_form' action='controller/customerController.php?add=y' method='post'>";
}
?>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_customer'])){
		    echo"
		   <li><a href='index.php?content=customer' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=customer' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link' ><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	  		}else if(isset($_GET['view_detail'])){
		   echo"
		   <li><a href='index.php?content=customer' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=customer&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>
		  ";
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
//*********************************************** เพิ่มลูกค้าใหม่********************************************************// 
if(isset($_GET['add'])){ 
	echo"<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>คำนำหน้า :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>"; getTitleRadio(); echo"</td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัส :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='customer_code' id='customer_code' class='form-control input-sm' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='first_name' id='first_name' class='form-control input-sm' required='required' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='last_name' id='last_name' class='form-control input-sm' required='required'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ร้าน/บริษัท :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='company' id='company' class='form-control input-sm' autocomplete='off'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>อีเมล์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='email' id='email' class='form-control input-sm' autocomplete='off'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัสผ่าน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='password' name='password' id='password' class='form-control input-sm' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>วันเกิด :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='day' style='width: 15%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectDay(); echo"</select>
			<select name='month' style='width: 35%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectMonth(); echo"</select>
			<select name='year' style='width: 20%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectYear(); echo"</select>
		</td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>วงเงินเครดิต :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<input type='text' name='credit_amount' id='credit_amount'  style='width: 40%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px; text-align:right; padding-right:5px;' value='0'/> 
			<label for='credit_amount' style='margin-left:5px; margin-right:25px;'>บาท</label>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>เครดิตเทอม :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<input type='text' name='credit_term' id='credit_term'  style='width: 40%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px; text-align:right; padding-right:5px;' value='0'/> 
			<label for='credit_amount' style='margin-left:5px; margin-right:25px;'>วัน</label>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
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
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top; '>กลุ่มลูกค้า :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>"; customerGroupTable(); echo" <span class='help-block'>อนุญาติให้ลูกค้ารายนี้เข้าถึงหมวดหมู่ที่เลือกได้</span></td>
		<td style='padding-bottom:10px; vertical-align:text-top; padding-left:15px;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>กลุ่มหลัก :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='default_group' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectCustomerGroup(); echo"</select>
			<span class='help-block'>หมวดหมู่หลักของลูกค้าสำหรับใช้กับส่วนลด</span></td>
			<td style='padding-bottom:10px; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>พนักงานขาย :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='id_sale' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; saleList(); echo"</select>
			<span class='help-block'>พนักงานขายที่รับผิดชอบลูกค้าคนนี้</span></td>
			<td style='padding-bottom:10px; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top; '>&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><span class='help-block'><sub style='color:red; vertical-align:text-top;'>*</sub>&nbsp;รายการที่จำเป็นต้องกรอก</span></td>
	</tr>
	<tr>
		<td colspan='3'><h3>&nbsp;</h3></td>
	</tr>
	</table></form>";
	
//*********************************************** จบหน้าเพิ่ม ****************************************************//
}else if(isset($_GET['edit'])&&isset($_GET['id_customer'])){
//*********************************************** แก้ไข **************************************************************//
	$id_customer = $_GET['id_customer'];
	$customer = new customer($id_customer);
	echo"<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
		<td width='20%' align='right' style='padding-bottom:10px;'>คำนำหน้า :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>"; getTitleRadio($customer->id_gender); echo"</td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัส :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='customer_code' id='customer_code' class='form-control input-sm' value='".$customer->customer_code."' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='first_name' id='first_name' class='form-control input-sm' value='".$customer->first_name."' required='required' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>นามสกุล :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='last_name' id='last_name' class='form-control input-sm' value='".$customer->last_name."' /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ร้าน/บริษัท :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='company' id='company' class='form-control input-sm'value='".$customer->company."'  autocomplete='off'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>อีเมล์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='email' id='email' class='form-control input-sm' value='".$customer->email."'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>รหัสผ่าน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='password' name='password' id='password' class='form-control input-sm'/></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>วันเกิด :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='day' style='width: 20%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectDay(date('d',strtotime($customer->birthday))); echo"</select>
			<select name='month' style='width: 45%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectMonth(date('m',strtotime($customer->birthday))); echo"</select>
			<select name='year' style='width: 25%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectYear(date('Y',strtotime($customer->birthday))); echo"</select>
		</td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>วงเงินเครดิต :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<input type='text' name='credit_amount' id='credit_amount' style='width: 40%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px; text-align:right; padding-right:5px;' value='".$customer->credit_amount."'/> 
			<label for='credit_amount' style='margin-left:5px; margin-right:25px;'>บาท</label>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>เครดิตเทอม :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<input type='text' name='credit_term' id='credit_term'  style='width: 40%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px; text-align:right; padding-right:5px;' value='".$customer->credit_term."'/> 
			<label for='credit_amount' style='margin-left:5px; margin-right:25px;'>วัน</label>
		</td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'></td>
	</tr>"; if($customer->active ==1){ $active  = "checked='checked'"; $disactive="";}else{ $active = ""; $disactive= "checked='checked'";} echo"
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>สถานะ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>			
				<input type='radio' name='active' id='yes' value='1' $active />
				<label for='yes' style='margin-left:5px; margin-right:25px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>
				<input type='radio' name='active' id='no' value='0' $disactive />
				<label for='no' style='margin-left:5px; margin-right:25px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label>
			</td><td>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top; '>กลุ่มลูกค้า :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>"; customerGroupTable($id_customer); echo" <span class='help-block'>อนุญาติให้ลูกค้ารายนี้เข้าถึงหมวดหมู่ที่เลือกได้</span></td>
		<td style='padding-bottom:10px; vertical-align:text-top; padding-left:15px;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>กลุ่มหลัก :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='default_group' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; selectCustomerGroup($customer->id_default_group); echo"</select>
			<span class='help-block'>หมวดหมู่หลักของลูกค้าสำหรับใช้กับส่วนลด</span></td>
			<td style='padding-bottom:10px; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>พนักงานขาย :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='id_sale' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; saleList($customer->id_sale); echo"</select>
			<span class='help-block'>พนักงานขายที่รับผิดชอบลูกค้าคนนี้</span></td>
			<td style='padding-bottom:10px; vertical-align:text-top; padding-left:15px;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top; '>&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><span class='help-block'><sub style='color:red; vertical-align:text-top;'>*</sub>&nbsp;รายการที่จำเป็นต้องกรอก</span></td>
	</tr>
	<tr><td colspan='3'></td></tr>
	</table>";
	//////////////////////////////////////////  ส่วนลดลูกค้า ///////////////////////////////////////////
	echo"
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
	<div class='row'>
	<div class='col-sm-6' style='vertical-align:text-bottom;'><h3>ส่วนลดลูกค้า</h3></div>
	<div class='col-sm-6'>
       <ul class='nav navbar-nav navbar-right'>
	   	 <li><button type='submit' class='btn btn-link' ><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></li>
		 </ul>
		 </div>
		 </div>	 
	   
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
	<table width='100%' border='0'>
	<tr><input type='hidden' name='customer_discount' id='customer_discount' value='0'/>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'><input type='radio' name='apply' id='apply1' value='1'/><label for='apply1' style='padding-left:15px;'>ส่วนลด :&nbsp;</label></td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
		<div class='input-group'>
		<input type='text' name='discount_all' id='discount_all' class='form-control input-sm'  disabled='disabled' />
		<span class='input-group-addon'> % </span></div>
		<span class='help-block'>กำหนดส่วนลดในช่องนี้หากต้องการให้ส่วนลดนี้ในทุกรายการสินค้า</span>	</td><td style='padding-left:15px;'>&nbsp;</td>
	</tr>  
	<tr><td colspan='3'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></td></tr>
	<tr><td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'><input type='radio' name='apply' id='apply2' value='2' checked='checked'/><label for='apply2' style='padding-left:15px;'>ส่วนลด :&nbsp;</label></td><td colspan='2' align='left' style='padding-bottom:10px;'>กำหนดส่วนลดตามหมวดหมู่ หากกำหนดล่วนลดนี้จะยกเลิกการให้ส่วนลดด้านบน</td></tr>
	";
	$cate = new category;
	$list = $cate->categoryList();
	$row = dbNumRows($list);
	$i =0;
	while($i<$row){
		list($id_category, $category_name, $array) = dbFetchArray($list);
		echo"<tr><td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>$category_name : &nbsp;</td><td width='40%' align='left' style='padding-bottom:10px;'>
		<div class='input-group'>
		<input type='text' class='form-control input-sm' name='category[$id_category]' id='discount[]' value='".$customer->getDiscount($id_category)."' />
		<span class='input-group-addon'> % </span></div>
		</td></tr>";
		$i++;
	}
	echo"
	<tr><td colspan='3'><h3>&nbsp;</h3></td><tr>
	</table></form>
	";
	
//*********************************************** จบหน้าแก้ไข ****************************************************//
}else if(isset($_GET['view_detail'])&&isset($_GET['id_customer'])){
	echo"</form>";
//********************** แสดงรายละเอียด *********************************************************//
	$id_customer = $_GET['id_customer'];
	$customer = getCustomerDetail($id_customer);
	echo"
	<div class='row'>
	<div class='col-sm-6'>
		<table style='width:100%; padding:10px; border-right: 1px solid #ccc;'>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>ชื่อ : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".$customer['first_name']." &nbsp;".$customer['last_name'] ."</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>อีเมล์ : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".$customer['email']."</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>อายุ : </td>
		<td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>";if($customer['birthday'] !="0000-00-00"){ echo round(dateDiff($customer['birthday'],date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer['birthday']).")" ;}else{echo "-";} echo"</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>เพศ : </td>
		<td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>"; if($customer['id_gender']==1){ echo"ไม่ระบุ";}else if($customer['id_gender']==2){echo"ชาย";}else{echo"หญิง";} echo"</td></tr>
		</table>
	</div>
	<div class='col-sm-6'>
		<table style='width:100%; padding:10px;'>		
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>วันที่เป็นสมาชิก : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".thaiTextDate($customer['date_add'])."</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>วงเงินเครดิต : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".number_format($customer['credit_amount'])." &nbsp;บาท</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>เครดิตเทอม : </td><td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>".number_format($customer['credit_term'])."&nbsp;วัน</td></tr>
		<tr><td style='width:25%; text-align:right; padding-bottom:15px;'>สถานะ : </td>
		<td style='width:75%; text-align:left; padding-left:10px; padding-bottom:15px;'>"; if($customer['active']==1){echo"<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span>";}else{ echo "<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span>";} echo"</td></tr>
		</table>
	</div>
	</div>
	<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />";
	echo"
	<div class='row'>
	<div class='col-sm-12'>
	<h4>ที่อยู่ </h4>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<thead>
			<th style='width:20%;'>บริษัท/ร้าน</th><th style='width:20%;'>ชื่อ</th><th style='width:40%;'>ที่อยู่</th><th style='width:20%;'>เบอร์โทรศัพท์</th>
		</thead>";
		$sql = getCustomerAddress($id_customer);
		$row = dbNumRows($sql);
		if($row>0){
			$i = 0;
			while($i<$row){
				$result = dbFetchArray($sql);
				$company = $result['company'];
				$customer_name = $result['firstname']."&nbsp;".$result['lastname'];
				$address = $result['address1']."&nbsp;".$result['address2']."&nbsp; จ.".$result['city']."&nbsp;".$result['postcode'];
				$phone = $result['phone'];
				echo"<tr><td>$company</td><td>$customer_name</td><td>$address</td><td>$phone</td></tr>";
				$i++;
			}
		}else{
			echo "<tr><td colspan='4' align='center'><h4>ยังไม่มีข้อมูลที่อยู่</h4></td></tr>";
		}
		echo"		
		</table>
	</div></div>";
//************************************************ จบหน้าแสดงรายละเอียด ************************************************//
}else{
echo"</form>";
//************************************************ แสดงรายการ *************************************************//
if(isset($_GET['text'])){ $text = $_GET['text'];}else{ $text=""; }
echo "<div class='row'>
				<div class='col-xs-4 col-xs-offset-3'>
					<div class='input-group'>
            			<span class='input-group-addon'>&nbsp;&nbsp; ค้นหา &nbsp;&nbsp;</span>
            			<input type='text' name='search-text' id='search-text' class='form-control' value='$text' />
                		<span class='input-group-btn'>
               			 <button type='button' class='btn btn-default' id='search-btn'>&nbsp;&nbsp;<span class='glyphicon glyphicon-search'></span>&nbsp;&nbsp;</button>
                		</span>
           			 </div>
				</div>
				<div class='col-xs-1'>
					<a style='text-align:center; background-color:transparent; padding-bottom:0px;' href='index.php?content=customer'>
                		<button type='button' class='btn btn-default'>รีเซต</button>
                	</a>
				</div>
			</div>
			<div class='row'><div class='col-sm-12'>	<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' /></div></div>
				";
				
echo"
	<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12' id='result'>";
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	if(isset($_GET['searchtext'])){ $where = $_GET['searchtext']; }else{ $where = ""; }
	$paginator->Per_Page("tbl_customer",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=customer&searchtext=$where&text=$text");
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page; 
	
	echo"
		<table class='table table-striped table-hover'>
			<thead style='background-color:#48CFAD;'>
				<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>รหัส</th><th style='width:25%;'>ชื่อ</th><th style='width:20%;'>ร้าน/บริษัท</th>
				<th style='width:5%; text-align:center;'>สถานะ</th><th style='width:8%; text-align:center;'>วงเงินเครดิต</th>
				<th style='width:7%; text-align:center;'>เครดิตเทอม</th><th style='width:10%; text-align:center;'>วันที่สมัคร</th><th colspan='2' style='text-align:center;'>การกระทำ</th>
			</thead>";
			$sql = dbQuery("SELECT id_customer, customer_code, company, first_name, last_name, credit_amount, credit_term, active, date_add FROM tbl_customer $where LIMIT $Page_Start , $Per_Page");
			$row = dbNumRows($sql);
			$i = 0;
			if($row>0){
				while($i<$row){
					list($id_customer, $customer_code, $company, $first_name, $last_name, $credit_amount, $credit_term, $active, $date_add) = dbFetchArray($sql);			
					echo" <tr>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">$id_customer</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">$customer_code</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">$first_name $last_name</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">$company</td>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">"; 
					if($active == 1){ 
						echo"<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span>"; 
						}else{ 
						echo"<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span>";}	echo"</td>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".number_format($credit_amount)."</td>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".$credit_term."</td>
					<td align='center'>".thaiDate($date_add)."</td>
					<td align='center' >"; if($id_customer !=0){ echo"
						<a href='index.php?content=customer&edit=y&id_customer=$id_customer' $can_edit>
								<button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button>
						</a>"; } echo"
					</td>
					<td align='center' >"; if($id_customer !=0){ echo"
						<a href='controller/customerController.php?delete=y&id_customer=$id_customer' $can_delete>
							<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $first_name $last_name ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button>
						</a>"; } echo"
					</td>
				</tr>";
				$i++;
				}
			}else{ echo"<tr><td colspan='10' align='center'><h3>ไม่มีรายการ</h3></td></tr>";}
echo"</table>"; echo $paginator->display_pages(); echo"
	</div>
</div>";

		echo "<br><br>";
				
}
?>
</div>
<script src="../library/js/jquery.cookie.js"></script>
<script>
function validate(){
	var first_name = $("#first_name").val();
	var last_name = $("#last_name").val();
	var email = $("#email").val();
	$("#customer_form").submit();
}
$(document).ready(function() {
	$("#apply1").change(function() {
        $("#discount_all").removeAttr("disabled");
		$("#discount\\[\\]").each(function() {
            $(this).attr("disabled","disabled");
        });
		$("#discount_all").focus();
    });
});
$(document).ready(function() {
	$("#apply2").change(function() {
		$("#discount_all").attr("disabled","disabled");
		$("#discount\\[\\]").each(function() {
            $(this).removeAttr("disabled");
        });
		$("#discount").focus();
    });
});
function get_row(){
	$("#rows").submit();
}
	$("#search-text").bind("enterKey",function(){
	if($("#search-text").val() != ""){
		$("#search-btn").click();
	}
});
$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var rows = $.cookie("get_rows");
	$.ajax({
		url:"controller/customerController.php?text="+query_text+"&get_rows="+rows, type: "GET", cache:false,
		success: function(result){		
			$("#result").html(result);
		}
	});
});
</script>
