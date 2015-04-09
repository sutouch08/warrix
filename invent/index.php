<?php
require '../library/config.php';
require '../library/functions.php';
require "function/tools.php";
checkUser();
$user_id = $_COOKIE['user_id'];
$content = 'main.php';
$page = (isset($_GET['content'])&& $_GET['content'] !='')?$_GET['content']:'';
switch($page){
	//******* สินค้า *****************//
		case 'product':
		$content = "product.php";
		$pageTitle = "รายการสินค้า";
		break;
	case 'category':
		$content = 'category.php';
		$pageTitle = 'หมวดหมู่สินค้า';
		break;
	case 'color':
		$content = 'color.php';
		$pageTitle = 'รายการสี';
		break;
	case 'color_group':
		$content = 'color_group.php';
		$pageTitle = 'กลุ่มสี';
		break;
	case 'size':
		$content = 'size.php';
		$pageTitle = 'รายการไซด์';
		break;
	case "attribute":
		$content = "attribute.php";
		$pageTitle = "คุณลักษณะ";
		break;
	case "attribute_gen":
		$content = "attribute_gen.php";
		$pageTitle = "สร้างรายการสินค้าอัตโนมัติ";
		break;
		
		//********* คลังสินค้า *****************//
	case "product_in":
		$content = "product_in.php";
		$pageTitle = "รับสินค้าเข้า";
		break;
	case "order_return":
		$content = "order_return.php";
		$pageTitle = "รับคืนสินค้า";
		break;
	case "requisition";
		$content = "requisition.php";
		$pageTitle = "เบิกสินค้า";
		break;
	case "lend";
		$content = "lend.php";
		$pageTitle = "ยืมสินค้า";
		break;
	case "ProductMove":
		$content = "product_move.php";
		$pageTitle = "ย้ายพื้นที่จัดเก็บ";
		break;
	case "ProductCheck":
		$content = "product_check.php";
		$pageTitle = "ตรวจนับสินค้า";
		break;
	case "ProductAdjust":
		$content = "product_adjust.php";
		$pageTitle = "ปรับยอดสินค้า";
		break;
	case 'warehouse':
		$content = 'warehouse.php';
		$pageTitle = 'คลังสินค้า';
		break;
	case 'zone':
		$content = 'zone.php';
		$pageTitle = 'รายการโซน';
		break;
	case 'import_stock':
		$content = 'import_stock.php';
		$pageTitle = "นำเข้ารายการสินค้า";
		break;
	case 'tranfer':
		$content = 'tranfer.php';
		$pageTitle = "โอนคลัง";
		break;
	case 'program_product_in':
		$content = 'program_product_in.php';
		$pageTitle = "กำหนดการสินค้าเข้า";
		break;
	//*************** ออเดอร์ ***************//	
	case "order":
		$content = "order.php";
		$pageTitle= "ออเดอร์";
		break;
	case "sponsor";
		$content = "sponsor.php";
		$pageTitle = "สปอนเซอร์";
		break;
	case "consignment";
		$content = "consignment.php";
		$pageTitle = "ฝากขาย";
		break;
	case "order_pos" :
		$content = "order_pos.php";
		$pageTitle = "Order POS";
		break;
	case "prepare":
		$content = "product_prepare.php";
		$pageTitle = "จัดสินค้า";
		break;
	case "qc":
		$content = "product_qc.php";
		$pageTitle = "ตรวจสินค้า";
		break;
	case "bill":
		$content = "bill.php";
		$pageTitle = "รายการรอเปิดบิล";
		break;
	case "order_closed" :
		$content = "order_closed.php";
		$pageTitle = "รายการเปิดบิลแล้ว";
		break;	
	case "request" :
		$content = "order_request.php";
		$pageTitle = "ร้องขอสินค้า";
		break;
	//********** ลูกค้า ***********************//	
	case "customer";
		$content="customer.php";
		$pageTitle="ข้อมูลลูกค้า";
		break;
	case "address":
		$content = "address.php";
		$pageTitle = "ที่อยู่";
		break;
	case "group":
		$content = "group.php";
		$pageTitle = "กลุ่มลูกค้า";
		break;
	case "add_sponsor" :
		$content = "add_sponsor.php";
		$pageTitle = "สปอนเซอร์";
		break;
		
	//*************** กำหนดค่า ******************//
	case "config";
		$content = "setting.php";
		$pageTitle = "การตั้งค่า";
		break;
	case "Employee":
		$content = "employee.php";
		$pageTitle = "พนักงาน";
		break;
	case "sale";
		$content = "sale.php";
		$pageTitle = "พนักงานขาย";
		break;
	case "pos_sale" :
		$content = "pos_sale.php";
		$pageTitle = "พนักงานขายหน้าร้าน";
		break;
	case "Profile":
		$content = "profile.php";
		$pageTitle = "โปรไฟล์";
		break;
	case "securable":
		$content = "securable.php";
		$pageTitle = "กำหนดสิทธิ์";
		break;
	case "consumption":
		$content = "consumption.php";
		$pageTitle = "ค่าใช้จ่ายคงที่";
		break;
	case "fuel":
		$content = "fuel.php";
		$pageTitle = "เชื้อเพลิง";
		break;
	case "social_config":
		$content = "social_config.php";
		$pageTitle = "Socail Media";
		break;
	case "shop" :
		$content = "shop.php";
		$pageTitle = "Shop";
		break;
		
	//************** นับสินค้า **************//
	case "checkstock" :
		$content = "check_stock.php";
		$pageTitle = "เช็คสต็อก";
		break;
	case "OpenCheck" :
		$content = "open_check.php";
		$pageTitle = "เปิดปิดการตรวจนับ";
		break;
	case "check_stock_moniter":
		$content = "check_stock_moniter.php";
		$pageTitle = "moniter";
		break;
	case "ProductCount" :
		$content = "product_count.php";
		$pageTitle = "ตรวจสอบยอดสินค้าจากการตรวจนับ";
		break;
		
	//************** บัญชี ****************//
	case "repay":
		$content = "repay.php";
		$pageTitle = "ตัดหนี้";
		break;
	case "consign":
		$content = "consign.php";
		$pageTitle = "ตัดยอดฝากขาย";
		break;
	case "calculate_travel":
		$content = "calculate_travel.php";
		$pageTitle = "คำนวนค่าเดินทาง";
		break;
	//************* รายงาน ******************//	
	/*************** รายงานทั่วไป *******************/
	case "current_stock":
		$content = "report/current_stock.php";
		$pageTitle = "รายงานสินค้าคงเหลือปัจจุบัน";
		break;
	case "stock_report":
		$content = "report/stock_report.php";
		$pageTitle = "รายงานสินค้าคงเหลือ";
		break;
	case "stock_zone_report";
		$content = "report/stock_zone_report.php";
		$pageTitle = " รายงานสินค้าคงเหลือ ";
		break;
	case "fifo";
		$content = "report/stock_fifo_report.php";
		$pageTitle = " FIFO ";
		break;
	case "total_fifo" :
		$content = "report/stock_fifo_total.php";
		$pageTitle = "รายงานยอดรวมสินค้า เข้า - ออก";
		break;
	case "movement_by_reason":
		$content = "report/movement_by_reason.php";
		$pageTitle = "รายงานความเคลื่อนไหวสินค้า แยกตามเหตุผล";
		break;	
	case "consignment_by_zone" :
		$content = "report/consignment_by_zone.php";
		$pageTitle = "รายงานบิลส่งสินค้าไปฝากขายแยกตามโซน";
		break;	
	case "consign_by_zone" :
		$content = "report/consign_by_zone.php";
		$pageTitle = "รายงานสินค้าฝากขายแยกตามโซน";
		break;
	case "non_move":
		$content = "report/stock_non_move.php";
		$pageTitle = "รายงานสินค้าไม่เคลื่อนไหว";
		break;
	case "request_report":
		$content = "report/request_report.php";
		$pageTitle = "รายงานการร้องขอสินค้า";
		break;
	case "request_by_customer":
		$content = "report/request_by_customer.php";
		$pageTitle = "รายงานการร้องขอสินค้าแยกตามลูกค้า";
		break;
	case "sale_report_zone":
		$content = "report/sale_report_zone.php";
		$pageTitle = "รายงานยอดขาย แยกตามพื้นที่การขาย";
		break;
	case "sale_report_employee":
		$content = "report/sale_report_employee.php";
		$pageTitle = "รายงานยอดขาย แยกตามพนักงานขาย";
		break;
	case "sale_amount_detail":
		$content = "report/sale_amount_detail.php";
		$pageTitle = "รายงานรายละเอียดการขาย แยกตามพนักงานขาย";
		break;
	case "sale_amount_document":
		$content = "report/sale_amount_document.php";
		$pageTitle = "รายงานยอดขาย แยกตามพนักงานและเอกสาร";
		break;
	case "sale_report_customer":
		$content = "report/sale_report_customer.php";
		$pageTitle = "รายงานยอดขาย แยกตามลูกค้า";
		break;
	case "sale_report_product":
		$content = "report/sale_report_product.php";
		$pageTitle = "รายงานยอดขาย แยกตามสินค้า";
		break;
	case "sale_by_document":
		$content = "report/sale_by_document.php";
		$pageTitle = "รายงานยอดขาย แยกตามสินค้า";
		break;
	
	case "report_stock_backlogs":
		$content = "report/report_stock_backlogs.php";
		$pageTitle = "รายงานสินค้า ค้างส่ง";
		break;
	case "customer_by_product":
		$content = "report/customer_by_product.php";
		$pageTitle = "รายงานลูกค้า แยกตามสินค้า";
		break;
	case "customer_by_product_attribute":
		$content = "report/customer_by_product_attribute.php";
		$pageTitle = "รายงานลูกค้า แยกตามสินค้า";
		break;
	case "product_by_customer":
		$content = "report/product_by_customer.php";
		$pageTitle = "รายงานสินค้า แยกตามลูกค้า";
		break;
	case "document_by_customer":
		$content = "report/document_by_customer.php";
		$pageTitle = "รายงานเอกสาร แยกตามลูกค้า";
		break;
	case "product_attribute_by_customer":
		$content = "report/product_attribute_by_customer.php";
		$pageTitle = "รายงานรายการสินค้า แยกตามลูกค้า";
		break;
	case "document_by_product_attribute":
		$content = "report/document_by_product_attribute.php";
		$pageTitle = "รายงานเอกสาร แยกตามรายการสินค้า";
		break;
	case "discount_edit":
		$content = "report/discount_edit_report.php";
		$pageTitle = "รายงานการแก้ไขส่วนลด";
		break;
	case "sponser_report":
		$content = "report/sponser_report.php";
		$pageTitle = "รายงานยอดสปอนเซอร์";
		break;
	case "recieved_report":
		$content = "report/product_recieved_report.php";
		$pageTitle = "รายงานการรับสินค้า";
		break;
		/*************** รายงานวิเคราะห์ ****************/
	case "chart_move_movement_report":
		$content = "report/stock_move_chart_report.php";
		$pageTitle = "รายงานภาพรวมสินค้าเปรียบเทียบยอด เข้า / ออก";
		break;
	case "chart_movement_report":
		$content = "report/stock_chart_report.php";
		$pageTitle = "รายงานความเคลื่อนไหวสินค้า";
		break;
	case "sale_chart_zone":
		$content = "report/sale_chart_zone.php";
		$pageTitle = "กราฟรายงานยอดขาย เปรียบเทียบพื้นที่การขาย";
		break;
	case "attribute_chart_report":
		$content = "report/attribute_chart_report.php";
		$pageTitle = "รายงานวิเคราะห์คุณลักษณะสินค้า";
		break;
	case "sale_amount_report":
		$content = "report/sale_amount_report.php";
		$pageTitle = "สรุปยอดขายรวม";
		break;
	case "sale_leader_board" :
		$content = "report/sale_leader_board.php";
		$pageTitle = "สรุปยอดขายแยกตามพนักงาน";
		break;
	case "sale_leader_group" :
		$content = "report/sale_leader_group.php";
		$pageTitle = "สรุปยอดขายแยกตามพื้นที่";
		break;
	case "sale_calendar" :
		$content = "report/sale_calendar.php";
		$pageTitle = "ปฏิทินการขาย";
		break;
	case "stock_chart_zone_report" :
		$content = "report/stock_chart_zone_report.php";
		$pageTitle = "กราฟรายงานการเคลื่อนไหวสินค้า แยกตามพื้นที่การขาย";
		break;
	case "sale_chart":
		$content="report/sale_chart.php";
		$pageTitle = "กราฟรายงานวิเคราะห์ยอดขาย";
		break;
	case "sale_table":
		$content="report/sale_table.php";
		$pageTitle = "ตารางรายงานวิเคราะห์ยอดขาย";
		break;
	//******************** พิมพ์ฐานข้อมูล ***************************//
	case "product_db":
		$content="product_db.php";
		$pageTitle = "พิมพ์ฐานข้อมูลสินค้า";
		break;
	default:
		$content = 'main.php';
		$pageTitle = 'Smart Inventory';
		break;
}
require_once 'template.php';
?>

