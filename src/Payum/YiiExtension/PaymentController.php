<?php
namespace Payum\YiiExtension;

use Payum\Core\Request\InteractiveRequestInterface;
use Payum\Core\Request\RedirectUrlInteractiveRequest;
//use Payum\Core\Request\Http\RedirectUrlInteractiveRequest; // see issue #17
use Payum\Core\Request\SecuredCaptureRequest;
use Payum\Core\Exception\LogicException;

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

        $payment->execute($capture = new SecuredCaptureRequest($token));

        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        $this->redirect($token->getAfterUrl());
    }

    public function actionNotify()
    {
        throw new \LogicException('Not Implemented');
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

        $ro = new \ReflectionObject($interactiveRequest);

        $event->exception = new LogicException(
            sprintf('Cannot convert interactive request %s to Yii response.', $ro->getShortName()),
            null,
            $interactiveRequest
        );
    }

    /**
     * @return \Payum\YiiExtension\PayumComponent
     */
    protected function getPayum()
    {
        return \Yii::app()->payum;
    }
} 