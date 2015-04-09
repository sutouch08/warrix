<!-- Fixed navbar start -->
<div class="navbar navbar-tshop navbar-fixed-top megamenu" role="navigation">
  <div class="navbar-top">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-sm-6 col-xs-6 col-md-6">
        
        <div class="pull-left ">
            <ul class="userMenu ">
              <li> <a href="#"> <span class="hidden-xs">HELP</span><i class="glyphicon glyphicon-info-sign hide visible-xs "></i> </a> </li>
              <li class="phone-number"> 
              <a  href="callto:<?php echo $company->phone; ?> "> 
              <span> <i class="glyphicon glyphicon-phone-alt "></i></span> 
              <span class="hidden-xs" style="margin-left:5px"> <?php echo $company->phone; ?> </span> </a> </li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-xs-6 col-md-6 no-margin no-padding">
          <div class="pull-right">
            <ul class="userMenu">
             
              <?php if(!isset($_COOKIE['id_customer'])){ echo" 
			 
              <li> <a href='#'  data-toggle='modal' data-target='#ModalLogin'> <span class='hidden-xs'>เข้าระบบ (Login)</span> <i class='glyphicon glyphicon-log-in hide visible-xs '></i> </a> </li>";}else{ echo"
			   <li> <a href='index.php?content=account'><span class='hidden-xs'> บัญชีของฉัน (My Account)</span><i class='glyphicon glyphicon-user hide visible-xs '></i></a> </li>
			  <li> <a href='index.php?customer_logout=true' > <span class='hidden-xs'>ออกจากระบบ (Logout)</span> <i class='glyphicon glyphicon-log-in hide visible-xs '></i> </a> </li>";} ?>
              <?php if(!isset($_COOKIE['id_customer'])){ echo"<li class='hidden-xs'> <a href='#'  data-toggle='modal' data-target='#ModalSignup' > สมัครสมาชิก (Register)</a> </li>";} 
			  if(isset($_COOKIE['id_customer'])){
				$id_customer = $_COOKIE['id_customer'];
			  }else{
				$id_customer = "";
			  }
				?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/.navbar-top-->
  <?php include "minicart.php"; ?>
 
  <div class="container">
      <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only"> Toggle navigation </span> <span class="icon-bar"> </span> <span class="icon-bar"> </span> <span class="icon-bar"> </span> </button>
    <?php $cart_mini->total_for_mobile($id_cart,$id_customer);?>
      <a class="navbar-brand " href="index.php"> <img src="images/logo.png"> </a> 
      
      <!-- this part for mobile -->
      <div class="search-box pull-right hidden-lg hidden-md hidden-sm">
        <div class="input-group">
          <button class="btn btn-nobg" type="button"> <i class="fa fa-search"> </i> </button>
        </div> 
        <!-- /input-group --> 
        
      </div>
    </div><?php  $cart_mini->cartmini_for_mobile($id_cart,$id_customer);?>

	
  <!--------------------- แสดงเมนู ------------------->    
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li> <a href="index.php"> Home </a> </li>
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
					<li><a href='index.php?content=category&id_category=$id_category' style='display:block;'>".strtoupper($category_name)."</a></li>";
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo"<li><a href='index.php?content=category&id_category=$id_sub_category' style='display:block;'>".strtoupper($sub_category_name)."</a></li>";
				$n++;
				}
				echo "</ul>";
				$i++;
				}
				?>
            </li>
          </ul>
        </li>
      </ul>
      
  <!--------------------- ตะกร้าสินค้า ------------------->  
   <div id="txtHint"><?php  $cart_mini->cartmini($id_cart,$id_customer);?></div>
  <!--------------------- จบตะกร้าสินค้า ------------------->        
        <div class="search-box">
          <div class="input-group">
            <button class="btn btn-nobg" type="button"> <i class="fa fa-search"> </i> </button>
          </div>
          <!-- /input-group --> 
          
        </div>
        <!--/.search-box --> 
      </div>
      <!--/.navbar-nav hidden-xs--> 
    </div>
    <!--/.nav-collapse --> 
    
  </div>
  <!--/.container -->
  
  <div class="search-full text-right"> <a class="pull-right search-close"> <i class=" fa fa-times-circle"> </i> </a>
    <div class="searchInputBox pull-right">
    <form action="index.php?content=category&search" method="post">
      <input type="search" name="search" data-searchurl="search?=" placeholder="ค้นหาสินค้า" class="search-input"  >
      <button class="btn-nobg search-btn" type="submit"> <i class="fa fa-search"> </i> </button>
      </form>
    </div>
  </div>
  <!--/.search-full--> 
  
</div>
<!-- /.Fixed navbar  -->