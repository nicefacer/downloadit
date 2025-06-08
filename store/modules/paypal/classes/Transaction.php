<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Transaction
{
    /** @var string*/
    protected $id_transaction;

    /** @var float*/
    protected $total_paid;

    /** @var string*/
    protected $payment_status;

    /** @var string*/
    protected $currency;

    /** @var float*/
    protected $shipping;

    /** @var string*/
    protected $payment_date;

    /** @var string*/
    protected $id_invoice;

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = (string) $currency;
        return $this;
    }

    /**
     * @return float
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param float $shipping
     * @return self
     */
    public function setShipping($shipping)
    {
        $this->shipping = (float) $shipping;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentDate()
    {
        return $this->payment_date;
    }

    /**
     * @param string $payment_date
     * @return self
     */
    public function setPaymentDate($payment_date)
    {
        $this->payment_date = (string) $payment_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdInvoice()
    {
        return $this->id_invoice;
    }

    /**
     * @param string $id_invoice
     * @return self
     */
    public function setIdInvoice($id_invoice)
    {
        $this->id_invoice = (string) $id_invoice;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdTransaction()
    {
        return $this->id_transaction;
    }

    /**
     * @param string $id_transaction
     * @return self
     */
    public function setIdTransaction($id_transaction)
    {
        $this->id_transaction = (string) $id_transaction;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalPaid()
    {
        return $this->total_paid;
    }

    /**
     * @param float $total_paid
     * @return self
     */
    public function setTotalPaid($total_paid)
    {
        $this->total_paid = (float) $total_paid;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->payment_status;
    }

    /**
     * @param string $payment_status
     * @return self
     */
    public function setPaymentStatus($payment_status)
    {
        $this->payment_status = (string) $payment_status;
        return $this;
    }

    public function toArray()
    {
        return [
            'transaction_id' => ($this->id_transaction ? $this->id_transaction : ''), //For PaymentModule::validateOrder()
            'id_transaction' => ($this->id_transaction ? $this->id_transaction : ''), //For PaypalOrder::saveOrder()
            'id_invoice' => ($this->id_invoice ? $this->id_invoice : ''),
            'total_paid' => ($this->total_paid ? $this->total_paid : 0),
            'shipping' => ($this->shipping ? $this->shipping : 0),
            'currency' => ($this->currency ? $this->currency : ''),
            'payment_status' => ($this->payment_status ? $this->payment_status : ''),
            'payment_date' => ($this->payment_date ? $this->payment_date : ''),
        ];
    }
}