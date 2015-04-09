<?php
	class checkstock{
		public $id_employee_open;
		public $id_employee_close;
		public $id_check;
		public $name_check;
		public $date_start;
		public $date_stop;
		public $id_product_attribute;
		public $id_zone;
		public $qty_after;
		public $date_upd;
		public $warehouse_name;
		public $id_warehouse;
		public function __construct()
		{
		}
		
		public function detail($id_check) ////เพิ่มพนักงานใหม่
		{
			list($name_check,$id_employee_open,$id_employee_close,$status,$date_start,$date_stop,$id_warehouse) = dbFetchArray(dbQuery("SELECT name_check,id_employee_open,id_employee_close,status,date_start,date_stop,id_warehouse FROM tbl_check WHERE id_check = $id_check"));
			$this->name_check = $name_check;
			$this->id_employee_open = $id_employee_open;
			$this->id_employee_close = $id_employee_close;
			$this->status = $status;
			$this->date_start = $date_start;
			$this->date_stop = $date_stop;
			$this->id_warehouse = $id_warehouse;
			list($warehouse_name) = dbFetchArray(dbQuery("SELECT warehouse_name FROM tbl_warehouse WHERE id_warehouse = $id_warehouse"));
			$this->warehouse_name = $warehouse_name;
		}
		public function check_open(){
			 $value = dbNumRows(dbQuery("SELECT id_check FROM tbl_check WHERE status = 1"));
			 if($value > 0 ){
				 return true;
			 }else{
				 return false;
			 }
		}
		public function get_id_check(){
			list($id_check) = dbFetchArray(dbQuery("SELECT id_check FROM tbl_check WHERE status = 1"));
			return $id_check;
		}
		public function get_stock_check($id_stock_check)
		{
			list($id_product_attribute,$id_zone,$qty_after,$date_upd) = dbFetchArray(dbQuery("SELECT id_product_attribute,id_zone,qty_after,date_upd FROM tbl_stock_check WHERE id_stock_check = $id_stock_check"));
			$this->id_product_attribute = $id_product_attribute;
			$this->id_zone = $id_zone;
			$this->qty_after = $qty_after;
			$this->date_upd = $date_upd;
		}
		public function table_stock_diff($id_product,$id_check)
		{
				$check_stock = new checkstock();
				$check_stock->detail($id_check);
				$id_warehouse = $check_stock->id_warehouse;
			echo "<table class='table table-bordered table-striped'>
										<thead>
											<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:50%;'>รหัสสินค้า</th>
											<th style='width:15%; text-align:center;'>จำนวนสต็อก</th><th style='width:15%; text-align:center;'>จำนวนที่นับได้</th>
											<th style='width:15%; text-align:center;'>ยอดต่าง</th>
										</thead>";
											$qr = dbQuery("SELECT tbl_product_attribute.id_product_attribute,reference FROM tbl_product_attribute where id_product = $id_product ");
											$row = dbNumRows($qr); 
											$i = 0;
											$n = 1;
											if($row == 0){
												echo "<tr><td align='center' colspan='5'><h4>ยังไม่มีสินค้า</h4></td></tr>";
											}
											while($i<$row){
												list($id_product_attribute,$reference,) = dbFetchArray($qr);
												list($qty_stock,$qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_before),SUM(qty_after) FROM tbl_stock_check WHERE id_check = $id_check AND id_product_attribute = $id_product_attribute"));
												echo "<tr><td align='center'>$n</td><td>$reference</td><td align='center'>".number_format($qty_stock)."</td><td align='center'>".number_format($qty_check)."
												</td><td align='center'>".number_format($qty_check-$qty_stock)."
												</td></tr>";
												$i++;
												$n++;
											}
											echo "</table>";
									  
		}
	}

?>