<?php
namespace Payum\YiiExtension\Storage;

use InvalidArgumentException;
use Payum\Core\Exception\LogicException;
use Payum\Core\Model\Identificator;
use Payum\Core\Storage\AbstractStorage;

class ActiveRecordStorage extends AbstractStorage
{
    protected $_tableName;

    public function __construct($tableName, $modelClass)
    {
        parent::__construct($modelClass);

        $this->_tableName = $tableName;
    }

    /**
     * {@inheritDoc}
     */
    public function createModel()
    {
        return new $this->modelClass('insert', $this->_tableName);
    }

    /**
     * {@inheritDoc}
     */
    protected function doUpdateModel($model)
    {
        $model->save();
    }

    /**
     * {@inheritDoc}
     */
    protected function doDeleteModel($model)
    {
        $model->delete();
    }

    /**
     * {@inheritDoc}
     */
    protected function doGetIdentificator($model)
    {
        if (is_array($model->primaryKey())) {
            throw new LogicException('Composite primary keys are not supported by this storage.');
        }

        return new Identificator($model->{$model->primaryKey()}, $this->modelClass);
    }

    /**
     * {@inheritDoc}
     */
    function findModelById($id)
    {
        $className = $this->modelClass;
        return $className::findModelById($this->_tableName, $id);
    }

    /**
     * {@inheritDoc}
     */
    protected function assertModelSupported($model)
    {
        parent::assertModelSupported($model);

        if (!property_exists(get_class($model), 'activeRecord')
            || false == $model->activeRecord instanceof \CActiveRecord) {
            throw new InvalidArgumentException(
                'Model required to have activeRecord property, which should be sub class of CActiveRecord class.'
            );
        }
    }
}
