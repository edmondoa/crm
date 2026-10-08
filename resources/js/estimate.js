document.addEventListener('DOMContentLoaded', function () {

    const body = document.getElementById('estimateItemsBody');
    const template = document.getElementById('estimateItemTemplate');
    const addButton = document.getElementById('addEstimateItem');

    let itemIndex = body
        ? body.querySelectorAll('.estimate-item-row').length
        : 0;


    function money(value) {
        return Number(value || 0)
            .toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
    }


    function calculateRow(row) {

        const quantity =
            parseFloat(
                row.querySelector('.item-quantity')?.value
            ) || 0;

        const cost =
            parseFloat(
                row.querySelector('.item-cost')?.value
            ) || 0;

        const markup =
            parseFloat(
                row.querySelector('.item-markup')?.value
            ) || 0;

        const tax =
            parseFloat(
                row.querySelector('.item-tax')?.value
            ) || 0;


        const unitPrice =
            cost + (cost * markup / 100);

        const lineSubtotal =
            quantity * unitPrice;

        const taxAmount =
            lineSubtotal * tax / 100;

        const total =
            lineSubtotal + taxAmount;


        const priceInput =
            row.querySelector('.item-price');

        if (priceInput) {
            priceInput.value =
                unitPrice.toFixed(2);
        }


        const totalElement =
            row.querySelector('.item-total');

        if (totalElement) {
            totalElement.textContent =
                '₱' + money(total);
        }

        return total;
    }


    function calculateEstimate() {

        let subtotal = 0;

        body
            .querySelectorAll('.estimate-item-row')
            .forEach(function (row) {

                subtotal += calculateRow(row);

            });


        const discount =
            parseFloat(
                document.getElementById('discount')?.value
            ) || 0;

        const tax =
            parseFloat(
                document.getElementById('tax')?.value
            ) || 0;

        const otherCharges =
            parseFloat(
                document.getElementById('other_charges')?.value
            ) || 0;


        const grandTotal =
            Math.max(
                0,
                subtotal
                - discount
                + tax
                + otherCharges
            );


        const subtotalElement =
            document.getElementById(
                'summarySubtotal'
            );

        if (subtotalElement) {
            subtotalElement.textContent =
                '₱' + money(subtotal);
        }


        const grandTotalElement =
            document.getElementById(
                'summaryGrandTotal'
            );

        if (grandTotalElement) {
            grandTotalElement.textContent =
                '₱' + money(grandTotal);
        }
    }


    function addItem() {

        const html =
            template.innerHTML.replaceAll(
                '__INDEX__',
                itemIndex
            );

        body.insertAdjacentHTML(
            'beforeend',
            html
        );

        itemIndex++;

        calculateEstimate();
    }


    addButton?.addEventListener(
        'click',
        addItem
    );


    body?.addEventListener(
        'input',
        function (event) {

            if (
                event.target.matches(
                    '.item-quantity, .item-cost, .item-markup, .item-tax'
                )
            ) {
                calculateEstimate();
            }

        }
    );


    body?.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '.remove-item'
                );

            if (!button) {
                return;
            }

            const rows =
                body.querySelectorAll(
                    '.estimate-item-row'
                );

            if (rows.length <= 1) {
                return;
            }

            button
                .closest('.estimate-item-row')
                .remove();

            calculateEstimate();
        }
    );


    document
        .getElementById('discount')
        ?.addEventListener(
            'input',
            calculateEstimate
        );


    document
        .getElementById('tax')
        ?.addEventListener(
            'input',
            calculateEstimate
        );


    document
        .getElementById('other_charges')
        ?.addEventListener(
            'input',
            calculateEstimate
        );


    calculateEstimate();

});