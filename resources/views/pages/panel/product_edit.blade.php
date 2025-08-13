@extends('layouts.dashboard')
@section('title', __('Edit Product'))

@section('content')
<div class="container">
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <div class="card">
    <div class="card-header"><h3>{{ __('Edit Product') }}</h3></div>
    <div class="card-body">
      <form method="POST" action="{{ route('panel.vendor.product.update',$product) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row">
          <div class="col-md-8">
            {{-- Name & SKU (read-only) --}}
            <div class="row g-2">
              <div class="col-md-8 mb-3">
                <label class="form-label">{{ __('Title (read-only)') }}</label>
                <input type="text" class="form-control" value="{{ $product->title }}" disabled>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">{{ __('SKU (read-only)') }}</label>
                <input type="text" class="form-control" value="{{ $product->sku }}" disabled>
              </div>
            </div>

            {{-- Description --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Description') }}</label>
              <textarea name="description" rows="6" class="form-control">{{ old('description',$product->description) }}</textarea>
            </div>

            {{-- Specifications (existing + add new) --}}
            <div class="mb-3">
              <label class="form-label d-block">{{ __('Specifications') }}</label>

              <div id="specRows">
                @forelse($product->specs as $s)
                  <div class="row g-2 mb-2 spec-row">
                    <input type="hidden" name="spec_id[]" value="{{ $s->id }}">
                    <div class="col-md-4">
                      <input type="text" name="spec_key[]" class="form-control" value="{{ $s->spec_key }}">
                    </div>
                    <div class="col-md-5">
                      <input type="text" name="spec_value[]" class="form-control" value="{{ $s->spec_value }}">
                    </div>
                    <div class="col-md-2">
                      <input type="number" step="0.01" min="0" name="spec_price[]" class="form-control"
                             value="{{ number_format($s->extra_price ?? 0, 2, '.', '') }}">
                    </div>
                    <div class="col-md-1 d-grid">
                      <input type="checkbox" class="btn-check" name="delete_spec[]" value="{{ $s->id }}" id="del{{ $s->id }}">
                      <label class="btn btn-outline-danger" for="del{{ $s->id }}">×</label>
                    </div>
                  </div>
                @empty
                  {{-- no rows; show one empty row for new --}}
                  <div class="row g-2 mb-2 spec-row">
                    <input type="hidden" name="spec_id[]" value="">
                    <div class="col-md-4"><input type="text" name="spec_key[]" class="form-control" placeholder="{{ __('Key') }}"></div>
                    <div class="col-md-5"><input type="text" name="spec_value[]" class="form-control" placeholder="{{ __('Value') }}"></div>
                    <div class="col-md-2"><input type="number" step="0.01" min="0" name="spec_price[]" class="form-control" value="0.00"></div>
                    <div class="col-md-1 d-grid"><span class="btn btn-outline-secondary disabled">—</span></div>
                  </div>
                @endforelse
              </div>

              <button type="button" id="addSpec" class="btn btn-sm btn-secondary">{{ __('Add spec') }}</button>
              <div class="form-text">{{ __('Leave Extra Price as 0.00 when no additional charge.') }}</div>
            </div>

            {{-- Images --}}
            <div class="mb-3">
              <label class="form-label d-block">{{ __('Images') }}</label>

              @if($product->images->count())
                <div class="row g-2 mb-2">
                  @foreach($product->images as $img)
                    <div class="col-6 col-md-4 col-lg-3">
                      <div class="border rounded p-2 h-100">
                        <img src="{{ asset($img->path) }}" style="width:100%;height:120px;object-fit:cover;border-radius:6px;">
                        <div class="mt-2 d-flex gap-2 align-items-center">
                          <input type="radio" name="primary_image_id" value="{{ $img->id }}" {{ $img->is_primary ? 'checked' : '' }}>
                          <small>{{ __('Primary') }}</small>
                          <div class="ms-auto form-check">
                            <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $img->id }}" id="rm{{ $img->id }}">
                            <label class="form-check-label small" for="rm{{ $img->id }}">{{ __('Remove') }}</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif

              <div class="mb-2">
                <input type="file" name="images[]" accept="image/*" multiple class="form-control">
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="make_first_new_primary" id="mknew">
                <label class="form-check-label" for="mknew">{{ __('Make first newly uploaded image primary') }}</label>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            {{-- Price / Stock / Status --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Price') }} *</label>
              <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price',$product->price) }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Sale Price') }}</label>
              <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price',$product->sale_price) }}">
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Stock Quantity') }} *</label>
              <input type="number" min="0" name="stock_qty" class="form-control" value="{{ old('stock_qty',$product->stock_qty) }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Status') }}</label>
              @php $st = old('status',$product->status); @endphp
              <select name="status" class="form-control">
                <option value="draft" {{ $st==='draft'?'selected':'' }}>{{ __('Draft') }}</option>
                <option value="active" {{ $st==='active'?'selected':'' }}>{{ __('Active') }}</option>
                <option value="inactive" {{ $st==='inactive'?'selected':'' }}>{{ __('Inactive') }}</option>
              </select>
            </div>

            {{-- Category --}}
            <div class="mb-3">
              <label class="form-label">{{ __('Category') }} *</label>
              <select name="category_id" class="form-control" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ (string)old('category_id',optional($product->categories->first())->id) === (string)$cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Physical --}}
            <div class="row g-2">
              <div class="col-6 mb-3"><label class="form-label">{{ __('Weight') }}</label>
                <input type="number" step="0.01" name="weight" class="form-control" value="{{ old('weight',$product->weight) }}">
              </div>
              <div class="col-6 mb-3"><label class="form-label">{{ __('Length') }}</label>
                <input type="number" step="0.01" name="length" class="form-control" value="{{ old('length',$product->length) }}">
              </div>
              <div class="col-6 mb-3"><label class="form-label">{{ __('Width') }}</label>
                <input type="number" step="0.01" name="width" class="form-control" value="{{ old('width',$product->width) }}">
              </div>
              <div class="col-6 mb-3"><label class="form-label">{{ __('Height') }}</label>
                <input type="number" step="0.01" name="height" class="form-control" value="{{ old('height',$product->height) }}">
              </div>
            </div>
          </div>
        </div>

        <button class="btn btn-primary">{{ __('Save Changes') }}</button>
        <a href="{{ route('panel.vendor.product.edit') }}" class="btn btn-secondary">{{ __('Back to list') }}</a>
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
    <input type="hidden" name="spec_id[]" value="">
    <div class="col-md-4"><input type="text" name="spec_key[]" class="form-control" placeholder="{{ __('Key') }}"></div>
    <div class="col-md-5"><input type="text" name="spec_value[]" class="form-control" placeholder="{{ __('Value') }}"></div>
    <div class="col-md-2"><input type="number" step="0.01" min="0" name="spec_price[]" class="form-control" value="0.00" placeholder="{{ __('Extra Price') }}"></div>
    <div class="col-md-1 d-grid"><span class="btn btn-outline-secondary disabled">—</span></div>
  `;
  wrap.appendChild(row);
});
</script>
@endpush
@endsection
