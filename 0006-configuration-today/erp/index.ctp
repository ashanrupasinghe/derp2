<?php
$status = ['0'=>'Disabled','1'=>'Enabled'];
 ?>
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Boolian Configurations') ?></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                <th><?= $this->Paginator->sort('config_name') ?></th>
                <th><?= $this->Paginator->sort('config_key') ?></th>
				<th><?= $this->Paginator->sort('config_value') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($configurations as $configuration): ?>
                <tr>
                    <td><?= h($configuration->config_name) ?></td>
                    <td><?= h($configuration->config_key) ?></td>
					<td><?= h($status[$configuration->config_value]) ?></td>
                    <td class="actions">
                        <?= $this->Form->postLink(__($configuration->config_value ? 'Disable' : 'Enable'), ['controller'=>'boolian-configurations','action' => 'changeStatus', $configuration->id], ['confirm' => __('Are you sure you want to change # {0}?', $configuration->id),'class'=>'x-btn x-btn-warning btn btn-warning btn-xs']) ?>
                       
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
			  
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Add unavailable date</h2>
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
					<form method="post" accept-charset="utf-8" action="/boolian-configurations/adddate"><div style="display:none;"><input name="_method" value="POST" type="hidden"></div><div class="form-group">
                        

                        <div class="col-sm-12">
                          <div class="input-group">
                            <input id="add-unavailable-date" name="date" class="form-control big-search" style="" type="text">
                            <span class="input-group-btn">
                                              
                            <button class="btn btn-primary" style="" type="submit">Save</button>                  
                                          </span>
                          </div>
                        </div>
                      </div>


 </form>  

                  </div>
                </div>
              </div>

			  
<div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Unavailable Dates') ?></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                <th><?= $this->Paginator->sort('date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($unavailable_dates as $date): ?>
                <tr>
                    <td><?= h(date('Y-m-d',strtotime($date->date))) ?></td>
                    <td class="actions">
                        <?= $this->Form->postLink(__('Delete'), ['controller'=>'boolian-configurations','action' => 'deletedate', $date->id], ['confirm' => __('Are you sure you want to delete # {0}?', $date->id),'class'=>'x-btn x-btn-danger btn btn-danger btn-xs']) ?>
                       
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
			  
