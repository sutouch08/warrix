<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
//*********************** เพิ่มลูกค้าใหม่ *****************************//
if(isset($_GET['add'])){
	if(isset($_POST['gender'])){ $gender = $_POST['gender'];}else{ $gender = 0; }
	$birthday = dbDate($_POST['day']."-".$_POST['month']."-".$_POST['year']);
	$group_checked = $_POST['groupcheck'];
	$data = array($_POST['customer_code'], $_POST['default_group'], $_POST['id_sale'], $gender, $_POST['company'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $birthday, 
						$_POST['credit_amount'], $_POST['credit_term'], $_POST['active']);
	$customer = new customer();
	if($customer->add($data)){
		if(!$customer->add_to_group($customer->id_customer, $_POST['default_group'])){
			$message = $customer->error_message;
		}else{
			foreach($group_checked as $id_group){
				if($id_group != $_POST['default_group']){
					$customer->add_to_group($customer->id_customer, $id_group);
				}
			}
		}
		$message = "เพิ่มลูกค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=customer&message=$message");
	}else{
		$message = $customer->error_message;
		header("location: ../index.php?content=customer&add=y&error=$message");
	}
}


//********************************* แก้ไขข้อมูลลูกค้า ******************************************//
if(isset($_GET['edit'])&&isset($_POST['id_customer'])){	
	if(isset($_POST['gender'])){ $gender = $_POST['gender'];}else{ $gender = 0; }
	$id_customer = $_POST['id_customer'];
	$birthday = dbDate($_POST['day']."-".$_POST['month']."-".$_POST['year']);
	$group_checked = $_POST['groupcheck'];
	$data = array($id_customer, $_POST['customer_code'], $_POST['default_group'], $_POST['id_sale'], $gender, $_POST['company'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], 
	$_POST['password'], $birthday, $_POST['credit_amount'], $_POST['credit_term'], $_POST['active']);
	$customer = new customer();
	if($customer->edit($data)){
			//**************  Update discount ***********************//	
			if(isset($_POST['customer_discount'])){
				$apply = $_POST['apply'];
				if($apply ==1){
					$discount = $_POST['discount_all'];
					$cate = new category();
					$list = $cate->categoryList();
					$row = dbNumRows($list);
					$i = 0;
						while($i<$row){
							list($id_category, $array) = dbFetchArray($list);
							$customer->update_discount($id_customer, $id_category, $discount);
							echo $id_category. "<br/>";
							$i++;
						}
					}else{
						$category = $_POST['category'];
						foreach($category as $id_category => $discount){
							$customer->update_discount($id_customer, $id_category, $discount);	
						}
					}		
				}
			$message = "แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว";
			header("location: ../index.php?content=customer&edit=y&id_customer=$id_customer&message=$message");
	}else{
		$message = $customer->error_message;
		header("location: ../index.php?content=customer&edit=y&id_customer=$id_customer&error=$message");
		exit;	
	}		
}

//************************************** ลบข้อมูลลูกค้า *******************************************//
if(isset($_GET['delete'])&&isset($_GET['id_customer'])){
	$id_customer = $_GET['id_customer'];
	$customer = new customer();
	if($customer->delete($id_customer)){
		$message = "ลบลูกค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=customer&message=$message");
	}else{
		$message = $customer->error_message;
		header("location: ../index.php?content=customer&error=$message");
	}
}


//************************  ข้อมูลลูกค้า  ***********************//
if(isset($_GET['get_info'])&&isset($_GET['id_customer'])){
	$id_customer = $_GET['id_customer'];
	$customer = new customer($id_customer);
	$customer->customer_stat();
	$result = "<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<tr><input type='hidden' id='id_customer' value='".$customer->id_customer."' />
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; ".$customer->full_name."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตเทอม : &nbsp;".$customer->credit_term."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp;".$customer->email."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงินเครดิต :&nbsp;".number_format($customer->credit_amount,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อายุ :&nbsp;"; 
		if($customer->birthday !="0000-00-00"){ $result .= round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{$result .= "-";} 
		$result .="</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตใช้ไป :&nbsp;".number_format($customer->credit_used,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เพศ : &nbsp;"; 
		if($customer->id_gender==1){ $result .="ไม่ระบุ";}else if($customer->id_gender==2){$result .="ชาย";}else{$result .="หญิง";} $result .="</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตคงเหลือ : &nbsp;".number_format($customer->credit_balance,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp;".thaiTextDate($customer->date_add)."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ยอดเงินตั้งแต่เป็นสมาชิก : &nbsp;".number_format($customer->total_spent,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>&nbsp;</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ออเดอร์ตั้งแต่เป็นสมาชิก : &nbsp;".$customer->total_order_place."</td></tr>
		</table>";
		echo $result;
}
$header = "
	<table class='table table-striped table-hover'>
			<thead style='background-color:#48CFAD;'>
				<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>รหัส</th><th style='width:25%;'>ชื่อ</th><th style='width:20%;'>ร้าน/บริษัท</th>
				<th style='width:5%; text-align:center;'>สถานะ</th><th style='width:8%; text-align:center;'>วงเงินเครดิต</th>
				<th style='width:7%; text-align:center;'>เครดิตเทอม</th><th style='width:10%; text-align:center;'>วันที่สมัคร</th><th colspan='2' style='text-align:center;'>การกระทำ</th>
			</thead>";
//******************************************  ค้นหาลูกค้า ***********************************************//		
if(isset($_GET['text'])){
	$id_tab = 21;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	$text = $_GET['text'];
//	$html = $header;
	/////////////////////////////////
	$paginator = new paginator();
	if(isset($_GET['get_rows'])){$get_rows = $_GET['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	$query = "WHERE company LIKE '%$text%' OR first_name LIKE '%$text%' OR last_name LIKE'%$text%' OR customer_code LIKE'%$text%'";
	$paginator->Per_Page("tbl_customer",$query,$get_rows);
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page; 
	$sql = dbQuery("SELECT id_customer FROM tbl_customer WHERE company LIKE '%$text%' OR first_name LIKE '%$text%' OR last_name LIKE'%$text%' OR customer_code LIKE '%$text%' LIMIT $Page_Start , $Per_Page");
	//////////////////////////////////////
	$html = $paginator->display($get_rows,"index.php?content=customer&searchtext=$query&text=$text");
	$html .= $header;
	$row = dbNumRows($sql);
			$i = 0;
			if($row>0){
				while($i<$row){
					list($id_customer) = dbFetchArray($sql);
					$customer = new customer($id_customer);
					$html .=" <tr>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">$id_customer</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".$customer->customer_code."</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".$customer->full_name."</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".$customer->company."</td>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">"; 
					if($customer->active == 1){ 
						$html .="<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span>"; 
						}else{ 
						$html .="<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span>";}	
						$html .="</td>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".number_format($customer->credit_amount)."</td>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".$customer->credit_term."</td>
					<td align='center'>".thaiDate($customer->date_add)."</td>
					<td align='center' >
						<a href='index.php?content=customer&edit=y&id_customer=$id_customer' $can_edit>
								<button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button>
						</a>
					</td>
					<td align='center' >
						<a href='controller/customerController.php?delete=y&id_customer=$id_customer' $can_delete>
							<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ ".$customer->full_name." ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button>
						</a>
					</td>
				</tr>";
				$i++;
				}
			}else{ $html .="<tr><td colspan='10' align='center'><h3>ไม่มีรายการ</h3></td></tr>";}
				$html .="</table>";
		echo $html;
	}

?>