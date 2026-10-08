<template id="estimateItemTemplate">

<tr class="estimate-item-row">

    <td>

        <select
            name="items[__INDEX__][item_type]"
            class="form-select form-select-sm item-type"
        >

            <option value="material">
                Material
            </option>

            <option value="labor">
                Labor
            </option>

            <option value="service" selected>
                Service
            </option>

            <option value="equipment">
                Equipment
            </option>

            <option value="other">
                Other
            </option>

        </select>

    </td>


    <td>

        <input
            type="text"
            name="items[__INDEX__][description]"
            class="form-control form-control-sm item-description"
            placeholder="Item description"
            required
        >

        <input
            type="hidden"
            name="items[__INDEX__][sku]"
            class="item-sku"
        >

    </td>


    <td>

        <input
            type="number"
            step="0.001"
            min="0.001"
            name="items[__INDEX__][quantity]"
            class="form-control form-control-sm item-quantity"
            value="1"
            required
        >

    </td>


    <td>

        <input
            type="text"
            name="items[__INDEX__][unit]"
            class="form-control form-control-sm item-unit"
            value="unit"
            required
        >

    </td>


    <td>

        <input
            type="number"
            step="0.01"
            min="0"
            name="items[__INDEX__][unit_cost]"
            class="form-control form-control-sm item-cost"
            value="0"
            required
        >

    </td>


    <td>

        <input
            type="number"
            step="0.01"
            min="0"
            name="items[__INDEX__][markup_percent]"
            class="form-control form-control-sm item-markup"
            value="0"
        >

    </td>


    <td>

        <input
            type="number"
            step="0.01"
            class="form-control form-control-sm item-price"
            value="0"
            readonly
        >

    </td>


    <td>

        <input
            type="number"
            step="0.01"
            min="0"
            max="100"
            name="items[__INDEX__][tax_percent]"
            class="form-control form-control-sm item-tax"
            value="0"
        >

    </td>


    <td class="text-end">

        <strong class="item-total">
            ₱0.00
        </strong>

    </td>


    <td>

        <button
            type="button"
            class="btn btn-sm btn-outline-danger remove-item"
        >
            <i class="bi bi-trash"></i>
        </button>

    </td>

</tr>

</template>