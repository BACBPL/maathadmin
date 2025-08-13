@extends('layouts.dashboard')
@section('title', __('Vendor Wise Products'))

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header">
      <h3 class="mb-0">{{ __('Vendor Wise Products') }}</h3>
    </div>

    <div class="card-body">
      @if($vendorList->isEmpty())
        <div class="alert alert-info mb-0">
          {{ __('No products found for any vendor yet.') }}
        </div>
      @else
        {{-- Tabs --}}
        <ul class="nav nav-tabs" role="tablist">
          @foreach($vendorList as $i => $vendor)
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ (int)$activeVendorId === (int)$vendor->id ? 'active' : '' }}"
                      id="tab-{{ $vendor->id }}"
                      data-bs-toggle="tab"
                      data-bs-target="#pane-{{ $vendor->id }}"
                      type="button" role="tab"
                      aria-controls="pane-{{ $vendor->id }}"
                      aria-selected="{{ (int)$activeVendorId === (int)$vendor->id ? 'true' : 'false' }}">
                {{ $vendor->name }}
              </button>
            </li>
          @endforeach
        </ul>

        {{-- Tab panes --}}
        <div class="tab-content border border-top-0 rounded-bottom p-3">
          @foreach($vendorList as $vendor)
            @php
              $rows = $productsByVendor->get($vendor->id) ?? collect();
            @endphp
            <div class="tab-pane fade {{ (int)$activeVendorId === (int)$vendor->id ? 'show active' : '' }}"
                 id="pane-{{ $vendor->id }}" role="tabpanel" aria-labelledby="tab-{{ $vendor->id }}">

              <div class="table-responsive">
                <table class="table table-striped align-middle">
                  <thead class="table-light">
                    <tr>
                      <th style="width:240px">{{ __('Images') }}</th>
                      <th>{{ __('Title') }}</th>
                      <th>{{ __('SKU') }}</th>
                      <th>{{ __('Price') }}</th>
                      <th>{{ __('Stock') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th>{{ __('Categories') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($rows as $p)
                      <tr>
                        <td>
                          {{-- Horizontal image viewer --}}
                          <div class="d-flex gap-2 overflow-auto py-1" style="white-space:nowrap;">
                            @forelse($p->images as $img)
                              <img src="{{ asset($img->path) }}"
                                   alt="img"
                                   style="height:70px;width:100px;object-fit:cover;border-radius:6px;display:inline-block">
                            @empty
                              <span class="text-muted">—</span>
                            @endforelse
                          </div>
                        </td>
                        <td>{{ $p->title }}</td>
                        <td>{{ $p->sku }}</td>
                        <td>{{ number_format($p->price, 2) }}</td>
                        <td>{{ $p->stock_qty }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($p->status) }}</span></td>
                        <td>
                          @if($p->categories && $p->categories->count())
                            {{ $p->categories->pluck('name')->join(', ') }}
                          @else
                            —
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                          {{ __('No products uploaded by this vendor.') }}
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
