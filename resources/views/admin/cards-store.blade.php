@extends('layouts.admin')

@section('title', 'مخزن البطاقات')
@section('page-title', 'مخزن البطاقات')

@push('styles')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endpush

@section('content')
  @if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="page-header">
    <h1><i class="fas fa-warehouse"></i> مخزن البطاقات</h1>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <button class="btn btn-primary" onclick="openModal('uploadModal')">
        <i class="fas fa-file-upload"></i> رفع ملف بطاقات
      </button>
      <button class="btn btn-outline" onclick="openModal('addCardModal')">
        <i class="fas fa-plus"></i> إضافة يدوي
      </button>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info">
        <h3>إجمالي البطاقات</h3>
        <div class="amount">{{ $cards->count() }}</div>
      </div>
      <div class="stat-icon blue"><i class="fas fa-credit-card"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>بطاقات متوفرة</h3>
        <div class="amount">{{ $cards->where('status', 'available')->count() }}</div>
      </div>
      <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>بطاقات مباعة</h3>
        <div class="amount">{{ $cards->where('status', 'sold')->count() }}</div>
      </div>
      <div class="stat-icon purple"><i class="fas fa-shopping-bag"></i></div>
    </div>
  </div>

  <!-- Filters -->
  <div class="filters-bar">
    <input type="text" id="searchCards" placeholder="بحث باسم المستخدم أو الباقة..." oninput="filterCards()">
    <select id="filterPackage" onchange="filterCards()">
      <option value="">كل الباقات</option>
      @foreach($packages as $package)
        <option value="{{ $package->name }}">{{ $package->name }}</option>
      @endforeach
    </select>
    <select id="filterCardStatus" onchange="filterCards()">
      <option value="">كل الحالات</option>
      <option value="available">متوفر</option>
      <option value="sold">مباع</option>
    </select>
  </div>

  <!-- Cards Table -->
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>اسم المستخدم</th>
          <th>كلمة المرور</th>
          <th>الباقة</th>
          <th>الحالة</th>
          <th>تاريخ الإضافة</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody id="cardsTableBody">
        @forelse($cards as $i => $card)
          <tr
            data-username="{{ strtolower($card->username) }}"
            data-package="{{ $card->package->name ?? '' }}"
            data-status="{{ $card->status }}">
            <td>{{ $i + 1 }}</td>
            <td><code style="background:#f1f5f9;padding:3px 8px;border-radius:4px;font-size:13px">{{ $card->username }}</code></td>
            <td><code style="background:#f1f5f9;padding:3px 8px;border-radius:4px;font-size:13px">{{ $card->password }}</code></td>
            <td>{{ $card->package->name ?? '-' }}</td>
            <td>
              <span class="badge-status {{ $card->status === 'available' ? 'good' : 'low' }}">
                {{ $card->status === 'available' ? 'متوفر' : 'مباع' }}
              </span>
            </td>
            <td>{{ $card->created_at->format('Y-m-d') }}</td>
            <td>
              @if($card->status === 'available')
                <button class="btn btn-primary" style="padding:6px 12px;font-size:13px"
                  onclick="openSellCard({{ $card->id }}, '{{ addslashes($card->username) }}', '{{ addslashes($card->password) }}', '{{ addslashes($card->package->name ?? '') }}')">
                  <i class="fas fa-shopping-cart"></i> بيع
                </button>
              @endif
              <form method="POST" action="{{ route('admin.cards.destroy', $card->id) }}" style="display:inline"
                onsubmit="return confirm('هل أنت متأكد من حذف هذه البطاقة؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="padding:6px 12px;font-size:13px">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr id="emptyRow">
            <td colspan="7">
              <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>لا يوجد بطاقات</h3>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

