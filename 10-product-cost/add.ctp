<?php
$availability=['0'=>'Not available','1'=>'Available'];
$status = ['0'=>'Desabled','1'=>'Active'];
?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Add Product') ?> <small>add new product</small></h2>
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

<?= $this->Form->create($product,['class'=>'form-horizontal form-label-left']) ?>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span></label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">                          
                          <?php echo $this->Form->input('name',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_si">Name in Sinhala <span class="required">*</span></label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">                          
                          <?php echo $this->Form->input('name_si',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name_ta">Name in Tamil <span class="required">*</span></label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">                          
                          <?php echo $this->Form->input('name_ta',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description">Description</label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('description',['label' => false,'class'=>'form-control col-md-7 col-xs-12','rows'=>3]);?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="price">Price</label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">                          
                          <?php  echo $this->Form->input('price',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
					  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="price">Coset</label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">                          
                          <?php  echo $this->Form->input('cost',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="package" class="control-label col-md-3 col-sm-3 col-xs-12">Package</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('package',['label' => false,'empty'=>'select package','options'=>$packages,'class'=>'form-control col-md-7 col-xs-12']);?>                                     
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="availability" class="control-label col-md-3 col-sm-3 col-xs-12">Availability</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('availability',['label' => false,'empty'=>'select availability','options'=>$availability,'class'=>'form-control col-md-7 col-xs-12']);?>                                                              
                        </div>
                      </div>
                      
                      
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image">Image</label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">
                          <?php  echo $this->Form->input('image',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                                            
                      
                      <div class="form-group">
                        <label for="supplierId" class="control-label col-md-3 col-sm-3 col-xs-12">Supplier(s)</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('supplierId',['label' => false,'empty'=>'select supplier','options'=>$suppliers,'multiple'=>'multiple','class'=>'form-control col-md-7 col-xs-12']);?>           
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="status" class="control-label col-md-3 col-sm-3 col-xs-12">Status</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">                          
                          <?php echo $this->Form->input('status',['label' => false,'options'=>$status,'empty'=>'select status','class'=>'form-control col-md-7 col-xs-12']); ?>           
                        </div>
                      </div> 
                      
                      
                      
                      
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <!--<button type="submit" class="btn btn-primary">Cancel</button>
                          <button type="submit" class="btn btn-success">Submit</button>-->
                             <?= $this->Form->button(__('Submit'),['class'=>'btn btn-success']) ?>
                        </div>
                      </div>
    <?= $this->Form->end() ?>                  </div>
                </div>
              </div>
<?php // http://stackoverflow.com/questions/32999490/how-do-i-create-a-keyvalue-pair-by-combining-having-two-fields-in-cakephp-3?>
