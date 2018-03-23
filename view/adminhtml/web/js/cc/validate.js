requirejs([
    'jquery',
    'jquery/validate',
    'Az2009_Cielo/js/model/credit-card-validation/credit-card-number-validator'
], function($, validate, cardNumberValidator){

    $.validator.addMethod(
        "validate-credit-card",
        function (value, element) {
            var result = cardNumberValidator(value);
            return result.isValid;
        },
        $.mage.__("Invalid Credit Card")
    );

    $.validator.addMethod(
        "validate-credit-card-due-date",
        function (value, element) {
            var date = new Date();
            var month = $('.select-month').val();
            var year = $('.select-year').val();

            if (typeof year == 'undefined' || typeof month == 'undefined') {
                alert('Elements(.select-month, .select-year) necessary to validation(validate-credit-card-due-date) not found');
                return false;
            }

            if (year < date.getFullYear()
                || ((month - 1) < date.getMonth()
                    && year <= date.getFullYear())
            ) {
                return false;
            }

            return true;
        },

        $.mage.__("Invalid Due Date of Credit Card")
    );

});