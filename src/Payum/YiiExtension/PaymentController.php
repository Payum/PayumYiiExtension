<?php
namespace Payum\YiiExtension;

use Payum\Core\Request\BinaryMaskStatusRequest;
use Payum\Core\Request\RedirectUrlInteractiveRequest;
use Payum\Core\Request\SecuredCaptureRequest;

class PaymentController extends \CController
{
    public function actionCapture()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $payment->execute($status = new BinaryMaskStatusRequest($token));
        if (false == $status->isNew()) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
        }

        if ($interactiveRequest = $payment->execute(new SecuredCaptureRequest($token), true)) {
            if ($interactiveRequest instanceof RedirectUrlInteractiveRequest) {
                $this->redirect($interactiveRequest->getUrl(), true);
            }

            throw new \LogicException('Unsupported interactive request', null, $interactiveRequest);
        }

        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        $this->redirect($token->getAfterUrl());
    }

    /**
     * @return \Payum\YiiExtension\PayumComponent
     */
    protected function getPayum()
    {
        return \Yii::app()->payum;
    }
} 