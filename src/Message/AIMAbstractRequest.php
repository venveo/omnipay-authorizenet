<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\AuthorizeNetItem;
use Omnipay\AuthorizeNet\AuthorizeNetItemBag;
use Omnipay\AuthorizeNet\Model\CardReference;
use Omnipay\AuthorizeNet\Model\TransactionReference;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Authorize.Net AIM Abstract Request
 */
abstract class AIMAbstractRequest extends AbstractRequest
{
    protected $requestType = 'createTransactionRequest';
    protected $action = null;

    public function getApiLoginId()
    {
        return $this->getParameter('apiLoginId');
    }

    public function setApiLoginId($value)
    {
        return $this->setParameter('apiLoginId', $value);
    }

    public function getTransactionKey()
    {
        return $this->getParameter('transactionKey');
    }

    public function setTransactionKey($value)
    {
        return $this->setParameter('transactionKey', $value);
    }

    // AIM does not use signatureKey, but it is included here just
    // to get the tests to run. Some structural refactoring will be
    // needed to work around this.

    public function getSignatureKey()
    {
        return $this->getParameter('signatureKey');
    }

    public function setSignatureKey($value)
    {
        return $this->setParameter('signatureKey', $value);
    }

    public function getDeveloperMode()
    {
        return $this->getParameter('developerMode');
    }

