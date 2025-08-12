@extends('layouts.dashboard')

@section('title','Vendor-Wise Wallet Balance')

@section('content')
<div class="container">
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  <div class="card mb-4">
    <div class="card-header"><h3>{{ __('Vendor-Wise Wallet Balance') }}</h3></div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr><th>{{ __('Vendor') }}</th><th>{{ __('Wallet Balance') }}</th><th>{{ __('Action') }}</th></tr>
        </thead>
        <tbody>
        @foreach($vendors as $v)
          <tr>
            <td>{{ $v->name }}</td>
            <td>₹{{ number_format($v->walletDetail->wallet_balance ?? 0, 2) }}</td>
            <td><button type="button" class="btn btn-sm btn-primary add-wallet-btn" data-id="{{ $v->id }}" data-name="{{ $v->name }}">{{ __('Add Wallet') }}</button></td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="addWalletModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <form id="addWalletForm" method="POST" action="">{{ csrf_field() }}
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Add Wallet for') }} <span id="modalName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">{{ __('Amount') }}</label>
        <input type="number" name="amount" step="0.01" min="0.01" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
      </div>
    </form>
  </div></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const addRouteTemplate = "{{ route('panel.vendor.wallet.add', ['vendor' => '__ID__']) }}";
  document.querySelectorAll('.add-wallet-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id, name = btn.dataset.name;
      document.getElementById('modalName').innerText = name;
      document.getElementById('addWalletForm').action = addRouteTemplate.replace('__ID__', id);
      new bootstrap.Modal(document.getElementById('addWalletModal')).show();
    });
  });
});
</script>
@endpush
