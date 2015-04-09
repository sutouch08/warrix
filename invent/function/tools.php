<?php  
function root_category_tree($id_cat=""){
		if($id_cat ==""){ $id_cat =0; }
		list($parent_id) =dbFetchArray(dbQuery("SELECT parent_id FROM tbl_category WHERE id_category = $id_cat"));
		echo" <ul class='tree'>";
		$qr=dbQuery("SELECT id_category, category_name FROM tbl_category WHERE level_depth = 0 ORDER BY category_name ASC");
		$row = dbNumRows($qr);
		$i = 0;
		while($i<$row){
			$category = dbFetchArray($qr);
			$id_category = $category['id_category'];
			$category_name = $category['category_name'];
			echo "<li><input type='radio' name='root_category'  value='$id_category' id='$category_name' "; if($parent_id==$id_category){ echo" checked='checked'";} echo"/><label for='$category_name'>&nbsp;&nbsp;  $category_name</label>";
				$q1 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category AND level_depth = 1 AND id_category != $id_cat ORDER BY category_name ASC");
				$r1 = dbNumRows($q1);
				$n1 = 0;
				if($r1>0){ echo"<ul>";}
				while($n1<$r1){
					$sub_cat1 = dbFetchArray($q1);
					$id_sub1 = $sub_cat1['id_category'];
					$sub_name1 = $sub_cat1['category_name'];
					echo "<li><input type='radio' name='root_category'  value='$id_sub1' id='$sub_name1' "; if($parent_id==$id_sub1){ echo" checked='checked'";} echo"/><label for='$sub_name1'>&nbsp;&nbsp;  $sub_name1</label>";
					$q2 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub1 AND level_depth = 2 AND id_category != $id_cat ORDER BY category_name ASC");
					$r2 = dbNumRows($q2);
					$n2 = 0;
					if($r2>0){ echo"<ul>";}
						while($n2<$r2){
							$sub_cat2 = dbFetchArray($q2);
							$id_sub2 = $sub_cat2['id_category'];
							$sub_name2 = $sub_cat2['category_name'];
							echo "<li><input type='radio' name='root_category'  value='$id_sub2' id='$sub_name2' "; if($parent_id==$id_sub2){ echo" checked='checked'";} echo"/><label for='$sub_name2'>&nbsp;&nbsp;  $sub_name2</label>";
							$q3 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub2 AND level_depth = 3 AND id_category != $id_cat  ORDER BY category_name ASC");
							$r3 = dbNumRows($q3);
							$n3 = 0;
							if($r3>0){ echo "<ul>"; }
								while($n3<$r3){
									$sub_cat3 = dbFetchArray($q3);
									$id_sub3 = $sub_cat3['id_category'];
									$sub_name3 = $sub_cat3['category_name'];
									echo "<li><input type='radio' name='root_category'  value='$id_sub3' id='$sub_name3'"; if($parent_id==$id_sub3){ echo" checked='checked'";} echo" /><label for='$sub_name3'>&nbsp;&nbsp;  $sub_name3</label>";	
									$q4 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub3 AND level_depth = 4 AND id_category != $id_cat ORDER BY category_name ASC");
									$r4 = dbNumRows($q4);
									$n4 = 0;
									if($r4>0){ echo "<ul>"; }
										while($n4<$r4){
											$sub_cat4 = dbFetchArray($q4);
											$id_sub4 = $sub_cat4['id_category'];
											$sub_name4 = $sub_cat4['category_name'];
											echo "<li><input type='radio' name='root_category'  value='$id_sub4' id='$sub_name4' "; if($parent_id==$id_sub4){ echo" checked='checked'";} echo" /><label for='$sub_name4'>&nbsp;&nbsp;  $sub_name4</label></li>";
											$n4++;
										}
									if($r4>0){ echo "</ul>";}
									$n3++;
								}
								if($r3>0){ echo "</ul>";} echo"</li>";
							$n2++;
						}
						if($r2>0){ echo "</ul>";} echo"</li>";
					$n1++;
				}
				if($r1>0){ echo "</ul>";} echo"</li>";		
			$i++;
		}
		echo"</ul>";
}
function category_tree($id_product=0){///checkbox
		echo" <ul class='tree'>";
		$qr=dbQuery("SELECT id_category, category_name FROM tbl_category WHERE level_depth = 0 ORDER BY category_name ASC");
		$row = dbNumRows($qr);
		$i = 0;
		while($i<$row){
			$category = dbFetchArray($qr);
			$id_category = $category['id_category'];
			$category_name = $category['category_name'];
			$check =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_category AND id_product=$id_product"));
			echo "<li><input type='checkbox' name='category_id[]' value='$id_category' id='$category_name' "; if($check==1){ echo" checked='checked'";} echo"/><label for='$category_name'>&nbsp;&nbsp;  $category_name</label>";
				$q1 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category AND level_depth = 1 ORDER BY category_name ASC");
				$r1 = dbNumRows($q1);
				$n1 = 0;
				if($r1>0){ echo"<ul>";}
				while($n1<$r1){
					$sub_cat1 = dbFetchArray($q1);
					$id_sub1 = $sub_cat1['id_category'];
					$sub_name1 = $sub_cat1['category_name'];
					$check1 =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_sub1 AND id_product=$id_product"));
					echo "<li><input type='checkbox' name='category_id[]' value='$id_sub1' id='$sub_name1' "; if($check1==1){ echo" checked='checked'";} echo"/><label for='$sub_name1'>&nbsp;&nbsp;  $sub_name1</label>";
					$q2 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub1 AND level_depth = 2 ORDER BY category_name ASC");
					$r2 = dbNumRows($q2);
					$n2 = 0;
					if($r2>0){ echo"<ul>";}
						while($n2<$r2){
							$sub_cat2 = dbFetchArray($q2);
							$id_sub2 = $sub_cat2['id_category'];
							$sub_name2 = $sub_cat2['category_name'];
							$check2 =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_sub2 AND id_product=$id_product"));
							echo "<li><input type='checkbox' name='category_id[]' value='$id_sub2' id='$sub_name2' "; if($check2==1){ echo" checked='checked'";} echo"/><label for='$sub_name2'>&nbsp;&nbsp;  $sub_name2</label>";
							$q3 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub2 AND level_depth = 3 ORDER BY category_name ASC");
							$r3 = dbNumRows($q3);
							$n3 = 0;
							if($r3>0){ echo "<ul>"; }
								while($n3<$r3){
									$sub_cat3 = dbFetchArray($q3);
									$id_sub3 = $sub_cat3['id_category'];
									$sub_name3 = $sub_cat3['category_name'];
									$check3 =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_sub3 AND id_product=$id_product"));
									echo "<li><input type='checkbox' name='category_id[]' value='$id_sub3' id='$sub_name3'"; if($check3==1){ echo" checked='checked'";} echo" /><label for='$sub_name3'>&nbsp;&nbsp;  $sub_name3</label>";	
									$q4 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub3 AND level_depth = 4 ORDER BY category_name ASC");
									$r4 = dbNumRows($q4);
									$n4 = 0;
									if($r4>0){ echo "<ul>"; }
										while($n4<$r4){
											$sub_cat4 = dbFetchArray($q4);
											$id_sub4 = $sub_cat4['id_category'];
											$sub_name4 = $sub_cat4['category_name'];
											$check4 =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_sub4 AND id_product=$id_product"));
											echo "<li><input type='checkbox' name='category_id[]' value='$id_sub4' id='$sub_name4' "; if($check4==1){ echo" checked='checked'";} echo" /><label for='$sub_name4'>&nbsp;&nbsp;  $sub_name4</label></li>";
											$n4++;
										}
									if($r4>0){ echo "</ul>";}
									$n3++;
								}
								if($r3>0){ echo "</ul>";} echo"</li>";
							$n2++;
						}
						if($r2>0){ echo "</ul>";} echo"</li>";
					$n1++;
				}
				if($r1>0){ echo "</ul>";} echo"</li>";		
			$i++;
		}
			echo "</ul>";
		}
