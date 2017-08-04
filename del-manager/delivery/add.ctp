<?php
//$availability=['0'=>'Not available','1'=>'Available'];
$user_role=[1=>'Admin',2=>'Callcenter',3=>'Supplier',4=>'Delivery',6=>'Delivery Manager'];
$status = ['0'=>'Desabled','1'=>'Active'];
?>
 <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Add User') ?> <small><?= __('add new user') ?></small></h2>
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
  			  		    <?= $this->Form->create($user,['class'=>'form-horizontal form-label-left']) ?>
  			  		    
  			  		    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="username">Username<span class="required">*</span></label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">                          
                          <?php echo $this->Form->input('username',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>                          
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="user_type" class="control-label col-md-3 col-sm-3 col-xs-12">User Type <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('user_type',['label' => false,'options'=>$user_role,'empty'=>'select user type','class'=>'form-control col-md-7 col-xs-12']);?>
                          
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">Password<span class="required">*</span></label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">                          
                          <?php echo $this->Form->input('password',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>                         
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="status" class="control-label col-md-3 col-sm-3 col-xs-12">User Type <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('status',['label' => false,'options'=>$status,'empty'=>'select status','class'=>'form-control col-md-7 col-xs-12']);?>
                          
                        </div>
                      </div>
        
		
        
        <?php //echo $this->Form->input('remember_token');?>
        
        
    	<div class="ln_solid"></div>
    				  <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">                          
                             <?= $this->Form->button(__('Submit'),['class'=>'btn btn-success']) ?>
                        </div>
                      </div>
    
    <?= $this->Form->end() ?>
  			  		
                  </div>
                </div>
</div>