    public function setDeveloperMode($value)
    {
        return $this->setParameter('developerMode', $value);
    }

    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }

    public function getHashSecret()
    {
        return $this->getParameter('hashSecret');
    }

    public function setHashSecret($value)
    {
        return $this->setParameter('hashSecret', $value);
    }

    public function setDuplicateWindow($value)
    {
        $this->setParameter('duplicateWindow', $value);
    }

    private function getDuplicateWindow()
    {
        return $this->getParameter('duplicateWindow');
    }

    public function getLiveEndpoint()
    {
        return $this->getParameter('liveEndpoint');
    }

    public function setLiveEndpoint($value)
    {
        return $this->setParameter('liveEndpoint', $value);
    }

    public function getDeveloperEndpoint()
    {
        return $this->getParameter('developerEndpoint');
    }

    public function setDeveloperEndpoint($value)
    {
        return $this->setParameter('developerEndpoint', $value);
    }

    public function getEndpoint()
    {
        return $this->getDeveloperMode() ? $this->getDeveloperEndpoint() : $this->getLiveEndpoint();
    }

    public function getSolutionId()
    {
        return $this->getParameter('solutionId');
    }

    public function setSolutionId($value)
    {
        return $this->setParameter('solutionId', $value);
    }

    public function getAuthCode()
    {
        return $this->getParameter('authCode');
    }

    public function setAuthCode($value)
    {
        return $this->setParameter('authCode', $value);
    }

    public function getTaxAmount()
    {
        return $this->getParameter('taxAmount');
    }

    public function setTaxAmount($value)
    {
        return $this->setParameter('taxAmount', $value);
    }

    public function getTaxName()
    {
        return $this->getParameter('taxName');
    }

    public function setTaxName($value)
    {
        return $this->setParameter('taxName', $value);
    }

    public function getTaxDescription()
    {
        return $this->getParameter('taxDescription');
    }

    public function setTaxDescription($value)
    {
        return $this->setParameter('taxDescription', $value);
    }


    public function getShippingAmount()
    {
        return $this->getParameter('shippingAmount');
    }

    public function setShippingAmount($value)
    {
        return $this->setParameter('shippingAmount', $value);
    }

    public function getShippingName()
    {
        return $this->getParameter('shippingName');
    }

    public function setShippingName($value)
    {
        return $this->setParameter('shippingName', $value);
    }

    public function getShippingDescription()
    {
        return $this->getParameter('shippingDescription');
    }

    public function setShippingDescription($value)
    {
        return $this->setParameter('shippingDescription', $value);
    }

    /**
     * @return TransactionReference
     */
    public function getTransactionReference()
    {
        return $this->getParameter('transactionReference');
    }

    public function setTransactionReference($value)
    {
        if (substr($value, 0, 1) === '{') {
            // Value is a complex key containing the transaction ID and other properties
            $transactionRef = new TransactionReference($value);
        } else {
            // Value just contains the transaction ID
            $transactionRef = new TransactionReference();
            $transactionRef->setTransId($value);
        }

        return $this->setParameter('transactionReference', $transactionRef);
    }

    /**
     * @param string|CardReference $value
     * @return AbstractRequest
     */
    public function setCardReference($value)
    {
        if (!($value instanceof CardReference)) {
            $value = new CardReference($value);
        }

        return parent::setCardReference($value);
    }

    /**
     * @param bool $serialize Determines whether the return value will be a string or object
     * @return string|CardReference
     */
    public function getCardReference($serialize = true)
    {
        $value = parent::getCardReference();

        if ($serialize) {
            $value = (string)$value;
        }

        return $value;
    }

    public function getInvoiceNumber()
    {
        return $this->getParameter('invoiceNumber');
    }

    public function setInvoiceNumber($value)
    {
        return $this->setParameter('invoiceNumber', $value);
    }

    /**
     * @link http://developer.authorize.net/api/reference/features/acceptjs.html Documentation on opaque data
     * @return string
     */
    public function getOpaqueDataDescriptor()
    {
        return $this->getParameter('opaqueDataDescriptor');
    }

    /**
     * @link http://developer.authorize.net/api/reference/features/acceptjs.html Documentation on opaque data
     * @return string
     */
    public function getOpaqueDataValue()
    {
        return $this->getParameter('opaqueDataValue');
    }

    /**
     * @link http://developer.authorize.net/api/reference/features/acceptjs.html Documentation on opaque data
     * @param string
     * @return string
     */
    public function setOpaqueDataDescriptor($value)
    {
        return $this->setParameter('opaqueDataDescriptor', $value);
    }

    /**
     * @link http://developer.authorize.net/api/reference/features/acceptjs.html Documentation on opaque data
     * @param string
     * @return string
     */
    public function setOpaqueDataValue($value)
    {
        return $this->setParameter('opaqueDataValue', $value);
    }

    public function sendData($data)
    {
        $headers = array('Content-Type' => 'text/xml; charset=utf-8');

        $data = $data->saveXml();
        $httpResponse = $this->httpClient->request('POST', $this->getEndpoint(), $headers, $data);

        return $this->response = new AIMResponse($this, $httpResponse->getBody()->getContents());
    }

    /**
     * @return mixed|\SimpleXMLElement
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getBaseData()
    {
        $data = new \SimpleXMLElement('<' . $this->requestType . '/>');

        $data->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');
        $this->addAuthentication($data);
        $this->addReferenceId($data);
        $this->addTransactionType($data);

        return $data;
    }

    protected function addAuthentication(\SimpleXMLElement $data)
    {
        $data->merchantAuthentication->name = $this->getApiLoginId();
        $data->merchantAuthentication->transactionKey = $this->getTransactionKey();
    }

    protected function addReferenceId(\SimpleXMLElement $data)
    {
        $txnId = $this->getTransactionId();

        if (!empty($txnId)) {
            $data->refId = $this->getTransactionId();
        }
    }

    protected function addTransactionType(\SimpleXMLElement $data)
    {
        if (! $this->action) {
            // The extending class probably hasn't specified an "action".
            throw new InvalidRequestException();
        }

        $data->transactionRequest->transactionType = $this->action;
    }

    protected function addSolutionId(\SimpleXMLElement $data)
    {
        $solutionId = $this->getSolutionId();

        if (!empty($solutionId)) {
            $data->transactionRequest->solution->id = $solutionId;
        }
    }

    protected function addOrderData(\SimpleXMLElement $data)
    {

        /** @var mixed $req */
        $req = $data->transactionRequest;

        $req->order->invoiceNumber = $this->getInvoiceNumber();
        $req->order->description = $this->getDescription();
        return $data;
    }

    /**
     * Adds billing data to a partially filled request data object.
     *
     * @param \SimpleXMLElement $data
     *
     * @return \SimpleXMLElement
     */
    protected function addBillingData(\SimpleXMLElement $data)
    {
        /** @var mixed $req */
        $req = $data->transactionRequest;

        // Merchant assigned customer ID
        $customer = $this->getCustomerId();
        if (!empty($customer)) {
            $req->customer->id = $customer;
        }

        //$req->order->description = $this->getDescription();

        /** @var CreditCard $card */
        if ($card = $this->getCard()) {
            // A card is present, so include billing and shipping details
            $req->customer->email = $card->getEmail();

            $req->billTo->firstName = $card->getBillingFirstName();
            $req->billTo->lastName = $card->getBillingLastName();
            $req->billTo->company = $card->getBillingCompany();
            $req->billTo->address = trim($card->getBillingAddress1() . " \n" . $card->getBillingAddress2());
            $req->billTo->city = $card->getBillingCity();
            $req->billTo->state = $card->getBillingState();
            $req->billTo->zip = $card->getBillingPostcode();
            $req->billTo->country = $card->getBillingCountry();
            $req->billTo->phoneNumber = $card->getBillingPhone();

            $req->shipTo->firstName = $card->getShippingFirstName();
            $req->shipTo->lastName = $card->getShippingLastName();
            $req->shipTo->company = $card->getShippingCompany();
            $req->shipTo->address = trim($card->getShippingAddress1() . " \n" . $card->getShippingAddress2());
            $req->shipTo->city = $card->getShippingCity();
            $req->shipTo->state = $card->getShippingState();
            $req->shipTo->zip = $card->getShippingPostcode();
            $req->shipTo->country = $card->getShippingCountry();
        }

        return $data;
    }

    protected function addTransactionSettings(\SimpleXMLElement $data)
    {
        $i = 0;

        // The test mode setting indicates whether or not this is a live request or a test request
        $data->transactionRequest->transactionSettings->setting[$i]->settingName = 'testRequest';
        $data->transactionRequest->transactionSettings->setting[$i]->settingValue = $this->getTestMode()
            ? 'true'
            : 'false';

        // The duplicate window setting specifies the threshold for AuthorizeNet's duplicate transaction detection logic
        if (!is_null($this->getDuplicateWindow())) {
            $i++;
            $data->transactionRequest->transactionSettings->setting[$i]->settingName = 'duplicateWindow';
            $data->transactionRequest->transactionSettings->setting[$i]->settingValue = $this->getDuplicateWindow();
        }

        return $data;
    }

    protected function addItemData(\SimpleXMLElement $data)
    {
        /** @var AuthorizeNetItemBag $itemBag */
        $itemBag = $this->getItems();

        if (empty($itemBag)) {
            return $data;
        }

        /* @var $item AuthorizeNetItem */
        foreach ($itemBag->all() as $idx => $item) {
            // 31 characters
            if ($item->getItemId()) {
                $data->transactionRequest->lineItems->lineItem[$idx]->itemId = $item->getItemId();
            }
            // 31 characters
            if ($item->getName()) {
                $data->transactionRequest->lineItems->lineItem[$idx]->name = $item->getName();
            }
            // 255 characters
            if ($item->getDescription()) {
                $data->transactionRequest->lineItems->lineItem[$idx]->description = $item->getDescription();
            }
            // Up to 4 decimal places
            if ($item->getQuantity()) {
                $data->transactionRequest->lineItems->lineItem[$idx]->quantity = $item->getQuantity();
            }
            // Decimal up to 4 decimal places
            if ($item->getPrice()) {
                $data->transactionRequest->lineItems->lineItem[$idx]->unitPrice = $item->getPrice();
            }
            // boolean
            if (is_bool($item->getTaxable())) {
                $data->transactionRequest->lineItems->lineItem[$idx]->taxable = $item->getTaxable();
            }
        }

        return $data;
    }


    protected function addTaxData(\SimpleXMLElement $data)
    {
        if ($this->getTaxAmount() !== null) {
            $data->transactionRequest->tax->amount = $this->getTaxAmount();
        }
        if ($this->getTaxName() !== null) {
            $data->transactionRequest->tax->name = $this->getTaxName();
        }
        if ($this->getTaxDescription() !== null) {
            $data->transactionRequest->tax->description = $this->getTaxDescription();
        }
        return $data;
    }

    protected function addShippingData(\SimpleXMLElement $data)
    {
        if ($this->getShippingAmount() !== null) {
            $data->transactionRequest->shipping->amount = $this->getTaxAmount();
        }
        if ($this->getShippingName() !== null) {
            $data->transactionRequest->shipping->name = $this->getShippingName();
        }
        if ($this->getShippingDescription() !== null) {
            $data->transactionRequest->shipping->description = $this->getShippingDescription();
        }
        return $data;
    }
}
