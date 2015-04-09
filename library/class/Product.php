<?php
	
class product {
	public $id_product;
	public $product_attribute;
	public $id_product_attribute;
	public $reference; //รหัสสินค้าของ SKU
	public $barcode;  // บาร์โค้ดสินค้าของ SKU
	public $id_color; 
	public $id_size; 
	public $id_attribute; 
	public $product_code; //รหัสสินค้าของ Style
	public $product_name; 
	public $product_price;
	public $product_cost;
	public $weight = 0.00;
	public $width = 0.00;
	public $length = 0.00;
	public $height = 0.00;
	public $cover_image; // รูปภาพใช้เป็นหน้าปก
	public $default_category_id; //หมวดหมู่หลักของสินค้า
	public $color_code;
	public $color_name;
	public $size_name;
	public $attribute_name;
	public $product_detail; 
	public $value;
	public $discount; //ส่วนลดสุดท้าย(เป็นจำนวนเงิน)
	public $product_discount; //ส่วนลดที่ตัวสินค้า
	public $discount_type; // ประเภทส่วนลด ( percentage or amount )
	public $date_add;
	public $date_upd;
	public $active;
	public $stock_qty;
	public $product_sell; //ราคาขายสุดท้ายหลังหักส่วนลด
	public $product_discount1;
	public $id_customer; // เก็บ id_customer ตอนที่เรียกใช้ function product_detail เอาไว้ใช้กับตัวอื่นต่อ
public function __construct(){
	return true;
}	
	//***************  Add new product to database  ***************************//
public function add_product(array $data){
		list($product_code, $product_name, $product_cost, $product_price, $weight, $width, $length, $height, $discount_type, $discount, $default_category_id, $active, $description, $category_id) = $data;
		$sql = "INSERT INTO tbl_product (product_code, product_name, product_cost, product_price, weight, width, length, height, discount_type, discount, default_category_id, active, date_add) VALUES ('$product_code', '$product_name', $product_cost, $product_price, $weight, $width, $length, $height, '$discount_type', $discount, $default_category_id, $active, NOW())";
		
		if(dbQuery($sql)){
			$id_product = $this->get_product_id_by_code($product_code);
			$this->set_product_description($id_product, $description);
			$this->set_product_category($id_product, $category_id);
			return true;
		}else{
			return false;
		}
}
	//**********************  Edit product *************************//
public function edit_product( array $data){
	list($id_product, $product_code, $product_name, $product_cost, $product_price, $weight, $width, $length, $height, $discount_type, $discount, $default_category_id, $active, $description, $category_id) = $data;
	$sql = "UPDATE tbl_product SET product_code ='$product_code', product_name = '$product_name', product_cost = $product_cost, product_price = $product_price, weight = $weight, width = $width, length = $length, height = $height, discount_type = '$discount_type', discount = $discount, default_category_id = $default_category_id, active = $active WHERE id_product = $id_product";
	if(dbQuery($sql)){
		$this->set_product_description($id_product, $description);
		$this->set_product_category($id_product, $category_id);
		return true;
	}else{
		return false;
	}
}

//******************* Delete Product  ************************//
public function delete_product($id_product){
	// ตรวจสอบ ยอดคงเหลือก่อนลบข้อมูล
	$checked = dbNumRows(dbQuery("SELECT qty FROM tbl_stock LEFT JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product AND qty>0")); 
	$r1 = dbNumRows(dbQuery("SELECT id_product FROM tbl_order_detail WHERE id_product = $id_product"));
	$r2 = dbNumRows(dbQuery("SELECT id_product FROM tbl_adjust_detail LEFT JOIN tbl_product_attribute ON tbl_adjust_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product"));
	$r3 = dbNumRows(dbQuery("SELECT id_product FROM tbl_recieved_detail LEFT JOIN tbl_product_attribute ON tbl_recieved_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product"));
	$r4 = dbNumRows(dbQuery("SELECT id_product FROM tbl_stock_movement LEFT JOIN tbl_product_attribute ON tbl_stock_movement.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product"));
	$transection = 0+$r1+$r2+$r3+$r4;
	if($checked !=0){
		$message = "คุณไม่สามารถลบสินค้านี้ได้เนื่องจากยังมียอดสินค้าคงเหลือ";
		return $message;
	}else if($transection>0){
		$message = "คุณไม่สามารถลบสินค้านี้ได้เนื่องจากมี transection ที่เกิดจากสินค้านี้ในระบบแล้ว";
		return $message;
	}else{
		dbQuery("DELETE FROM tbl_product WHERE id_product = $id_product");
		dbQuery("DELETE FROM tbl_product_attribute WHERE id_product = $id_product");
		dbQuery("DELETE FROM tbl_product_detail WHERE id_product = $id_product");
		dbQuery("DELETE FROM tbl_category_product WHERE id_product = $id_product");
		return true;
	}
}

//**********************  Add Combination  ******************//	
public function add_product_attribute(array $data){
	list($id_product, $reference, $barcode, $id_color, $id_size, $id_attribute, $cost, $price, $weight, $width, $length, $height, $id_image, $barcode_pack, $qty) = $data;
	$sql = "INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_color, id_size, id_attribute, cost, price, weight, width, length, height ) VALUES ($id_product, '$reference', '$barcode', $id_color, $id_size, $id_attribute, ";
	$sql .= "$cost, $price, $weight, $width, $length, $height)";
	if(dbQuery($sql)){
		if($id_image !=""){
			list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = $id_product AND reference = '$reference'"));
			$this->set_image($id_product_attribute, $id_image);
		}
		$this->set_pack($id_product_attribute, $barcode_pack, $qty);
		return true;
	}else{
		return false;
	}
}

//***************************  Edit Combination  ******************************//
public function edit_product_attribute(array $data){
	list($id_product_attribute, $reference, $barcode, $id_color, $id_size, $id_attribute, $cost, $price, $weight, $width, $length, $height, $id_image, $barcode_pack, $qty) = $data;
	$sql = "UPDATE tbl_product_attribute SET reference = '$reference', barcode = '$barcode', id_color = $id_color, id_size = $id_size, id_attribute = $id_attribute, cost = $cost, price = $price, weight = $weight, width = $width, 
			  length = $length, height = $height WHERE id_product_attribute = $id_product_attribute";
	if(dbQuery($sql) && $this->set_image($id_product_attribute, $id_image)&& $this->set_pack($id_product_attribute, $barcode_pack, $qty)){
		return true;
	}else{
		return false;
	}
}

//************************  Delete Combination  *********************************//	
public function deletd_product_attribute($id_product_attribute){
	$checked = dbNumRows(dbQuery("SELECT qty FROM stock_qty WHERE id_product_attribute=$id_product_attribute AND qty>0")); // ตรวจสอบ ยอดคงเหลือก่อนลบข้อมูล
	$r1 = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute"));
	$r2 = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_adjust_detail WHERE id_product_attribute = $id_product_attribute"));
	$r3 = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_recieved_detail WHERE id_product_attribute = $id_product_attribute"));
	$r4 = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute"));
	$transection = 0+$r1+$r2+$r3+$r4;
	if($checked !=0){
		$message = "คุณไม่สามารถลบสินค้านี้ได้เนื่องจากยังมียอดสินค้าคงเหลือ";
		return $message;
	}else if($transection>0){
		$message = "คุณไม่สามารถลบสินค้านี้ได้เนื่องจากมี transection ที่เกิดจากสินค้านี้ในระบบแล้ว";
		return $message;
	}else{
		dbQuery("DELETE FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute");
		dbQuery("DELETE FROM tbl_stock WHERE id_product_attribute = $id_product_attribute");
		dbQuery("DELETE FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute");
		return true;	
	}
}

//*****************************  คืนค่า id_product จากรหัสสินค้า ************//
public function get_product_id_by_code($product_code){
	list($id_product) = dbFetchArray(dbQuery("SELECT id_product FROM tbl_product WHERE product_code='$product_code'"));
	return $id_product;
}
//*********  ตรวจสอบว่า มีรูปผูกไว้กับสินค้านี้หรือไม่ ถ้ามี Update ถ้าไม่มี Insert
public function set_image($id_product_attribute, $id_image){ 
	$row = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute")); 
	if($row>0){
			$sql = "UPDATE tbl_product_attribute_image SET id_image = '$id_image' WHERE id_product_attribute = $id_product_attribute";
		}else{
			$sql = "INSERT INTO tbl_product_attribute_image (id_product_attribute, id_image) VALUES ($id_product_attribute, '$id_image')";
	}
	if(dbQuery($sql)){ 	return true; }else{ return false; }
}

//*********  บาร์โค้ดและจำนวนใน แพ็คสินค้า
public function set_pack($id_product_attribute, $barcode_pack, $qty){
	if($barcode_pack !=""){
	list($id_product_pack) = dbFetchArray(dbQuery("SELECT id_product_pack FROM tbl_product_pack WHERE id_product_attribute = $id_product_attribute"));
	if($id_product_pack != ""){
		$sql = "UPDATE tbl_product_pack SET barcode_pack = '$barcode_pack' , qty = '$qty' WHERE id_product_pack = $id_product_pack";
	}else{
		$sql = "INSERT INTO tbl_product_pack (id_product_attribute, qty, barcode_pack) VALUES ($id_product_attribute,$qty,$barcode_pack)";
	}
	if(dbQuery($sql)){ 	return true; }else{ return false; }
	}else{ 
		return true;
	}
}

//**************************  Product Description **********************************//
public function set_product_description($id_product, $description){
	$row = dbNumRows(dbQuery("SELECT id_product_detail FROM tbl_product_detail WHERE id_product = $id_product"));
	if($row>0)
		{
			$sql = "UPDATE tbl_product_detail SET product_detail = '$description' WHERE id_product = $id_product";
		 }else{
			 $sql = "INSERT INTO tbl_product_detail (id_product, product_detail) VALUES($id_product, '$description' )";
		 }	
	if(dbQuery($sql)){ 	return true; }else{ return false; }	 
}

public function set_product_category($id_product, array $category_id){
		dbQuery("DELETE FROM tbl_category_product WHERE id_product = $id_product");
		foreach($category_id as $cate_id){
			list($max) = dbFetchArray(dbQuery("SELECT max(position) as max FROM tbl_category_product"));
			$position = $max+1;
			dbQuery("INSERT INTO tbl_category_product (id_category, id_product, position) VALUES ($cate_id, $id_product, $position)");
		}
		return true;
}
public function product_detail($id_product,$id_customer=0){
		$sql = dbQuery("SELECT product_code, product_name, product_cost, product_price, weight, width, length, height, discount_type, discount, default_category_id, active FROM tbl_product WHERE id_product = $id_product");
		list($product_code, $product_name, $product_cost, $product_price, $weight, $width, $length, $height, $discount_type, $discount, $default_category_id, $active) = dbFetchArray($sql);
		$sqr = dbQuery("SELECT product_detail FROM tbl_product_detail WHERE id_product = $id_product");
		list($product_detail) = dbFetchArray($sqr);
		$this->id_customer = $id_customer;
		$this->id_product =$id_product;
		$this->product_code = $product_code;
		$this->product_name = $product_name;
		$this->product_price = $product_price;
		$this->product_cost = $product_cost;
		$this->weight = $weight;
		$this->width = $width;
		$this->length = $length;
		$this->height = $height;
		$this->discount_type = $discount_type;
		$this->product_detail = $product_detail;
		$this->product_discount = $discount;
		$this->default_category_id = $default_category_id;
		$this->active = $active;
		$this->get_discount($id_product, $product_price, $id_customer); // ได้ค่าเป็นส่วนลดเป็นจำนวนเงิน
		$this->product_sell = $this->product_price - $this->discount;
		$this->cover_image = $this->getCoverImage($id_product,2);	
}
public function product_attribute_detail($id_product_attribute, $id_customer=0){
		if($id_customer==0){ $id_customer = $this->id_customer; }
		$sql = dbQuery("SELECT id_product, reference, barcode, id_color, id_size, id_attribute, cost, price, weight, width, length, height, date_upd, active FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute");
		list($id_product, $reference, $barcode, $id_color, $id_size, $id_attribute, $cost, $price, $weight, $width, $length, $height, $date_upd, $active ) = dbFetchArray($sql);
		$this->id_product_attribute = $id_product_attribute;
		$this->id_product = $id_product;
		$this->reference = $reference;
		$this->barcode = $barcode;
		$this->id_attribute = $id_attribute;
		$this->id_color = $id_color;
		$this->color_code = $this->get_color_code($id_color);
		$this->color_name = $this->get_color_name($id_color);
		$this->id_size = $id_size;
		$this->size_name = $this->get_size_name($id_size);
		$this->attribute_name = $this->get_attribute_name($id_attribute);
		$this->product_cost = $cost;
		$this->product_price = $price;
		$this->weight = $weight;
		$this->width = $width;
		$this->length = $length;
		$this->height = $height;
		$this->date_upd = $date_upd;
		$this->active = $active;
		$this->id_image = $this->get_id_image_attribute($id_product_attribute);
		$this->image_attribute = $this->get_image_path($this->id_image,2);	
		$this->get_discount($this->id_product, $price, $id_customer);
		$this->product_sell = $this->product_price - $this->discount;
}

public function get_discount($id_product, $price, $id_customer=0){
	$sql = dbQuery("SELECT discount_type, discount FROM tbl_product WHERE id_product = $id_product");
	list($discount_type, $discount) = dbFetchArray($sql);	
	// หาส่วนลดที่มากที่สุดตามสิทธิ์ลูกค้า
		if($discount_type=="percentage"){ $product_discount = $price *($discount/100); }else{  $product_discount = $discount;} // แปลงส่วนลดที่ตัวสินค้าเป็นจำนวนเงิน
		$customer_discount = ($price*$this->get_max_discount($this->id_product,$id_customer))/100; // ดึงส่วนลดสูงสุดตามหมวดหมู่ที่ลูกค้าได้รับ แล้วแปลงเป็นจำนวนเงิน
		if($product_discount >= $customer_discount){ // ถ้าส่วนลดจากสินค้า "มากกว่า หรือ เท่ากับ " ส่วนลด
			if($product_discount == 0){
					$this->product_discount = "0"; //เอาไปแสดงผล
					$this->discount_type = ""; // เอาไว้คำนวณ
					$this->product_discount1 = 0; //เอาไว้ตรวจสอบเงื่อนไข
				}else{
					if($discount_type=="percentage"){
							$this->product_discount = "$discount %"; //เอาไปแสดงผล
							$this->product_discount1 = $discount; // เอาไว้คำนวณ
							$this->discount_type = "percentage"; //เอาไว้ตรวจสอบเงื่อนไข
						}else{
							$this->product_discount = "$discount ฿";//เอาไปแสดงผล
							$this->product_discount1 = $discount; //เอาไว้คำนวณ
							$this->discount_type = "amount"; //เอาไว้ตรวจสอบเงื่อนไข
					}
			} 
			$this->discount = $product_discount;  //ส่วนลดสุดท้าย
		}else{
			$this->product_discount = $this->get_max_discount($id_product,$id_customer)." %"; //เอาไปแสดงผล
			$this->product_discount1 = $this->get_max_discount($id_product,$id_customer); // เอาไว้คำนวณ
			$this->discount_type = "cus_percentage"; //เอาไว้ตรวจสอบเงื่อนไข
			$this->discount = $customer_discount;  //ส่วนลดสุดท้าย
		}
	return true;
}


public function getProductId($id_product_attribute){
	list($id_product) = dbFetchArray(dbQuery("SELECT id_product FROM tbl_product_attribute WHERE id_product_attribute = 	$id_product_attribute"));
	return $id_product;
}

public function get_size_name($id_size){
	$sql = dbQuery("SELECT size_name FROM tbl_size WHERE id_size = $id_size");
	list($size_name) = dbFetchArray($sql);
	return $size_name;
}

public function get_color_name($id_color){
	$sql = dbQuery("SELECT color_name FROM tbl_color WHERE id_color = $id_color");
	list($color_name) = dbFetchArray($sql);
	return $color_name;
}

public function get_color_code($id_color){
	$sql = dbQuery("SELECT color_code FROM tbl_color WHERE id_color = $id_color");
	list($color_code) = dbFetchArray($sql);
	return $color_code;
}

public function get_attribute_name($id_attribute){
	$sql = dbQuery("SELECT attribute_name FROM tbl_attribute WHERE id_attribute = $id_attribute");
	list($attribute_name) = dbFetchArray($sql);
	return $attribute_name;
}

public function get_pack($id_product_attribute){
	$sql = dbQuery("SELECT qty, barcode_pack FROM tbl_product_pack WHERE id_product_attribute = $id_product_attribute");
	list($qty, $barcode) = dbFetchArray($sql);
	return $arr = array("qty"=>$qty, "barcode"=>$barcode);
}

public function get_id_image_attribute($id_product_attribute){
	$sql = dbQuery("SELECT id_image FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute");
	list($id_image) = dbFetchArray($sql);
	return $id_image;
}
	
public function getCategoryId($id_product)
{
	$id_category = array();
	$sql = dbQuery("SELECT tbl_category.id_category FROM tbl_category LEFT JOIN tbl_category_product ON tbl_category_product.id_category = tbl_category.id_category WHERE id_product = $id_product");
	$row = dbNumRows($sql);
	$i = 0;
	while($i<$row){
		list($id) = dbFetchArray($sql);
		array_push($id_category, $id);
		$i++;
	}
	return $id_category;
}


public function productDiscount($id_product_attribute, $id_customer)
{
	$id_category = $this->getCategoryId($this->getProductId($id_product_attribute));
	list($discount) = dbFetchArray(dbQuery("SELECT discount FROM tbl_customer_discount WHERE id_customer = $id_customer AND id_category = $id_category"));
	return $discount;
}


public function get_max_discount($id_product, $id_customer){
	$id_category = $this->getCategoryId($id_product);
	$discount = array(0);
	foreach($id_category as $id){
		list($disc) = dbFetchArray(dbQuery("SELECT discount FROM tbl_customer_discount WHERE id_customer = '$id_customer' AND id_category = $id"));
		array_push($discount,$disc); 
	}
	$result = max($discount);
	return $result;
}


public function orderQty($id_product_attribute){
	$sql = dbQuery("select sum(product_qty) from tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order where id_product_attribute = '$id_product_attribute' and valid_detail = '0' and current_state NOT IN(6,7,8,9)");
	list($order_qty) = dbFetchArray($sql);
	return $order_qty;
}


public function available_qty($id_product_attribute="", $id_warehouse =''){
	if($id_product_attribute ==""){	$id_product_attribute = $this->id_product_attribute; }
	if($id_warehouse =="" ){ 
		list($qty) = dbFetchArray(dbQuery("select SUM(qty) from stock_qty where id_product_attribute = $id_product_attribute"));
		}else{
		list($qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_stock LEFT JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE id_product_attribute = $id_product_attribute AND tbl_zone.id_warehouse = $id_warehouse GROUP BY id_product_attribute"));
	}
	return $qty;
}
public function order_qty(){
	$id_product_attribute = $this->id_product_attribute;
	list($qty) = dbFetchArray(dbQuery("SELECT SUM(product_qty) FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE id_product_attribute = $id_product_attribute AND valid_detail = 0 AND current_state NOT IN(6,7,8,9)"));
	return $qty;
	}
	
	
public function get_id_product_attribute_by_barcode($barcode){
	$sql = dbQuery("SELECT tbl_product_attribute.id_product_attribute FROM tbl_product_attribute LEFT JOIN tbl_product_pack ON tbl_product_attribute.id_product_attribute = tbl_product_pack.id_product_attribute WHERE tbl_product_attribute.barcode ='$barcode' OR tbl_product_pack.barcode_pack ='$barcode' ");
		list($id_product_attribute) = dbFetchArray($sql);
		return $id_product_attribute;
	}
	
	
public function check_barcode($barcode){
		$sql = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE barcode ='$barcode'");
		$row = dbNumRows($sql);
		if($row>0){
			list($id_product_attribute) = dbFetchArray($sql);
			$qty = 1;
			}else{
			$sqr = dbQuery("SELECT id_product_attribute, qty FROM tbl_product_pack WHERE barcode_pack = '$barcode'");
			list($id_product_attribute, $qty) = dbFetchArray($sqr);
		}
		$arr = array('id_product_attribute'=>$id_product_attribute, 'qty'=>$qty);
		return $arr;
}

//********************  ส่งกลับ ยอดรวมของราคาทุนสินค้าแต่ละ Style **********************//
public function get_current_stock($id_product){
	$sql = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = $id_product");
	$row = dbNumRows($sql);
	$total_cost = 0;
	$total_qty = 0;
	$i =0;
	while($i<$row){
		list($id_product_attribute) = dbFetchArray($sql);
		list($cost) = dbFetchArray(dbQuery("SELECT cost FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute"));
		list($qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_stock WHERE id_product_attribute = $id_product_attribute"));
		$stock_moveing = stock_moveing($id_product_attribute); // ได้ค่ากลับมาเป็นจำนวนสินค้าที่กำลังถูกย้ายอยู่ (ไม่อยู่ในสต็อก)
		$qty += $stock_moveing;
		$cost_amount = $cost*$qty;
		$total_qty += $qty;
		$total_cost += $cost_amount;
		$i++;
	}
	$product['total_qty'] = $total_qty;
	$product['total_cost'] = $total_cost;
	return $product;
}

public function getCoverImage($id_productx,$use_size='',$class=''){
	if($class !=""){ $class_name = "class='".$class."'";}else{ $class_name ="";}
	$sql=dbQuery("SELECT * FROM tbl_image WHERE id_product =$id_productx AND cover=1");	
	$row = dbNumRows($sql);
	if($row==1){
			$list = dbFetchArray($sql);
			list($id_image, $id_product, $position, $cover) = $list;
			$count = strlen($id_image);
			$path = str_split($id_image);
			$image_path = WEB_ROOT."img/product";
			$n=0;
			while($n<$count){
				$image_path .= "/".$path[$n];
				$n++;
			}
			if($use_size != ""){
				switch($use_size){
					case "1" :
						$pre_fix = "product_mini_";
						$no_image = "no_image_mini";
						break;
					case "2" :
						$pre_fix = "product_default_";
						$no_image = "no_image_default";
						break;
					case "3" :
						$pre_fix = "product_medium_";
						$no_image = "no_image_medium";
						break;
					case "4" :
						$pre_fix = "product_lage_";
						$no_image = "no_image_lage";
						break;
					default :
						$pre_fix = "";
						$no_image = "no_image_mini";
						break;
				}
			}else{
				$pre_fix = "product_mini_";
			}
			$image_path .= "/".$pre_fix.$id_image.".jpg";
			return"<img ".$class_name."  src='$image_path' />";	
	}else{
		if($use_size != ""){
			switch($use_size){
				case "1" :
					$pre_fix = "product_mini_";
					$no_image = "no_image_mini";
					break;
				case "2" :
					$pre_fix = "product_default_";
					$no_image = "no_image_default";
					break;
				case "3" :
					$pre_fix = "product_medium_";
					$no_image = "no_image_medium";
					break;
				case "4" :
					$pre_fix = "product_lage_";
					$no_image = "no_image_lage";
					break;
				default :
					$pre_fix = "";
					$no_image = "no_image_mini";
					break;
			}
		}
		return "<img  ".$class_name." src='".WEB_ROOT."img/product/".$no_image.".jpg' />";
	}
}	


public function get_image_path($id_image,$use_size){
			$count = strlen($id_image);
			$path = str_split($id_image);
			$image_path = WEB_ROOT."img/product";
			$n=0;
					while($n<$count){
						$image_path .= "/".$path[$n];
						$n++;
					}
				$image_path .= "/";
				$image_path_name ="";
					switch($use_size){
						case "1" :
							$pre_fix = "product_mini_";
							$no_image = "no_image_mini";
							break;
						case "2" :
							$pre_fix = "product_default_";
							$no_image = "no_image_default";
							break;
						case "3" :
							$pre_fix = "product_medium_";
							$no_image = "no_image_medium";
							break;
						case "4" :
							$pre_fix = "product_lage_";
							$no_image = "no_image_lage";
							break;
						default :
							$pre_fix = "";
							$no_image = "no_image_mini";
							break;
					}	
					if($n == "0"){	
						$image_path_name = $image_path.$no_image.".jpg";
					}else{
						$image_path_name = $image_path.$pre_fix.$id_image.".jpg";
					}
		return $image_path_name;
}


public function get_product_attribute_image($id_product_attribute,$use_size){
		list($id_image) = dbFetchArray(dbQuery("SELECT id_image FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute"));
		list($id_product) = dbFetchArray(dbQuery("SELECT id_product FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute"));
		list($id_image_cover) = dbFetchArray(dbQuery("SELECT id_image FROM tbl_image WHERE id_product = $id_product AND cover = 1"));
		if($id_image !=""){
			$image = $this->get_image_path($id_image,$use_size);
		}else{
			$image = $this->get_image_path($id_image_cover,$use_size);
		}	
		return $image;
}


public function showImage($id_productx,$use_size){
		$sql=dbQuery("SELECT * FROM tbl_image WHERE id_product =$id_productx ORDER BY position ASC");
		$row = dbNumRows($sql);
		$i=0;
		$image_product = "";
		if($row>0){
				while($i<$row){
					$list = dbFetchArray($sql);
					list($id_image, $id_product, $position, $cover) = $list;
					$count = strlen($id_image);
					$path = str_split($id_image);
					$image_path = WEB_ROOT."img/product";
					
					$n=0;
						while($n<$count){
							$image_path .= "/".$path[$n];
							$n++;
						}
					if($use_size != ""){
					switch($use_size){
						case "1" :
							$pre_fix = "product_mini_";
							$no_image = "no_image_mini";
							break;
						case "2" :
							$pre_fix = "product_default_";
							$no_image = "no_image_default";
							break;
						case "3" :
							$pre_fix = "product_medium_";
							$no_image = "no_image_medium";
							break;
						case "4" :
							$pre_fix = "product_lage_";
							$no_image = "no_image_lage";
							break;
						default :
							$pre_fix = "";
							$no_image = "no_image_mini";
							break;
					}
				}
					$image_path .= "/";
					$image_path .= $pre_fix.$id_image.".jpg";
					$image_product .="<a href='$image_path'><img src='$image_path' class='img-responsive' alt='img'></a> ";
					$i++;
				}
		}else{
			$image_path = WEB_ROOT."img/product";
			if($use_size !=""){
				switch($use_size){
					case "1" :
						$pre_fix = "no_image_mini";
						break;
					case "2" :
						$pre_fix = "no_image_default";
						break;
					case "3" :
						$pre_fix = "no_image_medium";
						break;
					case "4" :
						$pre_fix = "no_image_lage";
						break;
					default :
						$pre_fix = "no_image_lage";
						break;
				}		
			}
			$image_path .= "/";
			$image_path .= $pre_fix.".jpg";
			$image_product .="<a href='$image_path'><img src='$image_path' class='img-responsive' alt='img'></a> ";
		}
		
		$this->image_product = $image_product;
	}

public function maxShowstock(){
	list($value) = dbFetchArray(dbQuery("select value from tbl_config where id_config = 9"));
	return $value;
}


public function attributeGrid($id_product){
		$maxss = $this->maxShowstock();
		if($maxss == "0"){
			$max = 10000000000;
		}else if($maxss = ""){
			$max = 10000000000;
		}else{
			$max = $this->maxShowstock();
		}
		$vertical = getConfig('ATTRIBUTE_GRID_VERTICAL');
		$vertical_name = "".$vertical."_name";
		$horizontal = getConfig('ATTRIBUTE_GRID_HORIZONTAL');
		$horizontal_name = "".$horizontal."_name";
		$additional = getConfig('ATTRIBUTE_GRID_ADDITIONAL');
		$additional_name = "".$additional."_name";
		list($id_color) = dbFetchArray(dbQuery("select id_color from tbl_product_attribute where id_product = '$id_product' and id_color != '0'"));
		list($id_size) = dbFetchArray(dbQuery("select id_size from tbl_product_attribute where id_product = '$id_product' and id_size != '0'"));
		list($id_attribute) = dbFetchArray(dbQuery("select id_attribute from tbl_product_attribute where id_product = '$id_product' and id_attribute != '0'")); 
		//--------------------------------- มี 3 อัน -------------------------------------------------//
		if($id_color != "" && $id_size != "" && $id_attribute != ""){
		$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
		$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
		if($additional == "color"){
			$color_code = ",color_code";
		}else{$color_code = "";}
		$list = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name $color_code from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals = dbNumRows($list);
			$rows = dbNumRows($rows1);
			$colums = dbNumRows($colum);
			$tl = 0;
			echo "<ul class='nav nav-tabs'>";
			while($tl<$additionals){
				if($tl == "0"){
					$active = "active";
				}else{
					$active = "";
				}
					$additionl = dbFetchArray($list);
					$addition_namel = $additionl["$additional_name"];
					if($additional == "color"){
						$add_code = $additionl["color_code"];
					}
			echo "
          <li class='$active'><a href='#Tab".($tl+1)."' data-toggle='tab'>$addition_namel";if($additional == "color"){ echo "&nbsp;:&nbsp;$add_code";}echo "</a></li>
        ";
		$tl++;
			}echo "</ul>";	
			$list1 = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals1 = dbNumRows($list1);
			$t = 0;
			while($t<$additionals1){
					$addition = dbFetchArray($list1);
					$addition_name = $addition["$additional_name"];
					$addition_id = $addition["id_$additional"];
			
		if($t == "0"){
        	echo "<div class='tab-content'>";
			$active = "active";
		}else{
			$active = "";
		}
		echo "<div class='tab-pane $active' id='Tab".($t+1)."'>";
			$width = $colums * 70;
			echo "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px vertical-align:middle;'><b>#</b></td>";
			$colum1 = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
			$colums1 = dbNumRows($colum1);
								$n = 0;
								$table_td = "";
								while($n<$colums1){
									$cols = dbFetchArray($colum1);
									$cols_name = $cols["$horizontal_name"];
									if($horizontal == "color"){
										$cols_code = $cols["color_code"];
									}
									echo "<td width='70px' align='center' style=' vertical-alignment:middle;'><b>$cols_name</b>";if($horizontal == "color"){echo "<br><b>$cols_code</b>";}echo "</td>";
									$n++;
								}
			echo "</tr>";
			$rows11 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
			$rows1 = dbNumRows($rows11);
			$i = 0 ;
			$detail1 = "";
			$detail3 = "";
			while($i < $rows1){
				$row = dbFetchArray($rows11);
				$row_name = $row["$vertical_name"];
				if($vertical == "color"){
					$row_code = $row["color_code"];
				}
				$row_id = $row["id_$vertical"];
				echo "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){echo "<br><b>$row_code</b>";}echo "</td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
				$colums2 = dbNumRows($col);
				while($l < $colums2){
					$colum2 = dbFetchArray($col);
					$col_id = $colum2["id_$horizontal"];
					$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')");
					list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
					$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
					$sumorder_qty = $this->orderQty($id_product_attribute);
					$qty = $qty_in-$sumorder_qty;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
						$disabled  = "disabled='disabled'";
						$qtymax = "";
					}else if($qty <= $max){
						$qtyshow = "$qty ";
						$disabled = "";
						$qtymax = $qty;
					}else{ 
						$qtyshow ="$max ";
						$disabled = "";
						$qtymax = $max;
					}
						@$m++;
								echo "<td width='100px' align='center'><div>"; echo "<input type='text' name='number".$n."' id='number".$n."' class='form-control' $disabled />
										<input type='hidden' name='qty".$n."' id='qty".$n."' value='$qtymax' >
										<input type='hidden' name='id_product_attribute".$id_product."".$m."' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute'  $disabled ></div><div>$qtyshow </div></td>";
										if(allow_under_zero() == false ){
											?>
								<script>
									$(document).ready(function(){
									$('#number<?php echo $n;?>').keyup(function(){		
									var n_order = $('#number<?php echo $n;?>').val();
									var n_stock = $('#qty<?php echo $n;?>').val();
									if(parseInt(n_order)>parseInt(n_stock)){
										alert('มีสินค้าในสต็อกแค่'+ n_stock);
										$('#number<?php echo $n;?>').val(n_stock);
										}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
										});
									}); 
								</script>
								<?php }?>
								<script>
								$(document).ready(function(){
									$('#number<?php echo $n;?>').keyup(function(){
									if(isNaN($('#number<?php echo $n;?>').val()))
										 {
											alert('ใส่ได้แต่ตัวเลขเท่านั้น');
											$('#number<?php echo $n;?>').val('');
											return false;
										 }
										 }); 
									}); 
								</script>
								<?php
				
				$l++;
				}
				echo "$detail1$detail2</tr>";
				$i++;
	
			}
			echo "</table></div>";
			$t++;
			}echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			echo "</div>";
			
			//--------------------------------มีไซต์มีสีไม่มีอื่นๆ----------------------------------->
		}else if($id_color != "" && $id_size != "" && $id_attribute == ""){
		$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
		$color = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
		$colums = dbNumRows($color);
			$rows = dbNumRows($size);
			$width = $colums * 70;
			echo "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
								$n = 0;
								$table_td = "";
								while($n<$colums){
									$cols = dbFetchArray($color);
									$color_name = $cols['color_name'];
									$color_code = $cols['color_code'];
									echo "<td width='70px' align='center' ><b>$color_code<br>$color_name</b></td>";
									$n++;
								}
			echo "</tr>";
			$i = 0 ;
			$m = "";
			$detail1 = "";
			$detail3 = "";
			while($i < $rows){
				$row = dbFetchArray($size);
				$size_name = $row['size_name'];
				$size_id = $row['id_size'];
				echo "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$size_name</b></td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_color from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
				while($l < $colums){
					$colum = dbFetchArray($col);
					$color_id = $colum['id_color'];
					$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')");
					list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
					$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
					$sumorder_qty = $this->orderQty($id_product_attribute);
					$qty = $qty_in-$sumorder_qty;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
						$disabled  = "disabled='disabled'";
						$qtymax = "";
					}else if($qty <= $max){
						$qtyshow = "$qty";
						$disabled = "";
						$qtymax = $qty;
					}else{ 
						$qtyshow ="$max";
						$disabled = "";
						$qtymax = $max;
					}
						$m++;
								echo "<td width='100px' align='center'><div>"; echo "<input type='text' name='number".$n."' id='number".$n."' class='form-control' $disabled />
										<input type='hidden' name='qty".$n."' id='qty".$n."' value='$qtymax' >
										<input type='hidden' name='id_product_attribute".$id_product."".$m."' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute'  $disabled ></div><div>$qtyshow</div></td>";
										if(allow_under_zero() == false ){
											?>
								<script>
									$(document).ready(function(){
									$('#number<?php echo $n;?>').keyup(function(){
										
									var n_order = $('#number<?php echo $n;?>').val();
									var n_stock = $('#qty<?php echo $n;?>').val();
									if(parseInt(n_order)>parseInt(n_stock)){
										alert('มีสินค้าในสต็อกแค่'+ n_stock);
										$('#number<?php echo $n;?>').val(n_stock);
										}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
										});
									}); 
								</script>
								<?php }?>
								<script>
								$(document).ready(function(){
									$('#number<?php echo $n;?>').keyup(function(){
									if(isNaN($('#number<?php echo $n;?>').val()))
										 {
											alert('ใส่ได้แต่ตัวเลขเท่านั้น');
											$('#number<?php echo $n;?>').val('');
											return false;
										 }
										 }); 
									}); 
								</script>
								<?php
				
				$l++;
				}
				echo "$detail1$detail2</tr>";
				$i++;
			}
			echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			echo "</table>";
			//------------------------------------มีสีกับอื่นๆ---------------------------------------------//
			}else{
				 if($id_color != "" && $id_size == "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$color = dbQuery("select tbl_product_attribute.id_color, color_name from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$attribute_row = dbNumRows($attribute);
					$color_row = dbNumRows($color);
					if($attribute_row >= "$color_row"){
						$horizontal = "color";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "color";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					
					$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					echo "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											if($horizontal == "color"){
												$cols_code = $cols["color_code"];
											}
											echo "<td width='70px' align='center'><b>$cols_name</b>";if($horizontal == "color"){echo "<br><b>$cols_code</b>";}echo "</td>";
											$n++;
										}
					echo "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						if($vertical == "color"){
							$row_code = $row["color_code"];
						}
						$row_id = $row["id_$vertical"];
						echo "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){echo "<br><b>$row_code</b>";}echo "</td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								$m++;
										echo "<td width='100px' align='center'><div>"; echo "<input type='text' name='number".$n."' id='number".$n."' class='form-control' $disabled />
												<input type='hidden' name='qty".$n."' id='qty".$n."' value='$qtymax' >
												<input type='hidden' name='id_product_attribute".$id_product."".$m."' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute'  $disabled ></div><div>$qtyshow</div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
						
						$l++;
						}
						echo "$detail1$detail2</tr>";
						$i++;
			
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					
					//---------------------------------มีไซต์กับอื่นๆ--------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$attribute_row = dbNumRows($attribute);
					$size_row = dbNumRows($size);
					if($attribute_row >= "$size_row"){
						$horizontal = "size";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "size";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select tbl_product_attribute.id_$vertical, $vertical_name from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select tbl_product_attribute.id_$horizontal, $horizontal_name from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					echo "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											echo "<td width='70px' align='center'><b>$cols_name</b></td>";
											$n++;
										}
					echo "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						$row_id = $row["id_$vertical"];
						echo "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b></td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								$m++;
										echo "<td width='100px' align='center'><div>"; echo "<input type='text' name='number".$n."' id='number".$n."' class='form-control' $disabled />
												<input type='hidden' name='qty".$n."' id='qty".$n."' value='$qtymax' >
												<input type='hidden' name='id_product_attribute".$id_product."".$m."' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute'  $disabled ></div><div>$qtyshow</div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
						
