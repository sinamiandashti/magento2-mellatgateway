/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'mellat',
                component: 'MagentoFarsi_Mellat/js/view/payment/method-renderer/mellat-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);