<?php

namespace Az2009\Cielo\Model\Method\BankSlip\Request;

class Payment extends \Magento\Framework\DataObject
{

    const TYPE = 'Boleto';

    const DATE_FORMAT = 'd/m/Y';

    /**
     * @var \Az2009\Cielo\Helper\BankSlip
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    public function __construct(
        \Az2009\Cielo\Helper\BankSlip $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($data);
    }

    public function getRequest()
    {
        $this->order = $this->getOrder();
        $payment = $this->order
                        ->getPayment()
                        ->getMethodInstance();

        $info = $payment->getInfoInstance();

        $this->setInfo($info);
        $this->setPayment($payment);

        return $this->setData(
                        [
                           'Payment' => [
                               'Type' => Payment::TYPE,
                               'Amount' => $info->getAmount(),
                               'Provider' => $this->helper->prepareString($this->helper->getProvider(), 15, 0),
                               'ExpirationDate' => $this->getExpDate(),
                               'Assignor' => $this->helper->prepareString($this->helper->getAssignor(), 200, 0),
                               'Address' => $this->helper->prepareString($this->helper->getAssignorAddress(),255, 0),
                               'BoletoNumber' => $this->helper->prepareString($this->helper->getBoletoNumber(), 9, 0),
                               'Demonstrative' => $this->helper->prepareString($this->helper->getDemonstrative(), 255, 0),
                               'Identification' => $this->helper->prepareString($info->getAdditionalInformation('bs_identification'), 14, 0),
                               'Instructions' => $this->helper->prepareString($this->getInstructions(), 450, 0)
                           ]
                        ]
                      )->toArray();


    }

    /**
     * generates the date expiration of bank slip
     * @return string
     * @throws \Az2009\Cielo\Exception\BankSlip
     */
    public function getExpDate()
    {
        try {
            $date = new \DateTime();
            $date->add(new \DateInterval($this->helper->getAdditionalDays()));

            return $date->format(self::DATE_FORMAT);

        } catch (\Az2009\Cielo\Exception\BankSlip $e) {
            $this->helper->getLogger()->info($e->getMessage());
            throw new \Az2009\Cielo\Exception\BankSlip(
                __('Occurred an error to generating the expiration date of Bank Slip')
            );
        }
    }

    /**
     * @return bool|string
     */
    public function getInstructions()
    {
        $desc = $this->getPayment()
                     ->getConfigData(
                         'instructions',
                         $this->order->getStoreId()
                     );

        return $desc;
    }
}