						$l++;
						}
						echo "$detail1$detail2</tr>";
						$i++;
			
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					//-----------------------------------สีอย่างเดียว---------------------------------------//
				}else if($id_color != "" && $id_size == "" && $id_attribute == ""){
					$horizontal = "size";
					$vertical = "color";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_color, color_name,color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$color_name = $re['color_name'];
						$id_color = $re['id_color'];
						$color_code = $re['color_code'];
						$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and id_color = $id_color"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_color = $id_color");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td width='180px'  style='vertical-align:middle;' align='right'>$color_name&nbsp;:&nbsp;$color_code&nbsp; </td><td width='100px' align='center'><div>"; echo "<input type='text' name='number".$n."' id='number".$n."' class='form-control' $disabled />$qtyshow
												<input type='hidden' name='qty".$n."' id='qty".$n."' value='$qtymax' >
												<input type='hidden' name='id_product_attribute".$id_product."".$m."' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute'  $disabled ></div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";			
					//---------------------------------ไซต์อยา่งเดียว-----------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute == ""){
					$horizontal = "color";
					$vertical = "size";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$size_name = $re['size_name'];
						$id_size = $re['id_size'];
						$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and id_size = $id_size"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_size = $id_size");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td width='180px'  style='vertical-align:middle;' align='right'>$size_name&nbsp;&nbsp;&nbsp; </td><td width='100px' align='center'><div>"; echo "<input type='text' name='number".$n."' id='number".$n."' class='form-control' $disabled />$qtyshow
												<input type='hidden' name='qty".$n."' id='qty".$n."' value='$qtymax' >
												<input type='hidden' name='id_product_attribute".$id_product."".$m."' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute'  $disabled ></div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					//-------------------------------------------------อื่นๆอย่างเดียว--------------------------------------------//
				}else if($id_color == "" && $id_size == "" && $id_attribute != ""){
					$horizontal = "color";
					$vertical = "attribute";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$attribute_name = $re['attribute_name'];
						$id_attribute = $re['id_attribute'];
						$quantity = dbFetchArray(dbQuery("select id_product_attribute, sum(qty) as qty from stock_qty where id_product = '$id_product' and id_attribute = $id_attribute"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_attribute = $id_attribute");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td width='180px'  style='vertical-align:middle;' align='right'>$attribute_name&nbsp;&nbsp;&nbsp; </td><td width='100px' align='center'><div>"; echo "<input type='text' name='number".$n."' id='number".$n."' class='form-control' $disabled />$qtyshow
												<input type='hidden' name='qty".$n."' id='qty".$n."' value='$qtymax' >
												<input type='hidden' name='id_product_attribute".$id_product."".$m."' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute'  $disabled ></div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
				}	
					
		}
		/*
		/**/
		
		
		if(isset($_COOKIE['id_cart'])){
			$id_cart = $_COOKIE['id_cart'];
		}else{
			$id_cart = "";
		}
		echo "<input type='hidden' id='id_cart' value='$id_cart' >";	
		}


	
