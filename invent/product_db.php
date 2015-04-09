<?php 

	$page_menu = "invent_stock_report";
	$page_name = "ฐานข้อมูลสินค้า";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='index.php?content=stock_report&stock_report=y' method='post'>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-list"></span>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(!isset($_GET['product_report'])){
		    echo"
       		<li><a style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link' id='report'><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button></a></li>
			<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' id='gogo'><span class='fa fa-file-excel-o' style='color:#5cb85c; font-size:35px;'></span><br />ส่งออก</button></a></li>
			";
		}else{
				echo"<li><a href='index.php?content=product_db' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
		}
				?>
                </ul>
                </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<!-- End page place holder -->
<?php
if(isset($_GET['product_report'])){
	if(isset($_POST['category'])){ $id_category = $_POST['category']; }else{ $id_category = 0; }
	if(isset($_POST['cost'])&&$_POST['cost']==1){ $show_cost = true; }else{ $show_cost = false; }
	if(isset($_POST['price'])&&$_POST['price']==1){ $show_price = true; }else{ $show_price = false; }
	echo " <div class='row'>
	<div class='col-lg-12'>
	<table class='table table-striped'>
	<thead> <th style='width:5%; text-align:center'>ลำดับ</th><th style='width:20%; text-align:center;'>บาร์โค้ด</th>
				<th style='width:30%;'>รหัสค้า</th><th style='width:30%;'>ชื่อสินค้า</th>";
				if($show_cost){ echo "<th style='width:15%; text-align:center;'>ทุน</th>"; } if($show_price){ echo"<th style='width:15%; text-align:center;'>ราคา</th>";}
	echo"	</thead>";
	if($id_category >0){ $where = "WHERE default_category_id = $id_category"; }else{ $where = ""; }
	$sql = dbQuery("SELECT id_product, product_name, product_cost, product_price FROM tbl_product $where");
	$n =1;
	while($row = dbFetchArray($sql)){
		$id_product = $row['id_product'];
		$product_name = $row['product_name'];
		$product_cost = $row['product_cost'];
		$product_price = $row['product_price'];
		$sqr = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = $id_product");
		while($rs=dbFetchArray($sqr)){
			$id_product_attribute = $rs['id_product_attribute'];
			$product = new product();
			$product->product_attribute_detail($id_product_attribute);
			echo"<tr><td align='center'>$n</td><td align='center'>".$product->barcode."</td><td>".$product->reference."</td><td>".$product_name."</td>";
			if($show_cost){ echo"<td align='center'>".number_format($product_cost,2)."</td>"; } 
			if($show_price){ echo"<td align='center'>".number_format($product_price,2)."</td>"; } echo"</tr>";
			$n++;
		}
	}
		
	echo"</table></div></div>";
}else{
	echo"
<div class='row'><div class='col-lg-12'>&nbsp;</div>
	<div class='col-lg-4 col-lg-offset-4'>
		<div class='input-group'>
			<span class='input-group-addon'>หมวดหมู่ : </span><select name='category' id='category' class='form-control' >".category_list()."</select>
		</div>
	</div>
	<div class='col-lg-4' style='padding-top:5px;'><input type='checkbox' name='cost' id='cost' value='1' checked /><label for='cost' style='padding-left:10px; padding-right:15px;'>แสดงราคาทุน</label><input type='checkbox' value='1' name='price' id='price' checked /><label for='price' style='padding-left:10px; padding-right:15px;'>แสดงราคาขาย</label></div>
	
</div></form> ";
}
	
?>   
</div>    
<script>
/*
$(document).ready(function() {
	$("#product_from" ).autocomplete(
	{
		 source: 'controller/productController.php'
	});
	
});
$(document).ready(function() {
	$("#product_to" ).autocomplete(
	{
		 source: 'controller/productController.php'
	});
});
*/
$(document).ready(function(e) {
    $("#gogo").click(function(){
		$("#report_form").attr("action", "controller/reportController.php?product_report=y");
		$(this).attr("type", "submit");
	});
});
$(document).ready(function(e) {
    $("#report").click(function(e) {
        $("#report_form").attr("action", "index.php?content=product_db&product_report=y");
    });
});

</script>