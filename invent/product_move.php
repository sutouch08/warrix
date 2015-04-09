<?php 
	$page_menu = "invent_product_move";
	$page_name = "ย้ายพื้นที่จัดเก็บ";
	$id_tab = 9;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($view==0){ echo accessDeny(); exit; }
	if($edit==0){ echo accessDeny(); exit; }
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
	?>

<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-transfer"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
    	<?php if(isset($_POST['zone'])){
			
		echo"
       	<li><a href='index.php?content=ProductMove' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-share' style='color:#5cb85c; font-size:30px;'></span><br />โซนใหม่</button></a></li>";
		echo"
       	<li><a href='index.php?content=ProductMove&productMove=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-log-out' style='color:#5cb85c; font-size:30px;'></span><br />สินค้าที่ย้าย</button></a></li>";
		}else if(isset($_GET['id_zone'])){
		echo"
       	<li><a href='index.php?content=ProductMove' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-share' style='color:#5cb85c; font-size:30px;'></span><br />โซนใหม่</button></a></li>";
		echo"
       	<li><a href='index.php?content=ProductMove&productMove=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-log-out' style='color:#5cb85c; font-size:30px;'></span><br />สินค้าที่ย้าย</button></a></li>";
		}else if(isset($_GET['productMove'])){
			 echo"
       		<li><a href='index.php?content=ProductMove' style='text-align:center; background-color:transparent;' ><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
		}else{
			echo"
       	<li ><a href='index.php?content=ProductMove&productMove=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-log-out' style='color:#5cb85c; font-size:30px;'></span><br />สินค้าที่ย้าย</button></a></li>";
		}
		?>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:15px;" />
<!-- End page place holder -->

<div class='col-sm-12'>
<?php 
if(isset($id_zone)){
	echo 
	"<form method='post' name='zone' action='controller/productmoveController.php?moveout=y'>
	<table border='0' width='80%' align='center'>
    	<tr>
			<td width='20%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>จำนวน</span>
                 	<input type='text' name='qty' class='form-control' placeholder='' value='1' required  >
					<input type='hidden' name='id_zone' class='form-control' placeholder='' value='$id_zone' >
				</div>
			</td>
			<td width='5%'>
			</td>
            <td width='45%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>บาร์โค้ด</span>
                 	<input type='text' name='barcode' class='form-control' placeholder='' required autofocus >
				</div>
			</td>
            <td width='10%' align='center'><input type='submit' class='btn btn-primary' value='ย้าย' /></td>
            <td width='20%' align='right'></td>
        </tr>
    </table>
    </form>";
}else if(isset($_GET['id_zone'])){
	echo 
	"<form method='post' name='zone' action='controller/productmoveController.php?moveout=y'>
	<table border='0' width='80%' align='center'>
    	<tr>
			<td width='20%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>จำนวน</span>
                 	<input type='text' name='qty' class='form-control' placeholder='' value='1' required  >
					<input type='hidden' name='id_zone' class='form-control' placeholder='' value='$id_zone' >
				</div>
			</td>
			<td width='5%'>
			</td>
            <td width='45%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>บาร์โค้ด</span>
                 	<input type='text' name='barcode' class='form-control' placeholder='' required autofocus >
				</div>
			</td>
            <td width='10%' align='center'><input type='submit' class='btn btn-primary' value='ย้าย' /></td>
            <td width='20%' align='right'></td>
        </tr>
    </table>
    </form>";
}else if(isset($_GET['productMove'])){
	if(isset($_GET['in'])){
		$id = $_GET['id'];
		list($id_product_attribute,$reference,$qty_move) = dbFetchArray(dbQuery("SELECT tbl_move.id_product_attribute,reference,qty_move FROM tbl_move LEFT JOIN tbl_product_attribute ON tbl_move.id_product_attribute = tbl_product_attribute.id_product_attribute where id_move = '$id'"));
		echo 
	"<form method='post' name='zone' action='controller/productmoveController.php?movein=y'>
	<table border='0' width='80%' align='center'>
		<tr>
			<td width='60%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>ชื่อสินค้า</span>
                 	<input type='text' name='reference' class='form-control' placeholder='' value='$reference' disabled='disabled' >
					<input type='hidden' name='id_move' class='form-control' placeholder='' value='$id' >
					<input type='hidden' name='id_product_attribute' class='form-control' placeholder='' value='$id_product_attribute' >
				</div>
			</td>
            <td width='40%' align='right'></td>
        </tr>
		 </table>
		 <table border='0' width='80%' align='center' height='50px'>
    	<tr>
			<td width='20%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>จำนวน</span>
                 	<input type='text' name='qty_move' class='form-control' placeholder='' value='$qty_move' required  >
					<input type='hidden' name='qty' class='form-control' placeholder='' value='$qty_move'  >
				</div>
			</td>
			<td width='5%'>
			</td>
            <td width='45%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>บาร์โค้ด,ชื่อ</span>
                 	<input type='text' name='zone' class='form-control' placeholder='' required autofocus >
				</div>
			</td>
            <td width='10%' align='center'><input type='submit' class='btn btn-primary' value='ย้าย' /></td>
            <td width='20%' align='right'></td>
        </tr>
    </table>
    </form>";
	}
}else{
	echo 
	"<form method='post' name='zone' action='index.php?content=ProductMove'>
	<table border='0' width='80%' align='center'>
    	<tr>
            <td width='65%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>ชื่อโซน,บาร์โค้ดโซน</span>
                 	<input type='text' name='zone' class='form-control' placeholder='' required autofocus >
				</div>
			</td>
            <td width='15%' align='center'><input type='submit' class='btn btn-primary' value='ตกลง' /></td>
            <td width='20%' align='right'></td>
        </tr>
    </table>
    </form>";
}
	?>