/// attribute หลังบ้าน //
	public function order_attribute_grid($id_product){
		$result = "";
		$maxss = $this->maxShowstock();
		if($maxss == "0"){
			$max = 10000000000;
		}else if($maxss = ""){
			$max = 10000000000;
		}else{
			$max = 1000000000;
		}
		$vertical = getConfig('ATTRIBUTE_GRID_VERTICAL');
		$vertical_name = "".$vertical."_name";
		$horizontal = getConfig('ATTRIBUTE_GRID_HORIZONTAL');
		$horizontal_name = "".$horizontal."_name";
		$additional = getConfig('ATTRIBUTE_GRID_ADDITIONAL');
		$additional_name = "".$additional."_name";
		
		list($id_color) = dbFetchArray(dbQuery("select id_color from tbl_product_attribute where id_product = '$id_product' and id_color != '0'"));
		list($id_size) = dbFetchArray(dbQuery("select id_size from tbl_product_attribute where id_product = '$id_product' and id_size != '0'"));
		list($id_attribute) = dbFetchArray(dbQuery("select id_attribute from tbl_product_attribute where id_product = '$id_product' and id_attribute != '0'")); 
		//---------------------------------มี 3 อัน-------------------------------------------------//
		if($id_color != "" && $id_size != "" && $id_attribute != ""){
		$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
		$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
		if($additional == "color_code"){
			$color_code = ",color_code";
		}else{
			$color_code = "";
		}
		$list = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name $color_code from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals = dbNumRows($list);
			$rows = dbNumRows($rows1);
			$colums = dbNumRows($colum);
			$tl = 0;
			$result .= "<ul class='nav nav-tabs'>";
			while($tl<$additionals){
				if($tl == "0"){
					$active = "active";
				}else{
					$active = "";
				}
					$additionl = dbFetchArray($list);
					$addition_namel = $additionl["$additional_name"];
					if($additional == "color_code"){
						$addition_code = $additionl["color_code"];
					}
			$result .= "
          <li class='$active'><a href='#Tab".($tl+1)."' data-toggle='tab'>$addition_namel";if($additional == "color"){$result .= "&nbsp;:&nbsp;$addition_code";}$result .= "</a></li>
        ";
		$tl++;
			}$result .= "</ul>";	
			$list1 = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals1 = dbNumRows($list1);
			$t = 0;
			while($t<$additionals1){
					$addition = dbFetchArray($list1);
					$addition_name = $addition["$additional_name"];
					$addition_id = $addition["id_$additional"];
			
		if($t == "0"){
        	$result .= "<div class='tab-content'>";
			$active = "active";
		}else{
			$active = "";
		}
		$result .= "<div class='tab-pane $active' id='Tab".($t+1)."'>";
			$width = $colums * 70;
			$result .= "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px vertical-align:middle;'><b>#</b></td>";
			$colum1 = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
			$colums1 = dbNumRows($colum1);
								$n = 0;
								$table_td = "";
								while($n<$colums1){
									$cols = dbFetchArray($colum1);
									$cols_name = $cols["$horizontal_name"];
									if($horizontal == "color"){
										$cols_code = $cols["color_code"];
									}
									$result .= "<td width='70px' align='center' style=' vertical-alignment:middle;'><b>$cols_name</b>";if($horizontal == "color"){$result .= "<br><b>$cols_code</b>";}$result .= "</td>";
									$n++;
								}
			$result .= "</tr>";
			$rows11 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
			$rows1 = dbNumRows($rows11);
			$i = 0 ;
			$detail1 = "";
			$detail3 = "";
			while($i < $rows1){
				$row = dbFetchArray($rows11);
				$row_name = $row["$vertical_name"];
				if($vertical == "color"){
					$row_code = $row["color_code"];
				}
				$row_id = $row["id_$vertical"];
				$result .= "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){$result .= "<br><b>$row_code</b>";}$result .= "</td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
				$colums2 = dbNumRows($col);
				while($l < $colums2){
					$colum2 = dbFetchArray($col);
					$col_id = $colum2["id_$horizontal"];
					$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')");
					list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
					$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
					$sumorder_qty = $this->orderQty($id_product_attribute);
					$qty = $qty_in-$sumorder_qty;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
						$disabled  = "disabled='disabled'";
						$qtymax = "";
					}else if($qty <= $max){
						$qtyshow = "$qty";
						$disabled = "";
						$qtymax = $qty;
					}else{ 
						$qtyshow ="$max";
						$disabled = "";
						$qtymax = $max;
					}
						@$m++;
								$result .= "<td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />
									<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div><div>$qtyshow</div></td>";
										if(allow_under_zero() == false ){
											$result .="
								<script>
									$(document).ready(function(){
									$('#number$n').keyup(function(){
										
									var n_order = $('#number$n').val();
									var n_stock = $('#qty$n').val();
									if(parseInt(n_order)>parseInt(n_stock)){
										alert('มีสินค้าในสต็อกแค่'+ n_stock);
										$('#number$n').val(n_stock);
										}
										});
									}); 
								</script>";
								 } 
								 $result .="
								<script>
								$(document).ready(function(){
									$('#number$n').keyup(function(){
									if(isNaN($('#number$n').val()))
										 {
											alert('ใส่ได้แต่ตัวเลขเท่านั้น');
											$('#number$n').val('');
											return false;
										 }
										 }); 
									}); 
								</script>";
								
				
				$l++;
				}
				$result .= "$detail1$detail2</tr>";
				$i++;
	
			}
			$result .= "</table></div>";
			$t++;
			}$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			$result .= "</div>";
			
			//--------------------------------มีไซต์มีสีไม่มีอื่นๆ----------------------------------->
		}else if($id_color != "" && $id_size != "" && $id_attribute == ""){
		$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
		$color = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
		$colums = dbNumRows($color);
			$rows = dbNumRows($size);
			$width = $colums * 70;
			$result .= "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
								$n = 0;
								$table_td = "";
								while($n<$colums){
									$cols = dbFetchArray($color);
									$color_name = $cols['color_name'];
									$color_code = $cols['color_code'];
									$result .= "<td width='70px' align='center' ><b>$color_code<br>$color_name</b></td>";
									$n++;
								}
			$result .= "</tr>";
			$i = 0 ;
			$m = "";
			$detail1 = "";
			$detail3 = "";
			while($i < $rows){
				$row = dbFetchArray($size);
				$size_name = $row['size_name'];
				$size_id = $row['id_size'];
				$result .= "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$size_name</b></td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_color from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
				while($l < $colums){
					$colum = dbFetchArray($col);
					$color_id = $colum['id_color'];
					$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')");
					list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
					$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
					$sumorder_qty = $this->orderQty($id_product_attribute);
					$qty = $qty_in-$sumorder_qty;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
						$disabled  = "disabled='disabled'";
						$qtymax = "";
					}else if($qty <= $max){
						$qtyshow = "$qty";
						$disabled = "";
						$qtymax = $qty;
					}else{ 
						$qtyshow ="$max";
						$disabled = "";
						$qtymax = $max;
					}
						$m++;
								$result .= "<td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />
									<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div><div>$qtyshow</div></td>";
										if(allow_under_zero() == false ){
											$result .="
								<script>
									$(document).ready(function(){
									$('#number$n').keyup(function(){
										
									var n_order = $('#number$n').val();
									var n_stock = $('#qty$n').val();
									if(parseInt(n_order)>parseInt(n_stock)){
										alert('มีสินค้าในสต็อกแค่'+ n_stock);
										$('#number$n').val(n_stock);
										}
										});
									}); 

								</script>";
								 }
								 $result .="
								<script>
								$(document).ready(function(){
									$('#number$n').keyup(function(){
									if(isNaN($('#number$n').val()))
										 {
											alert('ใส่ได้แต่ตัวเลขเท่านั้น');
											$('#number$n').val('');
											return false;
										 }
										 }); 
									}); 
								</script>";
				
				$l++;
				}
				$result .= "$detail1$detail2</tr>";
				$i++;
			}
			$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			$result .= "</table>";
			//------------------------------------มีสีกับอื่นๆ---------------------------------------------//
			}else{
				 if($id_color != "" && $id_size == "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$color = dbQuery("select tbl_product_attribute.id_color, color_name from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$attribute_row = dbNumRows($attribute);
					$color_row = dbNumRows($color);
					if($attribute_row >= "$color_row"){
						$horizontal = "color";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "color";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					$result .= "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											if($horizontal == "color"){
												$cols_code = $cols['color_code'];
											}
											$result .= "<td width='70px' align='center'><b>$cols_name</b>";if($horizontal == "color"){$result .= "<br><b>$cols_code</b>";}$result .= "</td>";
											$n++;
										}
					$result .= "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						if($vertical == "color"){
							$row_code = $row["color_code"];
						}
						$row_id = $row["id_$vertical"];
						$result .= "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){$result .= "<br><b>$row_code</b>";}$result .= "</td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								$m++;
										$result .= "<td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />
									<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div><div>$qtyshow</div></td>";
												if(allow_under_zero() == false ){
													$result .="
										<script>
											$(document).ready(function(){
											$('#number$n').keyup(function(){
											var n_order = $('#number$n').val();
											var n_stock = $('#qty$n').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number$n').val(n_stock);
												}
												});
											}); 
										</script>";
										 }
										 $result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
						
						$l++;
						}
						$result .= "$detail1$detail2</tr>";
						$i++;
			
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
					//-----------------------------------มีไซย์กับอื่นๆ---------------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$attribute_row = dbNumRows($attribute);
					$size_row = dbNumRows($size);
					if($attribute_row >= "$size_row"){
						$horizontal = "size";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "size";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select tbl_product_attribute.id_$vertical, $vertical_name from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select tbl_product_attribute.id_$horizontal, $horizontal_name from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					$result .= "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											$result .= "<td width='70px' align='center'><b>$cols_name</b></td>";
											$n++;
										}
					$result .= "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						$row_id = $row["id_$vertical"];
						$result .= "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b></td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								$m++;
										$result .= "<td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />
									<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div><div>$qtyshow</div></td>";
												if(allow_under_zero() == false ){
												$result .="
										<script>
											$(document).ready(function(){
											$('#number$n').keyup(function(){
												
											var n_order = $('#number$n').val();
											var n_stock = $('#qty$n').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number$n').val(n_stock);
												}
												});
											}); 
										</scrip>"; }
										$result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
						$l++;
						}
						$result .= "$detail1$detail2</tr>";
						$i++;
			
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
					///------------------- สีอย่างเดียว---------------------//
				}else if($id_color != "" && $id_size == "" && $id_attribute == ""){
					$horizontal = "size";
					$vertical = "color";
					$result .= "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$color_name = $re['color_name'];
						$id_color = $re['id_color'];
						$color_code = $re['color_code'];
						$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and id_color = $id_color"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_color = $id_color");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									$result .= "<tr>";
								}
										$result .= "<td width='180px'  style='vertical-align:middle;' align='right'>$color_name&nbsp;&nbsp;$color_code&nbsp; </td><td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />$qtyshow
												<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled></div></td>";
												if(allow_under_zero() == false ){
													$result .="
										<script>
											$(document).ready(function(){
											$('#number$n').keyup(function(){
												
											var n_order = $('#number$n').val();
											var n_stock = $('#qty$n').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number$n').val(n_stock);
												}
												});
											}); 
										</script>"; }
										$result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
										if(($i+1)%4 == "0"){
										$result .= "</tr>";
										}
										$i++;
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
			
					///------------------------ไซด์อย่างเดียว ------------------
				}else if($id_color == "" && $id_size != "" && $id_attribute == ""){
					$horizontal = "color";
					$vertical = "size";
					$result .= "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_color.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$size_name = $re['size_name'];
						$id_size = $re['id_size'];
						$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and id_size = $id_size"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_size = $id_size");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									$result .= "<tr>";
								}
										$result .= "<td width='180px'  style='vertical-align:middle;' align='right'>$size_name&nbsp;&nbsp;&nbsp; </td><td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />$qtyshow
												<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled></div></td>";
												if(allow_under_zero() == false ){
												$result .="
										<script>
											$(document).ready(function(){
											$('#number$n').keyup(function(){		
											var n_order = $('#number$n').val();
											var n_stock = $('#qty$n').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number$n').val(n_stock);
												}
												});
											}); 
										</script>"; }
										$result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
									
										if(($i+1)%4 == "0"){
										$result .= "</tr>";
										}
										$i++;
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
					//----------------------------มีอื่นๆอย่างเดียว-----------------------------------//
				}else if($id_color == "" && $id_size == "" && $id_attribute != ""){
					$horizontal = "color";
					$vertical = "attribute";
					$result .= "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$attribute_name = $re['attribute_name'];
						$id_attribute = $re['id_attribute'];
						$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and id_attribute = $id_attribute"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_attribute = $id_attribute");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									$result .= "<tr>";
								}
										$result .= "<td width='180px'  style='vertical-align:middle;' align='right'>$attribute_name&nbsp;&nbsp;&nbsp; </td><td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />$qtyshow
												<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled></div></td>";
												if(allow_under_zero() == false ){
													$result .="
										<script>
											$(document).ready(function(){
											$('#number$n').keyup(function(){
												
											var n_order = $('#number$n').val();
											var n_stock = $('#qty$n').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number$n').val(n_stock);
												}
												});
											}); 
										</script> "; }
										$result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
										
										if(($i+1)%4 == "0"){
										$result .= "</tr>";
										}
										$i++;
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
				}	
					
		}
			if(isset($_COOKIE['id_cart'])){
			$id_cart = $_COOKIE['id_cart'];
		}else{
			$id_cart = "";
		}
		$result .= "<input type='hidden' id='id_cart' value='$id_cart' >";	
		return $result;
		}
