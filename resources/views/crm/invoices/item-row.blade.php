<tr class="invoice-item-row">

    <td>
        <select
            name="items[{{ $index }}][item_type]"
            class="form-select form-select-sm item-type"
        >
            @foreach([
                'material',
                'labor',
                'service',
                'equipment',
                'other'
            ] as $type)

                <option
                    value="{{ $type }}"
                    @selected(($item->item_type ?? 'service') === $type)
                >
                    {{ ucfirst($type) }}
                </option>

            @endforeach
        </select>
    </td>

    <td>
        <input
            type="text"
            name="items[{{ $index }}][description]"
            class="form-control form-control-sm item-description"
            value="{{ old("items.$index.description", $item->description ?? '') }}"
            placeholder="Item description"
            required
        >

        <input
            type="hidden"
            name="items[{{ $index }}][sku]"
            value="{{ old("items.$index.sku", $item->sku ?? '') }}"
        >
    </td>

    <td>
        <input
            type="number"
            name="items[{{ $index }}][quantity]"
            class="form-control form-control-sm item-quantity"
            min="0.001"
            step="0.001"
            value="{{ old("items.$index.quantity", $item->quantity ?? 1) }}"
            required
        >
    </td>

    <td>
        <input
            type="text"
            name="items[{{ $index }}][unit]"
            class="form-control form-control-sm item-unit"
            value="{{ old("items.$index.unit", $item->unit ?? 'unit') }}"
            required
        >
    </td>

    <td>
        <input
            type="number"
            name="items[{{ $index }}][unit_price]"
            class="form-control form-control-sm item-price"
            min="0"
            step="0.01"
            value="{{ old("items.$index.unit_price", $item->unit_price ?? 0) }}"
            required
        >
    </td>

    <td>
        <input
            type="number"
            name="items[{{ $index }}][discount_percent]"
            class="form-control form-control-sm item-discount"
            min="0"
            max="100"
            step="0.01"
            value="{{ old("items.$index.discount_percent", $item->discount_percent ?? 0) }}"
        >
    </td>

    <td>
        <input
            type="number"
            name="items[{{ $index }}][tax_percent]"
            class="form-control form-control-sm item-tax"
            min="0"
            max="100"
            step="0.01"
            value="{{ old("items.$index.tax_percent", $item->tax_percent ?? 0) }}"
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
            title="Remove"
        >
            <i class="bi bi-trash"></i>
        </button>
    </td>

</tr>