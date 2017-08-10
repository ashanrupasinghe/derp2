<?php
$availability=['0'=>'Not available','1'=>'Available'];
$status = ['0'=>'Desabled','1'=>'Active'];
?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Impotr Product') ?> <small>import products using excel sheet</small></h2>
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
				  <div>
                  <?= $this->Form->create('',['type' => 'file','class'=>'form-horizontal form-label-left']) ?>
                  <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                  		<?= $this->Form->input('productsSheet', ['type' => 'file','required'=>true]);?>
                  		</div>
                  		</div>
                  		
                  		
                  		<div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <!--<button type="submit" class="btn btn-primary">Cancel</button>
                          <button type="submit" class="btn btn-success">Submit</button>-->
                             <?= $this->Form->button(__('Submit'),['class'=>'btn btn-success']) ?>
                        </div>
                      </div>
                      
                  <?= $this->Form->end() ?> 
                  </div>
                  </div>
                </div>
              </div>
<?php // http://stackoverflow.com/questions/32999490/how-do-i-create-a-keyvalue-pair-by-combining-having-two-fields-in-cakephp-3?>