//*************************************  Request attribute Grid  *****************************//
	public function request_attribute_grid($id_product){
		$max = 1000000;
		$result = "";
		$vertical = getConfig('ATTRIBUTE_GRID_VERTICAL');
		$vertical_name = "".$vertical."_name";
		$horizontal = getConfig('ATTRIBUTE_GRID_HORIZONTAL');
		$horizontal_name = "".$horizontal."_name";
		$additional = getConfig('ATTRIBUTE_GRID_ADDITIONAL');
		$additional_name = "".$additional."_name";
		
		list($id_color) = dbFetchArray(dbQuery("select id_color from tbl_product_attribute where id_product = '$id_product' and id_color != '0'"));
		list($id_size) = dbFetchArray(dbQuery("select id_size from tbl_product_attribute where id_product = '$id_product' and id_size != '0'"));
		list($id_attribute) = dbFetchArray(dbQuery("select id_attribute from tbl_product_attribute where id_product = '$id_product' and id_attribute != '0'")); 
		//---------------------------------มี 3 อัน-------------------------------------------------//
		if($id_color != "" && $id_size != "" && $id_attribute != ""){
		$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
		$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
		if($additional == "color_code"){
			$color_code = ",color_code";
		}else{
			$color_code = "";
		}
		$list = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name $color_code from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals = dbNumRows($list);
			$rows = dbNumRows($rows1);
			$colums = dbNumRows($colum);
			$tl = 0;
			$result .= "<ul class='nav nav-tabs'>";
			while($tl<$additionals){
				if($tl == "0"){
					$active = "active";
				}else{
					$active = "";
				}
					$additionl = dbFetchArray($list);
					$addition_namel = $additionl["$additional_name"];
					if($additional == "color_code"){
						$addition_code = $additionl["color_code"];
					}
			$result .= "
          <li class='$active'><a href='#Tab".($tl+1)."' data-toggle='tab'>$addition_namel";if($additional == "color"){$result .= "&nbsp;:&nbsp;$addition_code";}$result .= "</a></li>
        ";
		$tl++;
			}$result .= "</ul>";	
			$list1 = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals1 = dbNumRows($list1);
			$t = 0;
			while($t<$additionals1){
					$addition = dbFetchArray($list1);
					$addition_name = $addition["$additional_name"];
					$addition_id = $addition["id_$additional"];
			
		if($t == "0"){
        	$result .= "<div class='tab-content'>";
			$active = "active";
		}else{
			$active = "";
		}
		$result .= "<div class='tab-pane $active' id='Tab".($t+1)."'>";
			$width = $colums * 70;
			$result .= "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px vertical-align:middle;'><b>#</b></td>";
			$colum1 = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
			$colums1 = dbNumRows($colum1);
								$n = 0;
								$table_td = "";
								while($n<$colums1){
									$cols = dbFetchArray($colum1);
									$cols_name = $cols["$horizontal_name"];
									if($horizontal == "color"){
										$cols_code = $cols["color_code"];
									}
									$result .= "<td width='70px' align='center' style=' vertical-alignment:middle;'><b>$cols_name</b>";if($horizontal == "color"){$result .= "<br><b>$cols_code</b>";}$result .= "</td>";
									$n++;
								}
			$result .= "</tr>";
			$rows11 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
			$rows1 = dbNumRows($rows11);
			$i = 0 ;
			$detail1 = "";
			$detail3 = "";
			while($i < $rows1){
				$row = dbFetchArray($rows11);
				$row_name = $row["$vertical_name"];
				if($vertical == "color"){
					$row_code = $row["color_code"];
				}
				$row_id = $row["id_$vertical"];
				$result .= "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){$result .= "<br><b>$row_code</b>";}$result .= "</td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
				$colums2 = dbNumRows($col);
				while($l < $colums2){
					$colum2 = dbFetchArray($col);
					$col_id = $colum2["id_$horizontal"];
			//$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')");
					//list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
					//$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
				//	$sumorder_qty = $this->orderQty($id_product_attribute);
				//	$qty = $qty_in-$sumorder_qty;
					
						@$m++;
								$result .= "<td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off'  />";
									//<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' >
								echo"	<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' ></div></td>";
										
								 $result .="
								<script>
								$(document).ready(function(){
									$('#number$n').keyup(function(){
									if(isNaN($('#number$n').val()))
										 {
											alert('ใส่ได้แต่ตัวเลขเท่านั้น');
											$('#number$n').val('');
											return false;
										 }
										 }); 
									}); 
								</script>";
								
				
				$l++;
				}
				$result .= "$detail1$detail2</tr>";
				$i++;
	
			}
			$result .= "</table></div>";
			$t++;
			}$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			$result .= "</div>";
			
			//--------------------------------มีไซต์มีสีไม่มีอื่นๆ----------------------------------->
		}else if($id_color != "" && $id_size != "" && $id_attribute == ""){
		$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
		$color = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
		$colums = dbNumRows($color);
			$rows = dbNumRows($size);
			$width = $colums * 70;
			$result .= "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
								$n = 0;
								$table_td = "";
								while($n<$colums){
									$cols = dbFetchArray($color);
									$color_name = $cols['color_name'];
									$color_code = $cols['color_code'];
									$result .= "<td width='70px' align='center' ><b>$color_code<br>$color_name</b></td>";
									$n++;
								}
			$result .= "</tr>";
			$i = 0 ;
			$m = "";
			$detail1 = "";
			$detail3 = "";
			while($i < $rows){
				$row = dbFetchArray($size);
				$size_name = $row['size_name'];
				$size_id = $row['id_size'];
				$result .= "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$size_name</b></td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_color from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
				while($l < $colums){
					$colum = dbFetchArray($col);
					$color_id = $colum['id_color'];
					//$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')");
				//	list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
				//	$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
				//	$sumorder_qty = $this->orderQty($id_product_attribute);
				//	$qty = $qty_in-$sumorder_qty;
				
						$m++;
								$result .= "<td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off'  />
									
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' ></div></td>";
										
								 $result .="
								<script>
								$(document).ready(function(){
									$('#number$n').keyup(function(){
									if(isNaN($('#number$n').val()))
										 {
											alert('ใส่ได้แต่ตัวเลขเท่านั้น');
											$('#number$n').val('');
											return false;
										 }
										 }); 
									}); 
								</script>";
				
				$l++;
				}
				$result .= "$detail1$detail2</tr>";
				$i++;
			}
			$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			$result .= "</table>";
			//------------------------------------มีสีกับอื่นๆ---------------------------------------------//
			}else{
				 if($id_color != "" && $id_size == "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$color = dbQuery("select tbl_product_attribute.id_color, color_name from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$attribute_row = dbNumRows($attribute);
					$color_row = dbNumRows($color);
					if($attribute_row >= "$color_row"){
						$horizontal = "color";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "color";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					$result .= "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											if($horizontal == "color"){
												$cols_code = $cols['color_code'];
											}
											$result .= "<td width='70px' align='center'><b>$cols_name</b>";if($horizontal == "color"){$result .= "<br><b>$cols_code</b>";}$result .= "</td>";
											$n++;
										}
					$result .= "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						if($vertical == "color"){
							$row_code = $row["color_code"];
						}
						$row_id = $row["id_$vertical"];
						$result .= "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){$result .= "<br><b>$row_code</b>";}$result .= "</td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
					//		$quantity = dbFetchArray(dbQuery("select id_product_attribute,sum(qty) as qty from stock_qty where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
						//	list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
						//	$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
						
								$m++;
										$result .= "<td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off'  />
									
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' ></div></td>";
												
										 $result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
						
						$l++;
						}
						$result .= "$detail1$detail2</tr>";
						$i++;
			
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
					//-----------------------------------มีไซย์กับอื่นๆ---------------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$attribute_row = dbNumRows($attribute);
					$size_row = dbNumRows($size);
					if($attribute_row >= "$size_row"){
						$horizontal = "size";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "size";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select tbl_product_attribute.id_$vertical, $vertical_name from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select tbl_product_attribute.id_$horizontal, $horizontal_name from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					$result .= "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											$result .= "<td width='70px' align='center'><b>$cols_name</b></td>";
											$n++;
										}
					$result .= "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						$row_id = $row["id_$vertical"];
						$result .= "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b></td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							$n = $id_product_attribute;
						
								$m++;
										$result .= "<td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' />
								
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' ></div></td>";
												
										$result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
						$l++;
						}
						$result .= "$detail1$detail2</tr>";
						$i++;
			
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
					///------------------- สีอย่างเดียว---------------------//
				}else if($id_color != "" && $id_size == "" && $id_attribute == ""){
					$horizontal = "size";
					$vertical = "color";
					$result .= "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$color_name = $re['color_name'];
						$id_color = $re['id_color'];
						$color_code = $re['color_code'];
					
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_color = $id_color");
							
							
							$n = $id_product_attribute;
							
								@$m++;
								if(($i+1)%4 == "1"){
									$result .= "<tr>";
								}
										$result .= "<td width='180px'  style='vertical-align:middle;' align='right'>$color_name&nbsp;&nbsp;$color_code&nbsp; </td><td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off'  />
												
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' ></div></td>";
												
										$result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
										if(($i+1)%4 == "0"){
										$result .= "</tr>";
										}
										$i++;
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
			
					///------------------------ไซด์อย่างเดียว ------------------
				}else if($id_color == "" && $id_size != "" && $id_attribute == ""){
					$horizontal = "color";
					$vertical = "size";
					$result .= "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_color.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$size_name = $re['size_name'];
						$id_size = $re['id_size'];
						
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_size = $id_size");
						
							$n = $id_product_attribute;
							
								@$m++;
								if(($i+1)%4 == "1"){
									$result .= "<tr>";
								}
										$result .= "<td width='180px'  style='vertical-align:middle;' align='right'>$size_name&nbsp;&nbsp;&nbsp; </td><td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' />
												
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' ></div></td>";
												
										$result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
									
										if(($i+1)%4 == "0"){
										$result .= "</tr>";
										}
										$i++;
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
					//----------------------------มีอื่นๆอย่างเดียว-----------------------------------//
				}else if($id_color == "" && $id_size == "" && $id_attribute != ""){
					$horizontal = "color";
					$vertical = "attribute";
					$result .= "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$attribute_name = $re['attribute_name'];
						$id_attribute = $re['id_attribute'];
						
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_attribute = $id_attribute");
							
							$n = $id_product_attribute;
						
								@$m++;
								if(($i+1)%4 == "1"){
									$result .= "<tr>";
								}
										$result .= "<td width='180px'  style='vertical-align:middle;' align='right'>$attribute_name&nbsp;&nbsp;&nbsp; </td><td width='100px' align='center'><div>"; $result .= "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' />
												
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' ></div></td>";
												
										$result .="
										<script>
										$(document).ready(function(){
											$('#number$n').keyup(function(){
											if(isNaN($('#number$n').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number$n').val('');
													return false;
												 }
												 }); 
											}); 
										</script>";
										
										if(($i+1)%4 == "0"){
										$result .= "</tr>";
										}
										$i++;
					}
					$result .= "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					$result .= "</table>";
				}	
					
		}
			if(isset($_COOKIE['id_request_order'])){
			$id_request_order = $_COOKIE['id_request_order'];
		}else{
			$id_request_order = "";
		}
		$result .= "<input type='hidden' id='id_request_order' value='$id_request_order' >";	
		return $result;
		}
