<?php
	$page_menu = "invent_sale_report";
	$page_name = "รายงานยอดขายแยกตามเลขที่เอกสาร";
	$id_profile = $_COOKIE['profile_id'];
	?>

<div class="container">
 <form name='report_form' id='report_form' action='controller/reportController.php?export_sale_by_document' method='post'> 
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-list"></span>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	
		    echo"
       		<li><button type='button' class='btn btn-link' id='report'><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button></li>
			<li><button type='button' class='btn btn-link' id='gogo'><span class='fa fa-file-excel-o' style='color:#5cb85c; font-size:35px;'></span><br />ส่งออก</button></li>";
				?>
                </ul>
                </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php

	echo"
<div class='row'>
	<!-- <div class='col-lg-2'>
		<div class='row'>
			<div class='col-lg-2'><input type='radio' name='veiw' id='view_all' value='0' /></div>
			<div class='col-lg-10'><label class='form-control' for='view_all'>เลือกทั้งหมด</label></div>
    </div>
	</div> -->
    <div class='col-lg-3'>
		<div class='row'>
			<div class='col-lg-1'><input type='radio' name='veiw' id='view_in' value='1' checked /></div>
			<div class='col-lg-10'><select name='view_selected' id='view_selected' class='form-control' >"; get_view_list($view); echo"</select></div>
		</div>
    </div>
	<div class='col-lg-4'>
		<div class='row'>
			<div class='col-lg-1'><input type='radio' name='veiw' id='view_rank' value='2' /></div>
			<div class='col-lg-5'><input type='text' class='form-control' name='from_date' id='from_date'disabled /></div>
			<div class='col-lg-5'><input type='text' class='form-control' name='to_date' id='to_date' disabled /></div>
		</div>
    </div>
</div> ";

?>   
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' /><input type='hidden' id='view' value='1' />
<div class='row'>
	<div class='col-lg-12' id='result'></div>
    </div>
</div>     
<script>
$(document).ready(function(){ 
	$('input').iCheck({ checkboxClass: 'icheckbox_flat-blue', radioClass: 'iradio_square-blue', increaseArea: '20%' 
	 });
});
$(document).ready(function() {
   	    $("#view_all").change(function(){
		$("#view_selected").attr("disabled","disabled");
		$("#from_date").attr("disabled","disabled");
		$("#to_date").attr("disabled","disabled");
		$("#view").val(0);
	});
});
$(document).ready(function() {
   	    $("#view_in").change(function(){
		$("#view_selected").removeAttr("disabled");
		$("#from_date").attr("disabled","disabled");
		$("#to_date").attr("disabled","disabled");
		$("#view").val(1);
	});
});
$('input').on('ifChecked', function(event){
 $(this).change();
});
$(document).ready(function() {
   	    $("#view_rank").change(function(){
		$("#from_date").removeAttr("disabled");
		$("#to_date").removeAttr("disabled");
		$("#view_selected").attr("disabled","disabled");
		$("#view").val(2);
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
	var view = $("#view").val();
	if(view ==0){
		var view_report = 'all';
	}else if(view ==1){
		var view_select = $("#view_selected").val();
		var view_report = "view_selected="+view_select;
	}else if(view ==2){
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		if(from_date ==""){
			alert("กรุณาเลือกวันเริ่มต้น");
		}else if(to_date ==""){
			alert("กรุณาเลือกวันสิ้นสุด");
		}
		var view_report = "from_date="+from_date+"&to_date="+to_date;
	}else{
		var view_report = 'all';
	}
	$("#report_form").attr("action","controller/reportController.php?export_sale_by_document&view="+view+"&"+view_report);
	$(this).attr("type", "submit");
});
});
$(document).ready(function(e) {
    $("#report").click(function(e) {
		get_report();
    });
});
function get_report(){
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>Loading...</h4></td></tr></table>");
	var view = $("#view").val();
	if(view ==0){
		var view_report = 'all';
	}else if(view ==1){
		var view_select = $("#view_selected").val();
		var view_report = "view_selected="+view_select;
	}else if(view ==2){
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		if(from_date ==""){
			alert("กรุณาเลือกวันเริ่มต้น");
		}else if(to_date ==""){
			alert("กรุณาเลือกวันสิ้นสุด");
		}
		var view_report = "from_date="+from_date+"&to_date="+to_date;
	}else{
		var view_report = 'all';
	}
	$.ajax({
		url:"controller/reportController.php?sale_by_document&view="+view+"&"+view_report , type:"GET",cache:false,
		success: function(dataset){
			$("#result").html(dataset);
		}
	});
		
}
</script>