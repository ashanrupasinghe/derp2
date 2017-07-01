<?php
$status = ['0'=>'Desabled','1'=>'Active'];
?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Add Customer') ?><small><?= __('Add Customer') ?></small></h2>
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
                    <br />
                    <!--<form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">-->
					 <?= $this->Form->create($customer,['class'=>'form-horizontal form-label-left']) ?>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="firstname">First Name <span class="required">*</span></label>                        
						<div class="col-md-6 col-sm-6 col-xs-12">
                          <!--<input name="firstName" required="required" maxlength="100" id="firstname" type="text" class="form-control col-md-7 col-xs-12">-->
                          <?php echo $this->Form->input('firstName',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="lastname">Last Name</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">                          
                          <!--<input name="lastName" maxlength="100" id="lastname" type="text"  class="form-control col-md-7 col-xs-12">-->
                          <?php echo $this->Form->input('lastName',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                          
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Address <span class="required">*</span></label>                        
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <!--<input name="address" required="required" maxlength="255" id="address" type="text"  class="form-control col-md-7 col-xs-12" >-->
                          <?php echo $this->Form->input('address',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="city" class="control-label col-md-3 col-sm-3 col-xs-12">City <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('city',['label' => false,'options'=>$cities,'empty'=>'select city','class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="latitude" class="control-label col-md-3 col-sm-3 col-xs-12">Latitude</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('latitude',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="longitude" class="control-label col-md-3 col-sm-3 col-xs-12">Longitude</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('longitude',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>   
                      
                      <div class="form-group">
                        <label for="email" class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('email',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>  
                      
                      <div class="form-group">
                        <label for="mobileno" class="control-label col-md-3 col-sm-3 col-xs-12">Mobile No <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <?php echo $this->Form->input('mobileNo',['label' => false,'class'=>'form-control col-md-7 col-xs-12']);?>
                        </div>
                      </div>


						<div class="form-group">
                        <label for="created" class="control-label col-md-3 col-sm-3 col-xs-12">Joined Date <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div style="margin-bottom: 0px;" id="customer_created_date" class="input-group input-group-margin-0 date customer_created_date" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd hh:ii:ss">
                    <input class="form-control" type="text" value="<?= date('d F Y - H:i') ?>" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
				<input type="hidden" id="dtp_input2" value="<?= date('yyyy-mm-dd H:i') ?>" name="created" />
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
    <?= $this->Form->end() ?>
<!--                    </form>-->
                  </div>
                </div>
              </div>