</div>

<div class="row">
<div class="col-sm-12">
<br />
	<?php 
	
	if(isset($_GET['productMove'])){
		if(isset($_GET['message'])){
				$message = $_GET['message'];
				echo"<div class='alert alert-success' align='center'>$message</div>";
		}
		if(isset($_GET['error'])){
			$error_message = $_GET['error'];
			echo"<div id='error' class='alert alert-danger' >
			<b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
		} 
		?>
		<table class="table table-striped">
        <thead>
        <tr><th colspan="3" style="text-align:center">สินค้าที่กำลังย้าย</th></tr><tr><th width="55%" style="text-align: left">ชื่อสินค้า</th><th width="15%"  style="text-align: right">จำนวน</th><th width="15%"  style="text-align: center">เข้าโซน</th></tr>
		</thead>
		<?php 
		$sql = dbQuery("SELECT * FROM tbl_move LEFT JOIN tbl_product_attribute ON tbl_move.id_product_attribute = tbl_product_attribute.id_product_attribute");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			$result = dbFetchArray($sql);
			$id_move = $result['id_move'];
			$reference = $result['reference'];
			$qty_move = $result['qty_move'];
			$id_product_attribute = $result['id_product_attribute'];
			echo "<tr style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductMove&productMove=y&in=in&id=$id_move'\">
					<td align='left'>$reference</td><td align='right'>$qty_move</td><td align='center'><a href='index.php?content=ProductMove&productMove=y&in=in&id=$id_move' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-log-in' style='color:#5cb85c; font-size:16px;'></span></button></a></td>
				</tr>";
		$i++;
		}
		if($row == "0"){
			echo "<td align='left' colspan='3'><div class='alert alert-info'  align='center'>ไม่มีสินค้าที่กำลังย้าย</div></td>";
		}
	?>
	</table>
    <?php
	}
	if($check == "1"){
		if($id_zone == ""){
			echo "<div class='alert alert-danger' align='center'>ไม่มีโซนนี้กรุณาตรวจสอบ</div>";
		}else{
			if(isset($_GET['message'])){
				$message = $_GET['message'];
				echo"<div class='alert alert-success' align='center'>$message</div>";
			}
	?>
		<table class="table table-striped">
        <thead>
        <tr><th colspan="3" style="text-align:center">โซน <?php echo $zone_name;?></th></tr><tr><th width="70%" style="text-align: left">ชื่อสินค้า</th><th width="15%"  style="text-align: right">จำนวน</th></tr>
		</thead>
		<?php 
		$sql = dbQuery("SELECT * FROM stock where id_zone = '$id_zone'");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			$result = dbFetchArray($sql);
			$zone = $result['Zone'];
			$product = $result['Product'];
			$qty = $result['qty'];
			$id_product_attribute = $result['id_product_attribute'];
			list($qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff WHERE id_product_attribute = '$id_product_attribute' and id_zone = '$id_zone' and status_diff = '0'"));
			$sumqty = $qty +($qty_add - $qty_minus);
			echo "<tr>
					<td align='left'>$product</td><td align='right'>$sumqty</td>
				</tr>";
		$i++;
		}
		if($row == "0"){
			echo "<td align='left' colspan='3'><div class='alert alert-info'  align='center'>ไม่มีสินค้าในโซนนี้</div></td>";
		}
	?>
	</table>
	<?php 
	}
	}
	?>
</div>
</div></div>