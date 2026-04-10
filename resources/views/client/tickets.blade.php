@extends('layouts.client')

@section('title', 'الدعم الفني')

@push('styles')
<style>
  .ticket-card {
    background: var(--bg-card); border-radius: var(--radius); padding: 16px;
    box-shadow: var(--shadow); margin-bottom: 12px; cursor: pointer;
  }
  .ticket-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
  .ticket-title { font-weight: 700; font-size: 15px; color: var(--text-main); }
  .ticket-meta { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
  .badge-status {
    padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
  }
  .badge-status.open { background: #dbeafe; color: #1d4ed8; }
  .badge-status.in-progress { background: #fef3c7; color: #92400e; }
  .badge-status.resolved { background: #d1fae5; color: #065f46; }
  .ticket-replies { display: none; margin-top: 12px; border-top: 1px solid var(--border); padding-top: 12px; }
  .ticket-replies.open { display: block; }
  .reply-bubble {
    max-width: 85%; margin-bottom: 10px; padding: 10px 14px;
    border-radius: 12px; font-size: 14px; line-height: 1.5;
  }
  .reply-bubble.mine {
    background: var(--accent); color: white; margin-right: auto; border-bottom-right-radius: 4px;
  }
  .reply-bubble.admin {
    background: var(--bg-main); color: var(--text-main); margin-left: auto; border-bottom-left-radius: 4px;
    border: 1px solid var(--border);
  }
  .reply-meta { font-size: 11px; margin-top: 4px; opacity: 0.7; }
  .reply-form textarea {
    width: 100%; padding: 10px 12px; border: 1.5px solid var(--border);
    border-radius: var(--radius); font-size: 14px; font-family: 'Tajawal', sans-serif;
    background: var(--bg-main); color: var(--text-main); resize: none; min-height: 70px;
    margin-bottom: 8px;
  }
  .reply-form textarea:focus { outline: none; border-color: var(--accent); }
  .reply-send-btn {
    background: var(--accent); color: white; border: none; padding: 9px 18px;
    border-radius: var(--radius); font-family: 'Tajawal', sans-serif; font-size: 14px;
    font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;
  }
  .reply-send-btn:hover { background: var(--accent-dark); }
</style>
@endpush

@section('content')
  <div class="section-title" style="margin-bottom:16px">
    <i class="fas fa-headset"></i> الدعم الفني
  </div>

  <!-- New Ticket Form -->
  <div class="form-card">
    <div style="font-weight:700;font-size:16px;margin-bottom:16px;display:flex;align-items:center;gap:8px">
      <i class="fas fa-plus-circle" style="color:var(--accent)"></i> فتح تذكرة جديدة
    </div>
    <form method="POST" action="{{ route('client.tickets.store') }}">
      @csrf
      <div>
        <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block">عنوان المشكلة</label>
        <input type="text" name="title" placeholder="اكتب عنواناً واضحاً لمشكلتك..." value="{{ old('title') }}" required>
      </div>
      <div>
        <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block">التصنيف</label>
        <select name="category" required>
          <option value="">-- اختر التصنيف --</option>
          <option value="technical" {{ old('category')=='technical' ? 'selected' : '' }}>مشكلة تقنية</option>
          <option value="payment" {{ old('category')=='payment' ? 'selected' : '' }}>دفع ورصيد</option>
          <option value="card" {{ old('category')=='card' ? 'selected' : '' }}>بطاقة لا تعمل</option>
          <option value="general" {{ old('category')=='general' ? 'selected' : '' }}>استفسار عام</option>
        </select>
      </div>
      <div>
        <label style="font-weight:600;font-size:13px;margin-bottom:6px;display:block">تفاصيل المشكلة</label>
        <textarea name="message" placeholder="اشرح مشكلتك بالتفصيل..." rows="4" required>{{ old('message') }}</textarea>
      </div>
      <button type="submit" class="btn-submit">
        <i class="fas fa-paper-plane"></i> إرسال التذكرة
      </button>
    </form>
  </div>

  <!-- Existing Tickets -->
  @if($tickets->count() > 0)
    <div class="section-title" style="margin-top:24px;margin-bottom:12px">
      <i class="fas fa-list"></i> تذاكرك السابقة
    </div>

    @foreach($tickets as $ticket)
      <div class="ticket-card">
        <div class="ticket-card-header" onclick="toggleTicket({{ $ticket->id }})">
          <div>
            <div class="ticket-title">{{ $ticket->title }}</div>
            <div class="ticket-meta">
              <i class="fas fa-calendar" style="font-size:11px"></i> {{ $ticket->created_at->format('Y/m/d') }}
              &nbsp;·&nbsp;
              <i class="fas fa-comment" style="font-size:11px"></i> {{ $ticket->replies->count() }} رد
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:8px">
            <span class="badge-status {{ $ticket->status }}">
              @if($ticket->status == 'open') مفتوح
              @elseif($ticket->status == 'in-progress') قيد المعالجة
              @else مغلق @endif
            </span>
            <i class="fas fa-chevron-down" id="chevron-{{ $ticket->id }}" style="color:var(--text-muted);transition:0.3s"></i>
          </div>
        </div>

        <div class="ticket-replies" id="replies-{{ $ticket->id }}">
          @foreach($ticket->replies as $reply)
            <div style="display:flex;flex-direction:column;align-items:{{ $reply->is_admin ? 'flex-start' : 'flex-end' }};margin-bottom:10px">
              <div class="reply-bubble {{ $reply->is_admin ? 'admin' : 'mine' }}">
                {{ $reply->message }}
                <div class="reply-meta">
                  {{ $reply->is_admin ? 'فريق الدعم' : 'أنت' }} · {{ $reply->created_at->format('H:i') }}
                </div>
              </div>
            </div>
          @endforeach

          @if($ticket->status !== 'resolved')
            <div class="reply-form" style="margin-top:12px">
              <form method="POST" action="{{ route('client.tickets.reply', $ticket->id) }}">
                @csrf
                <textarea name="message" placeholder="اكتب ردك هنا..." required></textarea>
                <button type="submit" class="reply-send-btn">
                  <i class="fas fa-paper-plane"></i> إرسال الرد
                </button>
              </form>
            </div>
          @endif
        </div>
      </div>
    @endforeach
  @else
    <div class="empty-box" style="margin-top:20px">
      <i class="fas fa-ticket-alt"></i>
      <h3>لا توجد تذاكر بعد</h3>
    </div>
  @endif
@endsection

@push('scripts')
<script>
function toggleTicket(id) {
  const replies = document.getElementById('replies-' + id);
  const chevron = document.getElementById('chevron-' + id);
  replies.classList.toggle('open');
  chevron.style.transform = replies.classList.contains('open') ? 'rotate(180deg)' : '';
}
</script>
@endpush
