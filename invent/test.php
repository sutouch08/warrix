<?php
require '../library/config.php';
require '../library/functions.php';
require "function/tools.php";
?>

<!DOCTYPE HTML>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="../favicon.ico" />
    <title>ทดสอบระบบ</title>

    <!-- Core CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/paginator.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/bootflat.min.css" rel="stylesheet">
     <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
     <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
    
  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>
     
    
    
    <!-- SB Admin CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/template.css" rel="stylesheet">
    
   

</head>

<body>
   <div class="container">
   <div class="row">
   <div class="col-lg-12">
   <?php
  
		$prefix = getConfig("PREFIX_ORDER");
		$date = "2015-02-11";
		$sumtdate = date("y", strtotime($date));
		$m = date("m", strtotime($date));
		$sql="SELECT  MAX(reference) AS max FROM tbl_order WHERE role=1 AND reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC"; 
		echo $sql;
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$num = "00001";
		$str = $rs['max'];
		$s = 7; // start from "0" (nth) char
		$l = 7; // get "3" chars
		$str2 = substr_unicode($str, $s ,5)+1;
		$str1 = substr_unicode($str, 0 ,$l);
		if($str1=="$prefix-$sumtdate$m"){  
		$reference_no = "$prefix-$sumtdate$m".sprintf("%05d",$str2)."";
		}else{
		$reference_no = "$prefix-$sumtdate$m$num";
		}
		
		echo $reference_no;
	
?>
<script>
	$(document).ready(function(e) {
        $("#modal_popup").modal('show');
    });
</script>
   </div>
   <div class="col-lg-12">
   			
   </div>
   </div>
   </div>
  <script>
  $("#but").click(function(){
	  alert( $("#sm td").size() );
  });
  $("#tes1").change(function(e) {
    $("#1").click();
});
 $("#tex2").change(function(e) {
    $("#2").click();
});
  $("#1").click(function(){
	  $(this).insertAfter($("#head2"));
  });
  $("#2").click(function(){
	  $(this).insertAfter($("#head2"));
  });
  
  </script>
    
</body>

</html>