//********************************** End Request Attribute Grid  *********************************//		

		
	public function consign_attribute_grid($id_product,$id_customer,$id_zone){
		$maxss = $this->maxShowstock();
		if($maxss == "0"){
			$max = 10000000000;
		}else if($maxss = ""){
			$max = 10000000000;
		}else{
			$max = 1000000000;
		}
		$vertical = getConfig('ATTRIBUTE_GRID_VERTICAL');
		$vertical_name = "".$vertical."_name";
		$horizontal = getConfig('ATTRIBUTE_GRID_HORIZONTAL');
		$horizontal_name = "".$horizontal."_name";
		$additional = getConfig('ATTRIBUTE_GRID_ADDITIONAL');
		$additional_name = "".$additional."_name";
		
		list($id_color) = dbFetchArray(dbQuery("select id_color from tbl_product_attribute where id_product = '$id_product' and id_color != '0'"));
		list($id_size) = dbFetchArray(dbQuery("select id_size from tbl_product_attribute where id_product = '$id_product' and id_size != '0'"));
		list($id_attribute) = dbFetchArray(dbQuery("select id_attribute from tbl_product_attribute where id_product = '$id_product' and id_attribute != '0'")); 
		//---------------------------------มี 3 อัน-------------------------------------------------//
		if($id_color != "" && $id_size != "" && $id_attribute != ""){
		$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
		$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
		if($additional == "color_code"){
			$color_code = ",color_code";
		}else{
			$color_code = "";
		}
		$list = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name $color_code from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals = dbNumRows($list);
			$rows = dbNumRows($rows1);
			$colums = dbNumRows($colum);
			$tl = 0;
			echo "<ul class='nav nav-tabs'>";
			while($tl<$additionals){
				if($tl == "0"){
					$active = "active";
				}else{
					$active = "";
				}
					$additionl = dbFetchArray($list);
					$addition_namel = $additionl["$additional_name"];
					if($additional == "color_code"){
						$addition_code = $additionl["color_code"];
					}
			echo "
          <li class='$active'><a href='#Tab".($tl+1)."' data-toggle='tab'>$addition_namel";if($additional == "color"){echo "&nbsp;:&nbsp;$addition_code";}echo "</a></li>
        ";
		$tl++;
			}echo "</ul>";	
			$list1 = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals1 = dbNumRows($list1);
			$t = 0;
			while($t<$additionals1){
					$addition = dbFetchArray($list1);
					$addition_name = $addition["$additional_name"];
					$addition_id = $addition["id_$additional"];
			
		if($t == "0"){
        	echo "<div class='tab-content'>";
			$active = "active";
		}else{
			$active = "";
		}
		echo "<div class='tab-pane $active' id='Tab".($t+1)."'>";
			$width = $colums * 70;
			echo "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px vertical-align:middle;'><b>#</b></td>";
			$colum1 = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
			$colums1 = dbNumRows($colum1);
								$n = 0;
								$table_td = "";
								while($n<$colums1){
									$cols = dbFetchArray($colum1);
									$cols_name = $cols["$horizontal_name"];
									if($horizontal == "color"){
										$cols_code = $cols["color_code"];
									}
									echo "<td width='70px' align='center' style=' vertical-alignment:middle;'><b>$cols_name</b>";if($horizontal == "color"){echo "<br><b>$cols_code</b>";}echo "</td>";
									$n++;
								}
			echo "</tr>";
			$rows11 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
			$rows1 = dbNumRows($rows11);
			$i = 0 ;
			$detail1 = "";
			$detail3 = "";
			while($i < $rows1){
				$row = dbFetchArray($rows11);
				$row_name = $row["$vertical_name"];
				if($vertical == "color"){
					$row_code = $row["color_code"];
				}
				$row_id = $row["id_$vertical"];
				echo "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){echo "<br><b>$row_code</b>";}echo "</td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
				$colums2 = dbNumRows($col);
				while($l < $colums2){
					$colum2 = dbFetchArray($col);
					$col_id = $colum2["id_$horizontal"];
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = '$id_product' AND ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')"));
					$quantity = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
					$qty_in = $quantity['qty'];
					$n = $id_product_attribute;
					$qty = $qty_in;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
						$disabled  = "disabled='disabled'";
						$qtymax = "";
					}else if($qty <= $max){
						$qtyshow = "$qty";
						$disabled = "";
						$qtymax = $qty;
					}else{ 
						$qtyshow ="$max";
						$disabled = "";
						$qtymax = $max;
					}
						@$m++;
								echo "<td width='100px' align='center'><div>"; echo "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />
									<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div><div>$qtyshow</div></td>";
										if(allow_under_zero() == false ){
											?>
								<script>
									$(document).ready(function(){
									$('#number<?php echo $n;?>').keyup(function(){
										
									var n_order = $('#number<?php echo $n;?>').val();
									var n_stock = $('#qty<?php echo $n;?>').val();
									if(parseInt(n_order)>parseInt(n_stock)){
										alert('มีสินค้าในสต็อกแค่'+ n_stock);
										$('#number<?php echo $n;?>').val(n_stock);
										}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
										});
									}); 
								</script>
								<?php }?>
								<script>
								$(document).ready(function(){
									$('#number<?php echo $n;?>').keyup(function(){
									if(isNaN($('#number<?php echo $n;?>').val()))
										 {
											alert('ใส่ได้แต่ตัวเลขเท่านั้น');
											$('#number<?php echo $n;?>').val('');
											return false;
										 }
										 }); 
									}); 
								</script>
								<?php
				
				$l++;
				}
				echo "$detail1$detail2</tr>";
				$i++;
	
			}
			echo "</table></div>";
			$t++;
			}echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			echo "</div>";
			
			//--------------------------------มีไซต์มีสีไม่มีอื่นๆ----------------------------------->
		}else if($id_color != "" && $id_size != "" && $id_attribute == ""){
		$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
		$color = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
		$colums = dbNumRows($color);
			$rows = dbNumRows($size);
			$width = $colums * 70;
			echo "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
								$n = 0;
								$table_td = "";
								while($n<$colums){
									$cols = dbFetchArray($color);
									$color_name = $cols['color_name'];
									$color_code = $cols['color_code'];
									echo "<td width='70px' align='center' ><b>$color_code<br>$color_name</b></td>";
									$n++;
								}
			echo "</tr>";
			$i = 0 ;
			$m = "";
			$detail1 = "";
			$detail3 = "";
			while($i < $rows){
				$row = dbFetchArray($size);
				$size_name = $row['size_name'];
				$size_id = $row['id_size'];
				echo "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$size_name</b></td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_color from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
				while($l < $colums){
					$colum = dbFetchArray($col);
					$color_id = $colum['id_color'];
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = $id_product AND ( id_color = '$color_id' and id_size = '$size_id')"));
					$quantity = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
					$qty_in = $quantity['qty'];
					$n = $id_product_attribute;
					$qty = $qty_in;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
						$disabled  = "disabled='disabled'";
						$qtymax = "";
					}else if($qty <= $max){
						$qtyshow = "$qty";
						$disabled = "";
						$qtymax = $qty;
					}else{ 
						$qtyshow ="$max";
						$disabled = "";
						$qtymax = $max;
					}
						$m++;
								echo "<td width='100px' align='center'><div>"; echo "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />
									<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div><div>$qtyshow</div></td>";
										if(allow_under_zero() == false ){
											?>
								<script>
									$(document).ready(function(){
									$('#number<?php echo $n;?>').keyup(function(){
										
									var n_order = $('#number<?php echo $n;?>').val();
									var n_stock = $('#qty<?php echo $n;?>').val();
									if(parseInt(n_order)>parseInt(n_stock)){
										alert('มีสินค้าในสต็อกแค่'+ n_stock);
										$('#number<?php echo $n;?>').val(n_stock);
										}else if(parseInt(n_order)<1){
										alert("ต้องสั่งอย่างน้อย 1");
										$('#number<?php echo $n;?>').val('');
										}
										});
									}); 
								</script>
								<?php }?>
								<script>
								$(document).ready(function(){
									$('#number<?php echo $n;?>').keyup(function(){
									if(isNaN($('#number<?php echo $n;?>').val()))
										 {
											alert('ใส่ได้แต่ตัวเลขเท่านั้น');
											$('#number<?php echo $n;?>').val('');
											return false;
										 }
										 }); 
									}); 
								</script>
								<?php
				
				$l++;
				}
				echo "$detail1$detail2</tr>";
				$i++;
			}
			echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			echo "</table>";
			//------------------------------------มีสีกับอื่นๆ---------------------------------------------//
			}else{
				 if($id_color != "" && $id_size == "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$color = dbQuery("select tbl_product_attribute.id_color, color_name from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$attribute_row = dbNumRows($attribute);
					$color_row = dbNumRows($color);
					if($attribute_row >= "$color_row"){
						$horizontal = "color";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "color";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					echo "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											if($horizontal == "color"){
												$cols_code = $cols['color_code'];
											}
											echo "<td width='70px' align='center'><b>$cols_name</b>";if($horizontal == "color"){echo "<br><b>$cols_code</b>";}echo "</td>";
											$n++;
										}
					echo "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						if($vertical == "color"){
							$row_code = $row["color_code"];
						}
						$row_id = $row["id_$vertical"];
						echo "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){echo "<br><b>$row_code</b>";}echo "</td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = '$id_product' AND ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$quantity = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
					$qty_in = $quantity['qty'];
					$n = $id_product_attribute;
					$qty = $qty_in;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								$m++;
										echo "<td width='100px' align='center'><div>"; echo "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />
									<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div><div>$qtyshow</div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
						
						$l++;
						}
						echo "$detail1$detail2</tr>";
						$i++;
			
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					//-----------------------------------มีไซย์กับอื่นๆ---------------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$attribute_row = dbNumRows($attribute);
					$size_row = dbNumRows($size);
					if($attribute_row >= "$size_row"){
						$horizontal = "size";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "size";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select tbl_product_attribute.id_$vertical, $vertical_name from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select tbl_product_attribute.id_$horizontal, $horizontal_name from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					echo "<table class='table table-bordered'><tr><td width='50px' align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											echo "<td width='70px' align='center'><b>$cols_name</b></td>";
											$n++;
										}
					echo "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						$row_id = $row["id_$vertical"];
						echo "<tr valign='middle'><td width='70px' align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b></td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$quantity = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
					$qty_in = $quantity['qty'];
					$n = $id_product_attribute;
					$qty = $qty_in;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								$m++;
										echo "<td width='100px' align='center'><div>"; echo "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />
									<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
									<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div><div>$qtyshow</div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
						
						$l++;
						}
						echo "$detail1$detail2</tr>";
						$i++;
			
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					///------------------- สีอย่างเดียว---------------------//
				}else if($id_color != "" && $id_size == "" && $id_attribute == ""){
					$horizontal = "size";
					$vertical = "color";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$color_name = $re['color_name'];
						$id_color = $re['id_color'];
						$color_code = $re['color_code'];
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = '$id_product' and id_color = $id_color"));
							$quantity = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
					$qty_in = $quantity['qty'];
					$n = $id_product_attribute;
					$qty = $qty_in;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td width='180px'  style='vertical-align:middle;' align='right'>$color_name&nbsp;&nbsp;$color_code&nbsp; </td><td width='100px' align='center'><div>"; echo "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />$qtyshow
												<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
			
					///------------------------ไซด์อย่างเดียว ------------------
				}else if($id_color == "" && $id_size != "" && $id_attribute == ""){
					$horizontal = "color";
					$vertical = "size";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$size_name = $re['size_name'];
						$id_size = $re['id_size'];
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = '$id_product' and id_size = $id_size"));
						$quantity = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
					$qty_in = $quantity['qty'];
					$n = $id_product_attribute;
					$qty = $qty_in;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td width='180px'  style='vertical-align:middle;' align='right'>$size_name&nbsp;&nbsp;&nbsp; </td><td width='100px' align='center'><div>"; echo "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />$qtyshow
												<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					//----------------------------มีอื่นๆอย่างเดียว-----------------------------------//
				}else if($id_color == "" && $id_size == "" && $id_attribute != ""){
					$horizontal = "color";
					$vertical = "attribute";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$attribute_name = $re['attribute_name'];
						$id_attribute = $re['id_attribute'];
						list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = '$id_product' and id_attribute = $id_attribute"));
						$quantity = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
					$qty_in = $quantity['qty'];
					$n = $id_product_attribute;
					$qty = $qty_in;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
								$disabled  = "disabled='disabled'";
								$qtymax = "";
							}else if($qty <= $max){
								$qtyshow = "$qty";
								$disabled = "";
								$qtymax = $qty;
							}else{ 
								$qtyshow ="$max";
								$disabled = "";
								$qtymax = $max;
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td width='180px'  style='vertical-align:middle;' align='right'>$color_name&nbsp;&nbsp;&nbsp; </td><td width='100px' align='center'><div>"; echo "<input type='text' name='order_qty[]' id='number".$n."' class='form-control' autocomplete='off' $disabled />$qtyshow
												<input type='hidden' name='qty[]' id='qty".$n."' value='$qtymax' $disabled >
												<input type='hidden' name='id_product_attribute[]' id='id_product_attribute".$id_product."".$m."' value='$id_product_attribute' $disabled ></div></td>";
												if(allow_under_zero() == false ){
													?>
										<script>
											$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
												
											var n_order = $('#number<?php echo $n;?>').val();
											var n_stock = $('#qty<?php echo $n;?>').val();
											if(parseInt(n_order)>parseInt(n_stock)){
												alert('มีสินค้าในสต็อกแค่'+ n_stock);
												$('#number<?php echo $n;?>').val(n_stock);
												}
												});
											}); 
										</script>
										<?php }?>
										<script>
										$(document).ready(function(){
											$('#number<?php echo $n;?>').keyup(function(){
											if(isNaN($('#number<?php echo $n;?>').val()))
												 {
													alert('ใส่ได้แต่ตัวเลขเท่านั้น');
													$('#number<?php echo $n;?>').val('');
													return false;
												 }
												 }); 
											}); 
										</script>
										<?php
										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
				}	
					
		}
		}
		
		public function stock_in_zone($id_product_attribute,$all_zone=false){
			if($all_zone){
			$sql = dbQuery("SELECT Zone,  SUM(qty) AS qty FROM stock WHERE id_product_attribute = $id_product_attribute GROUP BY Zone  ORDER BY Zone ASC");
			}else{
			$sql = dbQuery("SELECT Zone,  SUM(qty) AS qty FROM stock WHERE id_product_attribute = $id_product_attribute AND id_zone !=0 AND id_warehouse = 1  GROUP BY Zone ORDER BY Zone ASC");
			}
			$result = "";
			while($row = dbFetchArray($sql)){
				$zone = $row['Zone'];
				$qty = $row['qty'];
				$result = $result." ".$zone." : ".$qty."<br />";
			}
			list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
			if($qty_moveing == ""){$result1 = $result;}else{
				$result1 = $result." กำลังย้ายโซน : ".$qty_moveing."<br />";
			}
			return $result1;
		}
		
