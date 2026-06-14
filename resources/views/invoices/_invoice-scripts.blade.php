<script>
const services = @json($services);
const goods = @json($goods);
const existingItems = @json($existingItems ?? []);
let itemIndex = 0;
let activeSelectForNewService = null;

function formatMoney(amount) {
    return '$' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function buildProductOptions(selectedId = '') {
    let html = '<option value="">— Select a product —</option>';
    html += '<optgroup label="Services">';
    services.forEach(p => {
        html += `<option value="${p.id}" data-price="${p.price}" data-type="service" ${selectedId == p.id ? 'selected' : ''}>${p.name} — $${parseFloat(p.price).toFixed(2)}/${p.unit}</option>`;
    });
    html += '</optgroup>';
    if (goods.length) {
        html += '<optgroup label="Goods">';
        goods.forEach(p => {
            html += `<option value="${p.id}" data-price="${p.price}" data-type="good" ${selectedId == p.id ? 'selected' : ''}>${p.name} — $${parseFloat(p.price).toFixed(2)}/${p.unit}</option>`;
        });
        html += '</optgroup>';
    }
    html += '<option value="__new__">+ Create new service...</option>';
    return html;
}

function addLineItem(productId = '', qty = 1, price = '') {
    const idx = itemIndex++;
    const row = document.createElement('div');
    row.className = 'line-item p-4 bg-slate-50 rounded-xl border border-slate-200';
    row.dataset.index = idx;
    row.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-5">
                <label class="block text-xs font-medium text-slate-500 mb-1">Product / Service</label>
                <select name="items[${idx}][product_id]" class="product-select w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none bg-white" required>
                    ${buildProductOptions(productId)}
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500 mb-1">Qty</label>
                <input type="number" name="items[${idx}][quantity]" value="${qty}" min="1" class="qty-input w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500 mb-1">Unit Price</label>
                <input type="number" name="items[${idx}][unit_price]" value="${price}" step="0.01" min="0" class="price-input w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-500 mb-1">Line Total</label>
                <div class="line-total px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-900">$0.00</div>
            </div>
            <div class="sm:col-span-1 flex justify-end">
                <button type="button" class="remove-item p-2 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition" title="Remove">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    `;
    document.getElementById('line-items').appendChild(row);
    bindRowEvents(row);
    if (productId) updateLineTotal(row);
    recalculateTotals();
}

function bindRowEvents(row) {
    const select = row.querySelector('.product-select');
    const qtyInput = row.querySelector('.qty-input');
    const priceInput = row.querySelector('.price-input');
    const removeBtn = row.querySelector('.remove-item');

    select.addEventListener('change', function() {
        if (this.value === '__new__') {
            activeSelectForNewService = this;
            this.value = '';
            openModal();
            return;
        }
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.price) {
            priceInput.value = parseFloat(opt.dataset.price).toFixed(2);
        }
        updateLineTotal(row);
        recalculateTotals();
    });

    qtyInput.addEventListener('input', () => { updateLineTotal(row); recalculateTotals(); });
    priceInput.addEventListener('input', () => { updateLineTotal(row); recalculateTotals(); });
    removeBtn.addEventListener('click', () => {
        if (document.querySelectorAll('.line-item').length <= 1) {
            alert('Invoice must have at least one line item.');
            return;
        }
        row.remove();
        recalculateTotals();
    });
}

function updateLineTotal(row) {
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    row.querySelector('.line-total').textContent = formatMoney(qty * price);
}

function recalculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.line-item').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        subtotal += qty * price;
    });
    const taxRate = parseFloat(document.getElementById('tax-rate').value) || 0;
    const tax = subtotal * (taxRate / 100);
    const total = subtotal + tax;

    document.getElementById('subtotal-display').textContent = formatMoney(subtotal);
    document.getElementById('tax-display').textContent = formatMoney(tax);
    document.getElementById('total-display').textContent = formatMoney(total);
}

function openModal() {
    document.getElementById('new-service-modal').classList.remove('hidden');
    document.getElementById('new-service-name').focus();
}

function closeModal() {
    document.getElementById('new-service-modal').classList.add('hidden');
    document.getElementById('new-service-error').classList.add('hidden');
    document.getElementById('new-service-name').value = '';
    document.getElementById('new-service-price').value = '';
    document.getElementById('new-service-unit').value = 'hr';
    document.getElementById('new-service-desc').value = '';
    activeSelectForNewService = null;
}

function refreshAllSelects(newProduct) {
    services.push(newProduct);
    document.querySelectorAll('.product-select').forEach(select => {
        const currentVal = select.value;
        select.innerHTML = buildProductOptions(currentVal);
    });
}

document.getElementById('add-item-btn').addEventListener('click', () => addLineItem());
document.getElementById('tax-rate').addEventListener('input', recalculateTotals);
document.getElementById('close-modal').addEventListener('click', closeModal);
document.getElementById('cancel-new-service').addEventListener('click', closeModal);
document.getElementById('modal-backdrop').addEventListener('click', closeModal);

document.getElementById('save-new-service').addEventListener('click', async function() {
    const name = document.getElementById('new-service-name').value.trim();
    const price = document.getElementById('new-service-price').value;
    const unit = document.getElementById('new-service-unit').value.trim() || 'hr';
    const description = document.getElementById('new-service-desc').value.trim();
    const errorEl = document.getElementById('new-service-error');

    if (!name || !price) {
        errorEl.textContent = 'Name and price are required.';
        errorEl.classList.remove('hidden');
        return;
    }

    this.disabled = true;
    this.textContent = 'Saving...';

    try {
        const res = await fetch('{{ route("products.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name, price, unit, description, type: 'service' }),
        });

        const data = await res.json();
        if (!res.ok) {
            const msg = data.message
                || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                || 'Failed to create service';
            throw new Error(msg);
        }

        refreshAllSelects(data.product);

        if (activeSelectForNewService) {
            activeSelectForNewService.value = data.product.id;
            const row = activeSelectForNewService.closest('.line-item');
            row.querySelector('.price-input').value = parseFloat(data.product.price).toFixed(2);
            updateLineTotal(row);
            recalculateTotals();
        }

        closeModal();
    } catch (err) {
        errorEl.textContent = err.message;
        errorEl.classList.remove('hidden');
    } finally {
        this.disabled = false;
        this.textContent = 'Save & Select';
    }
});

if (existingItems.length > 0) {
    existingItems.forEach(item => {
        addLineItem(item.product_id, item.quantity, parseFloat(item.unit_price).toFixed(2));
    });
} else {
    addLineItem();
}
</script>
