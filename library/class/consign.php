<?php
class consign{
	public $id_order_consign;
	public $id_order_consignment;
	public $date_add;
	public $id_customer;
	public $reference;
	public $comment;
	public $consign_status;
	public $id_consign_check;
	public function __construct($id_order_consign=""){
		if($id_order_consign ==""){ 
			return false;
		}else{
		$sql = dbQuery("SELECT * FROM tbl_order_consign WHERE id_order_consign=$id_order_consign");
		$consign = dbFetchArray($sql);
		$this->id_order_consign = $consign['id_order_consign'];
		$this->date_add = $consign['date_add'];
		$this->id_customer = $consign['id_customer'];
		$this->reference = $consign['reference'];
		$this->comment = $consign['comment'];
		$this->consign_status = $consign['consign_status'];
		$this->id_zone = $consign['id_zone'];
		$this->id_consign_check = $consign['id_consign_check'];
		}
	}
	public function get_zone($id_customer){
		list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_order_consignment WHERE id_customer = $id_customer"));
		return $id_zone;
	}
	public function order_qty($id_product_attribute,$id_zone){
		list($qty) = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
		return $qty;
	}
}//end class


?>
