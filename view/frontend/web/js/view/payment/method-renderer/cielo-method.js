/**
 * Copyright © Magento, Inc. All rights reserved.
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
                transactionResult: ''
            },
            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
            },
            getCode: function() {
                return 'az2009_cielo';
            },
            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult()
                    }
                };
            },
            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.az2009_cielo.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            },
            isActive: function () {
                return true;
            },
            isAvailable: function () {
                return true;
            }
        });
    });