// ******************************************  ใช้กับ รายงาน  *********************************************//
public function reportAttributeGrid($id_product){
		$max = 1000000000;
		$vertical = getConfig('ATTRIBUTE_GRID_VERTICAL');
		$vertical_name = "".$vertical."_name";
		$horizontal = getConfig('ATTRIBUTE_GRID_HORIZONTAL');
		$horizontal_name = "".$horizontal."_name";
		$additional = getConfig('ATTRIBUTE_GRID_ADDITIONAL');
		$additional_name = "".$additional."_name";
		list($id_color) = dbFetchArray(dbQuery("select id_color from tbl_product_attribute where id_product = '$id_product' and id_color != '0'"));
		list($id_size) = dbFetchArray(dbQuery("select id_size from tbl_product_attribute where id_product = '$id_product' and id_size != '0'"));
		list($id_attribute) = dbFetchArray(dbQuery("select id_attribute from tbl_product_attribute where id_product = '$id_product' and id_attribute != '0'")); 
		//---------------------------------มี 3 อัน-------------------------------------------------//
		if($id_color != "" && $id_size != "" && $id_attribute != ""){
		$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
		$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
		if($additional == "color"){
			$color_code = ",color_code";
		}else{$color_code = "";}
		$list = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name $color_code from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals = dbNumRows($list);
			$rows = dbNumRows($rows1);
			$colums = dbNumRows($colum);
			$tl = 0;
			echo "<ul class='nav nav-tabs'>";
			while($tl<$additionals){
				if($tl == "0"){
					$active = "active";
				}else{
					$active = "";
				}
					$additionl = dbFetchArray($list);
					$addition_namel = $additionl["$additional_name"];
					if($additional == "color"){
						$add_code = $additionl["color_code"];
					}
			echo "
          <li class='$active'><a href='#Tab".($tl+1)."' data-toggle='tab'>$addition_namel";if($additional == "color"){ echo "&nbsp;:&nbsp;$add_code";}echo "</a></li>
        ";
		$tl++;
			}echo "</ul>";	
			$list1 = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals1 = dbNumRows($list1);
			$t = 0;
			while($t<$additionals1){
					$addition = dbFetchArray($list1);
					$addition_name = $addition["$additional_name"];
					$addition_id = $addition["id_$additional"];
			
		if($t == "0"){
        	echo "<div class='tab-content'>";
			$active = "active";
		}else{
			$active = "";
		}
		echo "<div class='tab-pane $active' id='Tab".($t+1)."'>";
			$width = $colums * 70;
			echo "<table class='table table-bordered'><tr><td align='center' style='font-size:16px vertical-align:middle;'><b>#</b></td>";
			$colum1 = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
			$colums1 = dbNumRows($colum1);
								$n = 0;
								$table_td = "";
								while($n<$colums1){
									$cols = dbFetchArray($colum1);
									$cols_name = $cols["$horizontal_name"];
									if($horizontal == "color"){
										$cols_code = $cols["color_code"];
									}
									echo "<td align='center' style=' vertical-alignment:middle;'><b>$cols_name</b>";if($horizontal == "color"){echo "<br><b>$cols_code</b>";}echo "</td>";
									$n++;
								}
			echo "</tr>";
			$rows11 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
			$rows1 = dbNumRows($rows11);
			$i = 0 ;
			$detail1 = "";
			$detail3 = "";
			while($i < $rows1){
				$row = dbFetchArray($rows11);
				$row_name = $row["$vertical_name"];
				if($vertical == "color"){
					$row_code = $row["color_code"];
				}
				$row_id = $row["id_$vertical"];
				echo "<tr valign='middle'><td align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){echo "<br><b>$row_code</b>";}echo "</td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
				$colums2 = dbNumRows($col);
				while($l < $colums2){
					$colum2 = dbFetchArray($col);
					$col_id = $colum2["id_$horizontal"];
					$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
					//$sumorder_qty = $this->orderQty($id_product_attribute);
					$qty = $qty_in;//-$sumorder_qty;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
						$qtymax = "";
					}else{
						$qtyshow = "$qty";
						
					}
						@$m++;
								echo "<td align='center'><div></div><div>$qtyshow </div></td>";
				
				$l++;
				}
				echo "$detail1$detail2</tr>";
				$i++;
	
			}
			echo "</table></div>";
			$t++;
			}echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			echo "</div>";
			
			//--------------------------------มีไซต์มีสีไม่มีอื่นๆ----------------------------------->
		}else if($id_color != "" && $id_size != "" && $id_attribute == ""){
		$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
		$color = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
		$colums = dbNumRows($color);
			$rows = dbNumRows($size);
			$width = $colums * 70;
			echo "<table class='table table-bordered'><tr><td align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
								$n = 0;
								$table_td = "";
								while($n<$colums){
									$cols = dbFetchArray($color);
									$color_name = $cols['color_name'];
									$color_code = $cols['color_code'];
									echo "<td align='center' ><b>$color_code<br>$color_name</b></td>";
									$n++;
								}
			echo "</tr>";
			$i = 0 ;
			$m = "";
			$detail1 = "";
			$detail3 = "";
			while($i < $rows){
				$row = dbFetchArray($size);
				$size_name = $row['size_name'];
				$size_id = $row['id_size'];
				echo "<tr valign='middle'><td align='center' style='font-size:16px; vertical-align:middle;'><b>$size_name</b></td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_color from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
				while($l < $colums){
					$colum = dbFetchArray($col);
					$color_id = $colum['id_color'];
					$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
					//$sumorder_qty = $this->orderQty($id_product_attribute);
					$qty = $qty_in; //-$sumorder_qty;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
					}else if($qty <= $max){
						$qtyshow = "$qty";
					}
						$m++;
								echo "<td align='center'><div>$qtyshow</div></td>";
										
				$l++;
				}
				echo "$detail1$detail2</tr>";
				$i++;
			}
			echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			echo "</table>";
			//------------------------------------มีสีกับอื่นๆ---------------------------------------------//
			}else{
				 if($id_color != "" && $id_size == "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$color = dbQuery("select tbl_product_attribute.id_color, color_name from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$attribute_row = dbNumRows($attribute);
					$color_row = dbNumRows($color);
					if($attribute_row >= "$color_row"){
						$horizontal = "color";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "color";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					
					$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					echo "<table class='table table-bordered'><tr><td align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											if($horizontal == "color"){
												$cols_code = $cols["color_code"];
											}
											echo "<td align='center'><b>$cols_name</b>";if($horizontal == "color"){echo "<br><b>$cols_code</b>";}echo "</td>";
											$n++;
										}
					echo "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						if($vertical == "color"){
							$row_code = $row["color_code"];
						}
						$row_id = $row["id_$vertical"];
						echo "<tr valign='middle'><td align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){echo "<br><b>$row_code</b>";}echo "</td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							//$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in;//-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";						
							}else{
								$qtyshow = "$qty";
							}
								$m++;
										echo "<td align='center'><div>$qtyshow</div></td>";
						$l++;
						}
						echo "$detail1$detail2</tr>";
						$i++;
			
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					
					//---------------------------------มีไซต์กับอื่นๆ--------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$attribute_row = dbNumRows($attribute);
					$size_row = dbNumRows($size);
					if($attribute_row >= "$size_row"){
						$horizontal = "size";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "size";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select tbl_product_attribute.id_$vertical, $vertical_name from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select tbl_product_attribute.id_$horizontal, $horizontal_name from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					echo "<table class='table table-bordered'><tr><td align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											echo "<td align='center'><b>$cols_name</b></td>";
											$n++;
										}
					echo "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						$row_id = $row["id_$vertical"];
						echo "<tr valign='middle'><td align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b></td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							//$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in; //-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";								
							}else{
								$qtyshow = "$qty";								
							}
								$m++;
										echo "<td align='center'><div>$qtyshow</div></td>";
						$l++;
						}
						echo "$detail1$detail2</tr>";
						$i++;
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					//-----------------------------------สีอย่างเดียว---------------------------------------//
				}else if($id_color != "" && $id_size == "" && $id_attribute == ""){
					$horizontal = "size";
					$vertical = "color";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_color, color_name,color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$color_name = $re['color_name'];
						$id_color = $re['id_color'];
						$color_code = $re['color_code'];
						$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where id_product = '$id_product' and id_color = $id_color"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_color = $id_color");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							//$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in; //-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";								
							}else{
								$qtyshow = "$qty";								
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td  style='vertical-align:middle;' align='right'>$color_name&nbsp;:&nbsp;$color_code&nbsp; </td><td  align='center'><div>$qtyshow</div></td>";
										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}

					echo "</table>";			
					//---------------------------------ไซต์อยา่งเดียว-----------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute == ""){
					$horizontal = "color";
					$vertical = "size";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_color, color_name from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$color_name = $re['color_name'];
						$id_color = $re['id_color'];
						$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where id_product = '$id_product' and id_color = $id_color"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_color = $id_color");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							//$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in; //-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
							}else{
								$qtyshow = "$qty";								
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td  style='vertical-align:middle;' align='right'>$color_name&nbsp;&nbsp;&nbsp; </td><td  align='center'><div>$qtyshow</div></td>";

										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}
					echo "</table>";
					//-------------------------------------------------อื่นๆอย่างเดียว--------------------------------------------//
				}else if($id_color == "" && $id_size == "" && $id_attribute != ""){
					$horizontal = "color";
					$vertical = "attribute";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_color, color_name from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$color_name = $re['color_name'];
						$id_color = $re['id_color'];
						$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where id_product = '$id_product' and id_color = $id_color"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_color = $id_color");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							//$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in; //-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";								
							}else{
								$qtyshow = "$qty";
								}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td style='vertical-align:middle;' align='right'>$color_name&nbsp;&nbsp;&nbsp; </td><td align='center'><div>$qtyshow</div></td>";
								if(($i+1)%4 == "0"){ 	echo "</tr>"; }
										$i++;
					}
					echo "</table>";
				}	
					
		}
		/*
		/**/
		
		
		if(isset($_COOKIE['id_cart'])){
			$id_cart = $_COOKIE['id_cart'];
		}else{
			$id_cart = "";
		}
		echo "<input type='hidden' id='id_cart' value='$id_cart' >";	
		}
	// ******************************************  ใช้กับหลังบ้าน *********************************************//
