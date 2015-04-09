
<?php 
	$page_name = "รายงานบิลส่งสินค้าไปฝากขาย แยกตามโซน เรียงตามเอกสาร";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<form id="report_form" method="post">
<div class="row">
	<div class="col-sm-8"><h4 style="margin-bottom:0px; margin-top:0px;"><i class="fa fa-list"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-4"><p class="pull-right">
       <a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-info' id='report'><i class="fa fa-file-text-o"></i>&nbsp;รายงาน</button></a>
		<a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-success' id='gogo'><i class="fa fa-file-excel-o"></i>&nbsp;ส่งออก</button></a></p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<!-- End page place holder -->
<div class='row'>
<!-- ++++++++++++++++++++++++++++  โซน +++++++++++++++++++++++++++++-->    
<div class="col-lg-7" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding:0px; height:100px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">โซน</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-4" >
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='zone' id='zone1' value='0' checked="checked"/>&nbsp;
                </span>
                <label for="zone1" class="form-control" style="width:135px">ทั้งหมด</label>
                </div>
            </div>
            
            <div class="col-lg-8">
            		<div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        <input type='radio' name='zone' id='zone2' value='1'  />
                        <label for="zone2" style="padding-left:10px; margin-bottom:0px;">เฉพาะ</label>
                        </span>
                        <input type="hidden" name="zone_selected" id="zone_selected"  />
                		<input type="text" name="txt_zone" id="txt_zone" class="form-control" disabled="disabled" />
                	</div>
             </div>
             
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end โซน  +++++++++++++++++++++++++++++--> 

<!-- ++++++++++++++++++++++++++++  วันที่ +++++++++++++++++++++++++++++-->    
<div class="col-lg-5" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding:0px; height:100px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">วันที่</legend>
        <div class="col-lg-12">&nbsp;</div>
        <div class="col-lg-6" style="margin-bottom:10px;">
        	<div class="input-group input-group-sm">
            <span class="input-group-addon">จาก</span>
        	<input type='text' name='from_date' id='from_date' class='form-control' />
            </div>
        </div>
        <div class="col-lg-6" style="margin-bottom:10px;">
        	<div class="input-group input-group-sm">
            <span class="input-group-addon">ถึง&nbsp;&nbsp;</span>
        	<input type='text' name='to_date' id='to_date' class='form-control' />
            </div>
        </div>
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end วันที่  +++++++++++++++++++++++++++++--> 
</form>
 </div>
    

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' /><input type='hidden' id='zone' value='0' />
<div class='row'>
	<div class='col-lg-12' id='result'></div>
    </div>
</div>     
<script>
$(document).ready(function(e) {
    $("#txt_product_from").autocomplete({
		source:"controller/orderController.php?product_attribute",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#txt_product_from").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[0];
			$("#product_from").val(id);
			$(this).val(name);
		}
	});			
});
$(document).ready(function(e) {
    $("#txt_product_to").autocomplete({
		source:"controller/orderController.php?product_attribute",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#txt_product_to").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[0];
			$("#product_to").val(id);
			$(this).val(name);
		}
	});			
});
$(document).ready(function(e) {
   $("#txt_product_selected").autocomplete({
		source:"controller/orderController.php?product_attribute",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#txt_product_selected").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[0];
			$("#product_selected").val(id);
			$(this).val(name);
		}
	});			
});

$(document).ready(function(e) {
   $("#txt_zone").autocomplete({
		source:"controller/zoneController.php?get_consign_zone",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#txt_zone").val();
			var arr = data.split(':');
			var id = arr[1];
			var name = arr[0];
			$("#zone_selected").val(id);
			$(this).val(name);
		}
	});			
});
$(document).ready(function() {
   	    $("#product1").change(function(){
		$("#product").val(0);
		$("#txt_product_from").attr("disabled","disabled");
		$("#txt_product_to").attr("disabled","disabled");
		$("#txt_product_selected").attr("disabled","disabled");
	});
});

$(document).ready(function() {
   	    $("#product2").change(function(){
		$("#product").val(1);
		$("#txt_product_from").removeAttr("disabled");
		$("#txt_product_to").removeAttr("disabled");
		$("#txt_product_selected").attr("disabled","disabled");
		
	});
});

$(document).ready(function() {
   	    $("#product3").change(function(){
		$("#product").val(2);
		$("#txt_product_from").attr("disabled","disabled");
		$("#txt_product_to").attr("disabled","disabled");
		$("#txt_product_selected").removeAttr("disabled");
	});
});
$(document).ready(function() {
   	    $("#zone1").change(function(){
		$("#txt_zone").attr("disabled","disabled");
		$("#zone").val(0);
	});
});
$(document).ready(function() {
   	    $("#zone2").change(function(){
		$("#txt_zone").removeAttr("disabled");
		$("#zone").val(1);
	});
});

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
$(document).ready(function(e) {
    $("#gogo").click(function(){	
	var zone = $("#zone").val();
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
		if(from_date ==""){
			alert("กรุณาเลือกวันเริ่มต้น");
			return;
		}else if(to_date ==""){
			alert("กรุณาเลือกวันสิ้นสุด");
			return;
		}
		var view_report = "from_date="+from_date+"&to_date="+to_date;
	if(zone ==0){
		var zone_report = 'zone_all';
	}else if(zone ==1){
		var zone_select = $("#zone_selected").val();
		if(zone_select ==""){ alert("กรุณาเลือกโซน"); $("#txt_zone").focus(); return; }
		var zone_report = "zone_selected="+zone_select;	
	}else{
		var zone_report = 'zone_all';
	}
	$("#report_form").attr("action","controller/reportController.php?export_consignment_by_zone&"+view_report+"&zone="+zone+"&"+zone_report );
	$(this).attr("type", "submit");
});
});

$(document).ready(function(e) {
    $("#report").click(function(e) {
		get_report();
    });
});
function get_report(){
	var zone = $("#zone").val();
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
		if(from_date ==""){
			alert("กรุณาเลือกวันเริ่มต้น");
			return;
		}else if(to_date ==""){
			alert("กรุณาเลือกวันสิ้นสุด");
			return;
		}
		var view_report = "from_date="+from_date+"&to_date="+to_date;
	if(zone ==0){
		var zone_report = 'zone_all';
	}else if(zone ==1){
		var zone_select = $("#zone_selected").val();
		if(zone_select ==""){ alert("กรุณาเลือกโซน"); $("#txt_zone").focus(); return; }
		var zone_report = "zone_selected="+zone_select;	
	}else{
		var zone_report = 'zone_all';
	}
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังประมวลผล....</h4></td></tr></table>");
	$.ajax({
		url:"controller/reportController.php?consignment_by_zone&"+view_report+"&zone="+zone+"&"+zone_report , type:"POST",cache:false,
		success: function(dataset){
			$("#result").html(dataset);
		}
	});
		
}
</script>
