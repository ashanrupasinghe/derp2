<?php
$status=['0'=>'Disabled','1'=>'Active'];
?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Customers') ?><small><?= $s!=null ? 'Search Result for: '.$s:'Order by latest' ?></small></h2>
                    <ul class="nav navbar-right panel_toolbox">
					
                    <li><?= $this->Html->link(__('Add New Customer'), ['controller' => 'Customers', 'action' => 'add','class'=>'btn btn-default']) ?><li>
                    <li><?= $this->Html->link(__('Import Customers'), ['controller' => 'Customers', 'action' => 'import']) ?></li>
					<li class="search-inpuut" style="margin: 0px 5px 0px 5px;">
					<form action="/customers/index" type="get">
					<input type="text" name="s" class="form-control col-sm-3">
					</form>
					
					</li>
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
<th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('firstName') ?></th>
                <th><?= $this->Paginator->sort('lastName') ?></th>
                <!--<th><?= $this->Paginator->sort('address') ?></th>-->
                <th><?= $this->Paginator->sort('city') ?></th>
                <!--<th><?= $this->Paginator->sort('latitude') ?></th>
                <th><?= $this->Paginator->sort('longitude') ?></th>-->
                <th><?= $this->Paginator->sort('email') ?></th>
                <th><?= $this->Paginator->sort('mobileNo') ?></th>
                <!--<th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>-->
                <th><?= $this->Paginator->sort('status') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                      </thead>
                      <tbody>
            <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?= $this->Number->format($customer->id) ?></td>
                <td><?= h($customer->firstName) ?></td>
                <td><?= h($customer->lastName) ?></td>
                <!--<td><?= h($customer->address) ?></td>-->
				
                <td>
				<?php if($customer->city!=null){?>
				<?= h($cities[$customer->city]) ?>
				<?php }else{ 
						echo "-";
						} ?>
				</td>
				
                <!--<td><?= h($customer->latitude) ?></td>
                <td><?= h($customer->longitude) ?></td>-->
                <td><?= h($customer->email) ?></td>
                <td><?= h($customer->mobileNo) ?></td>
                <!--<td><?= h($customer->created) ?></td>
                <td><?= h($customer->modified) ?></td>-->
                <td><?= h($status[$customer->status]) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $customer->id],['class'=>'x-btn x-btn-primary btn btn-info btn-xs']) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $customer->id],['class'=>'x-btn x-btn-warning btn btn-warning btn-xs']) ?>
                   
					<?php $cus_name=$customer->firstName.' '.$customer->lastName;?>                    
                    <?= $this->Form->postLink(__('Proceed order'), ['action' => 'check', $customer->id], ['confirm' => __('Are you sure you want to proceed an order for {0}?', $cus_name),'class'=>'x-btn x-btn-success btn btn-success btn-xs']) ?>
                </td>
            </tr>
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