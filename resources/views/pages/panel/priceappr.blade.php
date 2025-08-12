@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h4 class="mb-3">{{ __('Check Vendor-Service Wise Price') }}</h4>

  @if($vendors->isEmpty())
    <div class="alert alert-info mb-0">{{ __('No vendors found.') }}</div>
    @return
  @endif

  {{-- Tabs --}}
  <ul class="nav nav-tabs mb-3">
    @foreach($vendors as $v)
      <li class="nav-item">
        <a class="nav-link {{ $v->id === $selectedVendorId ? 'active' : '' }}"
           href="{{ route('panel.vendor.price', ['vendor_id' => $v->id]) }}">
          {{ $v->name }}
        </a>
      </li>
    @endforeach
  </ul>

  {{-- Table --}}
  @if($rows->isEmpty())
    <div class="alert alert-warning">{{ __('No pending prices for this vendor.') }}</div>
  @else
    <div class="table-responsive">
      <table class="table table-striped align-middle" id="pending-price-table">
        <thead>
          <tr>
            <th style="width: 25%">{{ __('Vendor') }}</th>
            <th style="width: 35%">{{ __('Subcategory') }}</th>
            <th style="width: 20%">{{ __('Price') }}</th>
            <th style="width: 20%">{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
        @foreach($rows as $r)
          <tr id="row-{{ $r->id }}">
            <td>{{ $r->vendor->name ?? '—' }}</td>
            <td>{{ $r->subcategory->name ?? '—' }}</td>
            <td>{{ number_format($r->price, 2) }}</td>
            <td>
              <button type="button"
                      class="btn btn-success btn-sm approve-btn"
                      data-url="{{ route('panel.vendor.price.approve', $r->id) }}">
                {{ __('Approve') }}
              </button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

{{-- tiny JS to approve without reload --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const token = '{{ csrf_token() }}';
    document.querySelectorAll('.approve-btn').forEach(btn => {
      btn.addEventListener('click', async function() {
        const url = this.dataset.url;
        this.disabled = true;

        try {
          const res = await fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json'
            }
          });
          const data = await res.json();
          if (data.ok) {
            const row = document.getElementById('row-' + data.id);
            if (row) row.remove(); // instantly remove
          } else {
            this.disabled = false;
            alert('Failed to approve. Try again.');
          }
        } catch (e) {
          this.disabled = false;
          alert('Network error. Please try again.');
        }
      });
    });
  });
</script>
@endsection
