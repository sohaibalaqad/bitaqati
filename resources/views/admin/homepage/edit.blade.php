@extends('layouts.admin')

@section('title', 'تعديل القسم — ' . $section->label)
@section('page-title', 'تعديل القسم')

@push('styles')
<style>
.edit-card {
    background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:28px;
}
.field-group { margin-bottom:20px; }
.field-group label { display:block; font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:6px; }
.field-group input, .field-group textarea, .field-group select {
    width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px;
    font-size:14px; color:var(--text-dark); background:#fff; font-family:inherit;
    transition:border-color .15s;
}
.field-group input:focus, .field-group textarea:focus, .field-group select:focus {
    outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(14,165,233,.1);
}
.field-group textarea { min-height:90px; resize:vertical; }
.field-hint { font-size:11px; color:var(--text-muted); margin-top:4px; }
.repeater-item {
    border:1px solid var(--border); border-radius:8px; padding:16px; margin-bottom:10px;
    background:var(--bg-main); position:relative;
}
.repeater-remove {
    position:absolute; top:10px; left:10px; width:24px; height:24px; border:none;
    background:rgba(220,38,38,.1); color:#dc2626; border-radius:6px; cursor:pointer;
    display:flex; align-items:center; justify-content:center; font-size:14px;
    transition:background .15s;
}
.repeater-remove:hover { background:rgba(220,38,38,.2); }
.add-row-btn {
    display:inline-flex; align-items:center; gap:6px; padding:8px 16px;
    border:1px dashed var(--border); border-radius:8px; background:transparent;
    color:var(--text-muted); font-size:13px; cursor:pointer; transition:all .15s;
    width:100%; justify-content:center; margin-top:4px;
}
.add-row-btn:hover { border-color:var(--accent); color:var(--accent); background:rgba(14,165,233,.04); }
.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.section-type-badge {
    display:inline-flex; align-items:center; gap:6px; padding:4px 12px;
    background:rgba(14,165,233,.08); color:var(--accent); border-radius:20px;
    font-size:12px; font-weight:700; font-family:monospace; margin-bottom:20px;
}
</style>
@endpush

@section('content')
@if(session('success'))
  <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
  </div>
@endif

<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap">
  <a href="{{ route('admin.homepage.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:14px;display:flex;align-items:center;gap:4px">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    إدارة الأقسام
  </a>
  <span style="color:var(--border)">/</span>
  <h1 style="font-size:20px;font-weight:800;color:var(--text-dark)">{{ $section->label }}</h1>
</div>

