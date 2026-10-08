<tr class="estimate-item-row">

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
                    @selected(
                        old(
                            "items.$index.item_type",
                            $item->item_type ?? 'service'
                        ) === $type
                    )
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
            class="item-sku"
            value="{{ old("items.$index.sku", $item->sku ?? '') }}"
        >
    </td>

    <td>
        <input
            type="number"
            step="0.001"
            min="0.001"
            name="items[{{ $index }}][quantity]"
            class="form-control form-control-sm item-quantity"
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
            step="0.01"
            min="0"
            name="items[{{ $index }}][unit_cost]"
            class="form-control form-control-sm item-cost"
            value="{{ old("items.$index.unit_cost", $item->unit_cost ?? 0) }}"
            required
        >
    </td>

    <td>
        <input
            type="number"
            step="0.01"
            min="0"
            name="items[{{ $index }}][markup_percent]"
            class="form-control form-control-sm item-markup"
            value="{{ old("items.$index.markup_percent", $item->markup_percent ?? 0) }}"
        >
    </td>

    <td>
        <input
            type="number"
            step="0.01"
            class="form-control form-control-sm item-price"
            value="{{ old("items.$index.unit_price", $item->unit_price ?? 0) }}"
            readonly
        >
    </td>

    <td>
        <input
            type="number"
            step="0.01"
            min="0"
            max="100"
            name="items[{{ $index }}][tax_percent]"
            class="form-control form-control-sm item-tax"
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