<?php
			$weekDay = array( 'อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสฯ', 'ศุกร์', 'เสาร์');
			$thaiMon = array( "1" => "มกราคม", "2" => "กุมภาพันธ์", "3" => "มีนาคม", "4" => "เมษายน",
				  "5" => "พฤษภาคม","6" => "มิถุนายน", "7" => "กรกฎาคม", "8" => "สิงหาคม",
				  "9" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม");
			
			//Sun - Sat
			$month = isset($_GET['month']) ? $_GET['month'] : date('m'); //ถ้าส่งค่าเดือนมาใช้ค่าที่ส่งมา ถ้าไม่ส่งมาด้วย ใช้เดือนปัจจุบัน
			$year = isset($_GET['year']) ? $_GET['year'] : date('Y'); //ถ้าส่งค่าปีมาใช้ค่าที่ส่งมา ถ้าไม่ส่งมาด้วย ใช้ปีปัจจุบัน
			
			//วันที่
			$startDay = $year.'-'.$month."-01";   //วันที่เริ่มต้นของเดือน
			$timeDate = strtotime($startDay);   //เปลี่ยนวันที่เป็น timestamp
			$lastDay = date("t", $timeDate);   //จำนวนวันของเดือน	
			$endDay = $year.'-'.$month."-". $lastDay;  //วันที่สุดท้ายของเดือน
			$startPoint = date('w', $timeDate);   //จุดเริ่มต้น วันในสัปดาห์
			$start_date = $startDay; // เก็บวันที่
			$today = date("Y-m-d");
?>

 <script type='text/javascript'>
    function goTo(month, year){
   window.location.href = "index.php?content=sale_calendar&year="+ year +"&month="+ month;
    }
 </script>

<?php


$title = $thaiMon[$month]." ". $year;

//ลดเวลาลง 1 เดือน
$prevMonTime = strtotime ( '-1 month' , $timeDate  );
$prevMon = date('m', $prevMonTime);
$prevYear = date('Y', $prevMonTime);
//เพิ่มเวลาขึ้น 1 เดือน
$nextMonTime = strtotime ( '+1 month' , $timeDate  );
$nextMon = date('m', $nextMonTime);
$nextYear = date('Y', $nextMonTime);
echo "<div class='container'>
<div class='row'>
	<div class='col-sm-12'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<div class='row'>
					<div class='col-lg-3'><button class='btn btn-default' onclick='goTo($prevMon, $prevYear)'><< เดือนที่แล้ว</button></div>
					<div class='col-lg-6'><h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>$title</h4></div>
					<div class='col-lg-3'> <button class='btn btn-default pull-right' onclick='goTo($nextMon, $nextYear)'>เดือนต่อไป >></button></div>
				</div>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                ";
echo"
 <table class='table table-bordered table-striped' style='margin-bottom:0px;'>
                <thead>
                	<th style='text-align:center; width:12.5%;'><b>อาทิตย์</b></th><th style='text-align:center; width:12.5%;'><b>จันทร์</b></th><th style='text-align:center; width:12.5%;'><b>อังคาร</b></th>
                    <th style='text-align:center; width:12.5%;'><b>พุธ</b></th><th style='text-align:center; width:12.5%;'><b>พฤหัส</b></th><th style='text-align:center; width:12.5%;'><b>ศุกร์</b></th>
                    <th style='text-align:center; width:12.5%;'><b>เสาร์</b></th>
                </thead>";

echo "<tr>";    //เปิดแถวใหม่
$col = $startPoint;          //ให้นับลำดับคอลัมน์จาก ตำแหน่งของ วันในสับดาห์ 
if($startPoint < 7){         //ถ้าวันอาทิตย์จะเป็น 7
 echo str_repeat("<td> </td>", $startPoint); //สร้างคอลัมน์เปล่า กรณี วันแรกของเดือนไม่ใช่วันอาทิตย์
}
for($i=1; $i <= $lastDay; $i++){ //วนลูป ตั้งแต่วันที่ 1 ถึงวันสุดท้ายของเดือน
 $col++;       //นับจำนวนคอลัมน์ เพื่อนำไปเช็กว่าครบ 7 คอลัมน์รึยัง
 $n = $i-1;
 $day = date('Y-m-d', strtotime("+$n day $start_date"));
 if($day == $today){ $current = "style='background-color:#A0D468;'"; }else{ $current = ""; }
 $sale = new sale();
 $sale_amount = $sale->totalSale($day);
 echo "<td $current><div class='row'><div class='col-lg-12'><p class='pull-right'>", $i , "</p></div><div class='col-lg-12' style='text-align:center; color:#4A89DC;'><b>$sale_amount</b></div><div class='col-lg-12' style='text-align:center;'>&nbsp;</div></div></td>";  //สร้างคอลัมน์ แสดงวันที่ 
 if($col % 7 == false){   //ถ้าครบ 7 คอลัมน์ให้ขึ้นบรรทัดใหม่
  echo "</tr><tr>";   //ปิดแถวเดิม และขึ้นแถวใหม่
  $col = 0;     //เริ่มตัวนับคอลัมน์ใหม่
 }
}
if($col < 7 &&$col>0){         // ถ้ายังไม่ครบ7 วัน
 echo str_repeat("<td> </td>", 7-$col); //สร้างคอลัมน์ให้ครบตามจำนวนที่ขาด
}
echo '</tr>';  //ปิดแถวสุดท้าย
echo '</table>'; //ปิดตาราง
echo "</div></div>
	<div class='panel-footer'><div class='row'><div class='col-lg-8'><span style='font-size:16px; color: bule;'><b>รวม</b>ใส่ยอดรวมตรงนี้</span></div><div class='col-lg-4'><p class='pull-right'><button type='button' class='btn btn-default'>เดือนปัจจุบัน</button></div></div>

</div></div></div></div>";

?>
