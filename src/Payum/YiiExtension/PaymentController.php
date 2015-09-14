<?php
namespace Payum\YiiExtension;

use Payum\Core\Exception\LogicException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\ReplyInterface;
use Payum\Core\Request\Authorize;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Notify;
use Payum\Core\Request\Refund;
use Payum\Core\Reply\HttpResponse;

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

        $payment->execute($capture = new Capture($token));

        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        $this->redirect($token->getAfterUrl());
    }

    public function actionAuthorize()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $payment->execute($capture = new Authorize($token));

        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        $this->redirect($token->getAfterUrl());
    }

    public function actionNotify()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $payment->execute($capture = new Notify($token));
    }

    public function actionRefund()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $payment->execute($capture = new Refund($token));

        $this->redirect($token->getAfterUrl());
    }

    public function handleException(\CExceptionEvent $event)
    {
        if (false == $event->exception instanceof ReplyInterface) {
            return;
        }

        $reply = $event->exception;

        if ($reply instanceof HttpRedirect) {
            $this->redirect($reply->getUrl(), true);
            $event->handled = true;

            return;
        }

        if ($reply instanceof HttpResponse) {
            $this->layout = false;
            foreach ($reply->getHeaders() as $header) {
                header($header);
            }
            $this->renderText($reply->getContent());
            $event->handled = true;

            return;
        }

        $ro = new \ReflectionObject($reply);

        $event->exception = new LogicException(
            sprintf('Cannot convert reply %s to Yii response.', $ro->getShortName()),
            null,
            $reply
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