function category_list($selected =""){
	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0 ORDER BY category_name ASC");
	$option = "<option value='0'>------ทั้งหมด---------</option>";
	while($rs = dbFetchArray($sql)){	
		$id_category = $rs['id_category'];
		$category_name = $rs['category_name'];
		if($selected == $id_category){ $select = "selected='selected'"; }else{ $select = ""; }
		$option .="<option value='$id_category' $select >$category_name</option>";
	}
	return $option;
}		
	function get_product_list($selected=0){
		$sql = dbQuery("SELECT id_product, product_code, product_name FROM tbl_product");
		echo"<option value='0'>---เลือกรายการสินค้า---</option>";
			while($r=dbFetchArray($sql)){
				$id = $r['id_product'];
				$product_code = $r['product_code'];
				$product_name = $r['product_name'];
				if($selected == $id){ $select ="selected='selected'"; }else{ $select = ""; }
				echo"<option value='$id' $select>$product_code : $product_name</option>";
			}
		}
function getViewList($view,$from_date)
{
	if($from_date != "เลือกวัน"){$view = "";}
	echo "<option value='0' "; if($view==""){echo "selected='selected'";} echo ">เลือกการแสดงผล</option>";
	echo "<option value='week' "; if($view == "week"){echo "selected='selected'";} echo ">แสดงเป็นสัปดาห์</option>";
	echo "<option value='month' "; if($view == "month"){echo "selected='selected'";} echo ">แสดงเป็นเดือน</option>";
	echo "<option value='year' "; if($view == 'year'){echo "selected='selected'";} echo ">แสดงเป็นปี</option>";
}
function get_view_list($view=0)
{
	echo "<option value='0' "; if($view=="0"){echo "selected='selected'";} echo ">เลือกการแสดงผล</option>";
	echo "<option value='week' "; if($view == "week"){echo "selected='selected'";} echo ">แสดงเป็นสัปดาห์</option>";
	echo "<option value='month' "; if($view == "month"){echo "selected='selected'";} echo ">แสดงเป็นเดือน</option>";
	echo "<option value='year' "; if($view == 'year'){echo "selected='selected'";} echo ">แสดงเป็นปี</option>";
}


function getWeek($today){
	$day = date("l",strtotime("$today"));
	$from_date ='';
	$to_date = '';
	switch ($day){
		case 'Monday':
		$from_date = $today;
		$to_date = date('Y-m-d',strtotime("+6 day",strtotime("$today")));
		break;
		case 'Tuesday' :
		$from_date = date('Y-m-d',strtotime("-1 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+5 day",strtotime("$today")));
		break;
		case 'Wednesday' :
		$from_date = date('Y-m-d',strtotime("-2 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+4 day",strtotime("$today")));
		break;
		case 'Thursday' :
		$from_date = date('Y-m-d',strtotime("-3 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+3 day",strtotime("$today")));
		break;
		case 'Friday' :
		$from_date = date('Y-m-d',strtotime("-4 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+2 day",strtotime("$today")));
		break;
		case 'Saturday' :
		$from_date = date('Y-m-d',strtotime("-5 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+1 day",strtotime("$today")));
		break;
		case 'Sunday' :
		$from_date = date('Y-m-d',strtotime("-6 day",strtotime("$today")));
		$to_date =  $today;
		break;
		default :
		$from_date = $today;
		$to_date = date('Y-m-d',strtotime("+6 day",strtotime("$today")));
		break;
		
	}
	$array["from"] =$from_date;
	$array["to"] = $to_date;
	return $array;
}

function DateDiff($strDate1,$strDate2)
	{
				return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );  // 1 day = 60*60*24
	 }
	 
function getMonth($date=""){
	if($date ==""){
	$array["from"] = date('Y-m-01',strtotime('this month'));
	$array["to"] = date('Y-m-t',strtotime('this month'));
	}else{
	$d = date("m",strtotime($date));
	$y = date("Y",strtotime($date));
	$month = shortMonthName($d);
	$array['from'] = date($y.'-m-01',strtotime($month));
	$array['to'] = date($y.'-m-t',strtotime($month));
	}
	return $array;
}
function date_in_month($month,$year){
	$first = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
    $last = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));
	$thisTime = strtotime($first);
   	$endTime = strtotime($last);
	$date = array();
   while($thisTime <= $endTime)
   {
   		$thisDate = date('Y-m-d', $thisTime);
   		array_push($date,$thisDate);
 		$thisTime = strtotime('+1 day', $thisTime); // increment for loop
   }
   return $date;
}
function getYear($date=""){
	if($date ==""){
	$array["from"] = date('Y-01-01',strtotime('this year'));
	$array["to"] = date('Y-12-31',strtotime('this year'));
	}else{
	$y = date("Y", strtotime($date));
	$array['from'] = date($y.'-01-01', strtotime($y));
	$array['to'] = date($y.'-12-31', strtotime($y));
	}		
	return $array;
}
function getMonthName()
{
	$date = date("m",strtotime("this month"));
	switch($date){
		case "01" :
		$month = "มกราคม";
		break;
		case "02" :
		$month = "กุมภาพันธ์";
		break;
		case "03" :
		$month = "มีนาคม";
		break;
		case "04" :
		$month = "เมษายน";
		break;
		case "05" :
		$month = "พฤษภาคม";
		break;
		case "06" :
		$month = "มิถุนายน";
		break;
		case "07" :
		$month = "กรกฎาคม";
		break;
		case "08" :
		$month = "สิงหาคม";
		break;
		case "09" :
		$month = "กันยายน";
		break;
		case "10" :
		$month = "ตุลาคม";
		break;
		case "11" :
		$month = "พฤษจิกายน";
		break;
		case "12" :
		$month = "ธันวาคม";
		break;
		default :
		$month = "เดือนไม่ถูกต้อง";
		break;
	}
	return $month;
}
function MonthName($no)
{
	switch($no){
		case "1" :
		$month = "January";
		break;
		case "2" :
		$month = "February";
		break;
		case "3" :
		$month = "March";
		break;
		case "4" :
		$month = "April";
		break;
		case "5" :
		$month = "May";
		break;
		case "6" :
		$month = "June";
		break;
		case "7" :
		$month = "July";
		break;
		case "8" :
		$month = "August";
		break;
		case "9" :
		$month = "September";
		break;
		case "10" :
		$month = "October";
		break;
		case "11" :
		$month = " November";
		break;
		case "12" :
		$month = "December";
		break;
		default :
		$month = "เดือนไม่ถูกต้อง";
		break;
	}
	return $month;
}

