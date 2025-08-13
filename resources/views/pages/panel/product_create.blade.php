@extends('layouts.dashboard')

@section('title', __('Create Product'))

@section('content')
<div class="container">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-header">
      <h3>{{ __('Create Product') }}</h3>
    </div>
    <div class="card-body">
      <form action="{{ route('panel.vendor.product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
          <div class="col-md-8">
            {{-- Title --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Title') }} <sup class="text-danger">*</sup></label>
              <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                     value="{{ old('title') }}" required>
              @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Description --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Description') }}</label>
              <textarea name="description" rows="6"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Specifications (key/value/extra_price) --}}
            <div class="mb-3">
              <label class="form-label d-block">{{ __('Specifications') }}</label>
              <div id="specRows">
                <div class="row g-2 mb-2 spec-row">
                  <div class="col-md-4">
                    <input type="text" name="spec_key[]" class="form-control"
                           placeholder="{{ __('Key (e.g., Color)') }}">
                  </div>
                  <div class="col-md-5">
                    <input type="text" name="spec_value[]" class="form-control"
                           placeholder="{{ __('Value (e.g., Red)') }}">
                  </div>
                  <div class="col-md-2">
                    <input type="number" step="0.01" min="0" name="spec_price[]"
                           class="form-control" value="0.00"
                           placeholder="{{ __('Extra Price') }}">
                  </div>
                  <div class="col-md-1 d-grid">
                    <button type="button" class="btn btn-outline-danger remove-spec">&times;</button>
                  </div>
                </div>
              </div>
              <button type="button" id="addSpec" class="btn btn-sm btn-secondary">{{ __('Add spec') }}</button>
              <div class="form-text">{{ __('Leave Extra Price as 0.00 when there is no additional charge.') }}</div>
            </div>

            {{-- Images --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Images') }} <sup class="text-danger">*</sup></label>
              <input type="file" name="images[]" class="form-control @error('images') is-invalid @enderror"
                     accept="image/*" multiple required>
              @error('images')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">{{ __('First image will be set as primary.') }}</div>
            </div>
          </div>

          <div class="col-md-4">
            {{-- Price --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Price') }} <sup class="text-danger">*</sup></label>
              <input type="number" step="0.01" name="price"
                     class="form-control @error('price') is-invalid @enderror"
                     value="{{ old('price') }}" required>
              @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Sale Price') }}</label>
              <input type="number" step="0.01" name="sale_price"
                     class="form-control @error('sale_price') is-invalid @enderror"
                     value="{{ old('sale_price') }}">
              @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Stock --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Stock Quantity') }} <sup class="text-danger">*</sup></label>
              <input type="number" name="stock_qty" min="0"
                     class="form-control @error('stock_qty') is-invalid @enderror"
                     value="{{ old('stock_qty', 0) }}" required>
              @error('stock_qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- SKU --}}
            <div class="mb-3">
              <label class="form-label">{{ __('SKU') }}</label>
              <input type="text" name="sku"
                     class="form-control @error('sku') is-invalid @enderror"
                     value="{{ old('sku') }}">
              @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Status --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Status') }}</label>
              <select name="status" class="form-control @error('status') is-invalid @enderror">
                @php $st = old('status','active'); @endphp
                <option value="draft" {{ $st==='draft'?'selected':'' }}>{{ __('Draft') }}</option>
                <option value="active" {{ $st==='active'?'selected':'' }}>{{ __('Active') }}</option>
                <option value="inactive" {{ $st==='inactive'?'selected':'' }}>{{ __('Inactive') }}</option>
              </select>
              @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Category --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Category') }} <sup class="text-danger">*</sup></label>
              <select name="category_id" id="category_id"
                      class="form-control @error('category_id') is-invalid @enderror"
                      required>
                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>
                  {{ __('Select a category') }}
                </option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}"
                    {{ (string)old('category_id') === (string)$cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
              @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Physical fields --}}
            <div class="row g-2">
              <div class="col-6 mb-3">
                <label class="form-label">{{ __('Weight') }}</label>
                <input type="number" step="0.01" name="weight" class="form-control" value="{{ old('weight') }}">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">{{ __('Length') }}</label>
                <input type="number" step="0.01" name="length" class="form-control" value="{{ old('length') }}">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">{{ __('Width') }}</label>
                <input type="number" step="0.01" name="width" class="form-control" value="{{ old('width') }}">
              </div>
              <div class="col-6 mb-3">
                <label class="form-label">{{ __('Height') }}</label>
                <input type="number" step="0.01" name="height" class="form-control" value="{{ old('height') }}">
              </div>
            </div>
          </div>
        </div>

        <button class="btn btn-primary">{{ __('Save Product') }}</button>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('addSpec').addEventListener('click', function () {
  const wrap = document.getElementById('specRows');
  const row = document.createElement('div');
  row.className = 'row g-2 mb-2 spec-row';
  row.innerHTML = `
    <div class="col-md-4">
      <input type="text" name="spec_key[]" class="form-control" placeholder="{{ __('Key (e.g., Color)') }}">
    </div>
    <div class="col-md-5">
      <input type="text" name="spec_value[]" class="form-control" placeholder="{{ __('Value (e.g., Red)') }}">
    </div>
    <div class="col-md-2">
      <input type="number" step="0.01" min="0" name="spec_price[]" class="form-control" value="0.00" placeholder="{{ __('Extra Price') }}">
    </div>
    <div class="col-md-1 d-grid">
      <button type="button" class="btn btn-outline-danger remove-spec">&times;</button>
    </div>
  `;
  wrap.appendChild(row);
});

document.addEventListener('click', function (e) {
  if (e.target && e.target.classList.contains('remove-spec')) {
    const row = e.target.closest('.spec-row');
    if (row) row.remove();
  }
});
</script>
@endpush
@endsection
