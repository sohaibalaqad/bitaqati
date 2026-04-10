@extends('layouts.admin')

@section('title', 'البطاقات النشطة')
@section('page-title', 'البطاقات النشطة')

@section('content')
  @if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="page-header">
    <h1><i class="fas fa-bolt"></i> البطاقات النشطة</h1>
  </div>

  @php
    $usedCount = $recentSold->where('is_used', true)->count();
  @endphp

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info">
        <h3>بطاقات مباعة اليوم</h3>
        <div class="amount">{{ $soldToday }}</div>
      </div>
      <div class="stat-icon green"><i class="fas fa-shopping-cart"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>إجمالي المباعة</h3>
        <div class="amount">{{ $totalSold }}</div>
      </div>
      <div class="stat-icon blue"><i class="fas fa-check-double"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>مُستخدمة فعلياً</h3>
        <div class="amount">{{ $usedCount }}</div>
      </div>
      <div class="stat-icon orange"><i class="fas fa-wifi"></i></div>
    </div>
  </div>

  <div class="section-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px">
    <h2>آخر البطاقات المباعة</h2>
    <input type="text" id="searchCards" placeholder="بحث باسم المستخدم أو العميل..."
      oninput="filterActiveCards(this.value)"
      style="padding:9px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'Tajawal',sans-serif;font-size:14px;width:260px;background:var(--bg-main);color:var(--text-main)">
  </div>

  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>بيانات البطاقة</th>
          <th>الباقة</th>
          <th>بيعت إلى</th>
          <th>تاريخ البيع</th>
          <th><i class="fas fa-clock" style="color:var(--accent)"></i> أول استخدام</th>
          <th>الحالة</th>
        </tr>
      </thead>
      <tbody id="activeCardsBody">
        @forelse($recentSold as $i => $card)
          <tr data-search="{{ strtolower($card->username . ' ' . ($card->buyer->name ?? '')) }}">
            <td>{{ $i + 1 }}</td>
            <td>
              <code style="background:#f1f5f9;padding:3px 8px;border-radius:4px;font-size:13px;direction:ltr;display:inline-block">{{ $card->username }}</code>
            </td>
            <td>
              <span style="font-weight:600">{{ $card->package->name ?? '-' }}</span>
            </td>
            <td>
              <div style="font-weight:600">{{ $card->buyer->name ?? '-' }}</div>
              @if($card->buyer?->phone)
                <div style="font-size:12px;color:var(--text-muted)">{{ $card->buyer->phone }}</div>
              @endif
            </td>
            <td>{{ $card->sold_at ? $card->sold_at->format('Y-m-d H:i') : '-' }}</td>
            <td>
              @if($card->first_used_at)
                <div style="color:#059669;font-weight:600;font-size:13px">
                  <i class="fas fa-check-circle" style="margin-left:3px"></i>
                  {{ \Carbon\Carbon::parse($card->first_used_at)->format('Y-m-d') }}
                </div>
                <div style="font-size:12px;color:var(--text-muted)">{{ \Carbon\Carbon::parse($card->first_used_at)->format('H:i') }}</div>
              @else
                <span style="color:var(--text-muted);font-size:13px">—</span>
              @endif
            </td>
            <td>
              @if($card->first_used_at)
                <span class="badge-status good">نشطة</span>
              @else
                <span class="badge-status medium">لم تُستخدم</span>
              @endif
            </td>
          </tr>
        @empty
          <tr id="emptyCardsRow">
            <td colspan="7">
              <div class="empty-state">
                <i class="fas fa-bolt"></i>
                <h3>لا يوجد بطاقات مباعة بعد</h3>
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
function filterActiveCards(query) {
  const q = query.toLowerCase().trim();
  const rows = document.querySelectorAll('#activeCardsBody tr[data-search]');
  let visible = 0;
  rows.forEach(row => {
    const match = !q || row.dataset.search.includes(q);
    row.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  const emptyRow = document.getElementById('emptyCardsRow');
  if (emptyRow) emptyRow.style.display = visible === 0 ? '' : 'none';
}
</script>
@endpush