function shortMonthName($no)
{
	switch($no){
		case "1" :
		$month = "Jan";
		break;
		case "2" :
		$month = "Feb";
		break;
		case "3" :
		$month = "Mar";
		break;
		case "4" :
		$month = "Apr";
		break;
		case "5" :
		$month = "May";
		break;
		case "6" :
		$month = "Jun";
		break;
		case "7" :
		$month = "Jul";
		break;
		case "8" :
		$month = "Aug";
		break;
		case "9" :
		$month = "Sep";
		break;
		case "10" :
		$month = "Oct";
		break;
		case "11" :
		$month = " Nov";
		break;
		case "12" :
		$month = "Dec";
		break;
		default :
		$month = "เดือนไม่ถูกต้อง";
		break;
	}
	return $month;
}
function getThaiMonthName($date="")
{
	if($date==""){ $day = date("m",strtotime("this month")); }else{ $day = date("m", strtotime($date)); }
	switch($day){
		case "01" :
		$month = "มกราคม";
		break;
		case "02" :
		$month = "กุมภาพันธ์";
		break;
		case "03" :
		$month = "มีนาคม";
		break;
		case "04" :
		$month = "เมษายน";
		break;
		case "05" :
		$month = "พฤษภาคม";
		break;
		case "06" :
		$month = "มิถุนายน";
		break;
		case "07" :
		$month = "กรกฎาคม";
		break;
		case "08" :
		$month = "สิงหาคม";
		break;
		case "09" :
		$month = "กันยายน";
		break;
		case "10" :
		$month = "ตุลาคม";
		break;
		case "11" :
		$month = "พฤษจิกายน";
		break;
		case "12" :
		$month = "ธันวาคม";
		break;
		default :
		$month = "เดือนไม่ถูกต้อง";
		break;
	}
	return $month;
}
function thaiDate($date){
	return date('d-m-Y',strtotime($date));
}
function thaiTextDate($datetime) {
	$date=date('Y-m-d',strtotime($datetime));
	list($Y,$m,$d) = explode('-',$date); // แยกวันเป็น ปี เดือน วัน
	
	$Y = $Y+543; // เปลี่ยน ค.ศ. เป็น พ.ศ.
	switch($m) {
		case "01": $m = "ม.ค."; break;
		case "02": $m = "ก.พ."; break;
		case "03": $m = "มี.ค."; break;
		case "04": $m = "เม.ย."; break;
		case "05": $m = "พ.ค."; break;
		case "06": $m = "มิ.ย."; break;
		case "07": $m = "ก.ค."; break;
		case "08": $m = "ส.ค."; break;
		case "09": $m = "ก.ย."; break;
		case "10": $m = "ต.ค."; break;
		case "11": $m = "พ.ย."; break;
		case "12": $m = "ธ.ค."; break;
	}
		return $d." ".$m." ".$Y;
}
function recievedTable($view="",$from ="", $to =""){
	if($view=="week"){
		$sql = dbQuery("SELECT id_recieved_product, recieved_product_no, reference_no, date, id_employee, status FROM tbl_recieved_product ORDER BY date DESC LIMIT 50");
	}else{
	if($from==""){ $from= date('Y-m-d');}
	if($to ==""){ $to = date("Y-m-d");}
	$sql = dbQuery("SELECT id_recieved_product, recieved_product_no, reference_no, date, id_employee, status FROM tbl_recieved_product WHERE date BETWEEN '$from' AND '$to'");
	}
	return $sql;
}
function return_order_table($view="",$from="", $to=""){
			if($view =="month"){
				$sql = dbQuery("SELECT id_return_order, reference, id_customer, id_employee, date_add, status FROM tbl_return_order ORDER BY id_return_order DESC LIMIT 50");
			}else{
				if($from==""){ $from= date('Y-m-d');}else{ $from = dbDate($from); }
				if($to ==""){ $to = date("Y-m-d");}else{ $to = dbDate($to); }
				$sql = dbQuery("SELECT id_return_order, reference, id_customer, id_employee, date_add, status FROM tbl_return_order WHERE date_add BETWEEN '$from' AND '$to' ORDER BY id_return_order DESC"); 
			}
			return $sql;
	}
function newRecievedNO(){
	$y= date('y');
	$m= date('m');
	$run_no = "0001";
	list($id, $recieved_no) = dbFetchArray(dbQuery("SELECT id_recieved_product, recieved_product_no FROM tbl_recieved_product WHERE  id_recieved_product= (SELECT max(id_recieved_product) FROM tbl_recieved_product)"));
	$pre_fix = "RE-";
	$start = 7;
	$end = 7;
	$str1 = substr_unicode($recieved_no, $start, 11)+1;
	$str2 = substr_unicode($recieved_no, 0,$end);
	if($str2=="$pre_fix$y$m"){
		$result = $pre_fix.$y.$m.sprintf("%04d",$str1);
	}else{
		$result = $pre_fix.$y.$m.$run_no;
	}
	return $result;
}
function newAdjustNO(){
	$y= date('y');
	$m= date('m');
	$run_no = "0001";
	list($id, $adjust_on) = dbFetchArray(dbQuery("SELECT id_adjust, adjust_no FROM tbl_adjust ORDER BY id_adjust DESC "));
	$pre_fix = "AJ-";
	$start = 7;
	$end = 7;
	$str1 = substr_unicode($adjust_on, $start, 11)+1;
	$str2 = substr_unicode($adjust_on, 0,$end);
	if($str2=="$pre_fix$y$m"){
		$result = $pre_fix.$y.$m.sprintf("%04d",$str1);
	}else{
		$result = $pre_fix.$y.$m.$run_no;
	}
	return $result;
}
function getRecievedNO($id_recieved_product){
	list($recieved_no) = dbFetchArray(dbQuery("SELECT recieved_product_no FROM tbl_recieved_product WHERE id_recieved_product='$id_recieved_product' "));
	return $recieved_no;
}
function getRecievedReference($id){
	list($reference)= dbFetchArray(dbQuery("SELECT reference_no FROM tbl_recieved_product WHERE id_recieved_product='$id' "));
	return $reference;
}
function getRecievedDate($id){
	list($recieved_date)= dbFetchArray(dbQuery("SELECT date FROM tbl_recieved_product WHERE id_recieved_product='$id' "));
	return $recieved_date;
}
function getReturnNO($id_return_order){
	list($return_no) = dbFetchArray(dbQuery("SELECT reference FROM tbl_return_order WHERE id_return_order='$id_return_order' "));
	return $return_no;
}
function getRetrunReference($id){
	list($reference)= dbFetchArray(dbQuery("SELECT return_reference FROM tbl_return_order WHERE id_return_order='$id' "));
	return $reference;
}
function getReturnDate($id){
	list($recieved_date)= dbFetchArray(dbQuery("SELECT date_add FROM tbl_return_order WHERE id_return_order='$id' "));
	return $recieved_date;
}
//// หาค่าสูงสุดของ reference ของแต่ละ role 
function get_max_role_reference($config_name, $role, $date=""){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		if($date ==""){ $date = date("Y-m-d"); }
		$sumtdate = date("y", strtotime($date));
		$m = date("m", strtotime($date));
		if($role == 2 || $role == 6){
			$sql="SELECT  MAX(reference) AS max FROM tbl_order WHERE role IN(2,6) AND reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC"; 
		}else{
			$sql="SELECT  MAX(reference) AS max FROM tbl_order WHERE role=$role AND reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC"; 
		}
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
		
		return $reference_no;
}
function get_max_role_reference_consign($config_name, $role, $date=""){
		$prefix = getConfig($config_name);
		if($date ==""){ $date = date("Y-m-d"); }
		$sumtdate = date("y", strtotime($date));
		$m = date("m", strtotime($date));
		$sql="SELECT  MAX(reference) AS max FROM tbl_order_consign WHERE reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC"; 
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
		
		return $reference_no;
}
function get_max_role_reference_consign_check($config_name, $role=""){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		$sql="SELECT  MAX(reference) AS max FROM tbl_consign_check ORDER BY  reference DESC"; 
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$sumtdate = date("y");
		$m = date("m");
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
		
		return $reference_no;
}
function get_max_request_reference($config_name){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		$sql="SELECT  MAX(reference) AS max FROM tbl_request_order ORDER BY  reference DESC"; 
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$sumtdate = date("y");
		$m = date("m");
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
		
		return $reference_no;
}
function get_max_role_reference_tranfer($config_name){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		$sql="SELECT  MAX(reference) AS max FROM tbl_tranfer ORDER BY  reference DESC"; 
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$sumtdate = date("y");
		$m = date("m");
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
		
		return $reference_no;
}
//************************************* รายการสินค้าที่ยิงแล้วแต่ยังไม่บันทึกยอดสต็อก ********************************************///
function recievedDetail($id){
	$sql = dbQuery("SELECT * FROM recieved_detail_table WHERE id_recieved_product = $id ORDER BY id_detail DESC");
	$row = dbNumRows($sql);
	$total = 0;
	$total_row = "";
	$table = "";
	$i=0;
	$n=$row;
	if($row>0){
	while($i<$row){
	list($id_detail, $id_recieved, $reference, $qty, $warehouse, $zone, $date, $employee, $status) = dbFetchArray($sql);
	$table .="<tr><td align='center'>$n</td><td>$reference</td><td align='center'>$qty</td><td align='center'>$warehouse</td><td align='center'>$zone</td><td align='center'>$date</td><td align='center'>$employee</td><td align='center'>"; if($status==1){ $table .="<a href='controller/storeController.php?delete_stocked=y&id_recieved_detail=$id_detail'>";}else if($status==0){ $table .="<a href='controller/storeController.php?delete=y&id_recieved_detail=$id_detail'>";} $table .="<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $reference ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a></td></tr>";	
	$total = $total +$qty;
	$i++;
	$n--;
	}
	$total_row .="<tr><td colspan='8' align='center'><h4>รวม $total หน่วย </h4></td></tr>";
	$total_row .= $table;
	echo $total_row;
}	else{	
		echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการ</h3></td></tr>";
}
}
//*******************************************************  รายการสินค้าที่บันทึกยอดสต็อกแล้ว  ***************************************************************//
function getRecievedDetail($id){
	$sql = dbQuery("SELECT * FROM recieved_detail_table WHERE id_recieved_product = $id AND status =1");
	$row = dbNumRows($sql);
	$i=0;
	$n=1;
	if($row>0){
	while($i<$row){
	list($id_detail, $id_recieved, $reference, $qty, $warehouse, $zone, $date, $employee, $status) = dbFetchArray($sql);
	echo"<tr><td align='center'>$n</td><td>$reference</td><td align='center'>"; if($qty==NULL){ echo"0";}else{ echo $qty; } echo"</td><td align='center'>$warehouse</td><td align='center'>$zone</td><td align='center'>"; echo thaiDate($date); echo"</td><td align='center'>$employee</td></tr>";	
	$i++;
	$n++;
	}
}else{	
		echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการ</h3></td></tr>";
	}
}
function getLastDays($days){ /// คืนค่าวันที่เริ่มต้น และ สิ้นสุด ย้อนหลังตามจำนวนวันที่ต้องการ
	$today = date('Y-m-d', strtotime("+1 day",strtotime(date('Y-m-d'))));
	$from_date = date('Y-m-d', strtotime("-$days day",strtotime("$today")));
	$to_date = $today;
	$arr['from'] = $from_date;
	$arr['to'] = $to_date;
	return $arr;
}
function getOrderTable($view="",$from ="", $to ="", $Page_Start="",$Per_Page="",$role = "1,4"){
	if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
	return dbQuery("SELECT id_order,reference,id_customer,id_employee,payment,tbl_order.date_add,current_state,tbl_order.date_upd FROM tbl_order WHERE (tbl_order.date_add BETWEEN '$from' AND '$to') AND current_state !=1 AND role IN($role) AND order_status = 1 ORDER BY id_order DESC LIMIT $Page_Start , $Per_Page");
}

