<!-- styles needed by minimalect -->
<link href='assets/css/jquery.minimalect.min.css' rel='stylesheet'>
<script>
function dropproductcartfull(id_cart,id) {
		  $.ajax({
					type: 'GET',
					url: 'controller/cartController.php?dropproductcartfull=Y&id_cart='+id_cart+'&id_product_attribute='+id,
					success: function(row)	{
						$('#cartfull').html(row);
						dropproductcart(id_cart,id);
						showsumtotal(id_cart);
					}
				});
		
		
}
function updatecart(id_cart,id,n){
				var qty = $('#quanity'+n).val();
	 			$.ajax({
					type: 'GET',
					url: 'controller/cartController.php?updateproductcartfull=Y&id_cart='+id_cart+'&id_product_attribute='+id+'&qty='+qty,
					success: function(row)	{
						$('#cartfull').html(row);
						dropproductcart(id_cart,'0');
					}
				});
		
}
function up(id_cart,id_product_attribute,n){
	var qty = $('#quanity'+n).val();
	qtyup = parseInt(qty)+1;
	$('#quanity'+n).val(qtyup);
	updatecart(id_cart,id_product_attribute,n);
}
function down(id_cart,id_product_attribute,n){
	var qty = $('#quanity'+n).val();
	qtyup = qty-1;
	$('#quanity'+n).val(qtyup);
	updatecart(id_cart,id_product_attribute,n);
}
function getcondition(){
	
	if(checkboxes.checked){
		var id_customer = $("#customer_id").val();
		if(id_customer !=0){
			$("#checkcondition").html("<button class='btn btn-primary btn-lg' id='check_condition' width='50%' type='submit'><i class='fa fa-arrow-right'></i>&nbsp;   ดำเนินการสั่งซื้อ </button>");
		}else{
			alert("คุณยังไม่ได้ระบุลูกค้า");
		}
	}else{
		$("#checkcondition").html("<button class='btn btn-primary btn-lg' id='check_condition' width='50%'disabled='disabled' ><i class='fa fa-arrow-right'></i>&nbsp;   ดำเนินการสั่งซื้อ </button>");
	}
}
</script>
<div class='container'>
<?php
if(isset($_COOKIE['id_customer'])){ $id_customer = $_COOKIE['id_customer']; }else{ $id_customer = 0;}
if(!isset($id_customer)){
	echo"<form action ='controller/orderController.php?new=y' method='post'>
        <div class='col-lg-4 col-md-4 col-sm-6 col-xs-6 col-lg-offset-4 col-md-offset-4 col-sm-offset-3 col-xs-offset-3'>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>	<h3>เลือกลูกค้า </h3> </div>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><select name='id_customer' id='id_customer' class='form-control input-sm input-sx'>"; customerList(getSaleId($_COOKIE['user_id'])); echo "</select> </div>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>&nbsp; </div>
		 <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><button class='form-control btn-success' type='submit' >ตกลง</button></div>
		</div>
		</form>
	";
}else{
	$customer =new customer($id_customer);
	if(isset($_COOKIE['id_cart'])){ $id_cart = $_COOKIE['id_cart']; }else{ $id_cart="";}
	if(isset($_GET['id_category'])){ $id_cate = $_GET['id_category']; } 
	echo"<div class='modal fade' id='customer_change' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'>--- เลือกลูกค้า ---</h4>
									  </div>
									  <div class='modal-body'>
									  <form action ='controller/orderController.php?new=y' method='post'>
									<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><br /><select name='id_customer' id='id_customer' class='form-control input-sm input-sx'>"; 
									customerList(getSaleId($_COOKIE['user_id'])); echo "</select> </div>
									<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>&nbsp; </div>
									  </div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>ตกลง</button></form>
									  </div>
									</div>
								  </div>
								</div>";
	echo "<form action ='index.php?content=order&new=y' method='post'>
        <div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'><p class='pull-right'>";
		if($id_customer !=0){ echo"<input type='hidden' id='customer_id' value='$id_customer' />
		คุณกำลังสั่งสินค้าให้กับลูกค้า &nbsp; <span class='glyphicon glyphicon-arrow-right' ></span>&nbsp;<b>&nbsp;".$customer->full_name."</b>&nbsp;หากไม่ใช่กรุณา &nbsp;
		<a href='#' data-toggle='modal' data-target='#customer_change'>	
		<button type='button' class='btn btn-warning btn-xs'><span class='glyphicon glyphicon-pencil'></span>แก้ไข</button> </a>
		หรือ <a href='controller/orderController.php?cancle=true&id_cart=$id_cart&id_customer=$id_customer'><button type='button' class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-trash'></span>ยกเลิก</button></a>";
		}else{ echo" <input type='hidden' id='customer_id' value='$id_customer' /> คุณยังไม่ได้เลือกลูกค้า ต้องการสั่งสินค้ากรุณา <a href='#' data-toggle='modal' data-target='#customer_change'>	<button type='button' class='btn btn-success'><span class='fa fa-user'></span>เลือกลูกค้า</button> </a>";
		} echo"</p>
		 </div></div>
		</form>
		<div class='row'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></div>
	";
}
?>  
  <div class='row'>
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <h1 class='section-title-inner'><span><i class='glyphicon glyphicon-shopping-cart'></i> ตะกร้าสินค้า </span></h1>
    </div>
    <div class='col-lg-3 col-md-3 col-sm-5 rightSidebar'>
      <h4 class='caps'><a href='index.php'><i class='fa fa-chevron-left'></i>&nbsp;ซื้อสินค้าต่อ </a></h4>
    </div>
  </div><!--/.row-->
  <?php if(isset($_COOKIE['id_cart'])){
	  if(isset($_COOKIE['id_customer'])){
		  $id_customer = $_COOKIE['id_customer'];
	  }else{
		  $id_customer =0;
	  }
  ?>
<div id='cartfull'><?php if(isset($_GET['error'])){
				$error_message = $_GET['error'];
				 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
			}echo $cart_mini->cartfull($id_cart,$id_customer);  ?></div>

    <div style='clear:both'></div>

		<?php  $cart_mini->sale_confirm($id_cart,$id_customer);?>
     

  <div style='clear:both'></div><?php }else{
	  if(isset($_GET['finish'])){
	 echo "<div id='cartfull'><div class='alert alert-success'>ดำเนินการสั่งซื้อเรียบร้อยแล้ว</div></div>";

	  }else{
		 echo "<div id='cartfull'><div class='alert alert-warning'>ไม่มีสินค้าในตะกร้าของคุณ</div></div>"; 
	  }
  }
	  ?>
</div>
<!-- /.main-container-->
<div class='gap'></div>

