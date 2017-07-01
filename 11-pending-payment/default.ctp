<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$cakeDescription = 'Direct2Door.lk';
?>
<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?= $cakeDescription ?>:
            <?= $this->fetch('title') ?>
        </title>
        <?= $this->Html->meta('icon') ?>
        <?php 
        $current_cntrl=$this->request->params['controller'];
        if($current_cntrl=="DeliveryNotifications" || $current_cntrl=="SupplierNotifications"){ ?>
        <meta http-equiv="refresh" content="120">
        <?php } ?>


<!-- Bootstrap -->
    <!--<link href="/direct2door.erp/icing/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">-->
    <?= $this->Html->css('/icing/vendors/bootstrap/dist/css/bootstrap.min') ?>
    <!-- Font Awesome -->
    <!--<link href="/direct2door.erp/icing/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">-->
    <?= $this->Html->css('/icing/vendors/font-awesome/css/font-awesome.min') ?>
    <!-- NProgress -->
    <!--<link href="/direct2door.erp/icing/vendors/nprogress/nprogress.css" rel="stylesheet">-->
    <?= $this->Html->css('/icing/vendors/nprogress/nprogress') ?>
    <!-- iCheck -->
    <!--<link href="/direct2door.erp/icing/vendors/iCheck/skins/flat/green.css" rel="stylesheet">-->
    <?= $this->Html->css('/icing/vendors/iCheck/skins/flat/green') ?>
    
    <!-- Animate.css for login page-->
    <!--<link href="/direct2door.erp/icing/vendors/animate.css/animate.min.css" rel="stylesheet">-->
    <?= $this->Html->css('/icing/vendors/animate.css/animate.min') ?>



