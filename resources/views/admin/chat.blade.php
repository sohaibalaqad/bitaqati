@extends('layouts.admin')
@section('title', 'المحادثات')
@section('page-title', 'المحادثات')

@push('styles')
<style>
  /* ===== Chat wrapper ===== */
  .chat-wrap {
    display: flex;
    height: calc(100vh - 150px);
    min-height: 500px;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    background: var(--bg-card);
  }

  /* ===== Sidebar ===== */
  .chat-sb {
    width: 280px;
    flex-shrink: 0;
    border-left: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    background: var(--bg-main);
    overflow: hidden;
  }
  .chat-sb-head {
    padding: 14px 16px;
    font-weight: 700;
    font-size: 14px;
    border-bottom: 1px solid var(--border);
    background: var(--bg-card);
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
  }
  .chat-sb-head i { color: var(--accent); }
  .chat-sb-new {
    padding: 10px 12px;
    border-bottom: 1px solid var(--border);
    background: #f0fdf4;
    flex-shrink: 0;
  }
  .chat-sb-new select {
    width: 100%;
    padding: 7px 10px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: 'Tajawal', sans-serif;
    font-size: 13px;
    background: var(--bg-card);
    color: var(--text-main);
    margin-bottom: 6px;
  }
  .chat-sb-new button {
    width: 100%;
    padding: 8px;
    background: var(--accent);
    color: white;
    border: none;
    border-radius: var(--radius-sm);
    font-family: 'Tajawal', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }
  .chat-sb-new button:hover { background: var(--accent-dark); }
  .chat-sb-list { flex: 1; overflow-y: auto; }
  .client-row {
    padding: 12px 14px;
    cursor: pointer;
    border-bottom: 1px solid var(--border);
    background: var(--bg-card);
    transition: background 0.15s;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .client-row:hover { background: #f8fafc; }
  .client-row.active { background: #ecfdf5; border-right: 3px solid var(--accent); }
  .cl-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), #059669);
    color: white; display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 14px; flex-shrink: 0;
  }
  .cl-name { font-weight: 600; font-size: 14px; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .cl-unread {
    background: #ef4444; color: white; min-width: 18px; height: 18px;
    border-radius: 9px; font-size: 10px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 0 4px;
  }

  /* ===== Main area ===== */
  .chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    /* CRITICAL: overflow hidden so inner flex can scroll */
    overflow: hidden;
  }
  .chat-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: var(--text-muted); gap: 12px;
  }
  .chat-empty i { font-size: 52px; opacity: 0.15; }
  .chat-empty p { font-size: 15px; }

  /* Active conversation */
  .chat-conv {
    /* Fill remaining height */
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;   /* keep inner scroll areas working */
    min-height: 0;
  }
  .conv-head {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
    flex-shrink: 0;
    background: var(--bg-card);
  }
  .chat-msgs {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: var(--bg-main);
    display: flex;
    flex-direction: column;
    gap: 10px;
    /* min-height 0 allows flex child to scroll instead of grow */
    min-height: 0;
  }
  .msg-r { display: flex; }
  .msg-r.mine { justify-content: flex-end; }
  .msg-r.theirs { justify-content: flex-start; }
  .msg-bbl {
    max-width: 65%; padding: 10px 14px; border-radius: 14px;
    font-size: 14px; line-height: 1.5; word-break: break-word;
  }
  .msg-bbl.mine {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white; border-bottom-right-radius: 4px;
  }
  .msg-bbl.theirs {
    background: var(--bg-card); color: var(--text-main);
    border-bottom-left-radius: 4px; box-shadow: var(--shadow);
  }
  .msg-time { font-size: 11px; opacity: 0.6; margin-top: 4px; }

  /* ===== THE FIX: input bar must never shrink or overflow ===== */
  .chat-input-bar {
    flex-shrink: 0;          /* never compressed by flex parent */
    padding: 12px 14px;
    border-top: 2px solid var(--border);
    background: var(--bg-card);
    display: flex;
    align-items: flex-end;
    gap: 10px;
    min-height: 68px;        /* guaranteed minimum height */
    position: relative;      /* stay above any stacking issues */
    z-index: 2;
  }
  .chat-input-bar textarea {
    flex: 1;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: 20px;
    font-size: 14px;
    font-family: 'Tajawal', sans-serif;
    resize: none;
    min-height: 42px;
    max-height: 120px;
    line-height: 1.5;
    overflow-y: auto;
    background: var(--bg-main);
    color: var(--text-main);
    display: block;
    box-sizing: border-box;
  }
  .chat-input-bar textarea:focus { outline: none; border-color: var(--accent); }
  .send-btn {
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--accent); color: white; border: none;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
    transition: background 0.2s, transform 0.1s;
  }
  .send-btn:hover { background: var(--accent-dark); transform: scale(1.05); }
  .send-btn:disabled { background: #d1d5db; cursor: not-allowed; transform: none; }

  @media (max-width: 768px) {
    .chat-sb { width: 200px; }
    .msg-bbl { max-width: 80%; }
    .chat-wrap { height: calc(100vh - 120px); }
  }
</style>
@endpush

@section('content')
<div class="chat-wrap">

  {{-- ===== Sidebar ===== --}}
  <div class="chat-sb">
    <div class="chat-sb-head">
      <i class="fas fa-comments"></i> المحادثات
    </div>

    {{-- New conversation --}}
    <div class="chat-sb-new">
      <select id="newClientSel">
        <option value="">-- اختر عميلاً --</option>
        @foreach($allClients as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </select>
      <button onclick="startNewConv()">
        <i class="fas fa-plus"></i> محادثة جديدة
      </button>
    </div>

    {{-- Client list --}}
    <div class="chat-sb-list" id="clientList">
      @forelse($clients as $client)
        <div class="client-row" id="cl-{{ $client->id }}"
          onclick="openConv({{ $client->id }}, '{{ e($client->name) }}')">
          <div class="cl-avatar">{{ mb_substr($client->name, 0, 1) }}</div>
          <div class="cl-name">{{ $client->name }}</div>
        </div>
      @empty
        <div id="sbEmpty" style="padding:24px;text-align:center;color:var(--text-muted);font-size:13px">
          <i class="fas fa-comment-slash" style="display:block;font-size:28px;opacity:0.25;margin-bottom:8px"></i>
          لا توجد محادثات بعد
        </div>
      @endforelse
    </div>
  </div>

  {{-- ===== Main chat area ===== --}}
  <div class="chat-main">

    {{-- Empty state --}}
    <div class="chat-empty" id="chatEmpty">
      <i class="fas fa-comments"></i>
      <p>اختر عميلاً لبدء المحادثة</p>
    </div>

    {{-- Active conversation --}}
    <div class="chat-conv" id="chatConv" style="display:none">

      {{-- Header --}}
      <div class="conv-head">
        <div class="cl-avatar" id="convAvatar" style="width:38px;height:38px;font-size:16px"></div>
        <div>
          <div style="font-weight:700;font-size:15px" id="convName"></div>
          <div style="font-size:12px;color:var(--text-muted)" id="convSub">عميل</div>
        </div>
      </div>

      {{-- Messages --}}
      <div class="chat-msgs" id="chatMsgs"></div>

      {{-- ✅ FIXED Input Bar: flex-shrink:0 + z-index ensures always visible --}}
      <div class="chat-input-bar">
        <textarea
          id="msgInput"
          placeholder="اكتب رسالتك... (Enter للإرسال، Shift+Enter لسطر جديد)"
          rows="1"
          onkeydown="handleKey(event)"
          oninput="autoGrow(this)"></textarea>
        <button class="send-btn" id="sendBtn" onclick="sendMsg()" title="إرسال">
          <i class="fas fa-paper-plane"></i>
        </button>
      </div>

    </div>{{-- /chat-conv --}}
  </div>{{-- /chat-main --}}

</div>{{-- /chat-wrap --}}
@endsection

@push('scripts')
<script src="{{ asset('js/pusher.js') }}"></script>
<script src="{{ asset('js/echo.iife.js') }}"></script>
<script>
// ===== State =====
let _cid = null, _cname = '', _lastId = 0, _pollTimer = null;
const _csrf = document.querySelector('meta[name="csrf-token"]').content;

// ===== Helpers =====
function esc(s) {
  return String(s||'')
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function autoGrow(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}
function scrollBottom() {
  const el = document.getElementById('chatMsgs');
  if (el) requestAnimationFrame(() => { el.scrollTop = el.scrollHeight; });
}
function appendMsg(msg) {
  const el = document.getElementById('chatMsgs');
  if (!el) return;
  el.querySelector('.msg-placeholder')?.remove();

  const row = document.createElement('div');
  row.className = 'msg-r ' + (msg.is_mine ? 'mine' : 'theirs');
  row.dataset.id = msg.id;
  row.innerHTML = `<div class="msg-bbl ${msg.is_mine ? 'mine' : 'theirs'}">${esc(msg.message)}<div class="msg-time">${msg.created_at}</div></div>`;
  el.appendChild(row);
  if (msg.id > _lastId) _lastId = msg.id;
  scrollBottom();
}

// ===== Open conversation =====
function openConv(clientId, clientName) {
  _cid = clientId; _cname = clientName; _lastId = 0;

  // Sidebar highlight
  document.querySelectorAll('.client-row').forEach(r => r.classList.remove('active'));
  const row = document.getElementById('cl-' + clientId);
  if (row) { row.classList.add('active'); row.querySelector('.cl-unread')?.remove(); }

  // Show conversation panel
  document.getElementById('chatEmpty').style.display = 'none';
  const conv = document.getElementById('chatConv');
  conv.style.display = 'flex';

  document.getElementById('convName').textContent   = clientName;
  document.getElementById('convAvatar').textContent = clientName.charAt(0);

  const msgs = document.getElementById('chatMsgs');
  msgs.innerHTML = '<div class="msg-placeholder" style="text-align:center;color:var(--text-muted);padding:30px 16px;font-size:13px"><i class="fas fa-spinner fa-spin"></i></div>';

  // Auto-focus input
  setTimeout(() => document.getElementById('msgInput')?.focus(), 100);

  // Load history
  fetch(`/admin/chat/${clientId}`, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => {
    msgs.innerHTML = '';
    if (!data.messages?.length) {
      msgs.innerHTML = '<div class="msg-placeholder" style="text-align:center;color:var(--text-muted);padding:40px 16px;font-size:13px">لا توجد رسائل بعد، ابدأ المحادثة الآن</div>';
    } else {
      data.messages.forEach(m => appendMsg(m));
      _lastId = Math.max(...data.messages.map(m => m.id));
    }
  })
  .catch(() => {
    msgs.innerHTML = '<div class="msg-placeholder" style="text-align:center;color:#ef4444;padding:30px;font-size:13px"><i class="fas fa-exclamation-circle"></i> تعذر تحميل المحادثة</div>';
  });

  // Polling fallback every 3s
  if (_pollTimer) clearInterval(_pollTimer);
  _pollTimer = setInterval(pollNew, 3000);
}

// ===== Polling fallback =====
function pollNew() {
  if (!_cid) return;
  fetch(`/admin/chat/poll?client_id=${_cid}&last_id=${_lastId}`, {
    headers: { 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => { data.messages?.forEach(m => appendMsg(m)); })
  .catch(() => {});
}

// ===== Send message =====
function sendMsg() {
  const input = document.getElementById('msgInput');
  const btn   = document.getElementById('sendBtn');
  const text  = input.value.trim();
  if (!text || !_cid) return;

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

  fetch('/admin/chat/send', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
    body: JSON.stringify({ client_id: _cid, message: text })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      input.value = '';
      input.style.height = 'auto';
      appendMsg(data.message);
      ensureInSidebar(_cid, _cname);
    } else {
      showToast(data.message || 'خطأ في الإرسال', 'error');
    }
  })
  .catch(() => showToast('تعذر إرسال الرسالة', 'error'))
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane"></i>';
    input.focus();
  });
}

function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); }
  autoGrow(e.target);
}

