function bindItemDropdown(selectId, valueId, descId, qtyId) {
    const select = document.getElementById(selectId);
    const valueSpan = document.getElementById(valueId);
    const descSpan = document.getElementById(descId);
    const qtyInput = document.getElementById(qtyId);

    function update() {
        const selected = select.options[select.selectedIndex];
        const baseValue = parseFloat(selected.getAttribute('data-value')) || 0;
        const quantity = parseInt(qtyInput.value) || 1;

        valueSpan.textContent = baseValue * quantity;
        descSpan.textContent = selected.getAttribute('data-description') || '--';
    }

    select.addEventListener('change', update);
    qtyInput.addEventListener('input', update);
    window.addEventListener('DOMContentLoaded', update);
}

bindItemDropdown('offeredItemSelect', 'offeredItemValue', 'offeredItemDescription', 'offeredQuantity');
bindItemDropdown('requestedItemSelect', 'requestedItemValue', 'requestedItemDescription', 'requestedQuantity');
