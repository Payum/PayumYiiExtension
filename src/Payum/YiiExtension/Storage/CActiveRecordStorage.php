<?php

namespace Payum\YiiExtension\Storage;

use Payum\Core\Storage\AbstractStorage;
use Payum\Core\Model\Identity;

class CActiveRecordStorage extends AbstractStorage
{
    /**
     * {@inheritDoc}
     */
    public function doUpdateModel($model)
    {
        $model->save();
    }

    /**
     * {@inheritDoc}
     */
    public function doDeleteModel($model)
    {
        $model->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function doGetIdentity($model)
    {
        if ($model->getIsNewRecord()) {
            throw new LogicException('The model must be persisted before usage of this method');
        }

        return new Identity($model->{$model->primaryKey()}, $model);
    }

    /**
     * {@inheritDoc}
     */
    public function doFind($id)
    {
        return \CActiveRecord::model($this->modelClass)->findByPk($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria)
    {
        return \CActiveRecord::model($this->modelClass)->findByAttributes($criteria);
    }
}
