<?php 
	$page_menu = "invent_customer";
	$page_name = "กำหนดสิทธิ์";
	$id_tab = 29;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($view==0){ echo accessDeny(); exit; }
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   echo"
       		<li $can_edit><a href='#' style='text-align:center; background-color:transparent;' ><button type='button' class='btn btn-link' onclick='submitFrom();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}

	$tabb="";
echo "<div class='row'><div class='col-sm-3' style='margin-right:-9px'>";
$class1="active";

$result = dbQuery("SELECT id_profile,profile_name FROM tbl_profile ORDER BY id_profile ASC");
			$i=0;
			$n=1;
			$row = dbNumRows($result);
			while($i<$row){
				list($id_profile, $profile_name) = dbFetchArray($result);
				
echo "<ul class='nav nav-tabs nav-justified' >
		<li class='";if($n == "1"){echo "active";} echo "' id='tab".$n."'  onclick='Click()' $can_edit ><a href='#'>$profile_name</a></li>
	</ul>";
		
echo "<script>
    $('#tab".$n."').click(function(){
	$('#tab".$n."').addClass('active');
	$('#info".$n."').css('display','block');
	document.getElementById('GetFrom').value=".$n.";
});
</script>";

	$i++;
	$n++;
	}
	$admin="display:block;";