function getTrackOrderTable($view="",$from ="", $to ="", $Page_Start="",$Per_Page="",$role = "1",$id_sale){
	$sale = new sale($id_sale);
	$sale_name = $sale->full_name;
	if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
	return dbQuery("SELECT id_order,reference, tbl_order.id_customer, current_state, tbl_order.date_upd FROM tbl_customer LEFT JOIN tbl_order ON tbl_customer.id_customer = tbl_order.id_customer WHERE (tbl_order.date_add BETWEEN '$from' AND '$to') AND role IN($role)  AND id_sale = '$id_sale' ORDER BY id_order DESC LIMIT $Page_Start , $Per_Page");
}

 function state_color($current_state){
		$sql = dbQuery("SELECT color FROM tbl_order_state WHERE id_order_state = $current_state");
		list($color) = dbFetchArray($sql);
		return $color;
	}
function getTitleRadio($gender=""){
	$sql=dbQuery("SELECT * FROM tbl_gender WHERE type !=0");
	$row=dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_gender, $type, $prefix)=dbFetchArray($sql);
		echo"<input type='radio' name='gender' id='gender' value='$id_gender' style='margin-left:15px;'"; if($gender==$id_gender){echo" checked='checked'";} echo" /><label for='$id_gender' style='margin-left:15px;'>$prefix</label>";
		$i++;
	}
}
function selectDay($selected=""){
	echo"<option value='0'"; if($selected==""){echo" selected='selected'";} echo"> - </option>";
	$i=1;
	$day = 31;
	while($i<=$day){
		echo"<option value='$i' "; if($i==$selected){echo" selected='selected'";} echo">$i</option>";
		$i++;
	}
}
function selectMonth($selected=""){
	$array = array(" - ","มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
	$i=0;
	$month = 12;
	while($i<=$month){
		echo"<option value='$i' "; if($i==$selected){echo" selected='selected'";} echo">$array[$i]</option>";
		$i++;
	}
}
function selectYear($selected=""){
	$this_year= date('Y' ,strtotime("+1 year"));
	$n= $this_year - 100;
	$i= $this_year;
	echo"<option value='0000'  "; if($selected==""){echo" selected='selected'";} echo"> - </option>";
	while($i>=$n){
		echo"<option value='$i' "; if($i==$selected){echo" selected='selected'";} echo">";echo  $i+543 ." </option>";
		$i--;
	}
}
function customerGroupTable($id_cus="",$all=false){
	echo"<table class='table table-striped table-condensed' style='border:1px solid #ccc;'>
                    	<thead><th width='15%'><input type='checkbox' id='check_all' /></th><th width='15%'>ID</th><th width='70%'>กลุมลูกค้า</th></thead>";
						////*******ดึงข้อมูลมาแสดงเป็นตารางกลุ่มลูกค้า*******////
						$query = dbQuery("SELECT * FROM tbl_group");
						$rows = dbNumRows($query);
						$n =  0;
						$default = 1;
						while($n<$rows){
							$res = dbFetchArray($query);
							$id_group = $res['id_group'];
							$group_name = $res['group_name'];
							$checked = "";
							if($id_cus !=""){
							$default = "";
							$check = dbNumRows(dbQuery("SELECT * FROM tbl_customer_group WHERE id_customer=$id_cus AND id_group=$id_group"));
							if($all ==true){ $checked = "checked='checked'"; }else if($check==1){ $checked = "checked='checked'"; }else{$checked = "";} 
							}
							echo "<tr><td width='15%'><input type='checkbox' id='groupcheck$id_group' value='$id_group' name='groupcheck[]' $checked /></td>
									<td width='15%'>$id_group</td><td width='70%'><label for='groupcheck$id_group'>$group_name</label></td></tr>";
									$n++;
						}
						echo"</table>
						<script>
						$('#check_all').click(function(){
     	 					$(\":checkbox[name='groupcheck[]']\").prop('checked', this.checked);   
   						 });
						</script>";
}
function getCustomerDetail($id){
		$result = dbFetchArray(dbQuery("SELECT * FROM tbl_customer WHERE id_customer=$id"));
		return $result;
}
function customer_group($id_group){
	list($result) = dbFetchArray(dbQuery("SELECT group_name FROM tbl_group WHERE id_group = '$id_group'"));
	return $result;
}
function getCustomerGroup($id){
	$result = dbFetchArray(dbQuery("SELECT * FROM tbl_customer_group WHERE id_customer = $id"));
	return $result;
}

function selectCustomerGroup($selected=""){
		$default = 1;
		$sql = dbQuery("SELECT * FROM tbl_group");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			list($id_group, $group_name) = dbFetchArray($sql);
			echo"<option value='$id_group' "; if($selected =="" && $id_group==$default){ echo"selected='selected'";} else if($id_group == $selected){ echo"selected='selected'";} echo">$group_name</option>"; 
			$i++;
		}
}
function selectEmployeeGroup($selected=""){
	$profile = $_COOKIE['profile_id'];
		echo "<option value='' "; if($selected == ""){ echo"selected='selected'";} echo"><------ เลือก ------></option>";
		$sql = dbQuery("SELECT * FROM tbl_profile where id_profile >= '$profile'");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			list($id_profile, $profile_name) = dbFetchArray($sql);
			echo"<option value='$id_profile' "; if($id_profile == $selected){ echo"selected='selected'";} echo">$profile_name</option>"; 
			$i++;
		}
}
function getCustomerAddress($id){
	$result = dbQuery("SELECT * FROM tbl_address WHERE id_customer = $id");
	return $result;
}
function getAddressDetail($id){
	$result = dbFetchArray(dbQuery("SELECT * FROM tbl_address WHERE id_address = $id"));
	return $result;
}
function selectCity($selected=""){
	$array = array("---เลือกจังหวัด---","กรุงเทพมหานคร","กระบี่","กาญจนบุรี","กาฬสินธุ์"	,"กำแพงเพชร"	,"ขอนแก่น"	,"จันทบุรี"	,"ฉะเชิงเทรา"	,"ชัยนาท","ชัยภูมิ"	,"ชุมพร"	,"ชลบุรี"	,"เชียงใหม่"	,"เชียงราย"	,"ตรัง"	,"ตราด"	,"ตาก"	,"นครนายก","นครปฐม","นครพนม","นครราชสีมา"	,"นครศรีธรรมราช"	,"นครสวรรค์"	,"นราธิวาส","น่าน"	,"นนทบุรี"	,"บึงกาฬ"	,"บุรีรัมย์"	,"ประจวบคีรีขันธ์"	,"ปทุมธานี"	,"ปราจีนบุรี"	,"ปัตตานี"	,"พะเยา"	,"พระนครศรีอยุธยา"	,"พังงา"	,"พิจิตร","พิษณุโลก","เพชรบุรี"	,"เพชรบูรณ์","แพร่","พัทลุง","ภูเก็ต"	,"มหาสารคาม"	,"มุกดาหาร"	,"แม่ฮ่องสอน"	,"ยโสธร"	,"ยะลา"	,"ร้อยเอ็ด"	,"ระนอง"	,"ระยอง","ราชบุรี"	,"ลพบุรี"	,"ลำปาง"	,"ลำพูน"	,"เลย"	,"ศรีสะเกษ"	,"สกลนคร"	,"สงขลา"	,"สมุทรสาคร"	,"สมุทรปราการ"	,"สมุทรสงคราม"	,"สระแก้ว"	,"สระบุรี"	,"สิงห์บุรี"	,"สุโขทัย"	,"สุพรรณบุรี"	,"สุราษฎร์ธานี"	,"สุรินทร์"	,"สตูล"	,"หนองคาย"	,"หนองบัวลำภู"	,"อำนาจเจริญ"	,"อุดรธานี"	,"อุตรดิตถ์"	,"อุทัยธานี"	,"อุบลราชธานี"	,"อ่างทอง"	,"อื่นๆ"	);
	foreach($array as $city){
		echo"<option value=\""; if($city =="---เลือกจังหวัด---"){echo"\"";}else{echo"$city\"";} if($city==$selected){ echo"selected='selected'";} echo">$city </option>";
	}
}
function getGroupDetail($id_group=""){
	if($id_group !=""){
	$sql = dbQuery("SELECT * FROM tbl_group WHERE id_group = $id_group");
	return dbFetchArray($sql);
	}else{
	$sql = dbQuery("SELECT * FROM tbl_group");
	return dbFetchArray($sql);
	}
}
function customerTableByGroup($id_group){
	$sql = dbQuery("SELECT * FROM customer_group_table WHERE id_group = $id_group");
	return $sql;
}
function get_id_images($id_product){
	$sql = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = $id_product ORDER BY position ASC");
	return dbFetchArray($sql);
}
function employeeList($selected=""){
	$sql = dbQuery("SELECT id_employee, first_name, last_name FROM tbl_employee");
	echo "<option value='' "; if($selected == ""){ echo"selected='selected'";} echo">------ เลือก ------</option>";
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_employee, $first_name, $last_name) = dbFetchArray($sql);
		echo"<option value='$id_employee'"; if($selected==$id_employee){ echo"selected='selected'";} echo"> $first_name  $last_name</option>";
		$i++;
	}
}
function saleGroupList($selected=""){
	$sql = dbQuery("SELECT id_group, group_name FROM tbl_group WHERE id_group !=1");
	echo "<option value='' "; if($selected == ""){ echo"selected='selected'";} echo">------ เลือก ------</option>";
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_group, $group_name) = dbFetchArray($sql);
		echo"<option value='$id_group'"; if($selected==$id_group){ echo"selected='selected'";} echo">$group_name</option>";
		$i++;
	}
}
function sale_group_list($selected=""){
	$sql = dbQuery("SELECT id_group, group_name FROM tbl_group WHERE id_group !=1");
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_group, $group_name) = dbFetchArray($sql);
		echo"<option value='$id_group'"; if($selected==$id_group){ echo"selected='selected'";} echo">$group_name</option>";
		$i++;
	}
}
function saleList($selected=""){
	$sql = dbQuery("SELECT id_sale, first_name, last_name FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee");
	echo"<option value='0' "; if($selected==""){echo "selected='selected'";} echo "> ------- เลือก ------ </option>";
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_sale, $first_name, $last_name) = dbFetchArray($sql);
		echo "<option value='$id_sale'"; if($id_sale==$selected){ echo"selected='selected'";} echo ">$first_name $last_name</option>";
		$i++;
	}
}

