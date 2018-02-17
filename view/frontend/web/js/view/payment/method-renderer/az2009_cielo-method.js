/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/translate'
    ],
    function ($, Component, $t) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Az2009_Cielo/payment/form',
                timeoutMessage: $t('Sorry, but something went wrong. Please contact the seller.'),
            },

            initObservable: function () {

                this._super()
                    .observe([]);
                return this;
            },

            getCode: function() {
                return 'az2009_cielo';
            },

            getData: function() {
                return {
                    'method': this.item.method
                };
            },

            isShowLegend: function () {
                return true;
            },

            isAvailable: function () {
                return true;
            }
        });
    });