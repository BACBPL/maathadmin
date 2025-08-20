@extends('layouts.dashboard')

@section('title', isset($employee) 
    ? __('Step 2: Upload Documents') 
    : __('Step 1: Vendor Details')
)

@section('content')
<div class="container">

  {{-- Flash Message --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @php
    // Are we on Step 2?
    $isDocs = isset($employee);
  @endphp


  <div class="card mb-4" @if($isDocs) style="display:none" @endif>
    <div class="card-header"><h3>{{ __('Employee Details') }}</h3></div>
    <div class="card-body">
      <form method="POST" action="{{ route('panel.employee.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label">{{ __('Name') }}</label>
          <input name="name"
                 class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name') }}" required>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('Address') }}</label>
          <textarea name="address"
                    class="form-control @error('address') is-invalid @enderror"
                    required>{{ old('address') }}</textarea>
          @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('Phone') }}</label>
          <input name="phone"
                 class="form-control @error('phone') is-invalid @enderror"
                 value="{{ old('phone') }}" required>
          @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

         <div class="mb-3">
          <label class="form-label">{{ __('Email') }}</label>
          <input name="email"
                 class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email') }}" required>
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        
          <div class="mb-3">
          <label class="form-label">{{ __('Personal Image') }}</label>
          <input type="file"
                 name="personal_image"
                 class="form-control @error('personal_image') is-invalid @enderror"
                 accept="image/*">
          @error('personal_image')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <button class="btn btn-primary">{{ __('Next') }}</button>
      </form>
    </div>
  </div>


  <div class="card mb-4" @if(! $isDocs) style="display:none" @endif>
    <div class="card-header">
      <h3>{{ __('Upload Documents for:') }} {{ $vendor->company_name ?? '' }}</h3>
    </div>
    <div class="card-body">
      <form method="POST"
            action=""
            enctype="multipart/form-data">
        @csrf

        {{-- Aadhar --}}
        <div class="mb-3">
          <label class="form-label">{{ __('Aadhar Number') }}</label>
          <input name="aadhar_number"
                 class="form-control @error('aadhar_number') is-invalid @enderror"
                 value="{{ old('aadhar_number') }}" required>
          @error('aadhar_number')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('Aadhar Image') }}</label>
          <input type="file" name="aadhar_image"
                 class="form-control @error('aadhar_image') is-invalid @enderror"
                 accept="image/*" required>
          @error('aadhar_image')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- PAN --}}
        <div class="mb-3">
          <label class="form-label">{{ __('PAN Number') }}</label>
          <input name="pan_number"
                 class="form-control @error('pan_number') is-invalid @enderror"
                 value="{{ old('pan_number') }}" required>
          @error('pan_number')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('PAN Image') }}</label>
          <input type="file" name="pan_image"
                 class="form-control @error('pan_image') is-invalid @enderror"
                 accept="image/*" required>
          @error('pan_image')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Trade License --}}
        <div class="mb-3">
          <label class="form-label">{{ __('Trade License Number') }}</label>
          <input name="trade_license_number"
                 class="form-control @error('trade_license_number') is-invalid @enderror"
                 value="{{ old('trade_license_number') }}" required>
          @error('trade_license_number')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('Trade License Image') }}</label>
          <input type="file" name="trade_license_image"
                 class="form-control @error('trade_license_image') is-invalid @enderror"
                 accept="image/*" required>
          @error('trade_license_image')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- GST --}}
        <div class="mb-3">
          <label class="form-label">{{ __('GST Number') }}</label>
          <input name="gst_number"
                 class="form-control @error('gst_number') is-invalid @enderror"
                 value="{{ old('gst_number') }}" required>
          @error('gst_number')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('GST Image') }}</label>
          <input type="file" name="gst_image"
                 class="form-control @error('gst_image') is-invalid @enderror"
                 accept="image/*" required>
          @error('gst_image')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <a href="{{ route('panel.vendor.create') }}" class="btn btn-secondary">
          {{ __('Back') }}
        </a>
        <button class="btn btn-success">{{ __('Save Vendor') }}</button>
      </form>
    </div>
  </div>

</div>
@endsection
