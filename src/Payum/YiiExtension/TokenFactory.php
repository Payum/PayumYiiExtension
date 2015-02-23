<?php
namespace Payum\YiiExtension;

use Payum\Core\Security\AbstractTokenFactory;

class TokenFactory extends AbstractTokenFactory
{
    /**
     * @param string $path
     * @param array $parameters
     *
     * @return string
     */
    protected function generateUrl($path, array $parameters = array())
    {
        $ampersand = '&';
        $schema = '';

        return
            \Yii::app()->getRequest()->getHostInfo($schema).
            \Yii::app()->createUrl(trim($path,'/'),$parameters, $ampersand)
        ;
    }
}