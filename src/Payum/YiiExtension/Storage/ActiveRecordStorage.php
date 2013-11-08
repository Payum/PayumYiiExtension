<?php
namespace Payum\YiiExtension\Storage;

use InvalidArgumentException;
use Payum\Exception\LogicException;
use Payum\Model\Identificator;
use Payum\Storage\AbstractStorage;

class ActiveRecordStorage extends AbstractStorage
{
    /**
     * {@inheritDoc}
     */
    protected function doUpdateModel($model)
    {
        $model::save();
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
        if (is_array($model->primaryKey()) {
            throw new LogicException('Composite primary keys is not supported by this storage.');
        }

        return new Identificator($model->{$model->primaryKey()}, $this->modelClass);
    }

    /**
     * {@inheritDoc}
     */
    function findModelById($id)
    {
        return $this->modelClass::model()->findByPk($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function assertModelSupported($model)
    {
        parent::assertModelSupported($model);

        if (false == $model instanceof \CActiveRecord) {
            throw new InvalidArgumentException('Invalid model given. Should be sub class of CActiveRecord class.');
        }
    }
}