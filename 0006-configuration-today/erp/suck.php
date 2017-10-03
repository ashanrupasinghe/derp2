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
                        <?php foreach ($dates as $date): ?>
                <tr>
                    <td><?= h($date->date) ?></td>
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
