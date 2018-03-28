/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/translate',
        'Az2009_Cielo/js/model/credit-card-validation/validate-docnumber',
        'ko'
    ],
    function ($, Component, $t, validateDoc, ko) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Az2009_Cielo/payment/bank-slip-form',
                timeoutMessage: $t('Sorry, but something went wrong. Please contact the seller.'),
                bankSlipIdent: '',
                bankSlipValid: $t('Number')
            },

            initObservable: function () {
                this._super().observe([
                    'bankSlipIdent',
                    'bankSlipValid'
                ]);

                return this;
            },

            initialize: function() {

                this._super();
                var self = this;

                this.bankSlipIdent.subscribe(function (value) {

                    if (value.length > 14) {
                        value = value.substr(0, 14);
                        self.bankSlipIdent(value);
                    }

                    var result = validateDoc(value);

                    if (!result.isValid) {
                        self.bankSlipValid($t(result.message));
                    } else {
                        self.bankSlipValid(result.type.toUpperCase());
                    }

                });

            },

            getCode: function() {
                return 'az2009_cielo_bank_slip';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'bs_identification': this.bankSlipIdent()
                    }
                };
            },

            isShowLegend: function () {
                return true;
            },

            isAvailable: function () {
                return true;
            },

            isPlaceOrderActionAllowed: function() {
                var result = validateDoc(this.bankSlipIdent());
                if (!result.isValid) {
                    return false;
                }

                return true;
            }

        });
    });