<div class="edit-card">
  <div class="section-type-badge">
    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
    {{ $section->type }}
  </div>

  <form action="{{ route('admin.homepage.update', $section) }}" method="POST">
    @csrf @method('PUT')

    {{-- Common: Label + Active --}}
    <div class="grid-2" style="margin-bottom:20px">
      <div class="field-group" style="margin-bottom:0">
        <label>الاسم في لوحة التحكم <span style="color:#dc2626">*</span></label>
        <input type="text" name="label" value="{{ old('label', $section->label) }}" required maxlength="120">
      </div>
      <div class="field-group" style="margin-bottom:0;display:flex;flex-direction:column;justify-content:flex-end">
        <label style="margin-bottom:8px">الحالة</label>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" {{ $section->is_active ? 'checked' : '' }}
            style="width:18px;height:18px;accent-color:var(--accent)">
          <span style="font-size:14px;color:var(--text-dark)">مفعّل (يظهر في الصفحة)</span>
        </label>
      </div>
    </div>

    <hr style="border:none;border-top:1px solid var(--border);margin:24px 0">

    {{-- ═══════════════════  HERO  ═══════════════════ --}}
    @if($section->type === 'hero')
      @php $c = $section->content ?? []; @endphp

      <div class="field-group">
        <label>الشارة (Badge)</label>
        <input type="text" name="badge" value="{{ old('badge', $c['badge'] ?? '') }}" placeholder="مثال: الإصدار 2.0 متاح الآن">
      </div>
      <div class="field-group">
        <label>العنوان الرئيسي</label>
        <textarea name="title" rows="3" placeholder="استخدم سطر جديد للتقسيم">{{ old('title', $c['title'] ?? '') }}</textarea>
        <p class="field-hint">يمكنك استخدام سطر جديد (Enter) لتقسيم العنوان إلى جزأين.</p>
      </div>
      <div class="field-group">
        <label>النص الثانوي</label>
        <textarea name="subtitle">{{ old('subtitle', $c['subtitle'] ?? '') }}</textarea>
      </div>
      <div class="grid-2">
        <div class="field-group" style="margin-bottom:0">
          <label>زر رئيسي — النص</label>
          <input type="text" name="cta_primary_text" value="{{ old('cta_primary_text', $c['cta_primary']['text'] ?? '') }}">
        </div>
        <div class="field-group" style="margin-bottom:0">
          <label>زر رئيسي — الرابط</label>
          <input type="text" name="cta_primary_url" value="{{ old('cta_primary_url', $c['cta_primary']['url'] ?? '') }}" placeholder="#pricing">
        </div>
      </div>
      <div class="grid-2" style="margin-top:14px">
        <div class="field-group" style="margin-bottom:0">
          <label>زر ثانوي — النص</label>
          <input type="text" name="cta_secondary_text" value="{{ old('cta_secondary_text', $c['cta_secondary']['text'] ?? '') }}">
        </div>
        <div class="field-group" style="margin-bottom:0">
          <label>زر ثانوي — الرابط</label>
          <input type="text" name="cta_secondary_url" value="{{ old('cta_secondary_url', $c['cta_secondary']['url'] ?? '') }}" placeholder="#how">
        </div>
      </div>
      <div class="field-group" style="margin-top:14px">
        <label>عناصر الثقة (Trust Items)</label>
        <textarea name="trust_items" rows="4" placeholder="بدون رسوم إعداد&#10;14 يوم تجريبي مجاني&#10;دعم فني 24/7">{{ old('trust_items', implode("\n", $c['trust_items'] ?? [])) }}</textarea>
        <p class="field-hint">ضع كل عنصر في سطر منفصل.</p>
      </div>

    {{-- ═══════════════════  FEATURES  ═══════════════════ --}}
    @elseif($section->type === 'features')
      @php $c = $section->content ?? []; @endphp

      <div class="field-group">
        <label>عنوان القسم</label>
        <input type="text" name="title" value="{{ old('title', $c['title'] ?? '') }}">
      </div>
      <div class="field-group">
        <label>النص الثانوي</label>
        <textarea name="subtitle">{{ old('subtitle', $c['subtitle'] ?? '') }}</textarea>
      </div>

      <p style="font-size:13px;font-weight:700;color:var(--text-dark);margin-bottom:10px">قائمة المميزات</p>
      <div id="featuresRepeater">
        @foreach(old('item_title', array_column($c['items'] ?? [], 'title')) as $i => $title)
        <div class="repeater-item">
          <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
          <div class="grid-2" style="margin-bottom:10px">
            <div class="field-group" style="margin-bottom:0">
              <label>العنوان</label>
              <input type="text" name="item_title[]" value="{{ $title }}">
            </div>
            <div class="field-group" style="margin-bottom:0">
              <label>الأيقونة</label>
              <input type="text" name="item_icon[]" value="{{ old('item_icon.'.$i, ($c['items'][$i]['icon'] ?? 'bolt')) }}" placeholder="bolt / shield / chart / users / card / cog">
            </div>
          </div>
          <div class="field-group" style="margin-bottom:0">
            <label>الوصف</label>
            <textarea name="item_desc[]" rows="2">{{ old('item_desc.'.$i, ($c['items'][$i]['desc'] ?? '')) }}</textarea>
          </div>
        </div>
        @endforeach
        @if(empty(old('item_title', $c['items'] ?? [])))
          {{-- Show one empty row if nothing yet --}}
          <div class="repeater-item">
            <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
            <div class="grid-2" style="margin-bottom:10px">
              <div class="field-group" style="margin-bottom:0"><label>العنوان</label><input type="text" name="item_title[]"></div>
              <div class="field-group" style="margin-bottom:0"><label>الأيقونة</label><input type="text" name="item_icon[]" placeholder="bolt"></div>
            </div>
            <div class="field-group" style="margin-bottom:0"><label>الوصف</label><textarea name="item_desc[]" rows="2"></textarea></div>
          </div>
        @endif
      </div>
      <button type="button" class="add-row-btn" onclick="addFeatureRow()">+ إضافة ميزة</button>

    {{-- ═══════════════════  PRICING  ═══════════════════ --}}
    @elseif($section->type === 'pricing')
      @php $c = $section->content ?? []; @endphp

      <div class="field-group">
        <label>عنوان القسم</label>
        <input type="text" name="title" value="{{ old('title', $c['title'] ?? '') }}">
      </div>
      <div class="field-group">
        <label>النص الثانوي</label>
        <textarea name="subtitle">{{ old('subtitle', $c['subtitle'] ?? '') }}</textarea>
      </div>
      <div class="field-group">
        <label>ملاحظة أسفل الخطط</label>
        <input type="text" name="note" value="{{ old('note', $c['note'] ?? '') }}" placeholder="مثال: ✓ تجربة 14 يوم مجانية · ✓ لا رسوم خفية">
      </div>
      <div style="padding:14px;background:rgba(14,165,233,.06);border-radius:8px;border:1px solid rgba(14,165,233,.15);font-size:13px;color:var(--text-muted)">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:-2px;margin-left:4px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        تُدار الخطط الفعلية (الأسعار والمميزات) من صفحة <a href="{{ route('admin.homepage.plans') }}" style="color:var(--accent);font-weight:600">إدارة الخطط</a>.
      </div>

    {{-- ═══════════════════  ABOUT  ═══════════════════ --}}
    @elseif($section->type === 'about')
      @php $c = $section->content ?? []; @endphp

      <div class="field-group">
        <label>عنوان القسم</label>
        <input type="text" name="title" value="{{ old('title', $c['title'] ?? '') }}">
      </div>
      <div class="field-group">
        <label>النص الثانوي</label>
        <textarea name="subtitle">{{ old('subtitle', $c['subtitle'] ?? '') }}</textarea>
      </div>

      <p style="font-size:13px;font-weight:700;color:var(--text-dark);margin-bottom:10px">الإحصائيات</p>
      <div id="statsRepeater">
        @foreach(old('stat_value', array_column($c['stats'] ?? [], 'value')) as $i => $val)
        <div class="repeater-item">
          <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
          <div class="grid-2">
            <div class="field-group" style="margin-bottom:0"><label>القيمة (مثال: +500)</label><input type="text" name="stat_value[]" value="{{ $val }}"></div>
            <div class="field-group" style="margin-bottom:0"><label>التسمية</label><input type="text" name="stat_label[]" value="{{ old('stat_label.'.$i, ($c['stats'][$i]['label'] ?? '')) }}"></div>
          </div>
        </div>
        @endforeach
        @if(empty(old('stat_value', $c['stats'] ?? [])))
          <div class="repeater-item">
            <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
            <div class="grid-2">
              <div class="field-group" style="margin-bottom:0"><label>القيمة</label><input type="text" name="stat_value[]"></div>
              <div class="field-group" style="margin-bottom:0"><label>التسمية</label><input type="text" name="stat_label[]"></div>
            </div>
          </div>
        @endif
      </div>
      <button type="button" class="add-row-btn" onclick="addStatRow()">+ إضافة إحصائية</button>

    {{-- ═══════════════════  TESTIMONIALS  ═══════════════════ --}}
    @elseif($section->type === 'testimonials')
      @php $c = $section->content ?? []; @endphp

      <div class="field-group">
        <label>عنوان القسم</label>
        <input type="text" name="title" value="{{ old('title', $c['title'] ?? '') }}">
      </div>
      <div class="field-group">
        <label>النص الثانوي</label>
        <textarea name="subtitle">{{ old('subtitle', $c['subtitle'] ?? '') }}</textarea>
      </div>

      <p style="font-size:13px;font-weight:700;color:var(--text-dark);margin-bottom:10px">آراء العملاء</p>
      <div id="testimonialsRepeater">
        @foreach(old('t_name', array_column($c['items'] ?? [], 'name')) as $i => $name)
        <div class="repeater-item">
          <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
          <div class="grid-2" style="margin-bottom:10px">
            <div class="field-group" style="margin-bottom:0"><label>الاسم</label><input type="text" name="t_name[]" value="{{ $name }}"></div>
            <div class="field-group" style="margin-bottom:0"><label>الدور / المدينة</label><input type="text" name="t_role[]" value="{{ old('t_role.'.$i, ($c['items'][$i]['role'] ?? '')) }}"></div>
          </div>
          <div class="grid-2">
            <div class="field-group" style="margin-bottom:0"><label>التقييم (1-5)</label><input type="number" name="t_stars[]" value="{{ old('t_stars.'.$i, ($c['items'][$i]['stars'] ?? 5)) }}" min="1" max="5"></div>
            <div class="field-group" style="margin-bottom:0"><label>النص</label><textarea name="t_text[]" rows="2">{{ old('t_text.'.$i, ($c['items'][$i]['text'] ?? '')) }}</textarea></div>
          </div>
        </div>
        @endforeach
        @if(empty(old('t_name', $c['items'] ?? [])))
          <div class="repeater-item">
            <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
            <div class="grid-2" style="margin-bottom:10px">
              <div class="field-group" style="margin-bottom:0"><label>الاسم</label><input type="text" name="t_name[]"></div>
              <div class="field-group" style="margin-bottom:0"><label>الدور / المدينة</label><input type="text" name="t_role[]"></div>
            </div>
            <div class="grid-2">
              <div class="field-group" style="margin-bottom:0"><label>التقييم (1-5)</label><input type="number" name="t_stars[]" value="5" min="1" max="5"></div>
              <div class="field-group" style="margin-bottom:0"><label>النص</label><textarea name="t_text[]" rows="2"></textarea></div>
            </div>
          </div>
        @endif
      </div>
      <button type="button" class="add-row-btn" onclick="addTestimonialRow()">+ إضافة رأي</button>

    {{-- ═══════════════════  FAQ  ═══════════════════ --}}
    @elseif($section->type === 'faq')
      @php $c = $section->content ?? []; @endphp

      <div class="field-group">
        <label>عنوان القسم</label>
        <input type="text" name="title" value="{{ old('title', $c['title'] ?? '') }}">
      </div>
      <div class="field-group">
        <label>النص الثانوي</label>
        <textarea name="subtitle" rows="2">{{ old('subtitle', $c['subtitle'] ?? '') }}</textarea>
      </div>

      <p style="font-size:13px;font-weight:700;color:var(--text-dark);margin-bottom:10px">الأسئلة والأجوبة</p>
      <div id="faqRepeater">
        @foreach(old('faq_q', array_column($c['items'] ?? [], 'q')) as $i => $q)
        <div class="repeater-item">
          <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
          <div class="field-group" style="margin-bottom:10px"><label>السؤال</label><input type="text" name="faq_q[]" value="{{ $q }}"></div>
          <div class="field-group" style="margin-bottom:0"><label>الجواب</label><textarea name="faq_a[]" rows="3">{{ old('faq_a.'.$i, ($c['items'][$i]['a'] ?? '')) }}</textarea></div>
        </div>
        @endforeach
        @if(empty(old('faq_q', $c['items'] ?? [])))
          <div class="repeater-item">
            <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
            <div class="field-group" style="margin-bottom:10px"><label>السؤال</label><input type="text" name="faq_q[]"></div>
            <div class="field-group" style="margin-bottom:0"><label>الجواب</label><textarea name="faq_a[]" rows="3"></textarea></div>
          </div>
        @endif
      </div>
      <button type="button" class="add-row-btn" onclick="addFaqRow()">+ إضافة سؤال</button>

    {{-- ═══════════════════  CTA  ═══════════════════ --}}
    @elseif($section->type === 'cta')
      @php $c = $section->content ?? []; @endphp

      <div class="field-group">
        <label>العنوان الرئيسي</label>
        <input type="text" name="title" value="{{ old('title', $c['title'] ?? '') }}">
      </div>
      <div class="field-group">
        <label>النص الثانوي</label>
        <textarea name="subtitle">{{ old('subtitle', $c['subtitle'] ?? '') }}</textarea>
      </div>
      <div class="grid-2">
        <div class="field-group" style="margin-bottom:0">
          <label>زر رئيسي — النص</label>
          <input type="text" name="cta_primary_text" value="{{ old('cta_primary_text', $c['cta_primary']['text'] ?? '') }}">
        </div>
        <div class="field-group" style="margin-bottom:0">
          <label>زر رئيسي — الرابط</label>
          <input type="text" name="cta_primary_url" value="{{ old('cta_primary_url', $c['cta_primary']['url'] ?? '') }}">
        </div>
      </div>
      <div class="grid-2" style="margin-top:14px">
        <div class="field-group" style="margin-bottom:0">
          <label>زر ثانوي — النص</label>
          <input type="text" name="cta_secondary_text" value="{{ old('cta_secondary_text', $c['cta_secondary']['text'] ?? '') }}">
        </div>
        <div class="field-group" style="margin-bottom:0">
          <label>زر ثانوي — الرابط</label>
          <input type="text" name="cta_secondary_url" value="{{ old('cta_secondary_url', $c['cta_secondary']['url'] ?? '') }}">
        </div>
      </div>
      <div class="field-group" style="margin-top:14px">
        <label>ملاحظة</label>
        <input type="text" name="note" value="{{ old('note', $c['note'] ?? '') }}">
      </div>

    {{-- ═══════════════════  DEFAULT  ═══════════════════ --}}
    @else
      @php $c = $section->content ?? []; @endphp
      <div class="field-group">
        <label>العنوان</label>
        <input type="text" name="title" value="{{ old('title', $c['title'] ?? '') }}">
      </div>
      <div class="field-group">
        <label>النص الثانوي</label>
        <textarea name="subtitle">{{ old('subtitle', $c['subtitle'] ?? '') }}</textarea>
      </div>
    @endif

    {{-- Submit --}}
    <div style="display:flex;gap:10px;margin-top:28px;padding-top:20px;border-top:1px solid var(--border)">
      <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-size:14px">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        حفظ التغييرات
      </button>
      <a href="{{ route('admin.homepage.index') }}" class="btn btn-outline" style="padding:10px 24px;font-size:14px">إلغاء</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
