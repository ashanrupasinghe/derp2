<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Orders Model
 *
 * @property \Cake\ORM\Association\HasMany $OrderProducts
 *
 * @method \App\Model\Entity\Order get($primaryKey, $options = [])
 * @method \App\Model\Entity\Order newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Order[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Order|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Order[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Order findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrdersTable extends Table
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

        $this->table('orders');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('OrderProducts', [
            'foreignKey' => 'order_id'
        ]);
        $this->hasMany('SupplierNotifications',[
        	'foreignKey' => 'orderId'	
        ]);
        $this->hasMany('DeliveryNotifications',[
        		'foreignKey' => 'orderId'
        ]);
        $this->hasMany('UserNotifications',[
        		'foreignKey' => 'orderId'
        ]);
        $this->hasMany('Shipping',[
        		'foreignKey' => 'order_id'
        ]);
        
        
		$this->belongsTo('callcenter',['foreignKey'=>'callcenterId']);
		$this->belongsTo('delivery',['foreignKey'=>'deliveryId']);
		$this->belongsTo('city',['foreignKey'=>'city','propertyName'=>'cid']);
		$this->belongsTo('customers',['foreignKey'=>'customerId']);
		
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('customerId')
            ->requirePresence('customerId', 'create')
            ->notEmpty('customerId');

        $validator
            ->requirePresence('address', 'create')
            ->notEmpty('address');

         $validator
             ->integer('city')
             ->requirePresence('city', 'create')
             ->notEmpty('city');

        $validator
            ->allowEmpty('latitude');

        $validator
            ->allowEmpty('longitude');

        $validator
            ->integer('callcenterId')
            ->requirePresence('callcenterId', 'create')
            ->notEmpty('callcenterId');

        $validator
            ->integer('deliveryId')
            ->requirePresence('deliveryId', 'create')
            ->notEmpty('deliveryId');

        $validator
            ->numeric('subTotal')
            ->requirePresence('subTotal', 'create')
            ->notEmpty('subTotal');

        $validator
            ->numeric('tax')
            /* ->requirePresence('tax', 'create') */
            ->allowEmpty('tax');

        $validator
            ->numeric('discount')
            ->requirePresence('discount', 'create')
            ->notEmpty('discount');

        $validator
            ->allowEmpty('couponCode');

        $validator
            ->numeric('total')
            ->requirePresence('total', 'create')
            ->notEmpty('total');

        $validator
            ->requirePresence('paymentStatus', 'create')
            ->notEmpty('paymentStatus');

        $validator
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        return $validator;
    }
}
