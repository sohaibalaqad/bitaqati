@extends('layouts.admin')

@section('title', 'إدارة الصفحة الرئيسية')
@section('page-title', 'إدارة الصفحة الرئيسية')

@push('styles')
<style>
.hp-card {
    background:#fff; border:1px solid var(--border); border-radius:var(--radius);
    padding:0; transition:all .2s; overflow:hidden;
}
.hp-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.08); }
.hp-card-head {
    padding:18px 22px; display:flex; align-items:center;
    gap:14px; cursor:grab; border-bottom:1px solid var(--border);
    background:var(--bg-main);
}
.hp-card-head:active { cursor:grabbing; }
.drag-handle { color:var(--text-muted); flex-shrink:0; }
.hp-badge {
    padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700;
    background:rgba(16,185,129,.1); color:#059669;
}
.hp-badge.off { background:rgba(100,116,139,.1); color:var(--text-muted); }
.hp-card-body { padding:18px 22px; }
.hp-card-footer {
    padding:14px 22px; display:flex; align-items:center; gap:10px;
    border-top:1px solid var(--border); background:var(--bg-main);
}
.sort-num {
    width:28px; height:28px; background:var(--accent); color:#fff;
    border-radius:50%; display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:700; flex-shrink:0;
}
.ghost { opacity:.4; background:#e2e8f0 !important; }
</style>
@endpush

@section('content')
@if(session('success'))
  <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
  </div>
@endif

<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
  <h1 style="font-size:22px;font-weight:800;color:var(--text-dark)">
    <svg width="22" height="22" style="vertical-align:-4px;margin-left:8px;color:var(--accent)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
    إدارة الصفحة الرئيسية
  </h1>
  <div style="display:flex;gap:10px">
    <a href="{{ route('admin.homepage.plans') }}" class="btn btn-outline" style="font-size:14px;padding:9px 18px">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      إدارة الخطط
    </a>
    <a href="{{ url('/') }}" target="_blank" class="btn btn-primary" style="font-size:14px;padding:9px 18px">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
      معاينة الصفحة
    </a>
  </div>
</div>

<p style="font-size:14px;color:var(--text-muted);margin-bottom:20px">اسحب الأقسام لتغيير ترتيبها. التغييرات تُحفظ تلقائياً.</p>

<div id="sectionsList" style="display:flex;flex-direction:column;gap:14px">
  @foreach($sections as $section)
  <div class="hp-card" data-id="{{ $section->id }}">
    <div class="hp-card-head">
      <div class="drag-handle">
        <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 110 4 2 2 0 010-4zM7 8a2 2 0 110 4 2 2 0 010-4zM7 14a2 2 0 110 4 2 2 0 010-4zM13 2a2 2 0 110 4 2 2 0 010-4zM13 8a2 2 0 110 4 2 2 0 010-4zM13 14a2 2 0 110 4 2 2 0 010-4z"/></svg>
      </div>
      <div class="sort-num">{{ $loop->iteration }}</div>
      <div style="flex:1">
        <div style="font-weight:700;font-size:15px;color:var(--text-dark)">{{ $section->label }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;font-family:monospace">{{ $section->type }}</div>
      </div>
      <span class="hp-badge {{ $section->is_active ? '' : 'off' }}" id="badge-{{ $section->id }}">
        {{ $section->is_active ? 'مفعّل' : 'معطّل' }}
      </span>
    </div>
    <div class="hp-card-body">
      <div style="font-size:13px;color:var(--text-muted)">
        @php $c = $section->content ?? []; @endphp
        {{ Str::limit($c['title'] ?? $c['subtitle'] ?? '—', 80) }}
      </div>
    </div>
    <div class="hp-card-footer">
      <a href="{{ route('admin.homepage.edit', $section) }}" class="btn btn-outline" style="font-size:13px;padding:7px 14px">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        تعديل
      </a>
      <button onclick="toggleSection({{ $section->id }}, this)"
        class="btn {{ $section->is_active ? 'btn-outline' : 'btn-primary' }}"
        style="font-size:13px;padding:7px 14px" data-active="{{ $section->is_active ? '1' : '0' }}">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          @if($section->is_active)
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
          @else
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          @endif
        </svg>
        {{ $section->is_active ? 'إخفاء' : 'إظهار' }}
      </button>
      <form action="{{ route('admin.homepage.destroy', $section) }}" method="POST" style="margin-right:auto"
        onsubmit="return confirm('حذف هذا القسم نهائياً؟')">
        @csrf @method('DELETE')
        <button class="btn" style="font-size:13px;padding:7px 14px;background:rgba(220,38,38,.07);color:#dc2626;border:1px solid rgba(220,38,38,.2)">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
          حذف
        </button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
// ── Drag & Drop sort ───────────────────────────────────────────────
const list = document.getElementById('sectionsList');
Sortable.create(list, {
  handle: '.drag-handle',
  animation: 150,
  ghostClass: 'ghost',
  onEnd() {
    const order = [...list.children].map(el => el.dataset.id);
    fetch('{{ route('admin.homepage.reorder') }}', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body: JSON.stringify({ order }),
    });
    // Update displayed numbers
    [...list.children].forEach((el, i) => {
      el.querySelector('.sort-num').textContent = i + 1;
    });
  },
});

// ── Toggle active ─────────────────────────────────────────────────
function toggleSection(id, btn) {
  fetch(`/admin/homepage/${id}/toggle`, {
    method: 'POST',
    headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
  })
  .then(r => r.json())
  .then(data => {
    const badge = document.getElementById('badge-' + id);
    if (data.active) {
      badge.textContent = 'مفعّل'; badge.classList.remove('off');
      btn.innerHTML = btn.innerHTML.replace('إظهار','إخفاء');
      btn.className = 'btn btn-outline'; btn.style.cssText = 'font-size:13px;padding:7px 14px';
    } else {
      badge.textContent = 'معطّل'; badge.classList.add('off');
      btn.innerHTML = btn.innerHTML.replace('إخفاء','إظهار');
      btn.className = 'btn btn-primary'; btn.style.cssText = 'font-size:13px;padding:7px 14px';
    }
  });
}
</script>
@endpush
