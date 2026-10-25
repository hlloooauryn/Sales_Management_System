@extends('layouts.admin')

@section('title', 'Catat Pembelian Baru')
@section('header_title', '📥 Catat Pembelian / Restock Bunga')

@section('content')
<form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
    @csrf

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        
        <!-- Left: Item Table -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>Rincian Bunga yang Masuk</h3>
                    <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                        Pilih jenis bunga segar, kuantitas pasokan, dan harga beli per tangkai dari pemasok.
                    </p>
                </div>

                <button type="button" class="btn btn-outline btn-sm" onclick="addItemRow()">
                    ➕ Tambah Baris Bunga
                </button>
            </div>

            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="data-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Produk Bunga</th>
                                <th style="width: 20%;">Jumlah (Qty)</th>
                                <th style="width: 25%;">Harga Beli / Tangkai (Rp)</th>
                                <th style="width: 15%; text-align: right;">Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Dynamic Item Row 0 -->
                            <tr class="item-row" data-index="0">
                                <td>
                                    <select name="items[0][product_id]" class="form-control product-select" required onchange="onProductChange(this, 0)">
                                        <option value="">-- Pilih Bunga --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" data-cost="{{ $prod->buy_price }}" data-stock="{{ $prod->stock }}">
                                                {{ $prod->name }} (Stok saat ini: {{ $prod->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control qty-input" value="10" min="1" required oninput="calculateSubtotal(0)">
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_cost]" class="form-control cost-input" value="0" min="0" step="500" required oninput="calculateSubtotal(0)">
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    <span class="row-subtotal-text" id="subtotal_0">Rp 0</span>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)" style="padding: 4px 8px;" title="Hapus baris">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: var(--color-warm-ivory); font-weight: 700;">
                                <td colspan="3" style="text-align: right; padding: 16px;">
                                    TOTAL KESELURUHAN PEMBELIAN:
                                </td>
                                <td style="text-align: right; font-size: 16px; color: var(--color-muted-olive-dark); padding: 16px;">
                                    <span id="grandTotalText">Rp 0</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Transaction Header Info -->
        <div class="card">
            <div class="card-header">
                <h3>Informasi Transaksi</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="reference_number">No. Referensi / Surat Jalan <span class="req">*</span></label>
                    <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{ old('reference_number', $defaultRef) }}" required style="font-family: monospace; font-weight: 600;">
                    <small style="color: var(--color-text-muted); font-size: 12px;">Nomor faktur/surat jalan dari kebun.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="supplier_id">Pemasok / Kebun Bunga <span class="req">*</span></label>
                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="purchase_date">Tanggal Masuk Barang <span class="req">*</span></label>
                    <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="notes">Catatan Pembelian</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Contoh: Kondisi mawar segar, kelopak utuh, bonus 2 tangkai...">{{ old('notes') }}</textarea>
                </div>

                <div style="background-color: var(--color-sage-green-light); border: 1px solid #C4D9BC; border-radius: var(--radius-sm); padding: 12px; margin-bottom: 20px; font-size: 12.5px;">
                    🌿 <strong>Aturan Stok:</strong> Saat Anda menyimpan transaksi ini, kuantitas bunga di atas akan langsung ditambahkan ke stok fisik sistem.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                    💾 Simpan Pembelian & Tambah Stok
                </button>

                <a href="{{ route('purchases.index') }}" class="btn btn-outline" style="width: 100%; margin-top: 10px; text-align: center;">
                    Batal
                </a>
            </div>
        </div>

    </div>
</form>

<script>
    let rowIndex = 1;

    const productsData = @json($products);

    function onProductChange(selectElem, idx) {
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const cost = selectedOption.getAttribute('data-cost') || 0;
        const row = selectElem.closest('.item-row');
        const costInput = row.querySelector('.cost-input');
        costInput.value = cost;
        calculateSubtotal(idx);
    }

    function calculateSubtotal(idx) {
        const row = document.querySelector(`.item-row[data-index="${idx}"]`);
        if (!row) return;

        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
        const subtotal = qty * cost;

        const subtotalElem = row.querySelector('.row-subtotal-text');
        if (subtotalElem) {
            subtotalElem.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        }

        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            grandTotal += (qty * cost);
        });

        document.getElementById('grandTotalText').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    function addItemRow() {
        const tbody = document.getElementById('itemsBody');
        const currentIdx = rowIndex++;

        let productOptions = '<option value="">-- Pilih Bunga --</option>';
        productsData.forEach(p => {
            productOptions += `<option value="${p.id}" data-cost="${p.buy_price}" data-stock="${p.stock}">${p.name} (Stok: ${p.stock})</option>`;
        });

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.setAttribute('data-index', currentIdx);
        tr.innerHTML = `
            <td>
                <select name="items[${currentIdx}][product_id]" class="form-control product-select" required onchange="onProductChange(this, ${currentIdx})">
                    ${productOptions}
                </select>
            </td>
            <td>
                <input type="number" name="items[${currentIdx}][quantity]" class="form-control qty-input" value="10" min="1" required oninput="calculateSubtotal(${currentIdx})">
            </td>
            <td>
                <input type="number" name="items[${currentIdx}][unit_cost]" class="form-control cost-input" value="0" min="0" step="500" required oninput="calculateSubtotal(${currentIdx})">
            </td>
            <td style="text-align: right; font-weight: 600;">
                <span class="row-subtotal-text" id="subtotal_${currentIdx}">Rp 0</span>
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)" style="padding: 4px 8px;" title="Hapus baris">
                    ✕
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length <= 1) {
            alert('Transaksi pembelian harus memiliki minimal 1 item bunga.');
            return;
        }
        btn.closest('.item-row').remove();
        updateGrandTotal();
    }

    // Hitung subtotal awal baris 0
    document.addEventListener('DOMContentLoaded', () => {
        calculateSubtotal(0);
    });
</script>
@endsection
