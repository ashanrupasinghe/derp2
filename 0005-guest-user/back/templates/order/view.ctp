<?php
 $status=['1'=>'pending','2'=>'supplier informed','3'=>'products ready','4'=>'delivery tookover','5'=>'delivered','6'=>'completed',7=>'driver informed','9'=>'canceled'];
$payment_status=['1'=>'pending','2'=>'cash','3'=>'card', '4'=>'credit'];//2 was paid
$sup_status=[0=>'pending',1=>'available',2=>'not available',9=>'canceled'];
$pm=[1=>'Cash On Delivery',2=>'Card',3=>'Online'];
?>

<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('View Order') ?> <small><?= __('Order ID: '.$order->id) ?></small></h2>
                    <ul class="nav navbar-right panel_toolbox">                      
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
            <td>
			
			
			<?php
					if(sizeof($order->customer) > 0){
				?>
				<?=  $this->Html->link($order->customer->firstName.' '.$order->customer->lastName, ['controller' => 'Customers', 'action' => 'view', $order->customerId]) ?>
				<?php
				}elseif(sizeof($order->shipping) > 0 && $order->shipping->guest_name!=null){
				echo  $order->shipping->guest_name;
				}else{
				echo '-';
				}
				?>
			
			</td>
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
            <th><?= __('Phone') ?></th>
            <td>
			<?php if(isset($order->shipping) && $order->shipping->phone_number !=null): ?>
			<?=  h($order->shipping->phone_number) ?>
			<?php elseif( $order->customer->mobileNo!=null):?>
			<?=  h($order->customer->mobileNo)?>
			<?php else: ?>			
			<?=  h('-')?>
			<?php endif;?>
			</td>
        </tr>
		<tr>
            <th><?= __('Email') ?></th>
            <td>
			<?php
			if(sizeof($order->customer) > 0 && $order->customer->email!=null){
			?>
			<?=   h($order->customer->email) ?></td>
			<?php
			}elseif(sizeof($order->shipping) >0 && $order->shipping->email!=null){
				echo $order->shipping->email;
			}else{
				echo '-';
			}
			?>
			</td>
			
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
            <th><?= __('Created') ?></th>
            <td><?= h($order->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Delivery Date') ?></th>
            <td><?= $this->Time->format($order->delivery_date_time) ?></td>
        </tr>
        <tr>
            <th><?= __('Delivery Note') ?></th>
            <td><?= h($order->note) ?></td>
        </tr>
        


    </table>
    </div>
	
	<div class="orders col-lg-6 col-md-6 col-sm-12 columns content div-top-pad-0 div-left-pad-0">    
    <table class="table table-hover">
		<tr>
            <th><?= __('SubTotal') ?><small>&#40;<?= __('ordered time') ?>&#41;</small></th>
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
            <th><?= __('Total') ?> <small> &#40;<?= __('ordered time') ?>&#41;</small> </th>
            <td><?= $this->Number->currency($order->total,'LKR') ?></td>
        </tr>
         <tr>
         <?php if($order->status<3){?>
               
            <th>&nbsp;</th>
            <td></td>
         <?php }else{?>   
            <th><?= __('Total') ?> <small> &#40;<?= __('available products') ?>&#41;</small> </th>
            <td><?= $this->Number->currency($total_pdf['available'],'LKR') ?></td>
         <?php }?>   
        </tr>
        <tr>
            <th ><?= __('Order Status') ?></th>
            <td><?= h($status[$order->status]) ?></td>
        </tr>
        <tr>
            <th><?= __('PaymentStatus') ?></th>
            <td><?= h($payment_status[$order->paymentStatus]) ?></td>
        </tr>
		<tr>
            <th><?= __('Payment method') ?></th>
            <td><?= h($pm[$order->payment_method]) ?></td>
        </tr>
		</table>
	</div>
	</div>
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
