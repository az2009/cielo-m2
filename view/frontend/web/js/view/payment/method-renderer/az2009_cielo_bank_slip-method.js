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
                creditCardNumber: '',
                creditCardType: '',
                selectedCardType: '',
                messageValidateDoc: '',
                creditCardName: '',
                creditCardExpMonth: '',
                creditCardExpYear: '',
                creditCardCid: '',
                creditCardSave:'',
                creditCardInstallments:'',
                isShow:'',
                labelCardNumber:'',
                labelCardDue:'',
                labelCardHolder:'',
                labelCardBrand:'',
                labelCardCvv:'',
                labelCardDueMonth:'',
                labelCardDueYear:'',
            },

            initObservable: function () {
                this._super().observe([
                    'creditCardNumber',
                    'creditCardType',
                    'selectedCardType',
                    'messageValidateDoc',
                    'creditCardName',
                    'creditCardExpMonth',
                    'creditCardExpYear',
                    'creditCardCid',
                    'creditCardSave',
                    'creditCardInstallments',
                    'isShow',
                    'labelCardNumber',
                    'labelCardDue',
                    'labelCardHolder',
                    'labelCardBrand',
                    'labelCardCvv',
                    'labelCardDueMonth',
                    'labelCardDueYear',
                ]);

                return this;
            },

            initialize: function() {

                this._super();
                var self = this;

            },

            getCode: function() {
                return 'az2009_cielo_bank_slip';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_type': this.creditCardType(),
                        'cc_number' : this.creditCardNumber(),
                        'cc_name' : this.creditCardName(),
                        'cc_exp_month' : this.creditCardExpMonth(),
                        'cc_exp_year' : this.creditCardExpYear(),
                        'cc_cid' : this.creditCardCid(),
                        'cc_token':this.creditCardSave(),
                        'cc_installments': this.creditCardInstallments() ? this.creditCardInstallments() : 1
                    }
                };
            },

            isShowLegend: function () {
                return true;
            },

            isAvailable: function () {
                return true;
            },

            isPlaceOrderActionAllowed: function(value) {

                return true;
            },

        });
    });