function addFeatureRow() {
  const tmpl = `<div class="repeater-item">
    <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
    <div class="grid-2" style="margin-bottom:10px">
      <div class="field-group" style="margin-bottom:0"><label>العنوان</label><input type="text" name="item_title[]"></div>
      <div class="field-group" style="margin-bottom:0"><label>الأيقونة</label><input type="text" name="item_icon[]" placeholder="bolt"></div>
    </div>
    <div class="field-group" style="margin-bottom:0"><label>الوصف</label><textarea name="item_desc[]" rows="2"></textarea></div>
  </div>`;
  document.getElementById('featuresRepeater').insertAdjacentHTML('beforeend', tmpl);
}

function addStatRow() {
  const tmpl = `<div class="repeater-item">
    <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
    <div class="grid-2">
      <div class="field-group" style="margin-bottom:0"><label>القيمة</label><input type="text" name="stat_value[]"></div>
      <div class="field-group" style="margin-bottom:0"><label>التسمية</label><input type="text" name="stat_label[]"></div>
    </div>
  </div>`;
  document.getElementById('statsRepeater').insertAdjacentHTML('beforeend', tmpl);
}

function addTestimonialRow() {
  const tmpl = `<div class="repeater-item">
    <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
    <div class="grid-2" style="margin-bottom:10px">
      <div class="field-group" style="margin-bottom:0"><label>الاسم</label><input type="text" name="t_name[]"></div>
      <div class="field-group" style="margin-bottom:0"><label>الدور / المدينة</label><input type="text" name="t_role[]"></div>
    </div>
    <div class="grid-2">
      <div class="field-group" style="margin-bottom:0"><label>التقييم (1-5)</label><input type="number" name="t_stars[]" value="5" min="1" max="5"></div>
      <div class="field-group" style="margin-bottom:0"><label>النص</label><textarea name="t_text[]" rows="2"></textarea></div>
    </div>
  </div>`;
  document.getElementById('testimonialsRepeater').insertAdjacentHTML('beforeend', tmpl);
}

function addFaqRow() {
  const tmpl = `<div class="repeater-item">
    <button type="button" class="repeater-remove" onclick="this.closest('.repeater-item').remove()">×</button>
    <div class="field-group" style="margin-bottom:10px"><label>السؤال</label><input type="text" name="faq_q[]"></div>
    <div class="field-group" style="margin-bottom:0"><label>الجواب</label><textarea name="faq_a[]" rows="3"></textarea></div>
  </div>`;
  document.getElementById('faqRepeater').insertAdjacentHTML('beforeend', tmpl);
}
</script>
@endpush
