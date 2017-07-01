<?= $this->Form->create($order,['id'=>'order']) ?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Edit Order: '.$order->id) ?> <small><?= __('select the products here') ?></small></h2>
                    <!--<ul class="nav navbar-right panel_toolbox">
                    <li><?= $numOfOrders ?></li>
                    </ul>-->
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    
                    
                    
<div class="row"> 
<div class="form-horizontal col-lg-12 col-md-12 col-sm-12">                   
                     <div class="form-group">
                          
                          <div class="col-lg-12 col-md-12 col-sm-12">
                          <label for="product-list">Products</label>
                          <?php echo $this->Form->input('Orders.products_id',['label' => false,'class'=>'form-control','empty'=>'select product','options'=>$products,'name'=>'product_name_list[]','required'=>true,'multiple'=>'multiple', 'id'=>'product-list','value'=>$current_product_ids]);?>
                           
                          </div>                          
                     </div>
</div>                     
</div> 
                  </div>
                </div>
</div>




<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Product List') ?> <small>selected products</small></h2>                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                   <div class="dynamic-fields-product-list">
                   <?php if(sizeof($order_products)>0):?>
                   <?php foreach($order_products as $product):?>
                   <div class="group col-lg-12 col-md-12 columns prod x-y-z" id="x-<?= $product->product_id ?>" style="padding-left:0px; padding-right:0px;">
			          			<div class="col-lg-3 col-md-3 columns prod-left form-group">
			          				<input type="text" class="form-control" name="product_name[<?= $product->product_id ?>]" value="<?= $product->product->name ?>" disabled>
			          				<select name="product_name[]" class="form-control select2-display-none select-2-order-edit-product orders-products-id"><option value="<?= $product->product_id ?>" selected><?= $product->product->name ?></option></select>
			          			</div>
			          			
			          			<div class="col-lg-1 col-md-1 columns form-group">
			          			<input name="product_quantity[]" class="form-control product-quantity-x showclass" required="required" type="text" value="<?= $product->product_quantity ?>">
			          			</div>
			          			
			          			<div class="col-lg-2 col-md-2 columns form-group">
			          			<input name="Package" disabled="disabled" class="form-control packagetype " id="" type="text" value=" X <?= $product->product->package_type->type ?>">
			          			</div>
			          			
			          			<div class="col-lg-3 col-md-3 columns form-group">
			          			<input name="Ammount" disabled="disabled" class="form-control product-ammount showclass" id="" type="text" value="<?= $product->product_price.'X'.$product->product_quantity.' = '.$product->product_price*$product->product_quantity ?>">
			          			<input name="product_price[]" disabled="disabled" class="form-control product-ammount-hidden" id="" value="0" type="hidden" value="<?= $product->product_price ?>">
			          			</div>
			          			
			          			<div class="col-lg-3 col-md-3 columns sup-right form-group">
			          			<select id="sup-<?= $product->product_id ?>" name="product_supplier[]" class="form-control orders-suppliers-id showclass">
			          				
			          				<?php $sup=$product->product->product_suppliers;
			          				/* print '<pre>';
			          				print_r($sup);
			          				die(); */
			          				?>
			          				<?php for ($i = 0; $i < sizeof($sup); $i++){?>
		            	         <option value='<?=  $sup[$i]->supplier_id ?>' <?= $sup[$i]->supplier_id==$product->supplier_id ? 'selected':'' ?>><?=  $sup[$i]->supplier->firstName.' '.$sup[$i]->supplier->lastName.' - '.$sup[$i]->supplier->cid->cname ?></option>
		            	          
		            	        <?php }?>
			          			</select>
			          			</div>
			         </div>
			          		   	<?php endforeach;?>
                   <?php endif;?>
                   </div> 
                   
		  

                  </div>
                </div>
              </div>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Delivery Date/Time/Note') ?> <small>selected products</small></h2>                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    
                    <div class="row">
<div class="form-horizontal form-label-left orders col-lg-6 col-md-6 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
<div class="form-group">
                <label for="dtp_input2" class="col-md-3 control-label">Delivery Date</label>
                <div class="input-group date form_date col-md-9" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                    <input class="form-control" type="text" value="<?= $current_date_show ?>" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
				<input type="hidden" id="dtp_input2" value="<?= $current_date_hidden ?>" name="del-date" /><br/>
            </div>
<div class="form-group">
                <label for="del-note" class="col-md-3 control-label">Delivery Note</label>
                <div class="input-group col-md-9">
                    <textarea name="del-note" class="form-control" rows="4"><?= $order->note ?></textarea>
                    
                </div>
</div>
           
</div>		

<div class="form-horizontal form-label-left orders col-lg-6 col-md-6  col-sm-12 columns content div-top-pad-0 div-right-pad-0">
<div class="form-group">
                <label for="dtp_input3" class="col-md-3 control-label">Delivery Time</label>
                <div class="input-group date form_time col-md-9" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                    <input class="form-control" type="text" value="<?= $delivery_time ?>" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
					<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                </div>
				<input type="hidden" id="dtp_input3" value="<?= $delivery_time ?>" name="del-time" /><br/>
