<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Customer Entity
 *
 * @property int $id
 * @property string $firstName
 * @property string $lastName
 * @property string $address
 * @property int $city
 * @property string $latitude
 * @property string $longitude
 * @property string $email
 * @property string $mobileNo
 * @property string $created
 * @property string $modified
 * @property string $status
 */
class Customer extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => true
    ];
	
	 protected $_virtual = ['customer_full_name'];
	 protected function _getCustomerFullName(){
			return 'xyuxyu';//$this->_properties['firstName'].' '.$this->_properties['lastName'];		 
	 }
	



}
