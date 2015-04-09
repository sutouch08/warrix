<?php
	$page_menu = "invent_stock_non_move";
	$page_name = "รายงานสินค้าไม่เคลื่อนไหว";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='index.php?content=non_move&stock_report=y' method='post'>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-list"></span>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       		<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' id='report'><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button></a></li>
			<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' id='gogo'><span class='fa fa-file-excel-o' style='color:#5cb85c; font-size:35px;'></span><br />ส่งออก</button></a></li>
        </ul>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<div class='row'>
    <div class='col-lg-3'>
			<div class='input-group'>
            	<span class='input-group-addon'>เลือกคลัง</span>
				<select name='warehouse_selected' id='warehouse_selected' class='form-control' ><option value='0'>--- ทุกคลัง ----</option><?php  warehouseList(); ?></select>
			</div>
	</div>
   <div class='col-lg-2'>
   		<div class='input-group'>
        <span class='input-group-addon'>จาก</span>
        <input type='text' name='from_date' id='from_date' class='form-control' />
        </div>
    </div>
	<div class='col-lg-2'>
   		<div class='input-group'><span class='input-group-addon'>ถึง</span><input type='text' name='to_date' id='to_date' class='form-control' /></div>
    </div>
    </form>
</div>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<div class='row'>
	<div class='col-lg-12' id='result'></div>
    </div>
</div>     
<script>
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
		var action = $("#export").val();
		$("#report_form").attr("action", action );
		$(this).attr("type", "submit");
	});
});
$(document).ready(function(e) {
    $("#report").click(function(e) {
        $("#report_form").attr("action", "index.php?content=non_move&stock_report=y");
		$("#btn_submit").click();
    });
});

$(document).ready(function(e) {
    $("#gogo").click(function(){	
	var warehouse = $("#warehouse_selected").val();
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if(from_date =="" || to_date == ""){
		alert("ยังไม่ได้เลือกช่วงเวลา");
		return;
	}
	$("#report_form").attr("action", "controller/reportController.php?export_stock_non_move&id_warehouse="+warehouse+"&from_date="+from_date+"&to_date="+to_date);
	$(this).attr("type", "submit");
});
});

$(document).ready(function(e) {
    $("#report").click(function(e) {
		get_report();
    });
});
function get_report(){
	var warehouse = $("#warehouse_selected").val();
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if(from_date =="" || to_date == ""){
		alert("ยังไม่ได้เลือกช่วงเวลา");
		return;
	}
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังประมวลผล....</h4></td></tr></table>");
	$.ajax({
		url:"controller/reportController.php?stock_non_move&id_warehouse="+warehouse+"&from_date="+from_date+"&to_date="+to_date , type:"GET",cache:false,
		success: function(dataset){
			$("#result").html(dataset);
		}
	});
		
}
</script>