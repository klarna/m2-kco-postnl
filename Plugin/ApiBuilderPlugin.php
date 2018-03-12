<?php
/**
 * This file is part of the Klarna KCO module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\KcoPostnl\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use TIG\PostNL\Helper\DeliveryOptions\PickupAddress;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Framework\App\RequestInterface;
use Klarna\Kco\Model\QuoteRepository;
use Magento\Quote\Model\QuoteRepository as MageQuoteRepository;


/**
 * Class ApiBuilderPlugin
 * @package Klarna\EmdOtherAddress\Plugin
 */
class ApiBuilderPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $pickupAddressHelper;
    protected $addressFactory;
    protected $request;
    protected $quoteRepository;
    protected $mageQuoteRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        PickupAddress $pickupAddress,
        AddressFactory $addressFactory,
        RequestInterface $request,
        QuoteRepository $quoteRepository,
        MageQuoteRepository $mageQuoteRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->pickupAddressHelper = $pickupAddress;
        $this->addressFactory = $addressFactory;
        $this->request = $request;
        $this->quoteRepository = $quoteRepository;
        $this->mageQuoteRepository = $mageQuoteRepository;

    }

    /**
     * @param \Klarna\Core\Model\Api\Builder $subject
     * @param array $request
     * @param string $type
     * @return array
     */
    public function beforeSetRequest(\Klarna\Core\Model\Api\Builder $subject, array $request, $type = 'create')
    {
        if ($this->attachmentExists($request)) {
            $request['attachment'] = $this->updateAttachment($request);
        } else {
            $createAttachment = $this->createAttachment();
            if ($createAttachment) {
                $request['attachment'] = $createAttachment;
            }
        }
        return [$request, $type];
    }

    /**
     * @return array|bool
     */
    protected function createAttachment()
    {
        $attachmentBody = [];
        $postnlPickupAddressData = $this->getPostnlPickupAddressData();
        if ($postnlPickupAddressData) {
            $attachmentBody['other_delivery_address'][] = $postnlPickupAddressData;
            return [
                'content_type' => 'application/vnd.klarna.internal.emd-v2+json',
                'body' => json_encode($attachmentBody)
            ];
        }
        return false;

    }

    /**
     * @param $request
     * @return array
     */
    protected function updateAttachment($request)
    {
        $attachmentBody = json_decode($request['attachment']['body'], true);
        $postnlPickupAddressData = $this->getPostnlPickupAddressData();
        if ($postnlPickupAddressData) {
           // $attachmentBody['air_reservation_details'][] = ['pnr'=>'aa3223'];
            $attachmentBody['other_delivery_address'][] = $postnlPickupAddressData;
        }
        return [
            'content_type' => 'application/vnd.klarna.internal.emd-v2+json',
            'body' => json_encode($attachmentBody)
        ];
    }

    /**
     * @param array $request
     * @return bool
     */
    protected function attachmentExists(array $request)
    {
        return isset($request['attachment']) && isset($request['attachment']['content_type']) && isset($request['attachment']['body']);
    }

    /**
     * @return array|bool
     */
    protected function getPostnlPickupAddressData()
    {
        $klarnaOrderId = $this->request->getParam('id');
        $kcoQuote = $this->quoteRepository->getByCheckoutId($klarnaOrderId);
        if ($kcoQuote->getId() && $kcoQuote->getQuoteId()) {
            $quote = $this->mageQuoteRepository->get($kcoQuote->getQuoteId());
            $quotePgAddress = $this->pickupAddressHelper->getPakjeGemakAddressInQuote($quote->getId());
            if ($quotePgAddress->getId()) {
                $streetAddress = is_array($quotePgAddress->getStreet()) ? implode(" ",$quotePgAddress->getStreet()) : $quotePgAddress->getStreet();
                return [
                    'shipping_method' => 'pick-up point',
                    'first_name'      => $quotePgAddress->getFirstname(),
                    'last_name'       => $quotePgAddress->getLastname(),
                    'street_address'  => $streetAddress,
                    'postal_code'     => $quotePgAddress->getPostcode(),
                    'city'            => $quotePgAddress->getCity(),
                    'country'         => $quotePgAddress->getCountryId()
                ];

            }
        }
        return false;
    }
}