function newArrival($i,$id_customer){
	$NEW = new category();
	$company = new company();
		$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, product_price,discount_type, discount, product_detail FROM tbl_product LEFT JOIN tbl_product_detail ON tbl_product.id_product = tbl_product_detail.id_product WHERE active = 1 ORDER BY tbl_product.date_add DESC LIMIT $i");
		$row = dbNumRows($sql);
		if($row>0){
			while($data = dbFetchArray($sql)){
				$product = new product();
				$product->product_detail($data['id_product'],$id_customer);
				$array = $product->getCategoryId($product->id_product);
				$id_cat = array();
				foreach($array as $ar){
					array_push($id_cat,$ar);
				}
				$id_category = max($id_cat);	
				echo"	
		<div class='item'>
			<div class='product'>
			  <div class='image'> <a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".$product->getCoverImage($product->id_product,4,"img-responsive")."</a>
				<div class='promotion'>";
					$NEW->category_show_new($company->product_new,$product->id_product);
					echo "".$NEW->NEW."
				";/*if($product->product_discount>0){echo"<span class='discount'>".$product->product_discount."OFF</span>";} */echo" </div>
			  </div>
			  <div class='description'>
				<h4><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".$product->product_code." : ".$product->product_name."</a></h4>
				<p><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".substr_replace($product->product_detail,'....',200)."</a></p> </div>
			  <div class='price'> <span>&nbsp;</span>"; 
			  /*if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";} */echo" </div>
			  <div class='action-control'> <a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'><span class='btn btn-primary' style='width:50%;'>".number_format($product->product_price,2)." ฿</span></a>  </div>
			</div>
		  </div>";
			}
		}
	}
