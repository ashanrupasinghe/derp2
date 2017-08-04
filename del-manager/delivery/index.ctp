 <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?= __('Users') ?> <small>user list</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                    <li><?= $this->Html->link(__('Add New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
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
                <th><?= $this->Paginator->sort('username') ?></th>
                <th><?= $this->Paginator->sort('user_type') ?></th>
                <!--<th><?= $this->Paginator->sort('password') ?></th>-->
                <!--<th><?= $this->Paginator->sort('remember_token') ?></th>-->
<!--                <th><?= $this->Paginator->sort('created') ?></th>
                <th><?= $this->Paginator->sort('modified') ?></th>-->
                <th><?= $this->Paginator->sort('status') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $this->Number->format($user->id) ?></td>
                    <td><?= h($user->username) ?></td>
                    <?php
                    switch ($user->user_type) {
                        case '2':
                            $user_type = 'Callcentre';
                            break;
                        case '3':
                            $user_type = 'Supplier';
                            break;
                        case '4':
                            $user_type = 'Delivery Staff';
                            break;
						case '6':
                            $user_type = 'Delivery Manager';
                            break;	
                        default:
                            $user_type = 'Admin';
                            break;
                    }
                    ?>
                    <td><?= h($user_type) ?></td>
                    <!--<td><?= h($user->modified) ?></td>-->
                    <td><?= h(($user->status==1?'Enabled':'Disabled')) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $user->id],['class'=>'x-btn x-btn-primary btn btn-info btn-xs']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $user->id],['class'=>'x-btn x-btn-warning btn btn-warning btn-xs']) ?>
                       
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



