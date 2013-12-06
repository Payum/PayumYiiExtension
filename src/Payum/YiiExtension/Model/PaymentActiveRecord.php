<?php
/**
 * This is the model class for table $tableName, which is being used for token storage for Payum payments
 *
 * The following are the available columns in table $tableName:
 * @property string $_hash
 * @property string $_payment_name
 * @property string $_details
 * @property string $_after_url
 * @property string $_target_url
 *
 * Underscores are used because usually these would be private and to prevent
 * the ActiveRecord getters and setters clashing with the required
 * getters and setters from TokenInterface
 */

namespace Payum\YiiExtension\Model;

use Payum\Core\Exception\InvalidArgumentException;

class PaymentActiveRecord extends \CActiveRecord
{
    private static $_tableName;

    /**
     * Constructs a model corresponding to table $tableName
     * The table must have the columns identified above in the
     * comments for this class.
     *
     * @param string $scenario
     * @param $tableName
     * @throws \Payum\Core\Exception\InvalidArgumentException
     */
    public function __construct($scenario = 'insert', $tableName = '')
    {
        if ($scenario == 'insert' && $tableName == '') {
            throw new InvalidArgumentException(
                'Table name must be supplied when creating a new Payment'
            );
        }
        if ($tableName !== '') {
            self::$_tableName = $tableName;
        }
        parent::__construct($scenario);
/*        if ($scenario == 'insert') {
            $this->_hash = Random::generateToken();
        } */
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return self::$_tableName;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $tableName table corresponding to the model
     * @param string $className active record class name.
     * @return Payment the static model class
     * @throws \Payum\Core\Exception\InvalidArgumentException
     */
    public static function model($tableName, $className=__CLASS__)
    {
        if ($tableName == '') {
            throw new InvalidArgumentException(
                'Table name must be supplied when trying to find a Payment'
            );
        }
        self::$_tableName = $tableName;
        return parent::model($className);
    }

}
