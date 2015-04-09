
<?php
if (!defined('WEB_ROOT')) {
	exit;
}

$self = WEB_ROOT . 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title><?php echo $pageTitle ?></title>

    <!-- Bootstrap core CSS -->
    <link href="../library/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../library/css/navbar-static-top.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <!-- Static navbar -->
    <div class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
       <!-- <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Project name</a>
        </div> -->
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-folder-open"></span>&nbsp; สินค้า</a>
              <ul class="dropdown-menu">
                <li><a href="#"><span class="glyphicon glyphicon-tasks"></span>&nbsp;เพิ่ม/แก้ไข สินค้า</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-bookmark"></span>&nbsp;เพิ่ม/แก้ไข หมวดหมู่</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-tint"></span>&nbsp;เพิ่ม/แก้ไข สี</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-tag"></span>&nbsp;เพิ่ม/แก้ไข ไซด์</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-map-marker"></span>&nbsp;เพิ่ม/แก้ไข โซนสินค้า</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-home"></span>&nbsp;เพิ่ม/แก้ไข คลังสินค้า</a></li>
              </ul>
            </li>
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-home"></span>&nbsp;คลังสินค้า</a>
              <ul class="dropdown-menu">
                <li><a href="#"><span class="glyphicon glyphicon-import"></span>&nbsp;รับสินค้าเข้า</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-export"></span>&nbsp;จ่ายสินค้าออก</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-transfer"></span>&nbsp;ย้ายพื้นที่จัดเก็บ</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-check"></span>&nbsp;ตรวจนับสินค้า</a></li>
              </ul>
            </li>
         
          <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;ออเดอร์</a>
              <ul class="dropdown-menu">
                <li><a href="#"><span class="glyphicon glyphicon-th-list"></span>&nbsp;ออเดอร์</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-inbox"></span>&nbsp;จัดสินค้า</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;ตรวจสินค้า</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;รายการเปิดบิล</a></li>
              </ul>
            </li>
          </ul>
          <p class="navbar-text navbar-right"><a href="<?php echo "index.php?logout"; ?>" class="navbar-link">Sign Out</a></p>
          <p class="navbar-text navbar-right">Sign in as <?php echo $_COOKIE['UserName']; ?></p>
        </div><!--/.nav-collapse -->
      </div>
    </div>
<div class="starter-template">
  <?php
			include $content;	 
		?>
</div>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  <script src="../library/js/jquery.min.js"></script>
    <script src="../library/js/bootstrap.min.js"></script>
  </body>
</html>