<?php /*?>
        <?= $this->Html->css('select2.min') ?>
         <?= $this->Html->css('custom')?><?php */?>
             <!-- Select2 -->
    <!--<link href="/direct2door.erp/icing/vendors/select2/dist/css/select2.min.css" rel="stylesheet">-->
    <?= $this->Html->css('/icing/vendors/select2/dist/css/select2.min') ?>
    
    <!--bootstrap toggle; radio buttons-->
    	<?= $this->Html->css('bootstrap-toggle.min') ?>
    
        <!-- Custom Theme Style -->
    <!--<link href="/direct2door.erp/icing/build/css/custom.min.css" rel="stylesheet">-->
    <?= $this->Html->css('/icing/build/css/custom.min') ?>
    
    
    <!--date picker styles-->
    <!--<link href="/direct2door.erp/css/bootstrap-datetimepicker.css" rel="stylesheet">-->
    <?= $this->Html->css('bootstrap-datetimepicker') ?>
    
    
        <script type="text/javascript">var myBaseUrl = '<?php echo $this->Url->build('/'); ?>';</script>
        
        <?= $this->Html->script('https://use.fontawesome.com/aeb0ff1754.js');?>

  <?php /*?>      
        <?= $this->Html->script('select2.min') ?><?php */?>
          
        
        

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
    </head>
  <body class="nav-md <?php if(!$authUser){echo 'login';}?>">
    <div class="container body">
      <div class="main_container">
       <?php if ($authUser): ?>
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?php echo $this->Url->build('/customers/search'); ?>" class="site_title"><span>Direct2Door.lk ERP</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <?=$this->Html->image('/icing/production/images/img.jpg',['class'=>'img-circle profile_img', 'alt'=>'...'])?>
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?php echo $userName;?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <!--<h3>General</h3>-->
                <!--<ul class="nav side-menu">
                  <li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="index.html">Dashboard</a></li>
                      <li><a href="index2.html">Dashboard2</a></li>
                      <li><a href="index3.html">Dashboard3</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-edit"></i> Forms <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="form.html">General Form</a></li>
                      <li><a href="form_advanced.html">Advanced Components</a></li>
                      <li><a href="form_validation.html">Form Validation</a></li>
                      <li><a href="form_wizards.html">Form Wizard</a></li>
                      <li><a href="form_upload.html">Form Upload</a></li>
                      <li><a href="form_buttons.html">Form Buttons</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-desktop"></i> UI Elements <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="general_elements.html">General Elements</a></li>
                      <li><a href="media_gallery.html">Media Gallery</a></li>
                      <li><a href="typography.html">Typography</a></li>
                      <li><a href="icons.html">Icons</a></li>
                      <li><a href="glyphicons.html">Glyphicons</a></li>
                      <li><a href="widgets.html">Widgets</a></li>
                      <li><a href="invoice.html">Invoice</a></li>
                      <li><a href="inbox.html">Inbox</a></li>
                      <li><a href="calendar.html">Calendar</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-table"></i> Tables <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="tables.html">Tables</a></li>
                      <li><a href="tables_dynamic.html">Table Dynamic</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-bar-chart-o"></i> Data Presentation <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="chartjs.html">Chart JS</a></li>
                      <li><a href="chartjs2.html">Chart JS2</a></li>
                      <li><a href="morisjs.html">Moris JS</a></li>
                      <li><a href="echarts.html">ECharts</a></li>
                      <li><a href="other_charts.html">Other Charts</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-clone"></i>Layouts <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="fixed_sidebar.html">Fixed Sidebar</a></li>
                      <li><a href="fixed_footer.html">Fixed Footer</a></li>
                    </ul>
                  </li>
                </ul>
                -->
              </div>
              <div class="menu_section">
                <!--<h3>Live On</h3>-->
                <ul class="nav side-menu">
                <?php if($userLevel==3){?>
                 <li><a href="<?php echo $this->Url->build('/supplier-notifications/listnotifications'); ?>"><i class="fa fa-home"></i>Notifications</a></li>
                 <li><a href="<?php echo $this->Url->build('/supplier-notifications/schedule'); ?>"><i class="fa fa-bicycle"></i>Order Schedule</a></li>
                                  
                <?php }else if($userLevel==4){?>
                 <li><a href="<?php echo $this->Url->build('/delivery-notifications/listnotifications'); ?>"><i class="fa fa-home"></i>Notifications</a></li>
                 <li><a href="<?php echo $this->Url->build('/delivery-notifications/schedule'); ?>"><i class="fa fa-truck"></i>Schedule to Deliver</a></li>
                 <li><a href="<?php echo $this->Url->build('/delivery-notifications/list-suppliervice'); ?>"><i class="fa fa-upload"></i>Schedule to Pick Products</a></li>
                <?php   }else{ ?> 
                
                 <li>
                 <?php if($userLevel==1 || $userLevel==2):?>
                 <a href="<?php echo $this->Url->build('/customers/search'); ?>"><i class="fa fa-home"></i>Dashboard</a>
                 <?php else:?>
                 	<a href="<?php echo $this->Url->build('/index'); ?>"><i class="fa fa-home"></i>Dashboard</a>
                 <?php endif;?>	
                 	</li>
                 
                 <li><a href="<?php echo $this->Url->build('/products/index'); ?>"><i class="fa fa-bitbucket"></i>Products</a></li>
                 <li><a href="<?php echo $this->Url->build('/package-type/index'); ?>"><i class="fa fa-table"></i>Packages</a></li>
                 <li><a href="<?php echo $this->Url->build('/orders/index'); ?>"><i class="fa fa-suitcase"></i>Orders</a></li>
                 <?php if($userLevel==2):?>
                 <li><a href="<?php echo $this->Url->build('/orders/schedule'); ?>"><i class="fa fa-bicycle"></i>Order Schedule</a></li>
                 <?php endif;?>
                 <?php if($userLevel==1):?>
                <li><a href="<?php echo $this->Url->build('/callcenter/index'); ?>"><i class="fa fa-phone-square"></i>Call Centre Staff</a></li>   
                <li><a href="<?php echo $this->Url->build('/suppliers/index'); ?>"><i class="fa fa-wheelchair"></i>Suppliers</a></li>   
				<li><a href="<?php echo $this->Url->build('/delivery/index'); ?>"><i class="fa fa-truck"></i>Delivery Staff</a></li>
				<?php endif; ?>
				<li><a href="<?php echo $this->Url->build('/customers/index'); ?>"><i class="fa fa-group"></i>Customers</a></li>
				<?php if($userLevel==1):?>
				<li><a href="<?php echo $this->Url->build('/users/index'); ?>"><i class="fa fa-user"></i>Users</a></li>
				
				<li><a href="<?php echo $this->Url->build('/reports/index'); ?>"><i class="fa fa-file-text-o"></i>Reports</a></li>
				<?php endif; ?>
				<?php if($userLevel==2 || $userLevel==1 ):?>
				<li><a href="<?php echo $this->Url->build('/customers/search'); ?>"><i class="fa fa-search"></i>Search Customers</a></li>  
				<?php endif;?>                  
                <!--
                  <li><a><i class="fa fa-bug"></i> Additional Pages <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="e_commerce.html">E-commerce</a></li>
                      <li><a href="projects.html">Projects</a></li>
                      <li><a href="project_detail.html">Project Detail</a></li>
                      <li><a href="contacts.html">Contacts</a></li>
                      <li><a href="profile.html">Profile</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-windows"></i> Extras <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="page_403.html">403 Error</a></li>
                      <li><a href="page_404.html">404 Error</a></li>
                      <li><a href="page_500.html">500 Error</a></li>
                      <li><a href="plain_page.html">Plain Page</a></li>
                      <li><a href="login.html">Login Page</a></li>
                      <li><a href="pricing_tables.html">Pricing Tables</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-sitemap"></i> Multilevel Menu <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="#level1_1">Level One</a>
                        <li><a>Level One<span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li class="sub_menu"><a href="level2.html">Level Two</a>
                            </li>
                            <li><a href="#level2_1">Level Two</a>
                            </li>
                            <li><a href="#level2_2">Level Two</a>
                            </li>
                          </ul>
                        </li>
                        <li><a href="#level1_2">Level One</a>
                        </li>
                    </ul>
                  </li>                  
                  <li><a href="javascript:void(0)"><i class="fa fa-laptop"></i> Landing Page <span class="label label-success pull-right">Coming Soon</span></a></li>
                -->
                <?php }?>
                </ul>
               
              </div>

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <!--<div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>-->
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>

              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <!--<img src="/direct2door.erp/icing/production/images/img.jpg" alt="">-->
                    <?=$this->Html->image('/icing/production/images/img.jpg')?>
                    <?php echo $userName;?>
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <!--<li><a href="javascript:;"> Profile</a></li>
                    <li>
                      <a href="javascript:;">
                        <span class="badge bg-red pull-right">50%</span>
                        <span>Settings</span>
                      </a>
                    </li>
                    <li><a href="javascript:;">Help</a></li>-->
                    <li><a href="<?php echo $this->Url->build('/users/logout'); ?>"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                <li role="presentation" class="dropdown" id="notify">
                  <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green"><?php echo $notificationCount; ?></span>
                  </a>
                  <ul id="menu1" class="dropdown-menu list-unstyled msg_list notification-ul" role="menu">
                  <!---->
                    <?php foreach($notificationContent as $notification){
                    //print_r($notification);
                    ?>
                    <li id="<?= $notification->id ?>" class="notify-seen">
                      <a href="<?php echo $this->url->build('/user-notifications/genarateUrl/'.$notification->id.'/'.$userLevel.'/'.$userId);?>">
                      <!--
                      ex: supplier: http://localhost/direct2door.erp/supplier-notifications/edit/233[notifyid]
                      	  delivery: http://localhost/direct2door.erp/delivery-notifications/edit/x[notifyid]
                      	  callcenter: http://localhost/direct2door.erp/orders/view/179[orderid]
                      	  			  
                      -->
                        <!--<span class="image"><img src="images/img.jpg" alt="Profile Image" /></span>-->
                        <span>
                          <!--<span>user name</span>-->
                          <span><?php echo 'Order ID: '.$notification->orderId; ?></span>
                          <span class="time"><?php echo $notification->created;?></span>
                        </span>
                        <span class="message">
                          <?php echo $notification->notification;?>
                          <span class="see-sow"></span>
                        </span>
                       
                      </a>
                    </li>                   
                    <?php }?>
                    
                    <li>
                      <div class="text-center">
                        <a href="<?php echo $this->Url->build('/user-notifications/mynotifications/'.$userId); ?>";>
                          <strong>See All Notifications</strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                    <!---->
                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <?php endif; ?>
        <?php if(!$authUser){?>
         <?= $this->Flash->render() ?>
        <?php }?> 
        <!-- /top navigation -->
		<!-- /top navigation -->
		
        <!-- page content -->
        <div class="<?php if($authUser){echo 'right_col';}?>" role="main">
 
    <!--<div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>
                    Plain Page <small>Page subtile </small>
                </h3>
            </div>
 
            <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                    <div class="input-group">
                        <input class="form-control" placeholder="Search for..." type="text">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Go!</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
 -->
        <div class="row">
            <div class="col-md-12">
                <div class="x_content">
                        <!-- content starts here -->
        <?php if($authUser){?>
         <?= $this->Flash->render() ?>
        <?php }?>                         
 <?= $this->fetch('content') ?>
                        <!-- content ends here -->
                    </div>
            </div>
        </div>
    </div>
