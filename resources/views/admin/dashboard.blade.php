@extends('layouts.admin')

@section('title', 'الرئيسية')
@section('page-title', 'الرئيسية')

@section('content')
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info">
        <h3>رصيد البطاقات</h3>
        <div class="amount">{{ number_format($totalCards ?? 0, 0) }} <span>كرت</span></div>
      </div>
      <div class="stat-icon blue"><i class="fas fa-credit-card"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>رصيد المستخدمين</h3>
        <div class="amount">{{ format_currency($totalUserBalance ?? 0) }}</div>
      </div>
      <div class="stat-icon green"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>المبيعات اليوم</h3>
        <div class="amount">{{ format_currency($salesToday ?? 0) }}</div>
      </div>
      <div class="stat-icon purple"><i class="fas fa-shopping-cart"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>طلبات شحن معلقة</h3>
        <div class="amount">{{ $pendingShipping ?? 0 }} <span>طلب</span></div>
      </div>
      <div class="stat-icon orange"><i class="fas fa-truck"></i></div>
    </div>
  </div>

  <div class="section-header"><h2>إجراءات سريعة</h2></div>
  <div class="quick-actions">
    <div class="action-card" onclick="window.location.href='{{ route('admin.shipping') }}'">
      <div class="action-icon green"><i class="fas fa-money-bill-transfer"></i></div>
      <h4>طلبات الشحن</h4>
    </div>
    <div class="action-card" onclick="window.location.href='{{ route('admin.users') }}'">
      <div class="action-icon blue"><i class="fas fa-users-gear"></i></div>
      <h4>المستخدمين</h4>
    </div>
    <div class="action-card" onclick="window.location.href='{{ route('admin.cards-store') }}'">
      <div class="action-icon orange"><i class="fas fa-box-open"></i></div>
      <h4>مخزن البطاقات</h4>
    </div>
    <div class="action-card" onclick="window.location.href='{{ route('admin.invoices') }}'">
      <div class="action-icon red"><i class="fas fa-file-invoice-dollar"></i></div>
      <h4>الفواتير</h4>
    </div>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h3>حالة المخزون الحالي</h3>
      <a href="{{ route('admin.cards-store') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> عرض الكل</a>
    </div>
    <table class="data-table">
      <thead><tr><th>اسم الباقة</th><th>الكمية المتوفرة</th><th>الحالة</th></tr></thead>
      <tbody>
        @forelse ($packages ?? [] as $pkg)
          @php
            $cls = $pkg->available_count == 0 ? 'low' : ($pkg->available_count <= 5 ? 'medium' : 'good');
            $label = $pkg->available_count == 0 ? 'منخفض' : ($pkg->available_count <= 5 ? 'متوسط' : 'متوفر');
          @endphp
          <tr>
            <td>{{ $pkg->name }}</td>
            <td>{{ $pkg->available_count }} كرت</td>
            <td><span class="badge-status {{ $cls }}">{{ $label }}</span></td>
          </tr>
        @empty
          <tr><td colspan="3" style="text-align:center">لا يوجد باقات</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
