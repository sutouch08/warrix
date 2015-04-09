<!-- Fixed navbar start -->
  <div class="navbar navbar-tshop navbar-fixed-top megamenu" role="navigation">
  <?php 
if(isset($_COOKIE['id_customer'])){ $id_customer = $_COOKIE['id_customer']; }else{$id_customer = 0;}
  include "minicart.php"; ?>
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> 
      <span class="sr-only"> Toggle navigation </span> <span class="icon-bar"> </span> <span class="icon-bar"> </span> <span class="icon-bar"> </span> </button>
      <?php $cart_mini->total_for_mobile($id_cart,$id_customer);?>
      
     <!-- <a class="navbar-brand " href="index.php"> <img src="images/logo.png" alt="<?php //echo COMPANY; ?>"> </a> -->
       <!-- this part for mobile -->  
    </div>
	<?php  $cart_mini->cartmini_for_mobile($id_cart,$id_customer);?>

  <!--------------------- แสดงเมนู ------------------->    
    <div class="navbar-collapse collapse">
    
   <!-- <form class="navbar-form navbar-left" role="search">
        <div class="form-group" style="width:50%">
          <input type="text" class="form-control" placeholder="Search"><button type="submit" class="btn btn-default">Submit</button>
        </div>
      </form>-->
      <ul class="nav navbar-nav" >
        <li> <a href="index.php?content=order"> Home </a> </li>
        <li class="dropdown megamenu-fullwidth"> <a data-toggle="dropdown" class="dropdown-toggle" href="#"> Products <b class="caret"> </b> </a>
          <ul class="dropdown-menu">
            <li class="megamenu-content "> 
				<?php 
				$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND id_category !=0 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				echo" <ul class='col-lg-3  col-sm-3 col-md-3 unstyled noMarginLeft newCollectionUl'>
					<li><a href='index.php?content=order&id_category=$id_category' style='display:block;'>".strtoupper($category_name)."</a></li>";
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo"<li><a href='index.php?content=order&id_category=$id_sub_category' style='display:block;'>".strtoupper($sub_category_name)."</a></li>";
				$n++;
				}
				echo "</ul>";
				$i++;
				}
				?>
            </li>
          </ul>
        </li>
        <li> <a href="index.php?content=dashboard"> Dash Board </a> </li>
        <li> <a href="index.php?content=tracking"> ติดตามออเดอร์ </a> </li>
        <li> <a href="request/index.php"> request product </a> </li>
      </ul>
      
        <ul class="nav navbar-nav">
   		 <li class='dropdown'>
                    <a class='dropdown-toggle' style='color:#FFF; background-color:transparent;' data-toggle='dropdown' href='#'>
                        <i class='fa fa-gear fa-fw'></i>  <i class='fa fa-caret-down'></i>
                    </a>
                    <ul class='dropdown-menu dropdown-user'>
                        <li><a href="index.php?content=Employee&reset_password=y&id_employee=<?php echo $_COOKIE['user_id']; ?> "><i class='fa fa-key'></i> Reset Password</a>
                        </li>
                       
                        <li class='divider'></li>
                        <li><a href='index.php?logout'><i class='fa fa-sign-out fa-fw'></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
  <!--------------------- ตะกร้าสินค้า ------------------->  
     <div id="txtHint"><?php  $cart_mini->cartmini($id_cart,$id_customer);?></div>
  <!--------------------- จบตะกร้าสินค้า ------------------->        
	
      </div>
      <!--/.navbar-av hidden-xs--> 
      
    </div>
    <!--/.nav-collapse --> 
  
  </div>
  <!--/.container -->
 
</div>
<!-- /.Fixed navbar  -->