</div>
        
        
        

<!-- footer content -->
<?php if ($authUser): ?>
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
<?php endif; ?>        
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <!--<script src="/direct2door.erp/icing/vendors/jquery/dist/jquery.min.js"></script>-->
    <?= $this->Html->script('/icing/vendors/jquery/dist/jquery.min'); ?>
    <!-- Bootstrap -->
    <!--<script src="/direct2door.erp/icing/vendors/bootstrap/dist/js/bootstrap.min.js"></script>-->
    <?= $this->Html->script('/icing/vendors/bootstrap/dist/js/bootstrap.min'); ?>
    <!-- FastClick -->
    <!--<script src="/direct2door.erp/icing/vendors/fastclick/lib/fastclick.js"></script>-->
    <?= $this->Html->script('/icing/vendors/fastclick/lib/fastclick'); ?>
    <!-- NProgress -->
    <!--<script src="/direct2door.erp/icing/vendors/nprogress/nprogress.js"></script>-->
    <?= $this->Html->script('/icing/vendors/nprogress/nprogress'); ?>
    <!-- iCheck -->
    <!--<script src="/direct2door.erp/icing/vendors/iCheck/icheck.min.js"></script>-->
    <?= $this->Html->script('/icing/vendors/iCheck/icheck.min'); ?>

  <!-- Select2 -->
    <!--<script src="/direct2door.erp/icing/vendors/select2/dist/js/select2.full.min.js"></script>-->
    <?= $this->Html->script('/icing/vendors/select2/dist/js/select2.full.min'); ?>
   
   <!--date picker js-->
   <!--<script src="/direct2door.erp/js/bootstrap-datetimepicker.js"></script>-->
   		<?= $this->Html->script('bootstrap-datetimepicker'); ?>
   
        <?= $this->Html->script('customjs') ?>
        
        
    <!-- Custom Theme Scripts -->
    <!--<script src="/direct2door.erp/icing/build/js/custom.min.js"></script>-->
    		<?= $this->Html->script('/icing/build/js/custom.min'); ?>

	<!--Bootstrap Toggle radio-->  
	  		<?= $this->Html->script('bootstrap-toggle.min'); ?>
    
    <script type="text/javascript">
  $('select').select2({tags: true});
  $(".js-example-basic-multiple").select2();
  
  
  	$('.form_date').datetimepicker({
        language:  'en',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
    	$('.form_time').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 1,
		minView: 0,
		maxView: 1,
		forceParse: 0
    });
	
	$('#customer_created_date').datetimepicker({
		format: "dd MM yyyy - hh:ii",
        language:  'en',
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1

    });
