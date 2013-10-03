<?php

class PayumYiiComponent extends CApplicationComponent {

    public $storage_path;
    public $payments_details;
    private $_tokenStorage;
    private $_requestVerifier;
    private $_payments;
    private $_registry;
    private $_details_class;
    private $_storage;

    public function init() {
        Yii::setPathOfAlias('Payum', dirname(__FILE__) . '/vendors/Payum');
        Yii::setPathOfAlias('Buzz', dirname(__FILE__) . '/vendors/Buzz');

        $this->_tokenStorage = new \Payum\Storage\FilesystemStorage($this->storage_path, '\Payum\Model\Token', 'hash');
        $this->_requestVerifier = new \Payum\Security\PlainHttpRequestVerifier($this->_tokenStorage);
    }
    
    public function getTokenStorage() {
        return $this->_tokenStorage;
    }
    public function getRequestVerifier() {
        return $this->_requestVerifier;
    }
    
    public function getRegistry() {
        return $this->_registry;
    }
    
    public function getStorage() {
        return $this->_storage;
    }

    public function initRegistry($detailsClass, $paymentFactory, $name) {
        $this->_details_class = $detailsClass;
        $storages = array(
            $name => array(
                $detailsClass => new \Payum\Storage\FilesystemStorage($this->storage_path, $detailsClass, 'id')
            )
        );
        $this->_payments = array(
            $name => $paymentFactory
        );
        $this->_payments[$name]->addExtension(new Payum\Extension\StorageExtension($storages[$name][$detailsClass]));
        $this->_registry = new Payum\Registry\SimpleRegistry($this->_payments, $storages, null, null);
        $this->_storage = $this->_registry->getStorageForClass($this->_details_class, $name);
    }

    public function captureRequest() {
        $token = $this->_requestVerifier->verify($_REQUEST);
        $payment = $this->_registry->getPayment($token->getPaymentName());

        $payment->execute($status = new Payum\Request\BinaryMaskStatusRequest($token));
        if (false == $status->isNew()) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
        }

        if ($interactiveRequest = $payment->execute(new Payum\Request\SecuredCaptureRequest($token), true)) {
            if ($interactiveRequest instanceof Payum\Request\RedirectUrlInteractiveRequest) {
                Yii::app()->request->redirect($interactiveRequest->getUrl());
                Yii::app()->end();
            }

            throw new \LogicException('Unsupported interactive request', null, $interactiveRequest);
        }

        $this->_requestVerifier->invalidate($token);
        
        Yii::app()->request->redirect($token->getAfterUrl());
    }

    public function getPaymentStatus() {
        
    }
    
    public function donePayment() {
        $token = $this->_requestVerifier->verify($_REQUEST);
        $payment = $this->_registry->getPayment($token->getPaymentName());
        $payment->execute($status = new Payum\Request\BinaryMaskStatusRequest($token));
        
        if ($status->isSuccess()) {
            return 'payment captured successfully';
        } else {
            return 'payment captured not successfully';
        }
    }

}
