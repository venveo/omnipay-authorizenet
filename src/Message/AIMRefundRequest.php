<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.net AIM Refund Request
 */
class AIMRefundRequest extends AIMAbstractRequest
{
    protected $action = 'refundTransaction';

    public function shouldVoidIfRefundFails()
    {
        return !!$this->getParameter('voidIfRefundFails');
    }

    public function setVoidIfRefundFails($value)
    {
        $this->setParameter('voidIfRefundFails', $value);
    }

    public function getData()
    {
        $this->validate('transactionReference', 'amount');

        $data = $this->getBaseData();
        $data->transactionRequest->amount = $this->getParameter('amount');

        $transactionRef = $this->getTransactionReference();
        $card = $transactionRef->getCard();
        // If we have the full credit card number saved
        if ($card && strlen($card->number) > 4) {
            $data->transactionRequest->payment->creditCard->cardNumber = $card->number;
            $data->transactionRequest->payment->creditCard->expirationDate = $card->expiry;
        } elseif ($cardRef = $transactionRef->getCardReference()) {
            $data->transactionRequest->profile->customerProfileId = $cardRef->getCustomerProfileId();
            $data->transactionRequest->profile->paymentProfile->paymentProfileId = $cardRef->getPaymentProfileId();
        } else {
            // No expiration, so use last 4 provided as a card and transaction reference
            // Transaction reference only contains the transaction ID, so a card is required
            $this->validate('card');
            $card = $this->getCard();
            $data->transactionRequest->payment->creditCard->cardNumber = $card->getNumberLastFour();
            // This is not a mistake.
            $data->transactionRequest->payment->creditCard->expirationDate = 'XXXX';
        }
        $data->transactionRequest->refTransId = $transactionRef->getTransId();

        $this->addTransactionSettings($data);

        return $data;
    }

    public function send()
    {
        /** @var AIMResponse $response */
        $response = parent::send();

        if (!$response->isSuccessful() && $this->shouldVoidIfRefundFails() &&
            $response->getReasonCode() == AIMResponse::ERROR_RESPONSE_CODE_CANNOT_ISSUE_CREDIT
        ) {
            // This transaction has not yet been settled, hence cannot be refunded. But a void is possible.
            $voidRequest = new CIMVoidRequest($this->httpClient, $this->httpRequest);
            $voidRequest->initialize($this->getParameters());
            $response = $voidRequest->send();
        }

        return $response;
    }
}
