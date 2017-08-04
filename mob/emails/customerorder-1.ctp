<?php
 $status=['1'=>'pending','2'=>'supplier informed','3'=>'products ready','4'=>'delivery tookover','5'=>'delivered','6'=>'completed',7=>'driver informed','9'=>'canceled'];
$payment_status=['1'=>'pending','2'=>'paid'];
?>

<table class="table">
	<tbody>
		<tr>
			<td style="border-top: 0px;"  colspan="3">
				<img src="<?php echo $this->Url->build('/', true).'images/Direct-2-door-final.png'; ?>" style="height:75px;">
			</td>
		</tr>
		<tr>
			<td  colspan="3">
				<p><?= $order->customer->firstName.' '.$order->customer->lastName?>,</p><p>Thank you for your order from Direct 2 door.</p>
				<p style="margin-bottom:0px;">If you have questions about your order, you can email us at info@direct2door.lk.</p>								
			</td>
		</tr>
		<tr>
		<td style="border-top: 0px;padding-bottom: 0px;" colspan="3">
		<div class="x_title" style="border-bottom: 2px solid #E6E9ED;padding: 1px 5px 6px;margin-bottom: 10px;">
									
					<p style="margin-top: 0px;margin-bottom: 5px;font-size: 24px;font-weight: 500;border-top: 0px;padding-top: 0px;margin-bottom: 3px;">
					<?= __('Order '.'#'.$order->id) ?></p>
				</div>
				<p>Plase on <?= h($order->created) ?></p>
		</td>
		</tr>
		<tr>
		<td style="border-top: 0px;padding-top: 0px;">
			  <p style="font-size: 18px;font-weight: 550;border-top: 0px;padding-top: 0px;margin-bottom: 3px;">Billing Info</p>
  			  <p><?= $order->customer->firstName.' '.$order->customer->lastName?><br><?= h($order->address) ?><br><?= h($order->cid->cname) ?><br>Sri Lanka<br>T: <?= h($order->customer->mobileNo) ?></p>
			  </td>
			<td style="border-top: 0px;padding-top: 0px;">
			  <p style="font-size: 18px;font-weight: 550;border-top: 0px;padding-top: 0px;margin-bottom: 3px;">Payment Method</p>
  			  <p>Cash On Delivery</p>
			  </td>
			<td style="border-top: 0px;padding-top: 0px;">
			  <p style="font-size: 18px;font-weight: 550;border-top: 0px;padding-top: 0px;margin-bottom: 3px;">Shipping Method</p>
  			  <p>Free Shipping - Free</p>
			</td>
		</tr>
		
		
	</tbody>
</table>

			  

<table class="table">
    <thead>
        <tr>
            <th style="border-bottom:0px;"><?= __('Items') ?></th>
            <th style="border-bottom:0px"><?= __('Package') ?></th>
            <th style="border-bottom:0px;text-align:right"><?= __('Qty') ?></th>
            <th style="border-bottom:0px;text-align:right"><?= __('Price') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($order->order_products as $product): ?>                       
        <tr>
            <td style="border-bottom:0px"><b><?php echo $product['product']->name;?></b><!--<br>SKU: --><?php // echo $product['product']->sku; ?></td>
            <td style="border-bottom:0px"><?php echo $product['product']->package_type->type; ?></td>
            <td style="border-bottom:0px;text-align:right"><?php echo $product['product_quantity']; ?></td>                
            <td style="border-bottom:0px;text-align:right"><?php 
            //$item_price= $product['product']->price;
            $item_price= $product['product_price'];
            $item_qty=$product['product_quantity'];
            $item_tota_price=$item_price*$item_qty;
            if($product['status_s']==2){
            $item_tota_price=0;
            }
            echo $this->Number->currency($item_tota_price,'LKR');?>
            
			</td>
        </tr>
    <?php endforeach; ?>
		<tr>
            <td style="border-bottom:0px" colspan=""></td>
            <td style="border-bottom:0px" colspan=""></td>
            <td style="border-bottom:0px;text-align:right" colspan="">
            	Sub Total<br>
            	Shipping & Handling<br>
            	Discount<br>
            	<b>Grand Total</b><br>
            </td>
            <td style="border-bottom:0px;text-align:right">
            <?php echo $this->Number->currency($total_pdf['subtotal'],'LKR') ?><br>
            <?php echo $this->Number->currency(0,'LKR') ?><br>
            <?php echo $this->Number->currency($order->discount,'LKR') ?><br>
            <b><?php echo $this->Number->currency($total_pdf['available'],'LKR') ?></b>
            </td>
            </tr>
    </tbody>
</table>
<div style="position: fixed;bottom: 0;width: 100%;">
<table class="table">
    <tbody>
        <tr>
			<td style="border-bottom:0px;border-top:0px;">
				Phone : <b>011 75 56 600</b>
			</td>
			<td style="border-bottom:0px;border-top:0px;">
			Hours of Operation :<b> 9 am to 6 pm</b>
			</td>						
			<td style="border-bottom:0px;border-top:0px;">
			Direct 2 door, Colombo, Sri Lanka.
			</td>
		</tr>
	</tbody>
</table>
</div>