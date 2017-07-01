<?php
$availability=['0'=>'Not available','1'=>'Available'];
$status = ['0'=>'Desabled','1'=>'Active'];
?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Product Name: '.$product->name.'/ '.$product->name_ta.'/ '.$product->name_si) ?><small>product details</small></h2>
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
               			  <div class="row">
<div class="orders col-lg-2 col-md-2 columns content div-top-pad-0 div-left-pad-0"> 
<?php $img_base=$this->Url->build('/',true);
if($img_base=="http://d2derpnew.allinsl.com/"){
	$img_base="http://d2dfront.allinsl.com";
}
?>
<img src="<?php echo $img_base.$product->image; ?>" height="100%" class="img-responsive">
</div>    
<div class="orders  col-lg-5 col-md-5 columns content div-top-pad-0 div-left-pad-0">     
    <table class="table table-hover">
        <!--<tr>
            <th><?= __('Id') ?></th>
            <td><?= h($product->id) ?></td>
        </tr>-->
                <tr>
            <th><?= __('Price') ?></th>
            <td><?= $this->Number->format($product->price) ?></td>
        </tr>
		<tr>
            <th><?= __('Cost') ?></th>
            <td><?= $this->Number->format($product->cost) ?></td>
        </tr>
        <tr>
            <th><?= __('Quantity type') ?></th>
            <td><?= h($package_type[$product->package]) ?></td>
        </tr>
		
        
        </table>
</div>
       <div class="orders col-md-5 col-lg-5 columns content div-top-pad-0 div-left-pad-0 div-right-pad-0">   
<table class="table table-hover"> 
		<tr>
            <th><?= __('Availability') ?></th>
            <td><?= h($availability[$product->availability]) ?></td>
        </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= h($status[$product->status]) ?></td>
        </tr>
        <!--<tr>
            <th><?= __('Image') ?></th>
            <td><?= h($product->image) ?></td>
        </tr>-->
                <tr>
            <th><?= __('Description') ?></th>
            <td><?= h($product->description) ?></td>
        </tr>
        
        
        
       <!-- <tr>
            <th><?= __('SupplierId') ?></th>
            <td><?= $this->Number->format($product->supplierId) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($product->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($product->modified) ?></td>
        </tr>-->
    </table>
</div>

</div>

                  </div>
                </div>
              </div>
              
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Suppliers') ?> <small><?= __($product->name.' suppliers') ?></small></h2>
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
               			  <div class="row">
    
    
<?php
$j=1;
$size=sizeof($suppliers);
foreach($suppliers as $supplier){
if($j==1){?>
<div class="orders col-lg-6 col-md-6 columns content div-top-pad-0 div-left-pad-0">    
<table class="table table-hover"> 
<?php
}
if(($size/2)+1==$j){
?>
</table> 
</div>
<div class="orders col-lg-6 col-md-6 columns content div-top-pad-0 div-left-pad-0 div-right-pad-0">  
<table class="table table-hover"> 
<?php

}
?>
<tr>

            <td class="td-align-left"><?php $name= $supplier->supp['firstName']." " .$supplier->supp['lastName']?>
            <?= $this->Html->link(__($name), ['controller'=>'suppliers','action' => 'index']) ?>
            </td>
        </tr>
<?php
if($size==$j){
?>
</table> 
</div>

<?php
}

$j++;

}


?>

</div>

                  </div>
                </div>
              </div>              

