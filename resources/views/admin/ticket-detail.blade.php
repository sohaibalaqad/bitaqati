@extends('layouts.admin')

@section('title', 'تفاصيل التذكرة #' . $ticket->id)
@section('page-title', 'تفاصيل التذكرة')

@section('content')
  @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:var(--radius);margin-bottom:16px;font-weight:600">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="page-header">
    <h1><i class="fas fa-ticket-alt"></i> تذكرة #{{ $ticket->id }}</h1>
    <a href="{{ route('admin.support') }}" class="btn btn-outline"><i class="fas fa-arrow-right"></i> العودة</a>
  </div>

  <!-- Ticket Info -->
  <div class="table-container" style="padding:20px;margin-bottom:20px">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div>
        <h3 style="font-size:18px;margin-bottom:6px">{{ $ticket->title }}</h3>
        <div style="font-size:13px;color:var(--text-muted)">
          <i class="fas fa-user"></i> {{ $ticket->user->name ?? 'غير معروف' }}
          &nbsp;·&nbsp;
          <i class="fas fa-phone"></i> {{ $ticket->user->phone ?? '' }}
          &nbsp;·&nbsp;
          <i class="fas fa-calendar"></i> {{ $ticket->created_at->format('Y/m/d H:i') }}
        </div>
      </div>
      <form method="POST" action="{{ route('admin.support.update-status', $ticket->id) }}" style="display:flex;gap:8px;align-items:center">
        @csrf
        <select name="status" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'Tajawal',sans-serif;font-size:13px;background:var(--bg-main)">
          <option value="open" {{ $ticket->status=='open' ? 'selected' : '' }}>مفتوح</option>
          <option value="in-progress" {{ $ticket->status=='in-progress' ? 'selected' : '' }}>قيد المعالجة</option>
          <option value="resolved" {{ $ticket->status=='resolved' ? 'selected' : '' }}>مغلق</option>
        </select>
        <button type="submit" class="btn btn-primary" style="font-size:13px;padding:8px 16px">تحديث الحالة</button>
      </form>
    </div>
  </div>

  <!-- Chat thread -->
  <div class="table-container" style="padding:0;overflow:hidden">
    <div class="table-header" style="padding:16px 20px">
      <h3><i class="fas fa-comments" style="color:var(--accent);margin-left:8px"></i> المحادثة</h3>
    </div>
    <div style="padding:20px;min-height:300px;max-height:500px;overflow-y:auto;display:flex;flex-direction:column;gap:12px" id="chatThread">
      @foreach($ticket->replies as $reply)
        <div style="display:flex;flex-direction:column;align-items:{{ $reply->is_admin ? 'flex-end' : 'flex-start' }}">
          <div style="max-width:70%;padding:12px 16px;border-radius:12px;font-size:14px;line-height:1.6;
            background:{{ $reply->is_admin ? '#10b981' : '#f1f5f9' }};
            color:{{ $reply->is_admin ? '#fff' : '#1e293b' }};
            border-bottom-{{ $reply->is_admin ? 'right' : 'left' }}-radius:4px">
            {{ $reply->message }}
            <div style="font-size:11px;opacity:0.7;margin-top:4px">
              {{ $reply->is_admin ? 'الإدارة' : ($reply->user->name ?? 'العميل') }} · {{ $reply->created_at->format('H:i d/m') }}
            </div>
          </div>
        </div>
      @endforeach
      @if($ticket->replies->isEmpty())
        <div style="text-align:center;color:var(--text-muted);padding:40px">لا توجد ردود بعد</div>
      @endif
    </div>

    <!-- Reply form -->
    <div style="border-top:1px solid var(--border);padding:16px 20px">
      <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}">
        @csrf
        <div style="display:flex;gap:10px;align-items:flex-end">
          <textarea name="message" rows="2" placeholder="اكتب ردك هنا..."
            style="flex:1;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);
            font-size:14px;font-family:'Tajawal',sans-serif;resize:none;background:var(--bg-main);color:var(--text-dark)"
            required></textarea>
          <button type="submit" class="btn btn-primary" style="padding:10px 20px;white-space:nowrap">
            <i class="fas fa-paper-plane"></i> إرسال
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.getElementById('chatThread').scrollTop = document.getElementById('chatThread').scrollHeight;
</script>
@endpush
