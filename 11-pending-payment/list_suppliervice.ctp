
<?php
//$status=['0'=>'pending', '1'=>'took all', '2'=>'delevered','9'=>'canceled'];
//$status=['1'=>'pending','2'=>'supplier informed','3'=>'products ready','4'=>'delivery tookover','5'=>'delivered','6'=>'completed'];
$status=['1'=>'pending','2'=>'supplier informed','3'=>'products ready','4'=>'delivery tookover','5'=>'delivered','6'=>'completed',7=>'driver informed', '9'=>'canceled'];

?>
<?php
/*
print '<pre>';
foreach ($deliveryNotifications as $deliveryNotification):
print_r($deliveryNotification->order->supplier_notifications);
endforeach;
die();
*/
?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Supplier Vice Delivery Notifications') ?> <small><?= __('Supplier vice delivery notification list &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#91;Now: '.$dateNow.' '.$timeNow.'&#93;') ?></small></h2>
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
                
                <th><?= $this->Paginator->sort('orderId') ?></th>
                <th><?= $this->Paginator->sort('Orders.delivery_date_time','Delivery Date') ?></th>     
                <th>Available Total</th>
                <!--<th><?= __('Ready') ?></th>-->
                <!--<th><?= $this->Paginator->sort('Orders.status','Order Status') ?></th>-->
                                <th>Customer Name</th>
                <th>Customer Address</th>
                <th>Supplier</th>
                <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                         <?php 
                         foreach ($deliveryNotifications as $deliveryNotification):
                         foreach ($deliveryNotification->order->supplier_notifications as $suplier):                         
                         if($suplier->delivery_status>0){//if this products currently piked, not show
            
            ?>
            <tr style="color:<?=$deliveryNotification['order']->row_color_delivery?>">
                <td><?= $this->Number->format($deliveryNotification->orderId) ?></td>
                <td><?= $this->Time->format($deliveryNotification['order']->delivery_date_time) ?></td>
                
                <?php 
                $availableAmmount=0;
                $finalval=0;
                if(!empty($deliveryNotification['order']->order_products)){                	
                	$discount=$deliveryNotification['order']->discount;
                	foreach($deliveryNotification['order']->order_products as $product){
                		$availableAmmount+=$product['product_quantity']*$product['product_price'];
                	}
                	
                	$finalval=$availableAmmount-$discount;
                }
                ?>
                <td><?= $this->Number->currency($finalval,'LKR') ?></td>
                <?php
                $customer_name=$deliveryNotification['order']['customer']->firstName. ' '.$deliveryNotification['order']['customer']->lastName ;
                ?>
                <td><?= h($customer_name) ?></td>
                 <td><?= h($deliveryNotification['order']['customer']->address ) ?></td>
                 <?php
                 $supplier_name=$suplier->supplier->firstName.' '.$suplier->supplier->lastName;?>
                <td><?=$supplier_name?></td>
                <td class="actions">                    
                    <?= $this->Html->link(__('Edit'), ['action' => 'editSuppliervice', $deliveryNotification->id,$suplier->supplierId],['class'=>'x-btn x-btn-warning btn btn-warning btn-xs']) ?>
                    <?= $this->Form->postLink(__('Picked'), ['action' => 'picked', $suplier->supplierId,$deliveryNotification->orderId],['confirm' => __('Products PICKED from {0}?', $supplier_name),'class'=>'x-btn x-btn-danger btn btn-success btn-xs']) ?>
                </td>
            </tr>
            <?php 
            }
            endforeach; ?>
            <?php endforeach; ?>
                        
                      </tbody>
                    </table>
<div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>                    
                  </div>
                </div>
</div>

