document.addEventListener('DOMContentLoaded', function () {

    const itemsContainer = document.getElementById('invoiceItems');
    const addItemButton = document.getElementById('addInvoiceItem');

    if (!itemsContainer) {
        return;
    }

    let itemIndex = itemsContainer.querySelectorAll('.invoice-item-row').length;


    function money(value) {

        value = Number(value) || 0;

        return new Intl.NumberFormat('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);

    }


    function calculateRow(row) {

        const quantity =
            parseFloat(
                row.querySelector('.item-quantity')?.value
            ) || 0;

        const unitPrice =
            parseFloat(
                row.querySelector('.item-price')?.value
            ) || 0;

        const discountPercent =
            parseFloat(
                row.querySelector('.item-discount')?.value
            ) || 0;

        const taxPercent =
            parseFloat(
                row.querySelector('.item-tax')?.value
            ) || 0;


        const subtotal = quantity * unitPrice;

        const discount =
            subtotal * (discountPercent / 100);

        const taxableAmount =
            subtotal - discount;

        const tax =
            taxableAmount * (taxPercent / 100);

        const total =
            taxableAmount + tax;


        const totalElement =
            row.querySelector('.item-total');


        if (totalElement) {

            totalElement.textContent =
                '₱' + money(total);

        }


        return {
            subtotal,
            discount,
            tax,
            total
        };

    }


    function calculateInvoice() {

        let subtotal = 0;
        let discount = 0;
        let tax = 0;

        const rows =
            itemsContainer.querySelectorAll('.invoice-item-row');


        rows.forEach(function (row) {

            const result =
                calculateRow(row);

            subtotal += result.subtotal;
            discount += result.discount;
            tax += result.tax;

        });


        const otherCharges =
            parseFloat(
                document.getElementById('otherCharges')?.value
            ) || 0;


        const grandTotal =
            subtotal -
            discount +
            tax +
            otherCharges;


        const subtotalElement =
            document.getElementById('invoiceSubtotal');

        const discountElement =
            document.getElementById('invoiceDiscount');

        const taxElement =
            document.getElementById('invoiceTax');

        const otherElement =
            document.getElementById('invoiceOtherCharges');

        const grandTotalElement =
            document.getElementById('invoiceGrandTotal');


        if (subtotalElement) {
            subtotalElement.textContent =
                '₱' + money(subtotal);
        }

        if (discountElement) {
            discountElement.textContent =
                '₱' + money(discount);
        }

        if (taxElement) {
            taxElement.textContent =
                '₱' + money(tax);
        }

        if (otherElement) {
            otherElement.textContent =
                '₱' + money(otherCharges);
        }

        if (grandTotalElement) {
            grandTotalElement.textContent =
                '₱' + money(grandTotal);
        }

    }


    function bindRow(row) {

        row.querySelectorAll('input, select')
            .forEach(function (input) {

                input.addEventListener(
                    'input',
                    calculateInvoice
                );

                input.addEventListener(
                    'change',
                    calculateInvoice
                );

            });


        const removeButton =
            row.querySelector('.remove-invoice-item');


        if (removeButton) {

            removeButton.addEventListener(
                'click',
                function () {

                    const rows =
                        itemsContainer.querySelectorAll(
                            '.invoice-item-row'
                        );


                    if (rows.length <= 1) {

                        alert(
                            'An invoice must contain at least one item.'
                        );

                        return;
                    }


                    row.remove();

                    calculateInvoice();

                }
            );

        }

    }


    function addItem() {

        const index = itemIndex++;


        const row = document.createElement('tr');

        row.className =
            'invoice-item-row';


        row.innerHTML = `

            <td>

                <select name="items[${index}][item_type]"
                        class="form-select form-select-sm item-type">

                    <option value="material">Material</option>
                    <option value="labor">Labor</option>
                    <option value="service" selected>Service</option>
                    <option value="equipment">Equipment</option>
                    <option value="other">Other</option>

                </select>

            </td>


            <td>

                <input type="text"
                       name="items[${index}][description]"
                       class="form-control form-control-sm item-description"
                       placeholder="Item description"
                       required>

                <input type="hidden"
                       name="items[${index}][sku]"
                       class="item-sku">

            </td>


            <td>

                <input type="number"
                       name="items[${index}][quantity]"
                       class="form-control form-control-sm item-quantity"
                       value="1"
                       min="0.001"
                       step="0.001"
                       required>

            </td>


            <td>

                <input type="text"
                       name="items[${index}][unit]"
                       class="form-control form-control-sm item-unit"
                       value="unit"
                       required>

            </td>


            <td>

                <input type="number"
                       name="items[${index}][unit_price]"
                       class="form-control form-control-sm item-price"
                       value="0"
                       min="0"
                       step="0.01"
                       required>

            </td>


            <td>

                <input type="number"
                       name="items[${index}][discount_percent]"
                       class="form-control form-control-sm item-discount"
                       value="0"
                       min="0"
                       max="100"
                       step="0.01">

            </td>


            <td>

                <input type="number"
                       name="items[${index}][tax_percent]"
                       class="form-control form-control-sm item-tax"
                       value="0"
                       min="0"
                       max="100"
                       step="0.01">

            </td>


            <td class="text-end">

                <strong class="item-total">
                    ₱0.00
                </strong>

            </td>


            <td class="text-center">

                <button type="button"
                        class="btn btn-sm btn-outline-danger remove-invoice-item"
                        title="Remove item">

                    <i class="bi bi-trash"></i>

                </button>

            </td>

        `;


        itemsContainer.appendChild(row);

        bindRow(row);

        calculateInvoice();

    }


    if (addItemButton) {

        addItemButton.addEventListener(
            'click',
            addItem
        );

    }


    itemsContainer
        .querySelectorAll('.invoice-item-row')
        .forEach(function (row) {

            bindRow(row);

        });


    const otherCharges =
        document.getElementById('otherCharges');


    if (otherCharges) {

        otherCharges.addEventListener(
            'input',
            calculateInvoice
        );

    }


    /*
     * Filter properties based on selected customer.
     */
    const customerSelect =
        document.getElementById('customer_id');

    const propertySelect =
        document.getElementById('property_id');


    function filterProperties() {

        if (!customerSelect || !propertySelect) {
            return;
        }


        const customerId =
            customerSelect.value;


        Array.from(propertySelect.options)
            .forEach(function (option) {

                if (!option.value) {
                    return;
                }


                const optionCustomerId =
                    option.dataset.customerId;


                option.hidden =
                    customerId &&
                    optionCustomerId &&
                    optionCustomerId !== customerId;

            });


        const selected =
            propertySelect.options[
                propertySelect.selectedIndex
            ];


        if (
            selected &&
            selected.value &&
            selected.hidden
        ) {

            propertySelect.value = '';

        }

    }


    if (customerSelect) {

        customerSelect.addEventListener(
            'change',
            filterProperties
        );

        filterProperties();

    }


    calculateInvoice();

});