define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default'
    ],
    function (
        $,
        Component
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'MagentoFarsi_Mellat/payment/mellat-form',
            },

            getCode: function () {
                return 'mellat';
            },

            isActive: function () {
                return true;
            },

            /** Returns payment acceptance mark image path */
            getPaymentAcceptanceMarkSrc: function () {
                /** @namespace window.checkoutConfig.payment.mellat */
                /** @namespace window.checkoutConfig.payment.mellat.paymentAcceptanceMarkSrc */
                //return window.checkoutConfig.payment.mellat.paymentAcceptanceMarkSrc;
                return window.checkoutConfig.payment[this.getCode()].paymentAcceptanceMarkSrc;
            },
        });
    }
);