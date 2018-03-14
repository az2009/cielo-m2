/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
        'jquery',
        'uiComponent',
        'mage/translate',
        'Az2009_Cielo/js/model/credit-card-validation/credit-card-data',
        'Az2009_Cielo/js/model/credit-card-validation/credit-card-number-validator',
        'ko'
    ],
    function ($, Component, $t, creditCardData, cardNumberValidator, ko) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Az2009_Cielo/payment/cc-form',
                timeoutMessage: $t('Sorry, but something went wrong. Please contact the seller.'),
                creditCardNumber: '',
                creditCardType: '',
                selectedCardType: '',
                creditCardName: '',
                creditCardExpMonth: '',
                creditCardExpYear: '',
                creditCardCid: '',
                creditCardInstallments:''
            },

            initObservable: function () {
                this._super().observe([
                    'creditCardNumber',
                    'creditCardType',
                    'selectedCardType',
                    'creditCardName',
                    'creditCardExpMonth',
                    'creditCardExpYear',
                    'creditCardCid',
                    'creditCardInstallments'
                ]);

                return this;
            },

            initDataCard: function()
            {
                var self = this;
                var info = window.checkoutConfig.payment.az2009_cielo.info_credit_card;
                self.creditCardNumber(info.cc_number);
                self.creditCardType(info.cc_type);
                self.selectedCardType(info.cc_type);
                self.creditCardName(info.cc_name);
                self.creditCardExpMonth(info.cc_exp_month);
                self.creditCardExpYear(info.cc_exp_year);
                self.creditCardCid(info.cc_cid);
            },

            initialize: function() {
                this._super();
                var self = this;
                this.initDataCard();
                this.creditCardNumber.subscribe(function (value) {
                    var result;

                    if (value.length > 16) {
                        value = value.substr(0, 16);
                        self.creditCardNumber(value);
                    }

                    self.selectedCardType(null);
                    if (value === '' || value === null) {
                        return false;
                    }

                    result = cardNumberValidator(value);
                    if (!result.isPotentiallyValid && !result.isValid) {
                        self.creditCardType(null);
                        return false;
                    }

                    if (result.card !== null) {
                        self.selectedCardType(result.card.type);
                        creditCardData.creditCard = result.card;
                    }

                    if (result.isValid) {
                        creditCardData.creditCardNumber = value;
                        self.creditCardType(result.card.type);
                    } else {
                        self.creditCardType(null);
                    }
                });

            },

            validateCreditCard: function() {
                var number = this.creditCardNumber();
                var result = cardNumberValidator(number);
                if (!result.isValid) {
                    this.creditCardNumber('');
                }
            },

            getCode: function() {
                return 'az2009_cielo';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_type': this.creditCardType(),
                        'cc_number' : this.creditCardNumber(),
                        'cc_number_enc' : this.creditCardNumber(),
                        'cc_name' : this.creditCardName(),
                        'cc_owner' : this.creditCardName(),
                        'cc_exp_month' : this.creditCardExpMonth(),
                        'cc_exp_year' : this.creditCardExpYear(),
                        'cc_cid' : this.creditCardCid(),
                        'cc_cid_enc' : this.creditCardCid(),
                        'cc_token':'',
                        'cc_installments': this.creditCardInstallments() ? this.creditCardInstallments() : 1
                    }
                };
            },

            /**
             * Get list of available credit card types values
             * @returns {Object}
             */
            getCcAvailableTypesValues: function () {
                return _.map(this.getCcAvailableTypes(), function (value, key) {
                    return {
                        'value': key,
                        'type': value
                    };
                });
            },

            /**
             * Get list of available credit card types
             * @returns {Object}
             */
            getCcAvailableTypes: function () {
                return window.checkoutConfig.payment.az2009_cielo.availableTypes;
            },

            /**
             * Get payment icons
             * @param {String} type
             * @returns {Boolean}
             */
            getIcons: function (type) {
                return window.checkoutConfig.payment.az2009_cielo.icons.hasOwnProperty(type) ?
                    window.checkoutConfig.payment.az2009_cielo.icons[type]
                    : false;
            },

            hasInstallments: function() {
                var installments = this.getInstallments();
                if (installments) {
                    return true;
                }

                return false;
            },

            getInstallments: function() {
                var installments = window.checkoutConfig.payment.az2009_cielo.installments;
                var values = {};
                if (installments) {
                    values = _.map(installments, function (value, key) {
                        return {
                            'key': key,
                            'value': value
                        };
                    });
                }

                return values;
            },

            getMethodCurrent:function() {
                return window.checkoutConfig.payment.az2009_cielo.info_credit_card.method;
            }

        });
    });