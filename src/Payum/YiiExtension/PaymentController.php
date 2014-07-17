<?php
namespace Payum\YiiExtension;

use Payum\Core\Request\BinaryMaskStatusRequest;
use Payum\Core\Request\Http\RedirectUrlInteractiveRequest;
use Payum\Core\Request\SecuredCaptureRequest;

class PaymentController extends \CController
{
    public function actionCapture()
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($_REQUEST);
        $payment = $this->getPayum()->getRegistry()->getPayment($token->getPaymentName());

        $status = new BinaryMaskStatusRequest($token);
        $payment->execute($status);

        try {
            $capture = new SecuredCaptureRequest($token);
            $payment->execute($capture);
        } catch (RedirectUrlInteractiveRequest $interactiveRequest) {
            $this->redirect($interactiveRequest->getUrl(), true);
            return;
        } catch (\Exception $ex) {
            throw new \Exception('Unsupported request', null, $ex);
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