<?php
 $status=['1'=>'pending','2'=>'supplier informed','3'=>'products ready','4'=>'delivery tookover','5'=>'delivered','6'=>'completed',7=>'driver informed','9'=>'canceled'];
//$payment_status=['1'=>'pending','2'=>'paid'];
$payment_status=['1'=>'pending','2'=>'cash','3'=>'card', '4'=>'credit'];//2 was paid
$sup_status=[0=>'pending',1=>'available',2=>'not available',9=>'canceled'];
?>

<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('View Order') ?> <small><?= __('Order ID: '.$order->id) ?></small></h2>
                    <ul class="nav navbar-right panel_toolbox">  
                   		<li><?= $this->Html->link(__('Excel Invoice'), ['controller' => 'Orders', 'action' => 'getInvoice',$order->id]); ?></li>                    
                      <li><?= $this->Html->link('Print Invoice', ['controller' => 'Orders', 'action' => 'view', $order->id.'.pdf'])?></li>
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
				<div class="row">  
  			  <div class="orders col-lg-6 col-md-6 col-sm-12 columns content div-top-pad-0 div-left-pad-0">    
    <table class="table table-hover">
      
        
        <tr>
            <th class="td-40"><?= __('Customer Name') ?></th>
            <td><?= $this->Html->link($order->customer->firstName.' '.$order->customer->lastName, ['controller' => 'Customers', 'action' => 'view', $order->customerId])?></td>
        </tr>
        <tr>
            <th><?= __('Address') ?></th>
            <td><?= h($order->address) ?></td>
        </tr>
  <!--      <tr>
            <th><?= __('Latitude') ?></th>
            <td><?= h($order->latitude) ?></td>
        </tr>
        <tr>
            <th><?= __('Longitude') ?></th>
            <td><?= h($order->longitude) ?></td>
        </tr>-->
        <tr>
            <th><?= __('City') ?></th>
            <td><?= h($order->cid->cname) ?></td>
        </tr>
		<tr>
            <th><?= __('Email') ?></th>
            <td><?= h($order->cid->cname) ?></td>
        </tr>
		<tr>
            <th><?= __('Phone') ?></th>
            <td><?= h($order->cid->cname) ?></td>
        </tr>
        <tr>
            <th><?= __('Callcenter Name') ?></th>
            <?php $call_name=$order->callcenter->firstName.' '.$order->callcenter->lastName;?>
            <td><?= $this->Html->link($call_name, ['controller' => 'Callcenter', 'action' => 'view', $order->callcenterId])?></td>

        </tr>
        <tr>
            <th><?= __('Delivery name') ?></th>
            <?php $delivery_name=$order->delivery->firstName.' '.$order->delivery->lastName; ?>
            <td><?= $this->Html->link($delivery_name, ['controller' => 'Delivery', 'action' => 'view', $order->deliveryId])?></td>
        </tr>                
          <tr>
            <th><?= __('Delivery Note') ?></th>
            <td><?= h($order->note) ?></td>
        </tr>
        <tr>
            <th><?= __('Supplier Note') ?></th>
            <td><?= h($order->supplier_note) ?></td>
        </tr>
        
        <tr>
            <th>&nbsp;</th>
            <td></td>
        </tr>


    </table>
    </div>
	
	<div class="orders col-lg-6 col-md-6 col-sm-12 columns content div-top-pad-0 div-left-pad-0">    
    <table class="table table-hover">
		<tr>
            <th><?= __('SubTotal') ?><small> &#40;<?= __('ordered time') ?>&#41;</small></th>
            <td><?= $this->Number->currency($order->subTotal,'LKR') ?></td>
        </tr>
        <tr>
            <th><?= __('Tax') ?></th>
            <td><?= $this->Number->currency($order->tax,'LKR') ?></td>
        </tr>
        <tr>
            <th><?= __('Discount') ?></th>
            <td><?= $this->Number->currency($order->discount,'LKR') ?></td>
        </tr>
        <tr>
            <th><?= __('CouponCode') ?></th>
            <td><?= h($order->couponCode) ?></td>
        </tr>
        <tr>
            <th><?= __('Total') ?><small> &#40;<?= __('ordered time') ?>&#41;</small></th>
            <td><?= $this->Number->currency($order->total,'LKR') ?></td>
        </tr>
         
                <tr>
                <?php if($order->status<3){?>                
            	<th>&nbsp;</th>
            	<td></td>
         <?php }else{?>   
            <th><?= __('Total') ?> <small>&#40;<?= __('available products') ?>&#41;</small> </th>
            <td><?= $this->Number->currency($total_pdf['available'],'LKR') ?></td>
         <?php }?> 
        </tr>
        <tr>
            <th><?= __('Delivery Date') ?></th>
            <td><?= $this->Time->format($order->delivery_date_time) ?></td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td></td>
        </tr>
        
		</table>
	</div>
	
	
	
	
	
	</div>
	<!--  -->
	<div class="row">
	<div class="orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
		
		
		<table class="table table-hover">
		<tr>
            <td class="td-40">
             <h2><?= __('Send Notifications:') ?></h2>
            </td>
            <td></td>			
        </tr>        
		</table>
		
	</div>
	<div class="orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
		<?= $this->Form->create($order, ['url' => ['action' => 'sendsuppliernotifications'],'type'=>'post']); ?>
		
		<table class="table table-hover">
		<tr>
            <th class="td-40"><?= __('Notify to Suppliers') ?></th>
            <td>           
			<input type="hidden" value="<?php echo $order->id;?>" name="order_id">
			<?php 
            //nofified variable come from update method, checking nptification table, pending development
            if($notified_sup){
            	$nofification_text="notified";
            	$disabled=true;
            }else{
            	$nofification_text="send";
            	$disabled=false;
            }?>
			<?php ?>
            
			<?= $this->Form->button(__($nofification_text),['style'=>'float: right;','class'=>'btn btn-warning', 'disabled'=>$disabled]) ?>
			<?php ?>
			</td>
			
        </tr>
        
		</table>
		<?= $this->Form->end() ?>
	</div>
	<div class="orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
		<?= $this->Form->create($order, ['url' => ['action' => 'sendemails'],'type'=>'post']); ?>
		
		<table class="table table-hover">
		<tr>
            <th class="td-40"><?= __('Admin/Customer email') ?></th>
            <td>           
			<input type="hidden" value="<?php echo $order->id;?>" name="order_id">
			<?php 
            //nofified variable come from update method, checking nptification table, pending development
            if($notified_email){
            	$nofification_text="notified";
            	$disabled=true;
            }else{
            	$nofification_text="send";
            	$disabled=false;
            }?>
			<?php ?>
            
			<?= $this->Form->button(__($nofification_text),['style'=>'float: right;','class'=>'btn btn-warning', 'disabled'=>$disabled]) ?>
			<?php ?>
			</td>
			
        </tr>
        
		</table>
		<?= $this->Form->end() ?>
	</div>
	
	</div>
	
	<div class="row">
	<?= $this->Form->create($order, ['url' => ['action' => 'notify2']]); ?>
	<div class="orders col-lg-8 col-md-8 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
		
		
		<table class="table table-hover">
		<tr>
            <th class="td-40"><?= __('Notify to Delivery Staff') ?></th>
            <td>
           <?php echo $this->Form->input('deliveryId',['label' => false,'class'=>'form-control','options'=>$deliveries,'default'=>$order->deliveryId]); ?>
			<input type="hidden" value="<?php echo $order->id;?>" name="order_id">
			</td>
			
        </tr>
        
		</table>
	</div>
	<div class="orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
		<table class="table table-hover">
		<tr>
            
            <td>
            <?php 
            //nofified variable come from update method, checking nptification table, pending development
            if($notified){
            	$nofification_text="notified";
            	$disabled=true;
            }else{
            	$nofification_text="send deliver notification";
            	$disabled=false;
            }?>
			<?php ?>
            
			<?= $this->Form->button(__($nofification_text),['style'=>'float: right;','class'=>'btn btn-warning', 'disabled'=>$disabled]) ?>
			<?php ?>
			</td>
			<td></td>
			
        </tr>
        
		</table>
		
	</div>
	<?= $this->Form->end() ?>
	</div>
	
	<div class="row">
	<?= $this->Form->create($order,['id'=>'order']) ?>
	<div class="orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
		<table class="table table-hover">
				<tr>
            <th ><?= __('Order Status') ?></th>
            <td>
            <?php echo $this->Form->input('status',['label' => false,'class'=>'form-control','options'=>$status,'empty'=>'select status','default'=>$order->status]); ?>
            <!--<?= h($status[$order->status]) ?>-->
            </td>
        </tr>
        
		</table>
	</div>
	
	<div class="orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
			<table class="table table-hover">
		<tr>
            <th><?= __('PaymentStatus') ?></th>
            <td>
            <?php echo $this->Form->input('paymentStatus',['label' => false,'class'=>'form-control','options'=>$payment_status,'empty'=>'select status','default'=>$order->paymentStatus]); ?>
            <!--<?= h($payment_status[$order->paymentStatus]) ?>-->
            </td>
        </tr>
		</table>
	</div>
	<div class="orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
			<table class="table table-hover">
		<tr>
            
            <td>
            <?= $this->Form->button(__('Submit'),['class'=>'btn btn-success','style'=>'float: right;']) ?>
            </td>
            <td></td>
        </tr>
		</table>
	</div>
	
	<?= $this->Form->end() ?>
	</div>
	<!--  -->
                </div>
 </div>
 </div>
 <!---->
 <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Order Products Details') ?> <small><?= __('Order ID: '.$order->id) ?></small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
  			  		<table class="table table-hover">
                      <thead>
                        <tr>
                        <th><?= __('Status') ?></th>
                          <th><?= __('Name') ?></th>
                <th><?= __('Quantity') ?></th>
                <th><?= __('Package') ?></th>
                <th><?= __('Supplier') ?></th>
                <th><?= __('Address') ?></th>
                <th><?= __('phone') ?></th>