@section('modals')
  <!-- Upload Modal -->
  <div class="modal-overlay" id="uploadModal">
    <div class="modal">
      <div class="modal-header">
        <h3><i class="fas fa-file-upload"></i> رفع ملف بطاقات</h3>
        <button class="modal-close" onclick="closeModal('uploadModal')">&times;</button>
      </div>
      <form id="uploadForm" method="POST" action="{{ route('admin.cards.bulk-store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label>الباقة</label>
          <select class="form-control" name="package_id" id="uploadPackage" required>
            <option value="">اختر الباقة</option>
            @foreach($packages as $package)
              <option value="{{ $package->id }}">{{ $package->name }} - {{ format_currency($package->price) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>اختر ملف (PDF أو Excel)</label>
          <div style="border:2px dashed var(--border);border-radius:var(--radius);padding:40px 20px;text-align:center;cursor:pointer;transition:border-color 0.2s"
               id="dropZone" onclick="document.getElementById('fileInput').click()">
            <i class="fas fa-cloud-upload-alt" style="font-size:40px;color:var(--accent);margin-bottom:12px;display:block"></i>
            <p style="font-size:15px;font-weight:600;margin-bottom:6px">اسحب الملف هنا أو اضغط للاختيار</p>
            <p style="font-size:13px;color:var(--text-muted)">PDF, XLSX, XLS, CSV</p>
            <p id="fileName" style="margin-top:10px;font-weight:700;color:var(--accent);display:none"></p>
          </div>
          <input type="file" id="fileInput" name="cards_file" accept=".pdf,.xlsx,.xls,.csv" style="display:none" onchange="onFileSelected(this)">
        </div>
        <div id="previewArea" style="display:none">
          <div class="form-group">
            <label>معاينة البطاقات المستخرجة (<span id="previewCount">0</span> بطاقة)</label>
            <div style="max-height:200px;overflow-y:auto;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;font-size:13px;background:var(--bg-main)">
              <table style="width:100%;border-collapse:collapse">
                <thead>
                  <tr>
                    <th style="padding:6px 10px;text-align:right;border-bottom:1px solid var(--border)">Username</th>
                    <th style="padding:6px 10px;text-align:right;border-bottom:1px solid var(--border)">Password</th>
                  </tr>
                </thead>
                <tbody id="previewTableBody"></tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Hidden textarea to pass parsed cards as JSON -->
        <input type="hidden" name="parsed_cards" id="parsedCardsInput">
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" style="flex:1" id="uploadBtn" disabled>
            <i class="fas fa-upload"></i> رفع البطاقات
          </button>
          <button type="button" class="btn btn-outline" onclick="closeModal('uploadModal')">إلغاء</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Card Manual Modal -->
  <div class="modal-overlay" id="addCardModal">
    <div class="modal">
      <div class="modal-header">
        <h3>إضافة بطاقة يدوياً</h3>
        <button class="modal-close" onclick="closeModal('addCardModal')">&times;</button>
      </div>
      <form method="POST" action="{{ route('admin.cards.store') }}">
        @csrf
        <div class="form-group">
          <label>الباقة</label>
          <select class="form-control" name="package_id" required>
            <option value="">اختر الباقة</option>
            @foreach($packages as $package)
              <option value="{{ $package->id }}">{{ $package->name }} - {{ format_currency($package->price) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>اسم المستخدم (Username)</label>
          <input type="text" class="form-control" name="username" required placeholder="مثال: 340433433526">
        </div>
        <div class="form-group">
          <label>كلمة المرور (Password)</label>
          <input type="text" class="form-control" name="password" required placeholder="مثال: 564354">
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" style="flex:1"><i class="fas fa-save"></i> حفظ</button>
          <button type="button" class="btn btn-outline" onclick="closeModal('addCardModal')">إلغاء</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Sell Card Modal -->
  <div class="modal-overlay" id="sellCardModal">
    <div class="modal">
      <div class="modal-header">
        <h3><i class="fas fa-shopping-cart"></i> بيع بطاقة</h3>
        <button class="modal-close" onclick="closeModal('sellCardModal')">&times;</button>
      </div>
      <form method="POST" action="" id="sellCardForm">
        @csrf
        <input type="hidden" name="card_id" id="sellCardId">
        <div class="form-group">
          <label>بيانات البطاقة</label>
          <div style="background:var(--bg-main);padding:14px;border-radius:var(--radius-sm);font-size:14px">
            <p><strong>Username:</strong> <span id="sellCardUsername"></span></p>
            <p style="margin-top:6px"><strong>Password:</strong> <span id="sellCardPassword"></span></p>
            <p style="margin-top:6px"><strong>الباقة:</strong> <span id="sellCardPackage"></span></p>
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" style="flex:1"><i class="fas fa-check"></i> تأكيد البيع</button>
          <button type="button" class="btn btn-outline" onclick="closeModal('sellCardModal')">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
// Setup PDF.js
if (window.pdfjsLib) {
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
}

// Setup drag & drop
document.addEventListener('DOMContentLoaded', function() {
  const dropZone = document.getElementById('dropZone');
  if (dropZone) {
    dropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      dropZone.style.borderColor = 'var(--accent)';
      dropZone.style.background = '#ecfdf5';
    });
    dropZone.addEventListener('dragleave', () => {
      dropZone.style.borderColor = 'var(--border)';
      dropZone.style.background = '';
    });
    dropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropZone.style.borderColor = 'var(--border)';
      dropZone.style.background = '';
      const file = e.dataTransfer.files[0];
      if (file) {
        document.getElementById('fileInput').files = e.dataTransfer.files;
        onFileSelected(document.getElementById('fileInput'));
      }
    });
  }
});

