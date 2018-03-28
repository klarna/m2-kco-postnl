/**
 * This file is part of the Klarna KCO module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
define([
    'ko',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote',
    'Klarna_Kco/js/action/select-shipping-method'
], function (
    ko,
    selectShippingMethodAction,
    checkoutData,
    quote,
    kcoShippingMethod
) {
    var deliveryOptionsAreLoading = ko.observable(false),
        pickupOptionsAreLoading = ko.observable(false),
        fee = ko.observable(null),
        currentSelectedShipmentType = ko.observable(null),
        config = window.checkoutConfig.shipping.postnl,
        pickupAddress = ko.observable(null);

    var isLoading = ko.computed(function () {
        return deliveryOptionsAreLoading() || pickupOptionsAreLoading();
    });

    quote.shippingMethod.subscribe(function (shippingMethod) {
        if (shippingMethod.carrier_code === 'tig_postnl') {
            return;
        }

        pickupAddress(null);
    });

    /**
     * When switching from delivery to pickup, the fee must be removed.
     */
    currentSelectedShipmentType.subscribe(function (value) {
        if (value == 'pickup') {
            fee(0);
        }
    });

    return {
        deliveryPrice: ko.observable(0),
        pickupPrice: ko.observable(0),
        deliveryOptionsAreAvailable: ko.observable(true),
        deliveryOptionsAreLoading: deliveryOptionsAreLoading,
        pickupOptionsAreAvailable: ko.observable(true),
        pickupOptionsAreLoading: pickupOptionsAreLoading,
        currentSelectedShipmentType: currentSelectedShipmentType,
        currentOpenPane: ko.observable(config.is_deliverydays_active ? 'delivery' : 'pickup'),
        pickupAddress: pickupAddress,
        isLoading: isLoading,
        method: ko.observable(null),
        fee: fee,
        deliveryFee: ko.observable(0),
        pickupFee: ko.observable(0),

        /**
         * Make sure that the PostNL shipping method gets selected when the customer picks a delivery or pickup option.
         *
         * @returns {boolean}
         */
        selectShippingMethod: function () {
            selectShippingMethodAction(this.method());
            checkoutData.setSelectedShippingRate(this.method().carrier_code + '_' + this.method().method_code);
            kcoShippingMethod(this.method());
            return true;
        }
    };
});