public function order_report_attribute_grid($id_product){
		$max = 1000000000;
		$vertical = getConfig('ATTRIBUTE_GRID_VERTICAL');
		$vertical_name = "".$vertical."_name";
		$horizontal = getConfig('ATTRIBUTE_GRID_HORIZONTAL');
		$horizontal_name = "".$horizontal."_name";
		$additional = getConfig('ATTRIBUTE_GRID_ADDITIONAL');
		$additional_name = "".$additional."_name";
		list($id_color) = dbFetchArray(dbQuery("select id_color from tbl_product_attribute where id_product = '$id_product' and id_color != '0'"));
		list($id_size) = dbFetchArray(dbQuery("select id_size from tbl_product_attribute where id_product = '$id_product' and id_size != '0'"));
		list($id_attribute) = dbFetchArray(dbQuery("select id_attribute from tbl_product_attribute where id_product = '$id_product' and id_attribute != '0'")); 
		//---------------------------------มี 3 อัน-------------------------------------------------//
		if($id_color != "" && $id_size != "" && $id_attribute != ""){
		$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
		$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
		if($additional == "color"){
			$color_code = ",color_code";
		}else{$color_code = "";}
		$list = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name $color_code from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals = dbNumRows($list);
			$rows = dbNumRows($rows1);
			$colums = dbNumRows($colum);
			$tl = 0;
			echo "<ul class='nav nav-tabs'>";
			while($tl<$additionals){
				if($tl == "0"){
					$active = "active";
				}else{
					$active = "";
				}
					$additionl = dbFetchArray($list);
					$addition_namel = $additionl["$additional_name"];
					if($additional == "color"){
						$add_code = $additionl["color_code"];
					}
			echo "
          <li class='$active'><a href='#Tab".($tl+1)."' data-toggle='tab'>$addition_namel";if($additional == "color"){ echo "&nbsp;:&nbsp;$add_code";}echo "</a></li>
        ";
		$tl++;
			}echo "</ul>";	
			$list1 = dbQuery("select DISTINCT tbl_product_attribute.id_$additional,$additional_name from tbl_product_attribute left join tbl_$additional on tbl_product_attribute.id_$additional = tbl_$additional.id_$additional where tbl_product_attribute.id_product = '$id_product' order by tbl_$additional.position asc");
			$additionals1 = dbNumRows($list1);
			$t = 0;
			while($t<$additionals1){
					$addition = dbFetchArray($list1);
					$addition_name = $addition["$additional_name"];
					$addition_id = $addition["id_$additional"];
			
		if($t == "0"){
        	echo "<div class='tab-content'>";
			$active = "active";
		}else{
			$active = "";
		}
		echo "<div class='tab-pane $active' id='Tab".($t+1)."'>";
			$width = $colums * 70;
			echo "<table class='table table-bordered'><tr><td align='center' style='font-size:16px vertical-align:middle;'><b>#</b></td>";
			$colum1 = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
			$colums1 = dbNumRows($colum1);
								$n = 0;
								$table_td = "";
								while($n<$colums1){
									$cols = dbFetchArray($colum1);
									$cols_name = $cols["$horizontal_name"];
									if($horizontal == "color"){
										$cols_code = $cols["color_code"];
									}
									echo "<td align='center' style=' vertical-alignment:middle;'><b>$cols_name</b>";if($horizontal == "color"){echo "<br><b>$cols_code</b>";}echo "</td>";
									$n++;
								}
			echo "</tr>";
			$rows11 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
			$rows1 = dbNumRows($rows11);
			$i = 0 ;
			$detail1 = "";
			$detail3 = "";
			while($i < $rows1){
				$row = dbFetchArray($rows11);
				$row_name = $row["$vertical_name"];
				if($vertical == "color"){
					$row_code = $row["color_code"];
				}
				$row_id = $row["id_$vertical"];
				echo "<tr valign='middle'><td align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){echo "<br><b>$row_code</b>";}echo "</td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' and tbl_product_attribute.id_$additional = '$addition_id' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
				$colums2 = dbNumRows($col);
				while($l < $colums2){
					$colum2 = dbFetchArray($col);
					$col_id = $colum2["id_$horizontal"];
					$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where tbl_stock.id_zone !=0 AND id_warehouse !=2 AND id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')"));
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id' and id_$additional = '$addition_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
					$sumorder_qty = $this->orderQty($id_product_attribute);
					$qty = $qty_in - $sumorder_qty;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
						$qtymax = "";
					}else{
						$qtyshow = "$qty";
						
					}
						@$m++;
								echo "<td align='center'><div></div><div>$qtyshow </div></td>";
				
				$l++;
				}
				echo "$detail1$detail2</tr>";
				$i++;
	
			}
			echo "</table></div>";
			$t++;
			}echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			echo "</div>";
			
			//--------------------------------มีไซต์มีสีไม่มีอื่นๆ----------------------------------->
		}else if($id_color != "" && $id_size != "" && $id_attribute == ""){
		$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
		$color = dbQuery("select tbl_product_attribute.id_color, color_name, color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
		$colums = dbNumRows($color);
			$rows = dbNumRows($size);
			$width = $colums * 70;
			echo "<table class='table table-bordered'><tr><td align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
								$n = 0;
								$table_td = "";
								while($n<$colums){
									$cols = dbFetchArray($color);
									$color_name = $cols['color_name'];
									$color_code = $cols['color_code'];
									echo "<td align='center' ><b>$color_code<br>$color_name</b></td>";
									$n++;
								}
			echo "</tr>";
			$i = 0 ;
			$m = "";
			$detail1 = "";
			$detail3 = "";
			while($i < $rows){
				$row = dbFetchArray($size);
				$size_name = $row['size_name'];
				$size_id = $row['id_size'];
				echo "<tr valign='middle'><td align='center' style='font-size:16px; vertical-align:middle;'><b>$size_name</b></td>";
				$l = 0;
				$detail2 = "";
				$col = dbQuery("select tbl_product_attribute.id_color from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
				while($l < $colums){
					$colum = dbFetchArray($col);
					$color_id = $colum['id_color'];
					$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')");
					$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where tbl_stock.id_zone !=0 AND id_warehouse !=2 AND id_product = '$id_product' and ( id_color = '$color_id' and id_size = '$size_id')"));
					//$id_product_attribute = $quantity['id_product_attribute'];
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
					$n = $id_product_attribute;
					$sumorder_qty = $this->orderQty($id_product_attribute);
					$qty = $qty_in-$sumorder_qty;
					if($qty <1){
						$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
					}else if($qty <= $max){
						$qtyshow = "$qty";
					}
						$m++;
								echo "<td align='center'><div>$qtyshow</div></td>";
										
				$l++;
				}
				echo "$detail1$detail2</tr>";
				$i++;
			}
			echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
			echo "</table>";
			//------------------------------------มีสีกับอื่นๆ---------------------------------------------//
			}else{
				 if($id_color != "" && $id_size == "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$color = dbQuery("select tbl_product_attribute.id_color, color_name from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$attribute_row = dbNumRows($attribute);
					$color_row = dbNumRows($color);
					if($attribute_row >= "$color_row"){
						$horizontal = "color";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "color";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select * from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					
					$colum = dbQuery("select * from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					echo "<table class='table table-bordered'><tr><td align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											if($horizontal == "color"){
												$cols_code = $cols["color_code"];
											}
											echo "<td align='center'><b>$cols_name</b>";if($horizontal == "color"){echo "<br><b>$cols_code</b>";}echo "</td>";
											$n++;
										}
					echo "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						if($vertical == "color"){
							$row_code = $row["color_code"];
						}
						$row_id = $row["id_$vertical"];
						echo "<tr valign='middle'><td align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b>";if($vertical == "color"){echo "<br><b>$row_code</b>";}echo "</td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where tbl_stock.id_zone !=0 AND id_warehouse !=2 AND id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";						
							}else{
								$qtyshow = "$qty";
							}
								$m++;
										echo "<td align='center'><div>$qtyshow</div></td>";
						$l++;
						}
						echo "$detail1$detail2</tr>";
						$i++;
			
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					
					//---------------------------------มีไซต์กับอื่นๆ--------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute != ""){
					$attribute = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$size = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$attribute_row = dbNumRows($attribute);
					$size_row = dbNumRows($size);
					if($attribute_row >= "$size_row"){
						$horizontal = "size";
						$vertical = "attribute";
						
					}else{
						$horizontal = "attribute";
						$vertical = "size";
					}
					$horizontal_name = "".$horizontal."_name";
					$vertical_name = "".$vertical."_name";
					$rows1 = dbQuery("select tbl_product_attribute.id_$vertical, $vertical_name from tbl_product_attribute left join tbl_$vertical on tbl_product_attribute.id_$vertical = tbl_$vertical.id_$vertical WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$vertical order by tbl_$vertical.position asc");
					$colum = dbQuery("select tbl_product_attribute.id_$horizontal, $horizontal_name from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
					$rows = dbNumRows($rows1);
					$colums = dbNumRows($colum);
					$width = $colums * 70;
					echo "<table class='table table-bordered'><tr><td align='center' style='font-size:16px; vertical-align:middle;'><b>#</b></td>";
										$n = 0;
										$table_td = "";
										while($n<$colums){
											$cols = dbFetchArray($colum);
											$cols_name = $cols["$horizontal_name"];
											echo "<td align='center'><b>$cols_name</b></td>";
											$n++;
										}
					echo "</tr>";
					$i = 0 ;
					$m = "";
					$detail1 = "";
					$detail3 = "";
					while($i < $rows){
						$row = dbFetchArray($rows1);
						$row_name = $row["$vertical_name"];
						$row_id = $row["id_$vertical"];
						echo "<tr valign='middle'><td align='center' style='font-size:16px; vertical-align:middle;'><b>$row_name</b></td>";
						$l = 0;
						$detail2 = "";
						$col = dbQuery("select tbl_product_attribute.id_$horizontal from tbl_product_attribute left join tbl_$horizontal on tbl_product_attribute.id_$horizontal = tbl_$horizontal.id_$horizontal WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_$horizontal order by tbl_$horizontal.position asc");
						while($l < $colums){
							$colum = dbFetchArray($col);
							$col_id = $colum["id_$horizontal"];
							$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where tbl_stock.id_zone !=0 AND id_warehouse !=2 AND id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and ( id_$vertical = '$row_id' and id_$horizontal = '$col_id')");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";								
							}else{
								$qtyshow = "$qty";								
							}
								$m++;
										echo "<td align='center'><div>$qtyshow</div></td>";
						$l++;
						}
						echo "$detail1$detail2</tr>";
						$i++;
					}
					echo "<input type='hidden' name='loop".$id_product."' id='loop".$id_product."' value='$m' >";
					echo "</table>";
					//-----------------------------------สีอย่างเดียว---------------------------------------//
				}else if($id_color != "" && $id_size == "" && $id_attribute == ""){
					$horizontal = "size";
					$vertical = "color";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_color, color_name,color_code from tbl_product_attribute left join tbl_color on tbl_product_attribute.id_color = tbl_color.id_color WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_color order by tbl_color.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$color_name = $re['color_name'];
						$id_color = $re['id_color'];
						$color_code = $re['color_code'];
						$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where tbl_stock.id_zone !=0 AND id_warehouse !=2 AND id_product = '$id_product' and id_color = $id_color"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_color = $id_color");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";								
							}else{
								$qtyshow = "$qty";								
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td  style='vertical-align:middle;' align='right'>$color_name&nbsp;:&nbsp;$color_code&nbsp; </td><td  align='center'><div>$qtyshow</div></td>";
										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}

					echo "</table>";			
					//---------------------------------ไซต์อยา่งเดียว-----------------------------------//
				}else if($id_color == "" && $id_size != "" && $id_attribute == ""){
					$horizontal = "color";
					$vertical = "size";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_size, size_name from tbl_product_attribute left join tbl_size on tbl_product_attribute.id_size = tbl_size.id_size WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_size order by tbl_size.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$size_name = $re['size_name'];
						$id_size = $re['id_size'];
						$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where tbl_stock.id_zone !=0 AND id_warehouse !=2 AND id_product = '$id_product' and id_size = $id_size"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_size = $id_size");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";
							}else{
								$qtyshow = "$qty";								
							}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td  style='vertical-align:middle;' align='right'>$size_name&nbsp;&nbsp;&nbsp; </td><td  align='center'><div>$qtyshow</div></td>";

										if(($i+1)%4 == "0"){
										echo "</tr>";
										}
										$i++;
					}
					echo "</table>";
					//-------------------------------------------------อื่นๆอย่างเดียว--------------------------------------------//
				}else if($id_color == "" && $id_size == "" && $id_attribute != ""){
					$horizontal = "color";
					$vertical = "attribute";
					echo "<table class='table table-bordered' >";
					$query = dbQuery("select tbl_product_attribute.id_attribute, attribute_name from tbl_product_attribute left join tbl_attribute on tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE tbl_product_attribute.id_product = '$id_product' group by tbl_product_attribute.id_attribute order by tbl_attribute.position asc");
					$rows = dbNumRows($query);
					$i = 0;
					while($i < $rows){
						$re = dbFetchArray($query);
						$attribute_name = $re['attribute_name'];
						$id_attribute = $re['id_attribute'];
						$quantity = dbFetchArray(dbQuery("select tbl_product_attribute.id_product_attribute,sum(qty) AS qty from tbl_stock left join tbl_product_attribute on tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute left join tbl_zone on tbl_stock.id_zone = tbl_zone.id_zone where tbl_stock.id_zone !=0 AND id_warehouse !=2 AND id_product = '$id_product' and id_attribute = $id_attribute"));
							$id_product_attribute = $this->get_id_product_attribute("id_product = '$id_product' and id_attribute = $id_attribute");
							list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
							$qty_in = $quantity['qty']+$qty_moveing;
							$n = $id_product_attribute;
							$sumorder_qty = $this->orderQty($id_product_attribute);
							$qty = $qty_in-$sumorder_qty;
							if($qty <1){
								$qtyshow = "<font color='red' style='font-size:12px'>สินค้าหมด</font>";								
							}else{
								$qtyshow = "$qty";
								}
								@$m++;
								if(($i+1)%4 == "1"){
									echo "<tr>";
								}
										echo "<td style='vertical-align:middle;' align='right'>$attribute_name&nbsp;&nbsp;&nbsp; </td><td align='center'><div>$qtyshow</div></td>";
								if(($i+1)%4 == "0"){ 	echo "</tr>"; }
										$i++;
					}
					echo "</table>";
				}	
					
		}
		/*
		/**/
		
		
		if(isset($_COOKIE['id_cart'])){
			$id_cart = $_COOKIE['id_cart'];
		}else{
			$id_cart = "";
		}
		echo "<input type='hidden' id='id_cart' value='$id_cart' >";	
		}	
		public function get_id_product_attribute($where){
			list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE $where"));
			return $id_product_attribute;
		}
}//จบ class
?>


