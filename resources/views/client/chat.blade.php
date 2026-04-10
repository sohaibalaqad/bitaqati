@extends('layouts.client')

@section('title', 'المحادثة')

@push('styles')
<style>
  .chat-container {
    display: flex; flex-direction: column;
    height: calc(100vh - 160px);
    background: var(--bg-card); border-radius: var(--radius-lg);
    box-shadow: var(--shadow); overflow: hidden;
  }
  .chat-header {
    background: linear-gradient(135deg, #10b981, #047857);
    color: white; padding: 14px 16px;
    display: flex; align-items: center; gap: 10px;
  }
  .chat-header-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: rgba(255,255,255,0.3);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 16px;
  }
  .chat-header-info .name { font-weight: 700; font-size: 15px; }
  .chat-header-info .status { font-size: 12px; opacity: 0.85; }
  .chat-messages {
    flex: 1; overflow-y: auto; padding: 16px;
    display: flex; flex-direction: column; gap: 10px;
    background: var(--bg-main);
  }
  .msg-row { display: flex; }
  .msg-row.mine { justify-content: flex-end; }
  .msg-row.theirs { justify-content: flex-start; }
  .msg-bubble {
    max-width: 75%; padding: 10px 14px; border-radius: 16px;
    font-size: 14px; line-height: 1.5; word-break: break-word;
  }
  .msg-bubble.mine { background: #10b981; color: white; border-bottom-right-radius: 4px; }
  .msg-bubble.theirs { background: white; color: var(--text-main); border-bottom-left-radius: 4px; box-shadow: var(--shadow); }
  .msg-time { font-size: 11px; opacity: 0.65; margin-top: 3px; }
  .chat-input-bar {
    padding: 12px; border-top: 1px solid var(--border);
    background: var(--bg-card);
    display: flex; gap: 8px; align-items: flex-end;
  }
  .chat-input-bar textarea {
    flex: 1; padding: 10px 14px; border: 1.5px solid var(--border);
    border-radius: 20px; font-size: 14px; font-family: 'Tajawal', sans-serif;
    resize: none; min-height: 42px; max-height: 100px; line-height: 1.4;
    background: var(--bg-main); color: var(--text-main); overflow-y: auto;
  }
  .chat-input-bar textarea:focus { outline: none; border-color: var(--accent); }
  .chat-send-btn {
    width: 42px; height: 42px; border-radius: 50%;
    background: var(--accent); color: white; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0; transition: background 0.2s;
  }
  .chat-send-btn:hover { background: var(--accent-dark); }
  .chat-empty {
    text-align: center; padding: 40px 20px; color: var(--text-muted);
  }
  .chat-empty i { font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.3; }
</style>
@endpush

@section('content')
<div class="chat-container">
  <div class="chat-header">
    <div class="chat-header-avatar"><i class="fas fa-headset"></i></div>
    <div class="chat-header-info">
      <div class="name">فريق الدعم الفني</div>
      <div class="status">متصل عادةً</div>
    </div>
  </div>

  <div class="chat-messages" id="chatMessages">
    @if($messages->isEmpty())
      <div class="chat-empty">
        <i class="fas fa-comments"></i>
        <p>ابدأ محادثة مع فريق الدعم</p>
      </div>
    @else
      @foreach($messages as $msg)
        @php $isMine = $msg->sender_id === auth()->id(); @endphp
        <div class="msg-row {{ $isMine ? 'mine' : 'theirs' }}">
          <div class="msg-bubble {{ $isMine ? 'mine' : 'theirs' }}">
            {{ $msg->message }}
            <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
          </div>
        </div>
      @endforeach
    @endif
  </div>

  <div class="chat-input-bar">
    <textarea id="msgInput" placeholder="اكتب رسالتك..." rows="1"
      onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage()}"
      oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,100)+'px'"></textarea>
    <button class="chat-send-btn" onclick="sendMessage()" id="sendBtn">
      <i class="fas fa-paper-plane"></i>
    </button>
  </div>
</div>
@endsection

@push('scripts')
<script>
let lastMsgId = {{ $messages->last()?->id ?? 0 }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function scrollToBottom() {
  const el = document.getElementById('chatMessages');
  el.scrollTop = el.scrollHeight;
}

function appendMessage(msg, isMine) {
  const container = document.getElementById('chatMessages');
  const empty = container.querySelector('.chat-empty');
  if (empty) empty.remove();

  const row = document.createElement('div');
  row.className = 'msg-row ' + (isMine ? 'mine' : 'theirs');
  row.innerHTML = `<div class="msg-bubble ${isMine ? 'mine' : 'theirs'}">${escapeHtml(msg.message)}<div class="msg-time">${msg.created_at}</div></div>`;
  container.appendChild(row);
  scrollToBottom();
  if (msg.id > lastMsgId) lastMsgId = msg.id;
}

function escapeHtml(text) {
  return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function sendMessage() {
  const input = document.getElementById('msgInput');
  const msg = input.value.trim();
  if (!msg) return;

  const btn = document.getElementById('sendBtn');
  btn.disabled = true;

  fetch('{{ route("client.chat.send") }}', {
    method: 'POST',
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
    body: JSON.stringify({message: msg})
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      input.value = '';
      input.style.height = 'auto';
      appendMessage(data.message, true);
    }
  })
  .catch(() => {})
  .finally(() => { btn.disabled = false; });
}

// Poll every 3 seconds for new messages from admin
function pollMessages() {
  fetch(`{{ route("client.chat.poll") }}?last_id=${lastMsgId}`, {
    headers: {'X-Requested-With': 'XMLHttpRequest'}
  })
  .then(r => r.json())
  .then(data => {
    data.messages?.forEach(m => appendMessage(m, false));
  })
  .catch(() => {});
}

scrollToBottom();
setInterval(pollMessages, 3000);
</script>
@endpush