// ===== New conversation =====
function startNewConv() {
  const sel = document.getElementById('newClientSel');
  const id  = parseInt(sel.value);
  if (!id) return;
  const name = sel.options[sel.selectedIndex].text;
  ensureInSidebar(id, name);
  openConv(id, name);
  sel.value = '';
}

function ensureInSidebar(id, name) {
  if (document.getElementById('cl-' + id)) return;
  document.getElementById('sbEmpty')?.remove();
  const list = document.getElementById('clientList');
  const div  = document.createElement('div');
  div.className = 'client-row';
  div.id = 'cl-' + id;
  div.onclick = () => openConv(id, name);
  div.innerHTML = `<div class="cl-avatar">${esc(name.charAt(0))}</div><div class="cl-name">${esc(name)}</div>`;
  list.insertBefore(div, list.firstChild);
}

// ===== Real-Time via Laravel Reverb (WebSocket) =====
(function initReverb() {
  try {
    const EchoLib = window.LaravelEcho || window.Echo;
    if (!EchoLib) { console.warn('Echo not loaded'); return; }

    const echo = new EchoLib({
      broadcaster: 'reverb',
      key:         '{{ env("REVERB_APP_KEY") }}',
      wsHost:      '{{ env("REVERB_HOST", "localhost") }}',
      wsPort:       {{ env("REVERB_PORT", 8080) }},
      wssPort:      {{ env("REVERB_PORT", 8080) }},
      forceTLS:    {{ env("REVERB_SCHEME","http") === "https" ? "true" : "false" }},
      enabledTransports: ['ws', 'wss'],
    });

    // Listen on admin's private notification channel
    echo.private('notifications.{{ auth()->id() }}')
      .listen('.new-notification', (event) => {
        // If a client sent a message and admin is not in that chat → show sidebar badge
        if (event.type === 'message' && event.url) {
          const parts  = event.url.split('/');
          const fromId = parseInt(parts[parts.length - 1]);

          if (fromId && fromId !== _cid) {
            const row = document.getElementById('cl-' + fromId);
            if (row && !row.querySelector('.cl-unread')) {
              const badge = document.createElement('div');
              badge.className = 'cl-unread';
              badge.textContent = '!';
              row.appendChild(badge);
            }
            // Ensure client appears in sidebar
            if (!row) {
              // Fetch client name via notification title
              ensureInSidebar(fromId, event.title.replace('رسالة جديدة من ', ''));
            }
          } else if (fromId === _cid) {
            // Admin is in this chat — poll immediately for new msg
            pollNew();
          }
        }
      });

    console.log('✅ Reverb WebSocket connected for admin chat');
  } catch (e) {
    console.warn('Reverb init (admin chat):', e.message);
  }
})();
</script>
@endpush
