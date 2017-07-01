<?php
$availability=['0'=>'Not available','1'=>'Available'];
$status = ['0'=>'Desabled','1'=>'Active'];
                
 ?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Products') ?> <small><?= $s!=null ? 'Search Result for: '.$s:'product list' ?></small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <?php if($userLevel==1):?>
        <li><?= $this->Html->link(__('Add New Product'), ['controller' => 'Products', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('Import Product'), ['controller' => 'Products', 'action' => 'import']) ?></li>

<?php endif;?>
<li class="search-inpuut" style="margin: 0px 5px 0px 5px;">
					<form action="/products/index" type="get">
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
                <th><?= $this->Paginator->sort('name') ?></th>
                <th><?= $this->Paginator->sort('price') ?></th>
				<th><?= $this->Paginator->sort('cost') ?></th>
                <th><?= $this->Paginator->sort('Quantity type') ?></th>
                <th><?= $this->Paginator->sort('availability') ?></th>
                <!--<th><?= $this->Paginator->sort('image') ?></th>-->
                <!--<th><?= $this->Paginator->sort('supplierId') ?></th>-->
                <th><?= $this->Paginator->sort('status') ?></th>
                <!--<th><?= $this->Paginator->sort('created') ?></th>-->
                <!--<th><?= $this->Paginator->sort('modified') ?></th>-->
                <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $this->Number->format($product->id) ?></td>
                    <td><?= h($product->name) ?><br>
                    	<?php if($product->name_si!=null){?><?= h($product->name_si) ?><?php } ?><br>
                    	<?php if($product->name_ta!=null){?><?= h($product->name_ta) ?><?php } ?><br>
                    </td>
                    <td><?= $this->Number->format($product->price) ?></td>
					<td><?= $this->Number->format($product->cost) ?></td>
                    <td><?= h($package_type[$product->package]) ?></td>
                    <td><?= h($availability[$product->availability]) ?></td>
                    <!--<td><?= h($product->image) ?></td>-->
                    <!--<td><?= $this->Number->format($product->supplierId) ?></td>-->
                    <td><?= h($status[$product->status]) ?></td>
                    <!--<td><?= h($product->created) ?></td>-->
                    <!--<td><?= h($product->modified) ?></td>-->
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $product->id],['class'=>'x-btn x-btn-primary btn btn-info btn-xs']) ?>
                       <?php if($userLevel==1||$userLevel==2):?> <?= $this->Html->link(__('Edit'), ['action' => 'edit', $product->id],['class'=>'x-btn x-btn-warning btn btn-warning btn-xs']) ?>
                        
                        <?php endif;?>
                       
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
