@extends('layouts.admin')

@section('title', 'تقارير المبيعات')
@section('page-title', 'تقارير المبيعات')

@section('content')
  @if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="page-header">
    <h1><i class="fas fa-chart-line"></i> تقارير المبيعات</h1>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info">
        <h3>إجمالي الإيرادات</h3>
        <div class="amount">{{ format_currency($totalRevenue) }}</div>
      </div>
      <div class="stat-icon green"><i class="fas fa-coins"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>إيرادات اليوم</h3>
        <div class="amount">{{ format_currency($todayRevenue) }}</div>
      </div>
      <div class="stat-icon orange"><i class="fas fa-receipt"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>إيرادات الشهر</h3>
        <div class="amount">{{ format_currency($monthRevenue) }}</div>
      </div>
      <div class="stat-icon blue"><i class="fas fa-chart-pie"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>عدد البطاقات المباعة</h3>
        <div class="amount">{{ $totalSold }}</div>
      </div>
      <div class="stat-icon purple"><i class="fas fa-exchange-alt"></i></div>
    </div>
  </div>

  <!-- Sales by Package -->
  <div class="table-container">
    <div class="table-header">
      <h3>المبيعات حسب الباقة</h3>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>الباقة</th>
          <th>عدد المباعة</th>
          <th>الإيرادات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($packageStats as $stat)
          <tr>
            <td><strong>{{ $stat->name }}</strong></td>
            <td>{{ $stat->sold_count }}</td>
            <td>{{ format_currency($stat->revenue) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="3">
              <div class="empty-state">
                <i class="fas fa-chart-bar"></i>
                <h3>لا يوجد مبيعات بعد</h3>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Recent Invoices -->
  <div class="table-container">
    <div class="table-header">
      <h3>آخر الفواتير</h3>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>رقم الفاتورة</th>
          <th>العميل</th>
          <th>الباقة</th>
          <th>المبلغ</th>
          <th>التاريخ</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentInvoices as $invoice)
          <tr>
            <td><strong>#{{ $invoice->id }}</strong></td>
            <td>{{ $invoice->user->name ?? '-' }}</td>
            <td>{{ $invoice->package->name ?? '-' }}</td>
            <td>{{ format_currency($invoice->amount) }}</td>
            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5">
              <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>لا يوجد فواتير بعد</h3>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
