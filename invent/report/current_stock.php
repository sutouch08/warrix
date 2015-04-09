<?php 
	//include("function/tools.php");
	$page_menu = "invent_stock_report";
	$page_name = "รายงานสินค้าคงเหลือ";
	$id_tab = 10;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
//	if($view==0){ echo accessDeny(); exit; }
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3 style='margin-top:5px; margin-bottom:5px;'><i class='fa fa-cart'></i>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
    	<div calss='form-inline'>
        	<p class='pull-right'>
            	<button type='button' class='btn btn-success' id='show_all'>แสดงทั้งหมด</button>
                <button type='button' class='btn btn-primary' id='instock'>เฉพาะที่มียอด</button>
                <button type='button' class='btn btn-danger' id='non_stock'>เฉพาะที่ไม่มียอด</button></p></div>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class='row'>
<div class='col-lg-12' id='result'>
</div>
</div>
</div>
<script>
function getData(id_product){
	$.ajax({
		url:"controller/reportController.php?getData&id_product="+id_product,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
function getData1(id_product){
	$.ajax({
		url:"controller/reportController.php?getData&id_product="+id_product,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal1").css("width",table_w+"px");
				$("#modal_title1").html(title);
				$("#modal_body1").html(data);
				$("#btn_toggle1").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
$("#show_all").click(function(e) {
    var option = "show_all";
	get_report(option);
});
$("#instock").click(function(e) {
    var option = "in_stock";
	get_report(option);
});
$("#non_stock").click(function(e) {
    var option = "non_stock";
	get_report(option);
});

function get_report(option){
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังประมวลผล....</h4></td></tr></table>");
	$.ajax({
		url:"controller/reportController.php?get_stock&option="+option, type:"GET", cache:false,
		success: function(data){
			$("#result").html(data);
		}
	});
}
</script>
