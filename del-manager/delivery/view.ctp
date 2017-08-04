<?php
$user_role=[1=>'Admin',2=>'Callcenter',3=>'Supplier',4=>'Delivery',5=>'Customer',6=>'Delivery Manager'];
$status = ['0'=>'Desabled','1'=>'Active'];

?>

 <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('User ID: '.$user->id) ?> <small><?= __('user details') ?></small></h2>
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
            <th scope="row"><?= __('Username') ?></th>
            <td><?= h($user->username) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('User Type') ?></th>
            <td><?= h($user_role[$user->user_type]) ?></td>
        </tr>
        <!--<tr scope="row">
            <th><?= __('Password') ?></th>
            <td><?= h($user->password) ?></td>
        </tr>-->
        <!--<tr scope="row">
            <th><?= __('Remember Token') ?></th>
            <td><?= h($user->remember_token) ?></td>
        </tr>-->
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= h($status[$user->status]) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr>           
                      </body>
                    </table>  
                  	</div>
                  	<div class="col-md-6 col-sm-6 col-xs-12">
					<table class="table table-hover">
                      <tbody>
                              <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($user->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($user->modified) ?></td>
        </tr>
                      </body>
                    </table> 
                  	</div>  			  		
                  </div>
                </div>
</div>





  
<!--    <div class="related">
        <h4><?= __('Related Callcenter') ?></h4>
        <?php if (!empty($user->callcenter)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('FirstName') ?></th>
                <th><?= __('LastName') ?></th>
                <th><?= __('Email') ?></th>
                <th><?= __('Address') ?></th>
                <th><?= __('City') ?></th>
                <th><?= __('MobileNo') ?></th>
                <th><?= __('Status') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->callcenter as $callcenter): ?>
            <tr>
                <td><?= h($callcenter->id) ?></td>
                <td><?= h($callcenter->user_id) ?></td>
                <td><?= h($callcenter->firstName) ?></td>
                <td><?= h($callcenter->lastName) ?></td>
                <td><?= h($callcenter->email) ?></td>
                <td><?= h($callcenter->address) ?></td>
                <td><?= h($callcenter->city) ?></td>
                <td><?= h($callcenter->mobileNo) ?></td>
                <td><?= h($callcenter->status) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Callcenter', 'action' => 'view', $callcenter->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Callcenter', 'action' => 'edit', $callcenter->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Callcenter', 'action' => 'delete', $callcenter->id], ['confirm' => __('Are you sure you want to delete # {0}?', $callcenter->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
        </div>
    </div>
-->    
    <!--<div class="related">
        <h4><?= __('Related Delivery') ?></h4>
        <?php if (!empty($user->delivery)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('FirstName') ?></th>
                <th><?= __('LastName') ?></th>
                <th><?= __('Email') ?></th>
                <th><?= __('Address') ?></th>
                <th><?= __('City') ?></th>
                <th><?= __('Latitude') ?></th>
                <th><?= __('Longitude') ?></th>
                <th><?= __('MobileNo') ?></th>
                <th><?= __('VehicleNo') ?></th>
                <th><?= __('CompanyName') ?></th>
                <th><?= __('Status') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->delivery as $delivery): ?>
            <tr>
                <td><?= h($delivery->id) ?></td>
                <td><?= h($delivery->user_id) ?></td>
                <td><?= h($delivery->firstName) ?></td>
                <td><?= h($delivery->lastName) ?></td>
                <td><?= h($delivery->email) ?></td>
                <td><?= h($delivery->address) ?></td>
                <td><?= h($delivery->city) ?></td>
                <td><?= h($delivery->latitude) ?></td>
                <td><?= h($delivery->longitude) ?></td>
                <td><?= h($delivery->mobileNo) ?></td>
                <td><?= h($delivery->vehicleNo) ?></td>
                <td><?= h($delivery->companyName) ?></td>
                <td><?= h($delivery->status) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Delivery', 'action' => 'view', $delivery->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Delivery', 'action' => 'edit', $delivery->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Delivery', 'action' => 'delete', $delivery->id], ['confirm' => __('Are you sure you want to delete # {0}?', $delivery->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
-->    
    <!--<div class="related">
        <h4><?= __('Related Suppliers') ?></h4>
        <?php if (!empty($user->suppliers)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('FirstName') ?></th>
                <th><?= __('LastName') ?></th>
                <th><?= __('Email') ?></th>
                <th><?= __('Address') ?></th>
                <th><?= __('City') ?></th>
                <th><?= __('Latitude') ?></th>
                <th><?= __('Longitude') ?></th>
                <th><?= __('ContactNo') ?></th>
                <th><?= __('MobileNo') ?></th>
                <th><?= __('FaxNo') ?></th>
                <th><?= __('CompanyName') ?></th>
                <th><?= __('RegNo') ?></th>
                <th><?= __('Status') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->suppliers as $suppliers): ?>
            <tr>
                <td><?= h($suppliers->id) ?></td>
                <td><?= h($suppliers->user_id) ?></td>
                <td><?= h($suppliers->firstName) ?></td>
                <td><?= h($suppliers->lastName) ?></td>
                <td><?= h($suppliers->email) ?></td>
                <td><?= h($suppliers->address) ?></td>
                <td><?= h($suppliers->city) ?></td>
                <td><?= h($suppliers->latitude) ?></td>
                <td><?= h($suppliers->longitude) ?></td>
                <td><?= h($suppliers->contactNo) ?></td>
                <td><?= h($suppliers->mobileNo) ?></td>
                <td><?= h($suppliers->faxNo) ?></td>
                <td><?= h($suppliers->companyName) ?></td>
                <td><?= h($suppliers->regNo) ?></td>
                <td><?= h($suppliers->status) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Suppliers', 'action' => 'view', $suppliers->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Suppliers', 'action' => 'edit', $suppliers->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Suppliers', 'action' => 'delete', $suppliers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $suppliers->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    -->