</div>
<div class="form-group">
 <label for="supp-note" class="col-md-3 control-label">Supplier Note</label>
                <div class="input-group col-md-9">
                    <textarea name="supp-note" class="form-control" rows="4"><?= $order->supplier_note ?></textarea>
                    
                </div>
</div>
</div>

</div> 
		  

                  </div>
                </div>
              </div>              

                    
                     
                     
 

 <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Other Details') ?> <small>about order</small></h2>                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="row">
<div class="form-horizontal form-label-left orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Sub Total</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('subTotal',['label' => false,'class'=>'form-control','disabled'=>true]); ?>
</div>
</div>	
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Tax</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('tax',['label' => false,'class'=>'form-control','disabled'=>true]); ?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Discount</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('direct_discount',['value'=>$order->dicount,'label' => false,'class'=>'form-control','disabled'=>true, 'type'=>'hidden']); ?>
	<?php echo $this->Form->input('direct_discount',['value'=>$order->dicount,'label' =>false,'class'=>'form-control','disabled'=>false]); ?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Coupon Code</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('couponCode',['label' => false,'class'=>'form-control']); ?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Total</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('total',['label' => false,'class'=>'form-control','disabled'=>true]); ?>
</div>
</div>	
</div>    

<div class="form-horizontal form-label-left orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">

<?php //$payment_status=['1'=>'pending','2'=>'paid'];
$payment_status=['1'=>'pending','2'=>'cash','3'=>'card', '4'=>'credit'];//2 was paid
?>			
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Payment Status</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('paymentStatus',['label' => false,'class'=>'form-control','options'=>$payment_status,'empty'=>'select status']); ?>
</div>
<?php  $status=['1'=>'pending','2'=>'supplier informed','3'=>'products ready','4'=>'delivery tookover','5'=>'delivered','6'=>'completed',7=>'driver informed'];?>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Status</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('status',['label' => false,'class'=>'form-control','options'=>$status,'empty'=>'select status']); ?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Callcenter Staff</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('callcenterId',['label' => false,'class'=>'form-control','empty'=>'select callcenter','options'=>$callcenters,'disabled' => true,'default' =>$callcenterId,'name'=>'callcenterIdDisables','id'=>'callcenterIdDisables']); ?>
	<?php echo $this->Form->input('callcenterId',['label' => false,'class'=>'form-control','empty'=>'select callcenter','options'=>$callcenters,'disabled' => false,'type'=>'hidden','default' =>$callcenterId]);?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Delivery Staff</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('deliveryId',['label' => false,'class'=>'form-control','options'=>$deliveries]); ?>
</div>
</div>			



</div>

<div class="form-horizontal form-label-left orders col-lg-4 col-md-4 col-sm-12 columns content div-top-pad-0 div-left-pad-0">


			
<?php echo $this->Form->input('editorder',['label' => false,'class'=>'form-control','disabled'=>false,'type'=>'hidden','id'=>'edit-order-suppliers','default'=>0]); ?>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Customer Id</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('customerId',['label' => false,'class'=>'form-control','value'=>$clientid,'disabled'=>'true']);
		echo $this->Form->input('customerId',['label' => false,'class'=>'form-control','value'=>$clientid,'disabled' => false,'type'=>'hidden']);
	 ?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Address</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('address',['label' => false,'class'=>'form-control']); ?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">City</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('city',['label' => false,'class'=>'form-control','options'=>$cities,'empty'=>'select city']); ?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Latitude</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('latitude',['label' => false,'class'=>'form-control']); ?>
</div>
</div>
<div class="form-group">
<label class="control-label col-md-4 col-sm-4 col-xs-12">Longitude</label>
<div class="col-md-8 col-sm-8 col-xs-12">
	<?php echo $this->Form->input('longitude',['label' => false,'class'=>'form-control',]); ?>
</div>
</div>			
			

</div>   

  


              
                    
                    </div>

			<!--dinamic fields-->

<!--/..-->
 <div class="ln_solid"></div>
                    <div class="form-group">
                        <?= $this->Form->button(__('Submit'),['class'=>'btn btn-success','style'=>'float: right;']) ?>
                    </div>
 
                    
		  

                  </div>
                </div>
              </div>
 
 
                    
                     
    					<?= $this->Form->end() ?>
                    
                    
                    
                    
		  



<!-- jQuery -->
<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
-->
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>-->
<!-- jQuery Multifield -->

  
<script>
	/*
	$('#example-6').multifield({
		section: '.group',
		btnAdd:'#btnAdd-6',
		btnRemove:'.btnRemove'
	});
	*/
	
	
  /*$('select').select2();*/
  //$('#product-list').on('change', function(e) { alert("huu");});
  
  $(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});

</script>

<!-- Place this tag right after the last button or just before your close body tag. -->
<script async defer id="github-bjs" src="https://buttons.github.io/buttons.js"></script>



			  
              
