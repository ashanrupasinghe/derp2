<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class ShippingTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('shipping');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        
        
        
		$this->belongsTo('orders',['foreignKey'=>'order_id']);
		
		
    }

   
}
