<!-- Profile Settings Modal -->
<div class="modal-overlay" id="profileSettingsModal">
  <div class="modal">
    <div class="modal-header"><h3><i class="fas fa-user-cog"></i> إعدادات الحساب</h3><button class="modal-close" onclick="closeModal('profileSettingsModal')">&times;</button></div>
    <form method="POST" action="{{ route('admin.profile.update') }}">
      @csrf @method('PUT')
      <div class="form-group"><label>الاسم</label><input type="text" class="form-control" name="name" value="{{ auth()->user()->name ?? '' }}"></div>
      <div class="form-group"><label>البريد</label><input type="email" class="form-control" name="email" value="{{ auth()->user()->email ?? '' }}"></div>
      <div class="form-group"><label>الهاتف</label><input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone ?? '' }}"></div>
      <div class="form-actions"><button type="submit" class="btn btn-primary" style="flex:1"><i class="fas fa-save"></i> حفظ</button><button type="button" class="btn btn-outline" onclick="closeModal('profileSettingsModal')">إلغاء</button></div>
    </form>
  </div>
</div>

<!-- Change Password Modal -->
<div class="modal-overlay" id="changePasswordModal">
  <div class="modal">
    <div class="modal-header"><h3><i class="fas fa-key"></i> تغيير كلمة المرور</h3><button class="modal-close" onclick="closeModal('changePasswordModal')">&times;</button></div>
    <form method="POST" action="{{ route('admin.password.update') }}">
      @csrf @method('PUT')
      <div class="form-group"><label>الحالية</label><input type="password" class="form-control" name="current_password" required></div>
      <div class="form-group"><label>الجديدة</label><input type="password" class="form-control" name="password" required></div>
      <div class="form-group"><label>تأكيد</label><input type="password" class="form-control" name="password_confirmation" required></div>
      <div class="form-actions"><button type="submit" class="btn btn-primary" style="flex:1"><i class="fas fa-save"></i> تغيير</button><button type="button" class="btn btn-outline" onclick="closeModal('changePasswordModal')">إلغاء</button></div>
    </form>
  </div>
</div>