/// แสดงรายการสินค้าในส่วนของ เซลล์ ตามหมวดหมู่ที่เลือก
function product_grid($id_category, $id_cus=0){
	$sql = dbQuery("SELECT tbl_product.id_product FROM tbl_product  LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE id_category = $id_category AND tbl_product.active =1");
	$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			echo"<div class='item2 col-lg-3 col-md-3 col-sm-4 col-xs-6'>					
			<div class='product'>
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,3,"img-responsive")."</a></div>
			<div class='description' style='font-size:1.5vmin; '>
				<a href='#' onclick='getData(".$product->id_product.")'>".$product->product_code." <br> ".$product->product_name."</a>
			</div>
			  <div class='price'>"; if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";}else{ echo"<span class='old-price'>&nbsp;</span>";} echo" </div>
			  <div class='action-control'> <a href='#' data-toggle='modal' data-target='#".$product->id_product."'><span class='btn btn-primary' style='width:80%; font-size:1.5vmin;'>".number_format($product->product_sell,2)." ฿</span></a>  </div></div></div>";
			$i++;
		}
		echo "
		<input type='hidden' id='id_product'>
		<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
		<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
				<div class='modal-dialog' id='modal'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
							<h4 class='modal-title' id='modal_title'>title</h4>
						</div>
						<div class='modal-body'  id='modal_body'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
							<button type='button' class='btn btn-primary' onclick=\"submit_product()\">หยิบใส่ตะกร้า</button>
						</div>
					</div>
				</div>
			</div>";
	}else{ 
		echo"<h4 style='align:center;'>ไม่มีรายการสินค้าในหมวดหมู่นี้</h4>";
	}
}

/// แสดงรายการสินค้ามาใหม่ในส่วนของ เซลล์
function newProduct($i, $id_cus=0){
		$sql = dbQuery("SELECT id_product FROM tbl_product WHERE active = 1 ORDER BY date_add DESC LIMIT $i");
		$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		echo"<div class='row xsResponse'>";
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			echo"<div class='item2 col-lg-3 col-md-3 col-sm-4 col-xs-6'>					
			<div class='product'> 
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,3,"img-responsive")."</a></div>
			<div class='description' style='font-size:1.5vmin; min-height:60px;'>
				<a href='#' onclick='getData(".$product->id_product.")'>".$product->product_code." <br> ".$product->product_name."</a>
			</div>
			  <div class='price'>"; if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";}else{ echo"<span class='old-price'>&nbsp;</span>";} echo" </div>
			  <div class='action-control'> <a href='#' data-toggle='modal' data-target='#".$product->id_product."'><span class='btn btn-primary' style='width:80%; font-size:1.5vmin;'>".number_format($product->product_sell,2)." ฿</span></a>  </div></div></div>";
			$i++;
		}
		echo "
		<input type='hidden' id='id_product'>
		<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
		<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
				<div class='modal-dialog' id='modal'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
							<h4 class='modal-title' id='modal_title'>title</h4>
						</div>
						<div class='modal-body'  id='modal_body'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
							<button type='button' class='btn btn-primary' onclick=\"submit_product()\">หยิบใส่ตะกร้า</button>
						</div>
					</div>
				</div>
			</div>";
		//echo"</div>";
	}
}

function featureProduct($i, $id_cus=0){
	$NEW = new category();
	$company = new company();
		$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, product_price,discount_type, discount, product_detail FROM tbl_product LEFT JOIN tbl_product_detail ON tbl_product.id_product = tbl_product_detail.id_product LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE active = 1 AND id_category = 0 ORDER BY tbl_product.date_add DESC LIMIT $i");
		$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		echo"<div class='row xsResponse'>";
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$array = $product->getCategoryId($product->id_product);
				$id_cat = array();
				foreach($array as $ar){
					array_push($id_cat,$ar);
				}
				$id_category = max($id_cat);	
			echo"<div class='item col-lg-3 col-md-3 col-sm-4 col-xs-6'>			
			<div class='product'>
			<div class='image'><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".$product->getCoverImage($product->id_product,4,"img-responsive")."</a>
			<div class='promotion'> ";
					$NEW->category_show_new($company->product_new,$product->id_product);
					echo "".$NEW->NEW."
				"; //if($product->product_discount>0){echo"<span class='discount'>".$product->product_discount."OFF</span>";} 
				echo" </div>
				
			</div>
			
			<div class='description' style='min-height:60px;'>
				<h5><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".$product->product_code." : ".$product->product_name."</a></h5>
				<p><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".substr_replace($product->product_detail,'....',200)."</a></p> 
			</div>
			  <div class='price'>"; /*if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";}else{ echo"<span>&nbsp;</span>";} */ echo" </div>
			  <div class='action-control'> <a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'><span class='btn btn-primary' style='width:50%;'>".number_format($product->product_price /*$product->product_sell*/,2)." ฿</span></a>  </div></div></div>";
			$i++;
		}
		echo"</div>";
	}
}

function getSaleId($id_user){
	list($id_sale) = dbFetchArray(dbQuery("SELECT id_sale FROM tbl_sale WHERE id_employee = $id_user"));
	return $id_sale;
}
function customerList($id_user){
	$sql = dbQuery("SELECT id_customer, first_name, last_name FROM tbl_customer WHERE id_sale = ".$id_user);
	echo "<option value='0'>------ เลือกลูกค้า ------</option>";
	while($data=dbFetchArray($sql)){
		echo "<option value='".$data['id_customer']."'>".$data['first_name']."&nbsp;".$data['last_name']."</option>";
	}
}

function order_grid($id_cus=0, $id_order, $action="controller/orderController.php?add_to_order"){
		$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, product_price,discount_type, discount, product_detail FROM tbl_product LEFT JOIN tbl_product_detail ON tbl_product.id_product = tbl_product_detail.id_product WHERE active = 1 ORDER BY tbl_product.product_code ASC");
		$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		echo"<div class='row xsResponse'>";
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$table_w = "style='width:".(70*($colums+1)+100)."px;'";
			echo"<div class='item2 col-lg-1 col-md-1 col-sm-3 col-xs-4'><form action='$action' method='post'>
		<div class='modal fade' id='".$product->id_product."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' $table_w>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'>".$product->product_code."</h4><input type='hidden' name='id_order' value='$id_order' />
									  </div>
									  <div class='modal-body'>"; $product->order_attribute_grid($product->id_product); echo"</div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div>";
			echo"
			
			
			<div class='product'>
			<div class='image'><a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' style='font-size:12px;'>
				<a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->product_code."</a>	
			</div>
			  </div></form></div>";
			$i++;
		}
		echo"</div>";
	}
}
function order_grid_consign($id_cus=0, $id_order, $action="",$id_zone){
		$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, product_price,discount_type, discount, product_detail FROM tbl_product LEFT JOIN tbl_product_detail ON tbl_product.id_product = tbl_product_detail.id_product WHERE active = 1 ORDER BY tbl_product.product_code ASC");
		$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		echo"<div class='row xsResponse'>";
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$table_w = "style='width:".(70*($colums+1)+100)."px;'";
			echo"<div class='item2 col-lg-1 col-md-1 col-sm-3 col-xs-4'><form action='$action' method='post'>			
			<div class='modal fade' id='".$product->id_product."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' $table_w>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'>".$product->product_code."</h4><input type='hidden' name='id_order' value='$id_order' />
									  </div>
									  <div class='modal-body'>"; $product->consign_attribute_grid($product->id_product,$id_cus,$id_zone); echo"</div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div>
			
			
			
			<div class='product'>
			<div class='image'><a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' >
				<a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->product_code."</a>	
			</div>
			  </div></form></div>";
			$i++;
		}
		echo"</div>";
	}
}

