@extends('layouts.admin')

@section('title', 'الدعم الفني')
@section('page-title', 'الدعم الفني')

@section('content')
  @if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="page-header">
    <h1><i class="fas fa-headset"></i> الدعم الفني</h1>
  </div>

  @php
    $totalTickets = $tickets->count();
    $openTickets = $tickets->whereIn('status', ['open', 'in-progress'])->count();
    $resolvedTickets = $tickets->where('status', 'resolved')->count();
  @endphp

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info"><h3>إجمالي التذاكر</h3><div class="amount">{{ $totalTickets }}</div></div>
      <div class="stat-icon blue"><i class="fas fa-ticket-alt"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info"><h3>مفتوحة</h3><div class="amount">{{ $openTickets }}</div></div>
      <div class="stat-icon orange"><i class="fas fa-exclamation-circle"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info"><h3>محلولة</h3><div class="amount">{{ $resolvedTickets }}</div></div>
      <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
    </div>
  </div>

  <div class="filters-bar">
    <input type="text" id="searchTickets" placeholder="بحث بالعنوان أو اسم العميل..." oninput="filterTickets()">
    <select id="filterTicketStatus" onchange="filterTickets()">
      <option value="">كل الحالات</option>
      <option value="open">مفتوحة</option>
      <option value="in-progress">قيد المعالجة</option>
      <option value="resolved">محلولة</option>
    </select>
    <select id="filterPriority" onchange="filterTickets()">
      <option value="">كل الأولويات</option>
      <option value="high">عاجل</option>
      <option value="medium">متوسط</option>
      <option value="low">منخفض</option>
    </select>
  </div>

  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>العنوان</th>
          <th>العميل</th>
          <th>الأولوية</th>
          <th>التاريخ</th>
          <th>الحالة</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody id="ticketsBody">
        @forelse($tickets as $i => $ticket)
          @php
            $statusMap = [
              'open'        => ['label' => 'مفتوحة',        'cls' => 'medium'],
              'in-progress' => ['label' => 'قيد المعالجة',  'cls' => 'medium'],
              'resolved'    => ['label' => 'محلولة',        'cls' => 'good'],
            ];
            $priorityMap = [
              'high'   => ['label' => 'عاجل',    'cls' => 'low'],
              'medium' => ['label' => 'متوسط',   'cls' => 'medium'],
              'low'    => ['label' => 'منخفض',   'cls' => 'good'],
            ];
            $st = $statusMap[$ticket->status]   ?? $statusMap['open'];
            $pr = $priorityMap[$ticket->priority] ?? $priorityMap['medium'];
          @endphp
          <tr
            data-title="{{ strtolower($ticket->subject ?? $ticket->title ?? '') }}"
            data-user="{{ strtolower($ticket->user->name ?? '') }}"
            data-status="{{ $ticket->status }}"
            data-priority="{{ $ticket->priority ?? 'medium' }}">
            <td>{{ $i + 1 }}</td>
            <td>
              <strong>{{ $ticket->subject ?? $ticket->title ?? '-' }}</strong>
              @if(!empty($ticket->message ?? $ticket->desc))
                <br><small style="color:var(--text-muted)">{{ \Str::limit($ticket->message ?? $ticket->desc, 50) }}</small>
              @endif
            </td>
            <td>{{ $ticket->user->name ?? '-' }}</td>
            <td><span class="badge-status {{ $pr['cls'] }}">{{ $pr['label'] }}</span></td>
            <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
            <td><span class="badge-status {{ $st['cls'] }}">{{ $st['label'] }}</span></td>
            <td style="white-space:nowrap">
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <a href="{{ route('admin.support.show', $ticket->id) }}"
                  class="btn btn-outline" style="font-size:12px;padding:5px 12px">
                  <i class="fas fa-eye"></i> عرض
                </a>
                @if($ticket->status !== 'resolved')
                  <form method="POST" action="{{ route('admin.support.update-status', $ticket->id) }}" style="display:inline">
                    @csrf
                    <select name="status" onchange="this.form.submit()" class="form-control"
                      style="padding:5px 10px;font-size:12px;height:auto;display:inline-block;width:auto;border-radius:var(--radius-sm)">
                      <option value="" disabled selected>تغيير الحالة</option>
                      @if($ticket->status !== 'open')
                        <option value="open">مفتوحة</option>
                      @endif
                      @if($ticket->status !== 'in-progress')
                        <option value="in-progress">قيد المعالجة</option>
                      @endif
                      <option value="resolved">محلولة ✓</option>
                    </select>
                  </form>
                @else
                  <span style="color:#10b981;font-size:12px;font-weight:600"><i class="fas fa-check-circle"></i> محلولة</span>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr id="emptyRow">
            <td colspan="7">
              <div class="empty-state">
                <i class="fas fa-headset"></i>
                <h3>لا يوجد تذاكر دعم</h3>
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
function filterTickets() {
  const search = document.getElementById('searchTickets').value.toLowerCase();
  const statusFilter = document.getElementById('filterTicketStatus').value;
  const priorityFilter = document.getElementById('filterPriority').value;
  const rows = document.querySelectorAll('#ticketsBody tr[data-title]');
  let visibleCount = 0;

  rows.forEach(row => {
    const title = row.dataset.title || '';
    const user = row.dataset.user || '';
    const status = row.dataset.status || '';
    const priority = row.dataset.priority || '';
    const matchSearch = title.includes(search) || user.includes(search);
    const matchStatus = !statusFilter || status === statusFilter;
    const matchPriority = !priorityFilter || priority === priorityFilter;
    const visible = matchSearch && matchStatus && matchPriority;
    row.style.display = visible ? '' : 'none';
    if (visible) visibleCount++;
  });

  const emptyRow = document.getElementById('emptyRow');
  if (emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';
}
</script>
@endpush
