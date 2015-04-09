<?php 
	$page_menu = "invent_sale_report_zone";
	$page_name = "รายงานยอดขาย แยกตามพื้นที่การขาย";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-8"><h3><span class="glyphicon glyphicon-list"></span>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-4">
       <ul class="nav navbar-nav navbar-right">
			<li>
                <a style='text-align:center; background-color:transparent; padding-bottom:0px;'>
                	<button type='submit' class='btn btn-link' id='report'  onclick="get_report()"><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button>
                </a>
            </li>
			<li>
            	<a style='text-align:center; background-color:transparent; padding-bottom:0px;'>
                	<button type='button' class='btn btn-link' id='gogo'><span class='fa fa-file-excel-o' style='color:#5cb85c; font-size:35px;'></span><br />ส่งออก</button>
                </a>
            </li>
        </ul>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php
if(isset($_POST['from_date'])){ $from_date = $_POST['from_date']; }else{ $from_date = "เลือกวัน"; }
if(isset($_POST['to_date'])){ $to_date = $_POST['to_date']; }else{ $to_date = "เลือกวัน"; }
echo"
<div class='row'>
    <input type='hidden' id='export' value='controller/reportController.php?sale_report_zone&from_date=$from_date&to_date=$to_date'/>
   <div class='col-lg-2'>
   		<div class='input-group'><span class='input-group-addon'>จาก : </span><input type='text' name='from_date' id='from_date' class='form-control' value='$from_date' required /></div>
    </div>
	<div class='col-lg-2'>
   		<div class='input-group'><span class='input-group-addon'>ถึง : </span><input type='text' name='to_date' id='to_date' class='form-control' value='$to_date' required /></div>
    </div>
	<div class='col-lg-2'>
   		<button type='submit' id='btn_submit' style='display:none;' onclick='get_report()' >ตกลง</button>
    </div>
    </form>
</div> ";
echo "<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	//////////////    แสดงผลรายงาน ตามเงื่อนไขที่เลือกไป /////////////////////
	echo "<div class='row'>
<div class='col-lg-12' id='result'>
</div>
</div>
";
?>   
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
function get_report(){
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังประมวลผล....</h4></td></tr></table>");
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	$.ajax({
		url:"controller/reportController.php?SaleReportZone&from_date="+from_date+"&to_date="+to_date, type:"GET", cache:false,
		success: function(data){
			$("#result").html(data);
		}
	});
}

</script>