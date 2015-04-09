<?php
	$pop_on = "back";
	$sql = dbQuery("SELECT delay, start, end, content, width, height FROM tbl_popup WHERE pop_on = '$pop_on' AND active =1");
	$row = dbNumRows($sql);
	if($row>0){
		list($delay, $start, $end, $content, $width, $height ) = dbFetchArray($sql);
		$popup_content ="<div class='row'><div class='col-lg-12'>$content</div></div>";
		include "../library/popup.php";
		$today = date('Y-m-d H:i:s');
		if(isset($_COOKIE['pop_back'])&&$_COOKIE['pop_back'] !=$delay){ setcookie('pop_back','',time()-3600); }
		if($start<=$today &&$end>=$today){  
			if(!isset($_COOKIE['pop_back'])){
				setcookie("pop_back", $delay, time()+$delay);
				echo" <script> $(document).ready(function(e) {  $('#modal_popup').modal('show'); }); </script>";
			}
		}
	}
		
?>
<div class='container'>
	<div class='row'><div class='col-xs-12'>&nbsp;</div></div>
	<div class='row' style="margin-bottom:10px;">
   		 <div class='col-xs-2'><label  style="margin-left:10px; margin-right:10px;"><input type='radio' name="fillter" id="fillter" value="reference" checked="checked"/><span style="margin-left:10px; margin-right:10px;">รหัสสินค้า</span></label></div>
         <div class='col-xs-2'><label ><input type='radio' name="fillter" id="fillter" value="product_name"/><span style="margin-left:10px; margin-right:10px;">ชื่อสินค้า</span></label></div>
         <div class='col-xs-2'><label ><input type='radio' name="fillter" id="fillter" value="barcode"/><span style="margin-left:10px; margin-right:10px;">บาร์โค้ดสินค้า</span></label></div>
         <div class='col-xs-2'><label ><input type='radio' name="fillter" id="fillter" value="barcode_pack"/><span style="margin-left:10px; margin-right:10px;">บาร์โค้ดแพ๊ค</span></label></div>
         <div class='col-xs-2'><label ><input type='radio' name="fillter" id="fillter" value="zone_name"/><span style="margin-left:10px; margin-right:10px;">ชื่อโซน</span></label></div>
         <div class='col-xs-2'><label ><input type='radio' name="fillter" id="fillter" value="barcode_zone"/><span style="margin-left:10px; margin-right:10px;">บาร์โค้ดโซน</span></label></div>
    </div>
	<div class='row'>
    	<div class='col-xs-6 col-xs-offset-3'>
    		<div class='input-group'>
            	<span class='input-group-addon'>&nbsp;&nbsp; ค้นหา &nbsp;&nbsp;</span>
            	<input type='text' name='search-text' id='search-text' class='form-control' />
                <span class='input-group-btn'>
                  <button type='button' class='btn btn-default' id='search-btn'>&nbsp;&nbsp;<span id='load'><span class='glyphicon glyphicon-search'></span></span>&nbsp;&nbsp;</button>
                </span>
            </div>
        </div>
    </div>
    <div class='row'><div class='col-xs-12'><hr/></div></div>
    <div class='row'>
    <div class='col-xs-12' id='result'>
    </div>
    </div>
</div>
<script>
	$("#search-text").bind("enterKey",function(){
	if($("#search-text").val() != ""){
		$("#search-btn").click();
	}
});
$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var fillter = $("#fillter:checked").val();
	$("#load").html("<img src='../img/ajax-loader.gif' width='18' height='18' />");
	$.ajax({
		url:"controller/searchController.php?text="+query_text+"&fillter="+fillter , type: "GET", cache:false,
		success: function(result){
			$("#result").html(result);
			$("#load").html("<span class='glyphicon glyphicon-search'></span>");
	}
	});
});
	
</script>
