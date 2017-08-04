                    <?php
$status = ['0'=>'Desabled','1'=>'Active'];
?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Customer ID: '. $customer->id) ?><small><?= __('Customer ID: '. $customer->id) ?></small></h2>
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

<div class="col-md-6 col-sm-6 col-xs-12">

<table class="table table-hover">
                      <tbody>
                        <tr>
                          <th scope="row"><?= __('Id') ?></th>
                          <td><?= $this->Number->format($customer->id) ?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?= __('First Name') ?></th>
                          <td><?= h($customer->firstName) ?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?= __('Last Name') ?></th>
                          <td><?= h($customer->lastName) ?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?= __('Address') ?></th>
                          <td><?= h($customer->address) ?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?= __('City') ?></th>
                          <td><?= isset($customer->cid->cname) ? $customer->cid->cname:'' ?></td>
                        </tr>
<!-- 						<tr>
                          <th scope="row"><?= __('Latitude') ?></th>
                          <td><?= h($customer->latitude) ?></td>
                        </tr>
 						<tr>
                          <th scope="row"><?= __('Longitude') ?></th>
                          <td><?= h($customer->longitude) ?></td>
                        </tr>    -->                    
                      </tbody>
                    </table>
</div>

<div class="col-md-6 col-sm-6 col-xs-12">
<table class="table table-hover">
                      <tbody>
                        <tr>
                          <th scope="row"><?= __('Email') ?></th>
                          <td><?= h($customer->email) ?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?= __('Mobile No') ?></th>
                          <td><?= h($customer->mobileNo) ?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?= __('Created') ?></th>
                          <td><?= h($customer->created) ?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?= __('Modified') ?></th>
                          <td><?= h($customer->modified) ?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?= __('Status') ?></th>
                          <td><?= h($status[$customer->status]) ?></td>
                        </tr>
                   
                      </tbody>
                    </table>
</div>

     
    
        
        
  



                    
                    
		  

                  </div>
                </div>
              </div>


