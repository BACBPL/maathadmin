@extends('layouts.dashboard')
@section('title', __('My Products'))

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header"><h3>{{ __('My Products') }}</h3></div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>{{ __('Image') }}</th>
            <th>{{ __('Title') }}</th>
            <th>{{ __('SKU') }}</th>
            <th>{{ __('Price') }}</th>
            <th>{{ __('Stock') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Category') }}</th>
            <th class="text-end">{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $p)
            <tr>
              <td style="width:70px">
                @php $pi = $p->primaryImage; @endphp
                <img src="{{ $pi ? asset($pi->path) : asset('assets/img/placeholder.png') }}"
                     style="height:50px;object-fit:cover;border-radius:8px;">
              </td>
              <td>{{ $p->title }}</td>
              <td>{{ $p->sku }}</td>
              <td>{{ number_format($p->price,2) }}</td>
              <td>{{ $p->stock_qty }}</td>
              <td><span class="badge bg-secondary">{{ ucfirst($p->status) }}</span></td>
              <td>{{ optional($p->categories->first())->name }}</td>
              <td class="text-end">
                <a href="{{ route('panel.vendor.product.edit.form',$p) }}" class="btn btn-sm btn-primary">
                  {{ __('Edit') }}
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center py-4">{{ __('No products found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $products->links() }}
    </div>
  </div>
</div>
@endsection
