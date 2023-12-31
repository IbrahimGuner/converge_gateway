/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'jquery',
    ],
    function (ko, $) {
        'use strict';
        return {
            /**
             *
             * @param installments
             * @param grandTotal
             * @param precision
             * @param currencyCode
             * @returns {Array}
             */
            getInstallmentsWithPrices: function (allInstallments, grandTotal, precision, currencyCode) {
                let numberOfInstallments = [];
                let dividedAmount = 0;
                let dividedString = "";

                $.each(allInstallments, function (amount, installmentOptions) {
                    $.each(installmentOptions, function (key, installment) {
                        if (grandTotal >= amount) {
                            dividedAmount = (grandTotal / installment).toFixed(precision);
                            dividedString = installment + " x " + dividedAmount + " " + currencyCode;
                            numberOfInstallments.push({
                                key: [dividedString],
                                value: installment
                            });
                        }
                    });
                });

                return numberOfInstallments;
            }
        };
    }
);
