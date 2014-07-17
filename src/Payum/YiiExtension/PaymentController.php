<?php
namespace Payum\YiiExtension;

use Payum\Core\Request\BinaryMaskStatusRequest;
use Payum\Core\Request\InteractiveRequestInterface;
use Payum\Core\Request\Http\RedirectUrlInteractiveRequest;
use Payum\Core\Request\SecuredCaptureRequest;

class PaymentController extends \CController
{
    public function init()
    {
        parent::init();

        \Yii::app()->attachEventHandler('onException', array($this, 'handleException'));
    }

    public function actionCapture()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $status = new BinaryMaskStatusRequest($token);
        $payment->execute($status);

        $capture = new SecuredCaptureRequest($token);
        $payment->execute($capture);

        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        $this->redirect($token->getAfterUrl());
    }

    public function handleException(\CExceptionEvent $event)
    {
        if (false == $event->exception instanceof InteractiveRequestInterface) {
            return;
        }

        $interactiveRequest = $event->exception;

        if ($interactiveRequest instanceof RedirectUrlInteractiveRequest) {
            $this->redirect($interactiveRequest->getUrl(), true);
            $event->handled = true;
            return;
        }
    }

    /**
     * @return \Payum\YiiExtension\PayumComponent
     */
    protected function getPayum()
    {
        return \Yii::app()->payum;
    }
} 