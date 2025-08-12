@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h4 class="mb-3">{{ __('Set Service Price') }}</h4>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($items->isEmpty())
      <div class="alert alert-info">{{ __('No subcategories found for your services.') }}</div>
    @else
    <form method="POST" action="{{ route('panel.vendor.services.price.save') }}">
      @csrf

      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>{{ __('Main Category') }}</th>
              <th>{{ __('Sub Category') }}</th>
              <th style="width:180px">{{ __('Price') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $row)
              <tr>
                <td>{{ $row['category'] }}</td>
                <td>{{ $row['subcategory'] }}</td>
                <td>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="price[{{ $row['id'] }}]"
                    value="{{ old('price.'.$row['id'], $row['price']) }}"
                    class="form-control @error('price.'.$row['id']) is-invalid @enderror"
                    placeholder="{{ __('Enter price') }}"
                  >
                  @error('price.'.$row['id'])
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <button class="btn btn-primary">{{ __('Save Prices') }}</button>
    </form>
    @endif
</div>
@endsection
