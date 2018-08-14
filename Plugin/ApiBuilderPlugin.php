<?php
/**
 * This file is part of the Klarna KCO PostNL module
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
 * @package Klarna\KcoPostnl\Plugin
 */
class ApiBuilderPlugin
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var PickupAddress
     */
    private $pickupAddressHelper;
    /**
     * @var AddressFactory
     */
    private $addressFactory;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;
    /**
     * @var MageQuoteRepository
     */
    private $mageQuoteRepository;

    /**
     * ApiBuilderPlugin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param PickupAddress $pickupAddress
     * @param AddressFactory $addressFactory
     * @param RequestInterface $request
     * @param QuoteRepository $quoteRepository
     * @param MageQuoteRepository $mageQuoteRepository
     */
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
     * Plugin before klarna kco api builder set request
     *
     * @param \Klarna\Core\Model\Api\Builder $subject
     * @param array $request
     * @param string $type
     *
     * @return array
     * @SuppressWarnings(PMD.UnusedFormalParameter)
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
     * create attachment
     *
     * @return array|bool
     */
    private function createAttachment()
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
     * update attachment
     *
     * @param $request
     *
     * @return array
     */
    private function updateAttachment($request)
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
     * check if request has attachment already
     *
     * @param array $request
     *
     * @return bool
     */
    private function attachmentExists(array $request)
    {
        return isset($request['attachment'])
            && isset($request['attachment']['content_type'])
            && isset($request['attachment']['body']);
    }

    /**
     * get pick up address from postnl
     *
     * @return array|bool
     */
    private function getPostnlPickupAddressData()
    {
        $klarnaOrderId = $this->request->getParam('id');
        $kcoQuote = $this->quoteRepository->getByCheckoutId($klarnaOrderId);
        if ($kcoQuote->getId() && $kcoQuote->getQuoteId()) {
            $quote = $this->mageQuoteRepository->get($kcoQuote->getQuoteId());
            $quotePgAddress = $this->pickupAddressHelper->getPakjeGemakAddressInQuote($quote->getId());
            if ($quotePgAddress->getId()) {
                $streetAddress = is_array($quotePgAddress->getStreet())
                    ? implode(' ', $quotePgAddress->getStreet()) : $quotePgAddress->getStreet();
                return [
                    'shipping_method' => 'pick-up point',
                    'first_name' => $quotePgAddress->getFirstname(),
                    'last_name' => $quotePgAddress->getLastname(),
                    'street_address' => $streetAddress,
                    'postal_code' => $quotePgAddress->getPostcode(),
                    'city' => $quotePgAddress->getCity(),
                    'country' => $quotePgAddress->getCountryId()
                ];
            }
        }
        return false;
    }
}