</script>
<script type="text/javascript">
/*http://www.bootstraptoggle.com/*/
$(function() {
    $('.tog.supp').change(function() {
    if($(this).prop('checked')){
    	
      $(this).parent().next().val(1);
            }
      else{
      
      $(this).parent().next().val(2);
      
      }
    })
  })
$(function() {
    $('.tog.suppvice').change(function() {
    if($(this).prop('checked')){
    	
      $(this).parent().next().val(1);
            }
      else{
      
      $(this).parent().next().val(0);
      
      }
    })
  })  
  $(function() {
    $('.tog.del').change(function() {
    if($(this).prop('checked')){
    	
      $(this).parent().next().val(4);
            }
      else{
      
      $(this).parent().next().val(5);
      
      }
    })
  })  
  
</script>
<script>
$(function() {
/*
delivery-notifications/edit/ page select all unselect all
*/
	//$('select.pending-radio-dropdown').attr('disabled','disabled');
	$('select.pending-radio-dropdown').select2('destroy');
	
	

	$('.pending-radio-all').change(function() {		
	   if($(this).prop("checked") == true){
		$('.pending-radio').prop('checked', true);		
		}else{
		$('.pending-radio').prop('checked', false);		
		}		
	})
	
	$('.pending-radio-submit').click(function(){
		var order_id=$(this).parent().parent().find('.pending-radio').val();
		var payment_method=$(this).parent().parent().find('#payment_type_pending'+order_id).val();
		var paied_order=[{id:order_id, paymentStatus:payment_method}];	
		updatePendingPayment(paied_order);
	});
	$('#pending-radio-all-submit').click(function(){		
		var paied_order=[];	
		$("input[type=checkbox].pending-radio:checked").each(function(){
		  var order_id=$(this).val();
		  var payment_method =$(this).parent().parent().find('#payment_type_pending'+order_id).val();		  
		  paied_order.push({id: order_id,  paymentStatus: payment_method});		  
	    });
		 updatePendingPayment(paied_order);
	});
	
	function updatePendingPayment(orders_data){
		$.post(myBaseUrl+"delivery-notifications/paypendinpayments",
	    	    {orders:orders_data},
	    	    function(data, status){
	    	    	 location.reload(true);   	   	    	    		    	    	
	    	    });
	}
	
	
	$("#pending-radio-dropdown-all").change(function() { //this occurs when select 1 changes
		$(".pending-radio-dropdown").val($(this).val());  
	});
})

</script>
  </body>
</html>