//*********************** Dropdown List อัพเดตออเดอร์ ************************/
function orderStateList($id_order){
		$order = new order($id_order);
		$payment = $order->payment;
		if($payment =="โอนเงิน" || $payment =="เช็ค"){
		$sql = dbQuery("SELECT * FROM tbl_order_state WHERE id_order_state IN(1,2,3,6,8)");
		}else{
		$sql = dbQuery("SELECT * FROM tbl_order_state WHERE id_order_state IN(1,3,6,8)");
		}
		echo"<option value='0' selected='selected'> ---- สถานะ ---- </option>";
		while($i=dbFetchArray($sql)){
			echo"<option value='".$i['id_order_state']."'>".$i['state_name']."</option>";
		}
	}
	//-----------------------dropmovement------------------//
	function drop_movement($id_order){
		list($reference) = dbFetchArray(dbQuery("SELECT reference FROM tbl_order WHERE id_order = $id_order"));
		dbQuery("DELETE FROM tbl_stock_movement WHERE reference = '$reference'");
		dbQuery("DELETE FROM tbl_order_detail_sold WHERE id_order= '$id_order'");
	}
	//---------------- return ออร์เดอร์ที่เปิดบิลแล้ว----------------------//
	function order_return($id_order){
		$sql = dbQuery("SELECT id_product_attribute,product_qty FROM tbl_order_detail WHERE id_order = $id_order");
			$row = dbNumRows($sql); 
			$i=0;
			while($i<$row){
				list($id_product_attribute,$product_qty) = dbFetchArray($sql);
				list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = 0 AND id_product_attribute = $id_product_attribute"));
				if($id_stock !=""){
					$sumqty = $product_qty + $qty;
					dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = $id_stock");
				}else{
					dbQuery("INSERT INTO tbl_stock(id_zone,id_product_attribute,qty)VALUES(0,$id_product_attribute,'$product_qty')");
				}
			$i++;
			}
			drop_movement($id_order);
			dbQuery("UPDATE tbl_temp SET status = 3 WHERE id_order = $id_order");
	}
	function drop_temp_qc($id_order){
		dbQuery("DELETE FROM tbl_temp WHERE id_order = '$id_order'");
		dbQuery("DELETE FROM tbl_qc WHERE id_order = '$id_order'");
	}