<!--                <th><?= __('status') ?></th>-->
                          
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($order->order_products as $product): 
           
            ?>                       
            <tr>
             <?php
            $status_color="";
            if($product->status_s==1){
            $status_color="#26B99A";
            }elseif($product->status_s==2){
            $status_color="#d9534f";
            }
            ?>
            	<td style="color:<?= $status_color ?>;"><?php echo $sup_status[$product->status_s];?></td>
                <td><?php echo $product['product']->name;?></td>
                <td><?php echo $product['product_quantity']; ?></td>                
                <td><?php echo $product['product']->package_type->type; ?></td>
				
				<td><?php echo $this->Html->link($product['supplier']->firstName.' '.$product['supplier']->lastName, ['controller' => 'Suppliers', 'action' => 'view', $product['supplier']->id])?></td>
				<td><?php echo $product['supplier']->address.'<br><br>'.$product['supplier']->cid->cname; ?></td>
				<td><?php echo $product['supplier']->contactNo.'<br>'.$product['supplier']->mobileNo; ?></td>
                            
            </tr>
            
            <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
<?php if(sizeof($pending_payment_orders)>0):?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Pending Payment for Customer ID: '.$order->customerId) ?> <small><?= __('') ?></small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                          <li><a href="#">Settings 1</a>
                          </li>
                          <li><a href="#">Settings 2</a>
                          </li>
                        </ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">  
  			  		<table class="table table-hover">
						<thead>
							<tr>
								<th><?=' Order Id' ?></th>
								<th><?= 'Total' ?></th>
								<th><?= 'Delivery Date'  ?></th>
								<th><?= 'Created At'  ?></th>
								<th><?= 'Delivery staff'  ?></th>
								<th><?= 'Select' ?>
								<th><?= 'Actions'  ?></th>
								
							</tr>
						</thead>                      
						<tbody>
							<?php foreach ($pending_payment_orders as $order):?>
							<tr>
								<td><?= h($order->id) ?></td>
								<td><?= $this->Number->currency($order->total,'LKR') ?></td>
								<td><?= h($order->delivery_date_time) ?></td>
								<td><?= h($order->created) ?></td>															
								<td><?= h($order->delivery->firstName.' '.$order->delivery->lastName) ?><br>
									<?= h($order->delivery->mobileNo) ?>
								</td>
								<td><input type="checkbox" class="pending-radio" value="<?= $order->id ?>" id="<?= 'payment_type_pending_checkbox'.$order->id ?>"></td>
								<td class="pending-payment-actions">
								<?php echo $this->Form->input('paymentStatus',['label' => false,'class'=>'form-control pending-radio-dropdown', 'id'=>"payment_type_pending".$order->id,'options'=>$payment_status,'empty'=>'select status','default'=>$order->paymentStatus]); ?>								
								</td>								
								<td>
								<input type="button" class="btn btn-success pending-radio-submit" value="Update">								
								</td>
								<?php //echo $this->Html->link(__('View'), ['action' => 'view', $order->delivery_notifications[0]->id],['class'=>'x-btn x-btn-warning btn btn-warning btn-xs']) ?>
								
							</tr>
							<?php  endforeach; ?>
							<tr>
						    	<td colspan="4"></dt>
						    	<th>Bulk Select</dh>
								<td><input type="checkbox" class="pending-radio-all"></td>
								<td>
									<?php echo $this->Form->input('paymentStatus',['label' => false,'class'=>'form-control col-sm-1 pending-radio-dropdown','options'=>$payment_status,'empty'=>'select status','default'=>1,'id'=>'pending-radio-dropdown-all']); ?>
																				
								</dt>
								<td>
								<input type="button" class="btn btn-success" value="Update all" id="pending-radio-all-submit">
								</td>
							</tr>
						</tbody>                    
					</table>
					
					
					
                </div>
				</div>
</div>

<?php endif;?>
                