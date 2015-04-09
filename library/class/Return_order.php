<?php 
	class return_order{
		// tbl_return_order
		public $id_return_order;
		public $reference;
		public $id_return_reason;
		public $return_reason;
		public $id_customer;
		public $id_sale;
		public $id_employee;
		public $date_add;
		public $date_upd;
		public $remark;
		public $status;
		//tbl_return_order_detail
		public $id_return_order_detail;
		public $id_product_attribute;
		public $qty;
		public $id_zone;
		public $detail_date_add;
		public $detail_status;
		public $error_message;
public function __construct($id_return_order=""){
	if($id_return_order ==""){
		return true;
	}else{
		$sql = dbQuery("SELECT * FROM tbl_return_order WHERE id_return_order ='$id_return_order'");
		$ro = dbFetchArray($sql);
		$this->id_return_order = $ro['id_return_order'];
		$this->reference = $ro['reference'];
		$this->id_return_reason = $ro['id_return_reason'];
		list($reason) = dbFetchArray(dbQuery("SELECT reason_name FROM tbl_return_reason WHERE id_return_reason ='".$this->id_return_reason."'"));
		$this->return_reason = $reason;
		$this->id_customer = $ro['id_customer'];
		$this->id_sale = $ro['id_sale'];
		$this->id_employee = $ro['id_employee'];
		$this->date_add = $ro['date_add'];
		$this->date_upd = $ro['date_upd'];
		$this->remark = $ro['remark'];
		$this->status = $ro['status'];
	}
}

public function add(array $data){
	list($reference, $id_reason, $id_customer, $id_sale, $id_employee, $date_add, $remark) = $data;
	$sql = dbQuery("INSERT INTO tbl_return_order (reference, id_return_reason, id_customer, id_sale, id_employee, date_add, remark, status) 
						VALUES ('$reference', $id_reason, $id_customer, $id_sale, $id_employee, '$date_add', '$remark', 0 )");
	if($sql){
		return true;
	}else{
		$this->error_message = "เพิ่มเอกสารไม่สำเร็จ";
		return false;
	}
}

public function edit(array $data){
	list($id_return_order, $id_reason, $id_customer, $id_sale, $id_employee, $date_add, $remark) = $data;
	$sql = dbQuery("UPDATE tbl_return_order SET id_return_reason = $id_reason, id_customer = $id_customer, id_sale = $id_sale, id_employee = $id_employee, date_add = '$date_add', remark = '$remark' WHERE id_return_order = $id_return_order");
	if($sql){
		return true;
	}else{
		$this->error_message = "แก้ไขเอกสารไม่สำเร็จ";
		return false;
	}
}


public function getReturnId($reference){ // Return id_return_order  by input reference
	list($id_return_order)=dbFetchArray(dbQuery("SELECT id_return_order FROM tbl_return_order WHERE reference = '$reference'"));
	return $id_return_order;
}

public function return_order_detail($id_return_order_detail){
	if(empty($id_return_order_detail)){
		$message = "No return detail";
		return $message;
	}else{
		$sql = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order_detail = '$id_return_order_detail'");
		$rd = dbFetchArray($sql);					
		$this->id_return_order_detail = $rd['id_return_order_detail'];
		$this->id_return_order = $rd['id_return_order'];
		$this->id_product_attribute = $rd['id_product_attribute'];
		$this->qty = $rd['qty'];
		$this->id_zone = $rd['id_zone'];
		$this->detail_date_add = $rd['date_add'];
		$this->detail_status = $rd['status'];
		/////////////////  ////////////////////////////
		$sqr = dbQuery("SELECT * FROM tbl_return_order WHERE id_return_order =".$this->id_return_order);
		$ro = dbFetchArray($sqr);
		$this->id_return_order = $ro['id_return_order'];
		$this->reference = $ro['reference'];
		$this->id_return_reason = $ro['id_return_reason'];
		list($reason) = dbFetchArray(dbQuery("SELECT reason_name FROM tbl_return_reason WHERE id_return_reason ='".$this->id_return_reason."'"));
		$this->return_reason = $reason;
		$this->id_customer = $ro['id_customer'];
		$this->id_sale = $ro['id_sale'];
		$this->id_employee = $ro['id_employee'];
		$this->date_add = $ro['date_add'];
		$this->date_upd = $ro['date_upd'];
		$this->remark = $ro['remark'];
		$this->status = $ro['status'];
	}
}



}
?>