/****************************** เปลี่ยนสถานะออเดอร์ ***************************/	
function order_state_change($id_order, $id_order_state, $id_employee){
	$row_open = dbNumRows(dbQuery("SELECT tbl_order.id_order FROM tbl_order LEFT JOIN tbl_order_state_change ON tbl_order.id_order = tbl_order_state_change.id_order WHERE tbl_order.id_order = $id_order AND tbl_order_state_change.id_order_state = 9 AND tbl_order.current_state IN (9,7,6)"));
	if($id_order_state == 2){
		if($row_open > 0){
			order_return($id_order);
		}
		dbQuery("UPDATE tbl_order SET current_state = $id_order_state, valid = 1 WHERE id_order = $id_order");
	}else if($id_order_state==1){
		if($row_open > 0){
		order_return($id_order);
		}
		dbQuery("UPDATE tbl_order SET current_state = $id_order_state, valid = 0 WHERE id_order = $id_order");
		}else if($id_order_state==3){
		if($row_open > 0){
		order_return($id_order);
		}
		dbQuery("UPDATE tbl_order SET current_state = $id_order_state, valid = 0 WHERE id_order = $id_order");
	}else if($id_order_state==8){
		$numrow = dbNumRows(dbQuery("SELECT current_state FROM tbl_order WHERE id_order = $id_order AND current_state IN (9,7,6)"));
		if($numrow > 0 ){
			$sql = dbQuery("SELECT id_product_attribute,product_qty FROM tbl_order_detail WHERE id_order = $id_order");
			$row = dbNumRows($sql); 
			$i=0;
			while($i<$row){
				list($id_product_attribute,$product_qty) = dbFetchArray($sql);
				list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = 0 AND id_product_attribute = $id_product_attribute"));
				if($id_stock !=""){
					$sumqty = $product_qty + $qty;
					dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = $id_stock");
				}else{
					dbQuery("INSERT INTO tbl_stock(id_zone,id_product_attribute,qty)VALUES(0,$id_product_attribute,'$product_qty')");
				}
			$i++;
			}
			drop_movement($id_order);
		}
		dbQuery("UPDATE tbl_order SET current_state = $id_order_state WHERE id_order = $id_order");
	}else{
		
		dbQuery("UPDATE tbl_order SET current_state = $id_order_state WHERE id_order = $id_order");
	}
	dbQuery("INSERT INTO tbl_order_state_change SET id_order = $id_order, id_order_state = $id_order_state, id_employee = $id_employee");
	return true;
}	
 function check_current_qty($id_order, $id_product_attribute, $qty){
		 $sql = dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND status != 5");
		 $sqr =dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
		 list($re) = dbFetchArray($sql);
		 list($rs) = dbFetchArray($sqr);
		$result['current'] = $re;
		$result['order_qty'] = $rs;
		return $result;
	 }
	 function check_product_in_order($id_product_attribute, $id_order){
		 $sql = dbQuery("SELECT id_product_attribute FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
		 $row = dbNumRows($sql);
		 if($row>0){
			 return true;
		 }else{
			 return false;
		 }
	 }
	 function check_qty_in_order($id_product_attribute, $id_order){
		 $sql = dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
		 $row = dbNumRows($sql);
		 if($row>0){
			 list($qty) = dbFetchArray($sql);
		 }else{
			$qty = 0;
		 }
		  return $qty;
	 }
		 
	 function insert_to_temp($id_order, $id_product_attribute, $qty, $id_warehouse, $id_zone,$status, $id_employee){
		 if(dbQuery("INSERT INTO tbl_temp(id_order, id_product_attribute, qty, id_warehouse, id_zone, status, id_employee) VALUES ($id_order, $id_product_attribute, $qty, $id_warehouse, $id_zone, $status, $id_employee)")){
		 		return true;
		 }else{
			 	return false;
		 }
	 }
	 function update_stock_zone($qty, $id_zone, $id_product_attribute){
		 $sql = dbQuery("SELECT qty FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute");
		  $row = dbNumRows($sql);
		 if($row>0){
			  list($old_qty) = dbFetchArray($sql);
			  $new_qty = $old_qty + $qty;
		 	  if($new_qty ==0){
			  		dbQuery("DELETE FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute");
					return true;
				 }else if(dbQuery("UPDATE tbl_stock SET qty = $new_qty WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute ")){
			 		return true;
				 }
		 }else{
			 dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty, date_upd) VALUES ($id_zone, $id_product_attribute, $qty, NOW())");
			 return true;
		 }
	 }

	  function update_buffer_zone($qty, $id_product_attribute){
		  $sql = dbQuery("SELECT qty FROM tbl_stock WHERE id_zone = 0 AND id_product_attribute = $id_product_attribute");
		  $row = dbNumRows($sql);
		  if($row>0){
			  list($old_qty) = dbFetchArray($sql);
			  $new_qty = $old_qty + $qty;
			  if($new_qty ==0){
				  dbQuery("DELETE FROM tbl_stock WHERE id_zone = 0 AND id_product_attribute = $id_product_attribute");
			  }else{
				 if(dbQuery("UPDATE tbl_stock SET qty = $new_qty WHERE id_zone = 0 AND id_product_attribute = $id_product_attribute")){
					 return true;
				 }else{
					 return false;
				 }
			  }
		  }else{
			  	if(dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES (0 ,$id_product_attribute, $qty)")){
					 return true;
				}else{
					 return false;
				}
		 }
	 }
	 //*****************************  Copy รายการใน temp ที่เปิดบิลแล้วไปใส่ในตาราง tbl_order_detail_sold เก็บรายการที่ขายแล้ว ****************************//
	 function order_sold($id_order){
		 $sql = dbQuery("SELECT id_product_attribute, SUM(qty) AS qty FROM tbl_temp WHERE id_order = $id_order AND status = 4 GROUP BY id_product_attribute");
		 while($rs = dbFetchArray($sql)){
			 $id_product_attribute = $rs['id_product_attribute'];
			 $sold_qty = $rs['qty'];
			 $order = new order($id_order);
			 $id_role = $order->role;
			 $id_customer = $order->id_customer;
			 $customer = new customer($id_customer);
			 $id_sale = $customer->id_sale;
			 $id_employee = $order->id_employee;
			 $reference = $order->reference;
			 $order->order_product_detail($id_product_attribute);
			 $id_product = $order->id_product;
			 $product_name = $order->product_name;
			 $product_reference = $order->product_reference;
			 $barcode = $order->barcode;
			 $product_price = $order->product_price;
			 $order_qty = $order->product_qty;
			 $reduction_percent = $order->reduction_percent;
			 $reduction_amount = $order->reduction_amount;
			 $final_price = $order->final_price;
			 $full_amount = $sold_qty * $product_price;
			 $sold_amount = $sold_qty * $final_price;
			 $discount_amount = $full_amount - $sold_amount;
			 $total_amount = $sold_qty * $final_price;
			
			/* echo"*******************************************************************************<br>";
			 echo "id_product_attribute = $id_product_attribute <br>";
			 echo "sold_qty= ".$sold_qty."<br>"."id_sale = $id_sale <br> id_customer =  $id_customer <br>
			 id_product = $id_product <br>
			 product_name = $product_name <br>
			 reference = $product_reference <br>
			 barcode = $barcode <br>
			 price = $product_price <br>
			 order_qty = $order_qty <br>
			 percent = $reduction_percent <br>
			 amount = $reduction_amount <br>
			 final_price = $final_price <br>
			 full_amount = $full_amount <br>
			 sold_amount = $sold_amount <br>
			 discount_amount = $discount_amount <br>
			 total_amount = $total_amount <br>";
			 echo "*******************************************************************************************<br>";*/
			 dbQuery("INSERT INTO tbl_order_detail_sold(id_order, reference, id_role, id_customer, id_employee, id_sale, id_product, id_product_attribute, product_name, product_reference, barcode, product_price, order_qty, sold_qty, reduction_percent, reduction_amount, discount_amount, final_price, total_amount) VALUES( $id_order, '$reference', $id_role, $id_customer, '$id_employee', '$id_sale', $id_product, $id_product_attribute, '$product_name', '$product_reference', '$barcode', $product_price, $order_qty, $sold_qty, $reduction_percent, $reduction_amount, $discount_amount, $final_price, $total_amount)");
		 }
	 }
	 
	 
	 function get_zone($id_zone){
		 list($name_zone) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone"));
		 return $name_zone;
	 }
	 function qty_check_category_before($id_check,$id_category){
			list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_before) FROM tbl_stock_check LEFT JOIN tbl_product_attribute ON tbl_stock_check.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_product ON tbl_product.id_product = tbl_product_attribute.id_product LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE id_category = $id_category and id_check = $id_check"));
		return $total_qty_check;
	 }
	  function qty_check_category_after($id_check,$id_category){
			list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_after) FROM tbl_stock_check LEFT JOIN tbl_product_attribute ON tbl_stock_check.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_product ON tbl_product.id_product = tbl_product_attribute.id_product LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE id_category = $id_category and id_check = $id_check"));
		return $total_qty_check;
	 }
	 function qty_check_product_after($id_check,$id_product){
			list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_after) FROM tbl_stock_check LEFT JOIN tbl_product_attribute ON tbl_stock_check.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product and id_check = $id_check"));
		return $total_qty_check;
	 }
	 function qty_check_product_before($id_check,$id_product){
			list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_before) FROM tbl_stock_check LEFT JOIN tbl_product_attribute ON tbl_stock_check.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product and id_check = $id_check"));
		return $total_qty_check;
	 }
	 function sale_access($id_employee){
		$sql = dbQuery("SELECT id_sale FROM tbl_sale WHERE id_employee = $id_employee");
		$row = dbNumRows($sql);
		if($row>0){
			return true;
		}else{
			return false;
		}
	 }
	 function get_warehouse_name_by_id($id_warehouse){
			$sql = dbQuery("SELECT warehouse_name FROM tbl_warehouse WHERE id_warehouse = $id_warehouse");
			list($warehouse_name) = dbFetchArray($sql);
			 $result = $warehouse_name;
			return $result;
		}
		function get_warehouse_by_zone($id_zone){
			$sql = dbQuery("SELECT id_warehouse FROM tbl_zone WHERE id_zone = $id_zone");
			list($id_warehouse) = dbFetchArray($sql);
			 $result = $id_warehouse;
			return $result;
		}
	function get_category_name($id_category){
		$sql = dbQuery("SELECT category_name FROM tbl_category WHERE id_category = '$id_category'");
		$row = dbNumRows($sql);
		if($row>0){
			list($category_name) = dbFetchArray($sql);
		}else{ 
			$category_name = "ไม่พบหมวดหมู่";
		}
		return $category_name;
	}
	function stock_moveing($id_product_attribute){
		list($stock_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute'"));
		return $stock_moveing;
	}
	function insert_import_fales($id_consign_check,$barcode,$qty,$comment){
		dbQuery("INSERT INTO tbl_consign_import_fales (id_consign_check,barcode,qty,comment)VALUES($id_consign_check,'$barcode','$qty','$comment')");
	}
	function get_id_zone($zone_name){
		list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name = '$zone_name'"));
		return $id_zone;
	}
	
	function report_view($option){
	$today = date('Y-m-d');
	switch($option){
				case "week" :
						$rang = getWeek($today);
						break;
				case "month" :
						$rang = getMonth();
						break;
				case "year" :
						$rang = getYear();
						break ;
				default :
						$rang = getMonth();
						break;
			} 
			$rs['from'] = $rang['from']." 00:00:00";
			$rs['to'] = $rang['to']." 23:59:59";
			return $rs;	
}

function reorder($p_from, $p_to){
			if($p_to < $p_from){
				$from = $p_to;
				$to = $p_from;
			}else{
				$from = $p_from;
				$to = $p_to;
			}
			$arr['from'] = $from;
			$arr['to'] = $to;
			return $arr;
}
?>