// Client-side filtering
function filterCards() {
  const search = document.getElementById('searchCards').value.toLowerCase();
  const pkgFilter = document.getElementById('filterPackage').value.toLowerCase();
  const statusFilter = document.getElementById('filterCardStatus').value;
  const rows = document.querySelectorAll('#cardsTableBody tr[data-username]');
  let visibleCount = 0;

  rows.forEach(row => {
    const username = row.dataset.username || '';
    const pkg = (row.dataset.package || '').toLowerCase();
    const status = row.dataset.status || '';
    const matchSearch = username.includes(search) || pkg.includes(search);
    const matchPkg = !pkgFilter || pkg === pkgFilter;
    const matchStatus = !statusFilter || status === statusFilter;
    const visible = matchSearch && matchPkg && matchStatus;
    row.style.display = visible ? '' : 'none';
    if (visible) visibleCount++;
  });

  // Show/hide empty row
  const emptyRow = document.getElementById('emptyRow');
  if (emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';
}

// Open sell card modal
function openSellCard(id, username, password, packageName) {
  document.getElementById('sellCardId').value = id;
  document.getElementById('sellCardUsername').textContent = username;
  document.getElementById('sellCardPassword').textContent = password;
  document.getElementById('sellCardPackage').textContent = packageName;
  document.getElementById('sellCardForm').action = '/admin/cards/' + id + '/sell';
  openModal('sellCardModal');
}

// ========== File Upload Parsing ==========
let parsedCards = [];

function onFileSelected(input) {
  const file = input.files[0];
  if (!file) return;
  document.getElementById('fileName').textContent = file.name;
  document.getElementById('fileName').style.display = 'block';
  const ext = file.name.split('.').pop().toLowerCase();
  if (ext === 'pdf') {
    parsePDF(file);
  } else if (['xlsx', 'xls', 'csv'].includes(ext)) {
    parseExcel(file);
  }
}

async function parsePDF(file) {
  try {
    const arrayBuffer = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
    let fullText = '';
    for (let i = 1; i <= pdf.numPages; i++) {
      const page = await pdf.getPage(i);
      const content = await page.getTextContent();
      fullText += content.items.map(item => item.str).join(' ') + '\n';
    }
    extractCardsFromText(fullText);
  } catch (err) {
    alert('خطأ في قراءة ملف PDF: ' + err.message);
  }
}

function parseExcel(file) {
  const reader = new FileReader();
  reader.onload = function(e) {
    try {
      const workbook = XLSX.read(e.target.result, { type: 'binary' });
      const sheet = workbook.Sheets[workbook.SheetNames[0]];
      const data = XLSX.utils.sheet_to_json(sheet, { header: 1 });
      parsedCards = [];
      for (let row of data) {
        if (row.length >= 2) {
          const username = String(row[0]).trim();
          const password = String(row[1]).trim();
          if (username && password && username !== 'Username' && username !== 'username') {
            parsedCards.push({ username, password });
          }
        }
      }
      showPreview();
    } catch (err) {
      alert('خطأ في قراءة ملف Excel: ' + err.message);
    }
  };
  reader.readAsBinaryString(file);
}

function extractCardsFromText(text) {
  parsedCards = [];
  const regex = /Username\s+(\d+)\s*Password\s+(\d+)/gi;
  let match;
  while ((match = regex.exec(text)) !== null) {
    parsedCards.push({ username: match[1], password: match[2] });
  }
  if (parsedCards.length === 0) {
    alert('لم يتم العثور على بطاقات في الملف');
    return;
  }
  showPreview();
}

function showPreview() {
  document.getElementById('previewArea').style.display = 'block';
  document.getElementById('previewCount').textContent = parsedCards.length;
  document.getElementById('uploadBtn').disabled = false;
  document.getElementById('parsedCardsInput').value = JSON.stringify(parsedCards);

  const tbody = document.getElementById('previewTableBody');
  tbody.innerHTML = parsedCards.slice(0, 20).map(c => `
    <tr>
      <td style="padding:4px 10px;border-bottom:1px solid var(--border)">${c.username}</td>
      <td style="padding:4px 10px;border-bottom:1px solid var(--border)">${c.password}</td>
    </tr>
  `).join('');

  if (parsedCards.length > 20) {
    tbody.innerHTML += `<tr><td colspan="2" style="padding:8px;text-align:center;color:var(--text-muted)">... و ${parsedCards.length - 20} بطاقة أخرى</td></tr>`;
  }
}
</script>
@endpush
