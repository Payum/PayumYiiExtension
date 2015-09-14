<?php

namespace Payum\YiiExtension\Model;

use Payum\Core\Storage\StorageInterface;
use Payum\Core\Storage\AbstractStorage;
use Payum\Core\Model\Identity;

// class CActiveRecordStorage extends \CActiveRecord implements StorageInterface
class CActiveRecordStorage extends AbstractStorage
{
    public function doUpdateModel($model)
    {
        $model->save();
    }

    public function doDeleteModel($model)
    {
        $model->delete();
    }

    public function doGetIdentity($model)
    {
        if ($model->getIsNewRecord()) {
            throw new LogicException('The model must be persisted before usage of this method');
        }

        return new Identity($model->id, $model);
    }

    public function doFind($id)
    {
        return \CActiveRecord::model($this->modelClass)->findByPk($id);
    }

    public function findBy(array $criteria)
    {
        return \CActiveRecord::model($this->modelClass)->findByAttributes($criteria);
    }
}
