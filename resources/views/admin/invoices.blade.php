@extends('layouts.admin')

@section('title', 'الفواتير')
@section('page-title', 'الفواتير')

@section('content')
  @if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="page-header">
    <h1><i class="fas fa-file-invoice-dollar"></i> الفواتير</h1>
  </div>

  @php
    $totalInvoices = $invoices->count();
    $totalSales = $invoices->where('status', 'paid')->sum('amount');
    $pendingInvoices = $invoices->where('status', 'pending')->count();
  @endphp

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info">
        <h3>إجمالي الفواتير</h3>
        <div class="amount">{{ $totalInvoices }}</div>
      </div>
      <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>إجمالي المبيعات</h3>
        <div class="amount">{{ format_currency($totalSales) }}</div>
      </div>
      <div class="stat-icon green"><i class="fas fa-coins"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>فواتير معلقة</h3>
        <div class="amount">{{ $pendingInvoices }}</div>
      </div>
      <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
    </div>
  </div>

  <!-- Filters -->
  <div class="filters-bar">
    <input type="text" id="searchInvoices" placeholder="بحث برقم الفاتورة أو اسم العميل..." oninput="filterInvoices()">
    <select id="filterInvStatus" onchange="filterInvoices()">
      <option value="">كل الحالات</option>
      <option value="paid">مدفوعة</option>
      <option value="pending">معلقة</option>
    </select>
    <input type="date" id="filterDate" onchange="filterInvoices()">
  </div>

  <!-- Invoices Table -->
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>رقم الفاتورة</th>
          <th>العميل</th>
          <th>الباقة</th>
          <th>المبلغ</th>
          <th>التاريخ</th>
          <th>الحالة</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody id="invoicesTableBody">
        @forelse($invoices as $invoice)
          <tr
            data-id="{{ $invoice->id }}"
            data-user="{{ strtolower($invoice->user->name ?? '') }}"
            data-status="{{ $invoice->status }}"
            data-date="{{ $invoice->created_at->format('Y-m-d') }}">
            <td><strong>#{{ $invoice->id }}</strong></td>
            <td>{{ $invoice->user->name ?? '-' }}</td>
            <td>{{ $invoice->package->name ?? '-' }}</td>
            <td>{{ format_currency($invoice->amount) }}</td>
            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
            <td>
              <span class="badge-status {{ $invoice->status === 'paid' ? 'good' : 'medium' }}">
                {{ $invoice->status === 'paid' ? 'مدفوعة' : 'معلقة' }}
              </span>
            </td>
            <td>
              <button class="btn btn-outline" style="padding:6px 12px;font-size:13px"
                onclick="printInvoice({{ $invoice->id }}, '{{ addslashes($invoice->user->name ?? '') }}', '{{ addslashes($invoice->package->name ?? '') }}', {{ $invoice->amount }}, '{{ $invoice->created_at->format('Y-m-d') }}', '{{ $invoice->status }}')">
                <i class="fas fa-print"></i>
              </button>
            </td>
          </tr>
        @empty
          <tr id="emptyRow">
            <td colspan="7">
              <div class="empty-state">
                <i class="fas fa-file-invoice"></i>
                <h3>لا يوجد فواتير</h3>
                <p>لم يتم العثور على نتائج</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

@push('scripts')
<script>
function filterInvoices() {
  const search = document.getElementById('searchInvoices').value.toLowerCase();
  const statusFilter = document.getElementById('filterInvStatus').value;
  const dateFilter = document.getElementById('filterDate').value;
  const rows = document.querySelectorAll('#invoicesTableBody tr[data-id]');
  let visibleCount = 0;

  rows.forEach(row => {
    const id = row.dataset.id || '';
    const user = row.dataset.user || '';
    const status = row.dataset.status || '';
    const date = row.dataset.date || '';
    const matchSearch = id.includes(search) || user.includes(search);
    const matchStatus = !statusFilter || status === statusFilter;
    const matchDate = !dateFilter || date === dateFilter;
    const visible = matchSearch && matchStatus && matchDate;
    row.style.display = visible ? '' : 'none';
    if (visible) visibleCount++;
  });

  const emptyRow = document.getElementById('emptyRow');
  if (emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';
}

function printInvoice(id, user, packageName, amount, date, status) {
  const printWindow = window.open('', '_blank');
  printWindow.document.write(`
    <html dir="rtl">
    <head>
      <title>فاتورة #${id}</title>
      <style>
        body { font-family: 'Tajawal', Arial, sans-serif; padding: 40px; direction: rtl; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { font-size: 28px; }
        .details { margin: 20px 0; }
        .details p { margin: 10px 0; font-size: 16px; }
        .details strong { display: inline-block; width: 140px; }
        .footer { margin-top: 40px; text-align: center; color: #666; }
      </style>
    </head>
    <body>
      <div class="header">
        <h1>بطاقتي</h1>
        <p>فاتورة رقم #${id}</p>
      </div>
      <div class="details">
        <p><strong>العميل:</strong> ${user}</p>
        <p><strong>الباقة:</strong> ${packageName}</p>
        <p><strong>المبلغ:</strong> ${CURRENCY.format(amount)}</p>
        <p><strong>التاريخ:</strong> ${date}</p>
        <p><strong>الحالة:</strong> ${status === 'paid' ? 'مدفوعة' : 'معلقة'}</p>
      </div>
      <div class="footer"><p>شكراً لتعاملكم معنا</p></div>
    </body>
    </html>
  `);
  printWindow.document.close();
  printWindow.print();
}
</script>
@endpush
