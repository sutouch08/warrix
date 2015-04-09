<?php 
	$page_menu = "invent_product_check";
	$page_name = "ตรวจนับสินค้า";
	if(isset($_POST['zone'])){
		$zone = $_POST['zone'];
		list($id_zone,$zone_name) = dbFetchArray(dbQuery("select id_zone,zone_name from tbl_zone where barcode_zone = '$zone' or zone_name = '$zone'"));
		$check = "1";
	}else if(isset($_GET['id_zone'])){
		$id_zone = $_GET['id_zone'];
		list($zone_name) = dbFetchArray(dbQuery("select zone_name from tbl_zone where id_zone = '$id_zone'"));
		$check = "1";
	}else{
		$check = "";
	}
	$id_tab = 10;
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
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-check"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_POST['zone'])){ 
	   if($id_zone == ""){
	   }else{
	   		echo"
       	<li $can_add><a href='index.php?content=ProductCheck&add=y&id_zone=$id_zone' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />เพิ่ม</button></a></li>";
		   echo"
       		<li $can_edit ><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='document.edit_qty.submit();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }
	   }else if(isset($_GET['id_zone'])){
		   if(isset($_GET['add'])){
		    echo"
       		<li><a href='index.php?content=ProductCheck&id_zone=$id_zone&back=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
		   }
		   echo"
       	<li $can_add><a href='index.php?content=ProductCheck&add=y&id_zone=$id_zone' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />เพิ่ม</button></a></li>";
		   echo"
       		<li $can_edit><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='document.edit_qty.submit();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>"; 
			
	   }else{
		 
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:15px;" />
<!-- End page place holder -->

<div class='col-sm-12'>
<?php if(isset($_GET['add'])){
	echo 
	"<form method='post' name='add' action='controller/productcheckController.php?add=Y' >
    
    <table border='0' width='80%' align='center'>
    <tr>
    <td width='30%'><input type='hidden' name='id_zone' value='$id_zone'  />
    	<div class='input-group'>
            <span class='input-group-addon'>จำนวนที่นับจริง</span>
            <input type='text' name='number' class='form-control' placeholder='' required autofocus onkeypress='checknumber()' >
		</div></td>
    <td width='5%'></td>
    <td width='50%'>
    	<div class='input-group'>
            <span class='input-group-addon'>บาร์โค้ด</span>
            <input type='text' name='barcode_item' class='form-control' placeholder='' required onkeypress='checknumber()' >
		</div></td>
    <td width='15%' align='center'><input type='submit' class='btn btn-primary' value='ตกลง' /></td>
    </tr>
    </table></form>";
    if(isset($_GET['id_zone']) && isset($_GET['not'])){  echo "<br /><table border='0' width='100%' align='center'><tr><td  align='center'> <div class='alert alert-danger' align='center'>ไม่มีสินค้านี้</div></td></tr></table>";}
	}else{
		echo 
	"<form method='post' name='zone' action='index.php?content=ProductCheck'>
	<table border='0' width='80%' align='center'>
    	<tr>
            <td width='65%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>ชื่อโซน,บาร์โค้ดโซน</span>
                 	<input type='text' name='zone' class='form-control' placeholder='' required autofocus >
				</div>
			</td>
            <td width='15%' align='center'><input type='submit' class='btn btn-primary' value='ตกลง' /></td>
            <td width='20%' align='right'>"; if(isset($_GET['back'])){}else if(isset($_GET['id_zone'])){echo "<span class='glyphicon glyphicon-saved' style='color:#5cb85c; font-size:18px;'>บันทึกเรียบร้อย</span>";} echo "</td>
        </tr>
    </table>
    </form>";
 }?>
</div>

<div class="row">
<div class="col-sm-12">
<br />
	<?php 
	
	if($check == "1"){
		if($id_zone == ""){
			echo "<div class='alert alert-danger' align='center'>ไม่มีโซนนี้กรุณาตรวจสอบ</div>";
		}else{
	?>
    <form method="post" name="edit_qty" action="controller/productcheckcontroller.php?editqty=y"   >
		<table class="table table-striped">
        <thead>
        <tr>
		<th colspan="3" style="text-align:center">โซน <?php echo $zone_name;?> </th>
		</tr>
		<tr>
		<th width="60%" style="text-align: left">ชื่อสินค้า</th><th width="15%"  style="text-align: right">จำนวนก่อนนับ</th><th width="15%" style="text-align: right">จำนวนที่นับจริง</th><th width="10%" style="text-align: right">ยอดต่าง</th>
		</tr>
		</thead>
		<?php 
		$sql = dbQuery("SELECT * FROM stock where id_zone = '$id_zone'");
		$row = dbNumRows($sql);
		$i = 0;
		$n = "";
		while($i<$row){
			$result = dbFetchArray($sql);
			$n = $n + 1;
			$zone = $result['Zone'];
			$product = $result['Product'];
			$qty = $result['qty'];
			$id_product_attribute = $result['id_product_attribute'];
			list($qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff where id_zone = '$id_zone' and id_product_attribute = '$id_product_attribute' and status_diff != '2'"));
			$diff = $qty_add - $qty_minus;
			$qty_sum = $qty + $diff;
			echo "<tr>
						<td align='left'>$product</td><td align='right'>$qty<input type='hidden' name='qty[]' value='$qty_sum' /> </td><td ><div class='col-sm-3'></div><div class='col-sm-9'><input type='text' value='$qty_sum' name='qty_check[]' class='form-control' onkeypress='checknumber()'";if($can_edit != ""){echo "disabled='disabled'";}echo "/></div>
						<input type='hidden' name='id_product_attribute[]' value='$id_product_attribute' /></td><td align='right'>$diff</td>
					</tr>";
		$i++;
		}
		if($row == "0"){
			echo "<td align='left' colspan='4'><div class='alert alert-info'  align='center'>ไม่มีสินค้าในโซนนี้</div></td>";
		}
?>
		</table>
        <input type="hidden" name="id_zone" value="<?php echo $id_zone;?>"  />
        </form>
<?php 
	}
	}else{
		echo "<span style='text-align: center'><h3>กรุณาระบุโซน</h3></span>";
	}
?>

</div>
</div></div>