/**
 * This file is part of the Klarna KCO module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
/*global define*/
define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/view/shipping',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/get-totals',
    'Klarna_Kco/js/model/klarna',
    'Klarna_Kco/js/model/config',
    'Klarna_Kco/js/action/select-shipping-method',
    'TIG_PostNL/js/Helper/State'
], function (
    $,
    _,
    Component,
    ko,
    quote,
    selectShippingMethodAction,
    setShippingInformationAction,
    checkoutData,
    getTotals,
    klarna,
    config,
    kcoShippingMethod,
    State
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Klarna_KcoPostnl/shipping-method'
        },
        visible: ko.observable(!config.frontEndShipping),

        /**
         * @return {exports}
         */
        initialize: function () {
            var self = this;
            this._super();
        },

        setupListener: function () {
            $('#onepage-checkout-shipping-method-additional-load').on('change', 'input', function () {
                setShippingInformationAction();
            });
        },

        /**
         * Set shipping information handler
         */
        setShippingInformation: function () {
            if (this.validateShippingInformation()) {
                setShippingInformationAction();
            }
        },

        /**
         * @param {Object} shippingMethod
         * @return {Boolean}
         */
        selectShippingMethod: function (shippingMethod) {
            kcoShippingMethod(shippingMethod);
            return true;
        },

        canUseDeliveryOption: function () {
            var deliveryOptionsActive = window.checkoutConfig.shipping.postnl.shippingoptions_active == 1;
            var deliveryDaysActive = window.checkoutConfig.shipping.postnl.is_deliverydays_active;
            var pakjegemakActive = window.checkoutConfig.shipping.postnl.pakjegemak_active == '1';

            return deliveryOptionsActive && (deliveryDaysActive || pakjegemakActive);
        },

        isPostNLDeliveryMethod: function (method) {
            return method.carrier_code == 'tig_postnl';
        },

        canUsePostnlDeliveryOptions: function (method) {
            if (!this.canUseDeliveryOption()) {
                return false;
            }

            var result = this.isPostNLDeliveryMethod(method);

            if (result) {
                State.method(method);
            }

            return result;
        }
    });
});
