<?php 
	$page_menu = "invent_configuration";
	$page_name = "การตั้งค่า";
	$id_tab = 25;
	$id_profile = $_COOKIE['profile_id'];
	$id_employee = $_COOKIE['user_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($view==0){ echo accessDeny(); exit; }
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	if(isset($_GET['general'])){ $config = "ทั่วไป"; }else if(isset($_GET['product'])){ $config = "สินค้า"; }else if(isset($_GET['document'])){ $config = "เอกสาร"; }else if(isset($_GET['popup'])){ $config = "การแจ้งข่าว"; }
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $page_name.$config; ?></h3>
	</div>
    <div class="col-sm-6">
       <h3>
       <?php 
	   if(isset($_GET['pop_on'])){
		   echo"<span class='pull-right'><a><button type='submit' class='btn btn-link' id='btn_save'><i class='fa fa-floppy-o fa-3x' style='color:#5cb85c;'></i></button></a></span>
		   <span class='pull-right'><a href='index.php?content=config&popup=y'><button type='button' class='btn btn-link'><i class='fa fa-reply fa-3x' style='color:#5cb85c;'></i></button></a></span>
       		 ";
	   }else if(isset($_GET['popup'])){
		   
	   }else{
		    echo" <span class='pull-right'><a><button type='submit' class='btn btn-link' id='btn_save'><i class='fa fa-floppy-o fa-3x' style='color:#5cb85c;'></i></button></a></span>";
	   }
	   ?>
       </h3>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />

<!-- End page place holder -->
<?php 
if(isset($_GET['error'])){
	$error_message = base64_decode($_GET['error']);
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = base64_decode($_GET['message']);
	echo"<div class='alert alert-success'>$message</div>";
}
//**************************************  แสดงค่าจากฐานข้อมูล ******************************************//
	/// ออเดอร์
	$view_order_in_days = getConfig("VIEW_ORDER_IN_DAYS");//แสดงรายการออเดอร์ล่าสุดกี่วัน
	$pament_detail = getConfig("PAYMENT");//แสดงรายการออเดอร์ล่าสุดกี่วัน
	
echo" <form id='config_form' action='controller/settingController.php?config=".md5("y")."&em=$id_employee' method='post'>";
if(isset($_GET['popup'])){
	if(!isset($_GET['pop_on'])){
		echo "<div class='row'>
  <div class='col-sm-4 col-md-4'>
  	<a href='index.php?content=config&popup=y&pop_on=back'>
	<button type='button' class='btn btn-primary btn-block' style='padding:10px;'><h2 style='text-align:center; margin-top:30px; margin-bottom:30px;'> <i class='fa fa-home'></i>&nbsp;แจ้งหลังบ้าน</h2></button>
	</a>
  </div>
  <div class='col-sm-4 col-md-4'>
  	<a href='index.php?content=config&popup=y&pop_on=sale'>
	<button type='button' class='btn btn-success btn-block' style='padding:10px;'><h2 style='text-align:center; margin-top:30px; margin-bottom:30px;'> <i class='fa fa-rocket'></i>&nbsp;แจ้งเซล</h2></button>
	</a>
  </div>
  <div class='col-sm-4 col-md-4'>
  	<a href='index.php?content=config&popup=y&pop_on=front'>
	<button type='button' class='btn btn-danger btn-block' style='padding:10px;'><h2 style='text-align:center; margin-top:30px; margin-bottom:30px;'> <i class='fa fa-globe'></i>&nbsp;แจ้งหน้าบ้าน</h2></button>
	</a>
  </div>
</div>";
	}else{
		$pop_on = $_GET['pop_on'];
		$sql = dbQuery("SELECT * FROM tbl_popup WHERE pop_on = '$pop_on'");
		list($id_popup, $pop, $delay, $start, $end, $content, $width, $height, $active) = dbFetchArray($sql);
		if($active==1){ $toggle ="fa fa-toggle-on fa-3x"; }else{ $toggle ="fa fa-toggle-off fa-3x"; }
		function set_delay($selected=""){
		$loop = "<select name='loop' id='loop' class='form-control'>
						<option value='0' "; if($selected ==0){ $loop .= "selected='selected'"; } $loop .=">ตลอดเวลา</option><option value='3600' "; if($selected ==3600){ $loop .= "selected='selected'"; } $loop .=">ทุกชั่วโมง</option>
						<option value='10800' "; if($selected ==10800){ $loop .= "selected='selected'"; } $loop .=">ทุก 3 ชั่วโมง</option><option value='21600' "; if($selected ==21600){ $loop .= "selected='selected'"; } $loop .=">ทุก 6 ชั่วโมง</option>
						<option value='43200' "; if($selected ==43200){ $loop .= "selected='selected'"; } $loop .=">ทุก 12 ชั่วโมง</option><option value='86400' "; if($selected ==86400){ $loop .= "selected='selected'"; } $loop .=">ทุก 24 ชั่วโมง</option>
					</select>";
					return $loop;
		}
	echo" <input type='hidden' name='form_role' id='form_role' value='popup' /><input type='hidden' name='pop_on' value='$pop_on' />
			<div class='row'>
			<div class='col-lg-3' style='padding-right:0px;'>
				<div class='input-group'><span class='input-group-addon'>การแสดงผล</span>".set_delay($delay)."</div>
			</div>
			<div class='col-lg-2' style='width:12.5%; padding-right:0px;'>
				<div class='input-group'><span class='input-group-addon'>กว้าง</span><input type='text' name='width' class='form-control' value='$width' /></div>
			</div>
			<div class='col-lg-2' style='width:12.5%; padding-right:0px;'>
				<div class='input-group'><span class='input-group-addon'>สูง</span><input type='text' name='height' class='form-control' value='$height' /></div>
			</div>
			<div class='col-lg-2' style='padding-right:0px;'>
				<div class='input-group'><span class='input-group-addon'>เริ่ม</span><input type='text' name='from_date' id='from_date' class='form-control' value='".thaiDate($start)."' /></div>
			</div>
			<div class='col-lg-2' style='padding-right:0px;'>
				<div class='input-group'><span class='input-group-addon'>สิ้นสุด</span><input type='text' name='to_date' id='to_date' class='form-control' value='".thaiDate($end)."' /></div>
			</div>
			<div class='col-lg-1'>
				<input type='hidden' name='active' id='active' value='$active' />
				<div class='input-group'><span class='input-group-addon' style='color:#434A54; background-color:#FFF; border-color: #FFF;'>เปิดใช้งาน : </span><a href='#'><span onclick='update_status()'><i class='$toggle' id='toggle'></i></span></a></div>
				
			</div>
			</div>
		<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	";
	echo "
	<script src='../library/ckeditor/ckeditor.js'></script>
	<script src='../library/ckfinder/ckfinder.js'></script>
	<textarea id='content' name='content'>$content</textarea>
	<p style='margin-top:15px;'><input type='checkbox' name='update_all' id='update_all' /><label for='update_all' style='margin-left:15px;'>ใช้ร่วมกันทั้งหมด</label></p>
	<script>
	CKEDITOR.replace( 'content',{
		// อยากกำหนดอะไรก็ใส่ที่นี่
		
		filebrowserBrowseUrl : '../library/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl : '../library/ckfinder/ckfinder.html?Type=Images',
		filebrowserFlashBrowseUrl : '../library/ckfinder/ckfinder.html?Type=Flash',
		filebrowserUploadUrl : '../library/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl : '../library/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		filebrowserFlashUploadUrl : '../library/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
		} );
	</script>
	 ";
	}
}else if(isset($_GET['general'])){
	//*********************************************** ทั่วไป *******************************************************// 
	 ///ทั่วไป
	$company = getConfig("COMPANY_FULL_NAME");
	$brand = getConfig("COMPANY_NAME");
	$address = getConfig("COMPANY_ADDRESS");
	$post_code = getConfig("COMPANY_POST_CODE");
	$phone = getConfig("COMPANY_PHONE");
	$fax = getConfig("COMPANY_FAX_NUMBER");
	$tax_id = getConfig("COMPANY_TAX_ID");
	$email = getConfig("COMPANY_EMAIL");
	$url = getConfig("HOME_PAGE_URL");
	$barcode_type = getConfig("BARCODE_TYPE");
	$allow_under_zero = getConfig("ALLOW_UNDER_ZERO");
	$email_to_neworder = getConfig("EMAIL_TO_NEW_ORDER");
	if($allow_under_zero == 1){ $yes = "checked='checked'"; $no =""; }else{ $yes = ""; $no = "checked='checked'"; }
echo"
<div class='row'>
<div class='col-lg-12'>
    <table style='width:100%; border:none;'>
    	<tr><td colspan='5' ><input type='hidden' name='form_role' id='form_role' value='general' /></td></tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>ชื่อบริษัท :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='company' id='company' class='form-control' value='$company' /></td>
        	  <td style='width:10%; text-align:right; padding: 10px; vertical-align:text-top;'>แบรนด์ :</td>
              <td style='width:25%; padding: 10px; '><input type='text' name='brand' id='brand' class='form-control' value='$brand' /></td><td></td>	
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>ที่อยู่ :</td>
              <td style='width:25%; padding: 10px; '><textarea class='form-control' rows='5' name='company_address' id='company_address'>$address</textarea></td>
        	  <td style='width:10%; text-align:right; padding: 10px; vertical-align:text-top;'></td>
              <td style='width:25%; padding: 10px;'></td><td></td>	
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>รหัสไปรษณีย์ :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='post_code' id='post_code' class='form-control' value='$post_code' /></td>
        	  <td style='width:10%; text-align:right; padding: 10px; vertical-align:text-top;'></td>
              <td style='width:25%; padding: 10px; '></td><td></td>	
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>เบอร์โทร :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='phone' id='phone' class='form-control' value='$phone' /></td>
        	  <td style='width:10%; text-align:right; padding: 10px; vertical-align:text-top;'>เบอร์แฟ็กซ์ :</td>
              <td style='width:25%; padding: 10px; '><input type='text' name='fax' id='fax' class='form-control' value='$fax' /></td><td></td>	
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>เลขประจำตัวผู้เสียภาษี :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='tax_id' id='tax_id' class='form-control' value='$tax_id' /></td>
        	  <td style='width:10%; text-align:right; padding: 10px; vertical-align:text-top;'>อีเมล์ :</td>
              <td style='width:25%; padding: 10px; '><input type='email' name='email' id='email' class='form-control' value='$email' /></td><td></td>	
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>รูปแบบบาร์โค้ด :</td>
              <td style='width:25%; padding: 10px;'><select name='barcode_type' id='barcode_type' class='form-control' >
              														<option value='code93' "; if($barcode_type=="code93"){ echo"selected='selected'";} echo" >CODE 93</option>
                                                                    <option value='code39' "; if($barcode_type=="code39"){ echo"selected='selected'";} echo" >CODE 39</option>
                                                                    <option value='ean13' "; if($barcode_type=="ean13"){ echo"selected='selected'";} echo" >EAN 13</option>
                                                               </select>
              </td>
        	  <td style='width:10%; text-align:right; padding: 10px; vertical-align:text-top;'>Home page :</td>
              <td style='width:25%; padding: 10px; '><input type='text' name='home_page' id='home_page' class='form-control' value='$url' /></td><td></td>	
        </tr>
         <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>อนุญาตให้สต็อกติดลบได้ :</td>
              <td style='width:25%; padding: 10px;'>
              	<input type='radio' name='allow_under_zero' id='yes' value='1' $yes /><label for='yes' style='margin-left:10px;'>ใช่</label>
                <input type='radio' name='allow_under_zero' id='no' value='0' $no style='margin-left:50px;' /><label for='no' style='margin-left:10px;'>ไม่ใช่</label>
                </td>
        	  <td style='width:10%; text-align:right; padding: 10px; vertical-align:text-top;'></td>
              <td style='width:25%; padding: 10px; '></td><td></td>	
        </tr>
		 <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:middle;'>แสดงออเดอร์ล่าสุดไม่เกิน :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='view_order_in_days' id='view_order' class='form-control' value='$view_order_in_days' /></td>
        	  <td style='width:10%; text-align:left; padding: 10px; vertical-align:middle;'>วัน</td>
              <td style='width:25%; padding: 10px; '></td><td></td>	
        </tr>
		 <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>รายระเอียดการชำระเงิน :</td>
              <td style='width:25%; padding: 10px;'><textarea name='payment_detail' class='form-control' rows='8'>$pament_detail </textarea>
                </td>
        	  <td style='width:10%; text-align:right; padding: 10px; vertical-align:text-top;'>อีเมลแจ้งเตือนการสั่งซื้อ</td>
              <td style='width:25%; padding: 10px; '><textarea name='email_to_neworder' class='form-control' rows='8'>".$email_to_neworder."</textarea></td><td></td>	
        </tr>
    <tr><td colspan='5' >&nbsp;</td></tr>
    </table>
</div>
</div>";
}else if(isset($_GET['product'])){
//*******************************************  สินค้า  ********************************************//	
/// สินค้า
	$new_product_date = getConfig("NEW_PRODUCT_DATE"); /// จำนวนวันของสินค้าใหม่
	$features_product = getConfig("FEATURES_PRODUCT");//จำนวนรายการสินค้าหน้าแรก
	$max_show_stock =  getConfig("MAX_SHOW_STOCK"); //จำนวนตัวเลขสต็อกสูงสุดที่แสดง
	$new_product_qty = getConfig("NEW_PRODUCT_QTY"); //จำนวนสินค้าใหม่ที่แสดงในแถบ New alival
	$vertical_grid = getConfig("ATTRIBUTE_GRID_VERTICAL");
	$horizontal_grid = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
	$additional_grid = getConfig("ATTRIBUTE_GRID_ADDITIONAL");

echo"
<div class='row'>
<div class='col-lg-12'>
    <table style='width:100%; border:none;'>
    	<tr><td colspan='5' ><input type='hidden' name='form_role' id='form_role' value='product' /></td></tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:middle;'>อายุของสินค้าใหม่ :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='new_product_date' id='new_product_date' class='form-control' value='$new_product_date' /></td>
        	  <td style='width:10%; text-align:left; padding: 10px; vertical-align:middle;'>วัน</td>
              <td style='width:25%; padding: 10px; '></td><td></td>	
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:middle;'>แสดงสินค้ามาใหม่ :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='new_product_qty' id='new_product_qty' class='form-control' value='$new_product_qty' /></td>
        	  <td style='width:10%; text-align:left; padding: 10px; vertical-align:middle;'>รายการล่าสุด</td>
              <td style='width:25%; padding: 10px; '></td><td></td>	
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:middle;'>แสดงรายการสินค้าหน้าแรก :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='features_product' id='features_product' class='form-control' value='$features_product' /></td>
        	  <td style='width:10%; text-align:left; padding: 10px; vertical-align:middle;'>รายการ</td>
              <td style='width:25%; padding: 10px; '></td><td></td>	
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:middle;'>แสดงจำนวนสินค้าในสต็อกไม่เกิน :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='max_show_stock' id='max_show_stock' class='form-control' value='$max_show_stock' /></td>
        	  <td colspan='3' style='width:10%; text-align:left; padding: 10px; vertical-align:middle;'><span style='color:#CCC;'>แสดงจำนวนที่กำหนดเมื่อสต็อกสินค้ามากกว่าจำนวนที่กำหนด  กำหนดให้เป็น 0 ถ้าต้องการปิดการใช้งาน</span></td>	
        </tr>
         <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:middle;'>รูปแบบการแสดงผลตารางสินค้าคงเหลือ :</td>
			   <td colspan='4' style='width:25%; padding: 10px;'>
			   	<div class='row'>
            	<div class='col-lg-4'>
                	<div class='input-group'><span class='input-group-addon'>แนวตั้ง</span>
                				<select name='vertical' id='vertical' class='form-control'>
                                <option id='vertical-color' value='color'"; if($vertical_grid =="color"){ echo"selected='selected'"; } echo" >สี</option>
                                 <option id='vertical-size' value='size'"; if($vertical_grid =="size"){ echo"selected='selected'"; } echo" >ไซด์</option>
                                 <option id='vertical-additional' value='attribute'"; if($vertical_grid =="attribute"){ echo"selected='selected'"; } echo">คุณลักษณะอื่น</option>
                                 </select>
                     </div>
                </div>
                <div class='col-lg-4'>
                	<div class='input-group'><span class='input-group-addon'>แนวนอน</span>
                				<select name='horizontal' id='horizontal' class='form-control'>
                                <option id='horizontal-color' value='color' "; if($horizontal_grid =="color"){ echo"selected='selected'"; } echo">สี</option>
                                 <option id='horizontal-size' value='size'"; if($horizontal_grid =="size"){ echo"selected='selected'"; } echo" >ไซด์</option>
                                 <option id='horizontal-additional' value='attribute'"; if($horizontal_grid =="attribute"){ echo"selected='selected'"; } echo">คุณลักษณะอื่น</option>
                                 </select>
                     </div>
                </div>
                <div class='col-lg-4'>
                	<div class='input-group'><span class='input-group-addon'>แถบเสริม</span>
                				<select name='additional' id='additional' class='form-control'>
                                <option id='additional-color' value='color' "; if($additional_grid =="color"){ echo"selected='selected'"; } echo">สี</option>
                                 <option id='additional-size' value='size' "; if($additional_grid =="size"){ echo"selected='selected'"; } echo" >ไซด์</option>
                                 <option id='additional-additional' value='attribute'"; if($additional_grid =="attribute"){ echo"selected='selected'"; } echo">คุณลักษณะอื่น</option>
                                 </select>
                     </div>
                </div>
           </div>
            </td>
        </tr>
       
            
	 </table>
</div>
</div>";

}else if(isset($_GET['document'])){
//**************************************** ตั้งค่าเอกสาร ****************************************//
/// เอกสาร
	$prefix_order = getConfig("PREFIX_ORDER"); /// ขาย
	$prefix_recieve = getConfig("PREFIX_RECIEVE"); /// รับสินค้าเข้า
	$prefix_requistion = getConfig("PREFIX_REQUISITION"); //เบิกสินค้า
	$prefix_lend = getConfig("PREFIX_LEND");//ยืมสินค้า
	$prefix_sponsor = getConfig("PREFIX_SPONSOR");///สปอนเซอร์
	$prefix_consignment = getConfig("PREFIX_CONSIGNMENT"); //ฝากขาย
	$prefix_consign = getConfig("PREFIX_CONSIGN"); //ตัดยอดฝากขาย
	$prefix_return = getConfig("PREFIX_RETURN"); //รับสินค้าคืน
echo"
<div class='row'>
<div class='col-lg-12'>
    <table style='width:100%; border:none;'>
    	<tr><td colspan='5' ><input type='hidden' name='form_role' id='form_role' value='document' /></td></tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>รับสินค้าเข้า :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='prefix_recieve' id='prefix_recieve' class='form-control' value='$prefix_recieve' /></td>
        	  <td style='width:50%; text-align:left; padding: 10px; vertical-align:text-top;'></td>
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>ขายสินค้า :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='prefix_order' id='prefix_order' class='form-control' value='$prefix_order' /></td>
        	  <td style='width:50%; text-align:left; padding: 10px; vertical-align:text-top;'></td>
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>เบิกสินค้า :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='prefix_requistion' id='prefix_requistion' class='form-control' value='$prefix_requistion' /></td>
        	  <td style='width:50%; text-align:left; padding: 10px; vertical-align:text-top;'></td>
        </tr>
         <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>ยืมสินค้า :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='prefix_lend' id='prefix_lend' class='form-control' value='$prefix_lend' /></td>
        	  <td style='width:50%; text-align:left; padding: 10px; vertical-align:text-top;'></td>
        </tr>
         <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>สปอนเซอร์ :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='prefix_sponsor' id='prefix_sponsor' class='form-control' value='$prefix_sponsor' /></td>
        	  <td style='width:50%; text-align:left; padding: 10px; vertical-align:text-top;'></td>
        </tr>
        <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>ฝากขาย :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='prefix_consignment' id='prefix_consignment' class='form-control' value='$prefix_consignment' /></td>
        	  <td style='width:50%; text-align:left; padding: 10px; vertical-align:text-top;'></td>
        </tr>
		 <tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>ตัดยอดฝากขาย :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='prefix_consign' id='prefix_consign' class='form-control' value='$prefix_consign' /></td>
        	  <td style='width:50%; text-align:left; padding: 10px; vertical-align:text-top;'></td>
        </tr>
		<tr>
        	  <td style='width:25%; text-align:right; padding: 10px; vertical-align:text-top;'>รับสินค้าคืน :</td>
              <td style='width:25%; padding: 10px;'><input type='text' name='prefix_return' id='prefix_return' class='form-control' value='$prefix_return' /></td>
        	  <td style='width:50%; text-align:left; padding: 10px; vertical-align:text-top;'></td>
        </tr>
        </table>
        </div>
        </div>";
	
}
echo "</form>";
?>
</div>
<script>
function validate(){
	var vertical = $("#vertical").find("option:selected").val();
	var horizontal = $("#horizontal").find("option:selected").val();
	var addition = $("#additional").find("option:selected").val();
	if(vertical == horizontal){
		alert("ค่าของแนวตั้งกับแนวนอนซ้ำกัน คุณต้องเลือกค่าที่ไม่ซ้ำกันเลย");
	}else if(horizontal == addition){
		alert("ค่าของแนวนอนกับแถบเสริมซ้ำกัน คุณต้องเลือกค่าที่ไม่ซ้ำกันเลย");
	}else if(vertical == addition){
		alert("ค่าของแนวตั้งกับแถบเสริมซ้ำกัน คุณต้องเลือกค่าที่ไม่ซ้ำกันเลย");
	}else{
		$("#config_form").submit();
	}
}
$("#btn_save").click(function(e) {
	var role = $("#form_role").val();
	if(role =="product"){
	    validate();
	}else{
		$("#config_form").submit();
	}
});
function update_status(){
	var active = $("#active").val();
	if(active ==1){
		$("#active").val(0);
		$("#toggle").removeClass("fa-toggle-on");
		$("#toggle").addClass("fa fa-toggle-off fa-3x");
	}else if(active==0){
		$("#active").val(1);
		$("#toggle").removeClass("fa-toggle-off");
		$("#toggle").addClass("fa fa-toggle-on fa-3x");
	}
}
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
   function check_date() {
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if(from_date ==""){	
		alert("คุณยังไม่ได้เลือกช่วงเวลา");
		}else if(to_date ==""){
		alert("คุณยังไม่ได้เลือกวันสุดท้าย");	
	}else{
		$("#config_form").submit();
	}
}	
</script>