echo"  
</div><input type='hidden' name='Count' id='Count' value='$i'>"; 
$CountCheck = dbNumRows(dbQuery("SELECT * FROM tbl_tab"));
echo "</table><input type='hidden' name='CountCheck' id='CountCheck' value='$CountCheck'>";
$result = dbQuery("SELECT id_profile,profile_name FROM tbl_profile ORDER BY id_profile ASC");
			$i=0;
			$n=1;
			$row = dbNumRows($result);
			while($i<$row){	
		list($id_profile, $profile_name) = dbFetchArray($result);
		echo "<div class='col-sm-9' $can_edit id='info".$n."' style='";if($n == "1"){echo "display:block;";}else{echo "display:none;";} echo "background:#FFF; margin-left:-9px' >
			";
		if($id_profile == "1"){
			echo "ไม่สามารถแก้ไขสิทธิ์การเข้าใช้งานสำหรับผู้ดูแลระบบได้ <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
		}else{
			echo "<form method='post' name='securableFrom".$n."' id='securableFrom".$n."' action='controller/securableController.php' ><table class='table table-condensed' width='100%'>
			<thead><td width='30%'>Tabs</td><td width='10%'><input name='viewAll' type='checkbox' id='viewAll' value='Y' onClick='ClickViewAll".$n."(this)'>&nbsp;ดู</td><td width='10%'><input name='CheckAll' type='checkbox' id='CheckAll' value='Y' onClick='ClickAddAll".$n."(this)'>&nbsp;เพิ่ม</td><td width='10%'><input name='CheckAll' type='checkbox' id='CheckAll' value='Y' onClick='ClickEditAll".$n."(this)'>&nbsp;แก้ไข</td><td width='10%'><input name='CheckAll' type='checkbox' id='CheckAll' value='Y' onClick='ClickDeleteAll".$n."(this)'>&nbsp;ลบ</td><td width='10%'><input name='CheckAll' type='checkbox' id='CheckAll' value='Y' onClick='ClickCheckAll".$n."(this);ClickViewAll".$n."(this);ClickAddAll".$n."(this);ClickEditAll".$n."(this);ClickDeleteAll".$n."(this)'>&nbsp;ทั้งหมด</td><thead>
			";
$rs = dbQuery("SELECT tbl_access.id_tab,tab_name,tbl_access.view,tbl_access.add,tbl_access.edit,tbl_access.delete  FROM tbl_access LEFT JOIN tbl_tab ON tbl_access.id_tab = tbl_tab.id_tab where id_profile = '$id_profile' ORDER BY tbl_access.id_tab ASC");
		$is=0;
		$ns=1;
		$rows = dbNumRows($rs);
		while($is<$rows){
		list($id_tabs,$tab_name,$view,$add,$edit,$delete) = dbFetchArray($rs);
  			echo "<tr>
			<td>$tab_name<input type='hidden' name='id_profile' id='id_profile' value='$id_profile'><input type='hidden' name='id_tab".$ns."' id='id_tab".$ns."' value='$id_tabs'></td>
			<td><input type='checkbox' name='view".$ns."' id='view".$ns."' value='1' "; if($view==1){echo"checked='checked' ";} echo" /></td>
			<td><input type='checkbox' name='add".$ns."' id='add".$ns."' value='1' "; if($add==1){echo"checked='checked' ";} echo"/></td>
			<td><input type='checkbox' name='edit".$ns."' id='edit".$ns."' value='1' "; if($edit==1){echo"checked='checked' ";} echo"/></td>
			<td><input type='checkbox' name='delete".$ns."' id='delete".$ns."' value='1' "; if($delete==1){echo"checked='checked' ";} echo" /></td>
			<td><input type='checkbox' name='active".$ns."' id='active".$ns."' value='1' "; if($view && $add && $edit && $delete ==1){echo"checked='checked' ";} echo" onClick='ClickAll".$n."".$ns."(this)' /></td>
			<tr>";?>
			<script>
			function ClickAll<?php echo "$n$ns";?>(vol){
				if(vol.checked == true){
					eval('document.securableFrom<?php echo $n?>.view<?php echo $ns?>.checked=true');
					eval('document.securableFrom<?php echo $n?>.add<?php echo $ns?>.checked=true');
					eval('document.securableFrom<?php echo $n?>.edit<?php echo $ns?>.checked=true');
					eval('document.securableFrom<?php echo $n?>.delete<?php echo $ns?>.checked=true');
				}else{
					eval('document.securableFrom<?php echo $n?>.view<?php echo $ns?>.checked=false');
					eval('document.securableFrom<?php echo $n?>.add<?php echo $ns?>.checked=false');
					eval('document.securableFrom<?php echo $n?>.edit<?php echo $ns?>.checked=false');
					eval('document.securableFrom<?php echo $n?>.delete<?php echo $ns?>.checked=false');
				}
			}
			</script>
			<?php
  		$is++;
		$ns++;
		}}echo "</table><input type='hidden' name='loop' id='loop' value='$CountCheck'></form></div>";?>
		<script>
		function ClickCheckAll<?php echo $n?>(vol)
			{
				var CountCheck = $("#CountCheck").val();
				var i=1;
				for(i=1;i<=CountCheck;i++){
					if(vol.checked == true){
						eval("document.securableFrom<?php echo $n?>.active"+i+".checked=true");
					}else{
						eval("document.securableFrom<?php echo $n?>.active"+i+".checked=false");
					}
				}
			}
		function ClickViewAll<?php echo $n?>(vol)
			{
				var CountCheck = $("#CountCheck").val();
				var i=1;
				for(i=1;i<=CountCheck;i++){
					if(vol.checked == true){
						eval("document.securableFrom<?php echo $n?>.view"+i+".checked=true");
					}else{
						eval("document.securableFrom<?php echo $n?>.view"+i+".checked=false");
					}
				}
			}
		function ClickAddAll<?php echo $n?>(vol)
			{
				var CountCheck = $("#CountCheck").val();
				var i=1;
				for(i=1;i<=CountCheck;i++){
					if(vol.checked == true){
						eval("document.securableFrom<?php echo $n?>.add"+i+".checked=true");
					}else{
						eval("document.securableFrom<?php echo $n?>.add"+i+".checked=false");
					}
				}
			}
		function ClickEditAll<?php echo $n?>(vol)
			{
				var CountCheck = $("#CountCheck").val();
				var i=1;
				for(i=1;i<=CountCheck;i++){
					if(vol.checked == true){
						eval("document.securableFrom<?php echo $n?>.edit"+i+".checked=true");
					}else{
						eval("document.securableFrom<?php echo $n?>.edit"+i+".checked=false");
					}
				}
			}
		function ClickDeleteAll<?php echo $n?>(vol)
			{
				var CountCheck = $("#CountCheck").val();
				var i=1;
				for(i=1;i<=CountCheck;i++){
					if(vol.checked == true){
						eval("document.securableFrom<?php echo $n?>.delete"+i+".checked=true");
					}else{
						eval("document.securableFrom<?php echo $n?>.delete"+i+".checked=false");
					}
				}
			}
      </script> 
	<?php
	$i++;
	$n++;
	}?>
</div>
</div></div>
<input type='hidden' name='GetFrom' id='GetFrom' value=''>
<script>
function Click()
	{   var count = $("#Count").val();
		var i=1;
		for(i=1;i<=count;i++){
			$("#tab"+i+"").removeClass();
			$("#info"+i+"").css("display","none");
		}
	}
function submitFrom(){
	var GetFrom = $("#GetFrom").val();
	$("#securableFrom"+GetFrom+"").submit();
}
	</script>