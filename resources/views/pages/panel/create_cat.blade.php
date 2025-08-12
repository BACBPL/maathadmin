@extends('layouts.dashboard')

@section('title', isset($editCategory)
    ? __('Edit Category')
    : __('Create Category')
)

@section('content')
<div class="container">

  {{-- Flash message --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @php
    $isEdit   = isset($editCategory);
    $action   = $isEdit
      ? route('panel.category.update', $editCategory)
      : route('panel.category.store');
    $method   = $isEdit ? 'PUT' : 'POST';
    $btnText  = $isEdit ? __('Modify') : __('Save');
  @endphp

  {{-- Form --}}
  <div class="card mb-5">
    <div class="card-header">
      <h3>{{ $isEdit ? __('Edit Category') : __('Create Category') }}</h3>
    </div>
    <div class="card-body">
      <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method($method) @endif

        {{-- Name --}}
        <div class="mb-3">
          <label for="name" class="form-label">{{ __('Category Name') }}</label>
          <input
            type="text" name="name" id="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $isEdit ? $editCategory->name : '') }}"
            required
          >
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Current Image (edit) --}}
        @if($isEdit && $editCategory->image)
          <div class="mb-3">
            <strong>{{ __('Current Image') }}:</strong><br>
            <img
              src="{{ asset($editCategory->image) }}"
              style="height:80px; object-fit:cover; margin-top:8px;"
            >
          </div>
        @endif

        {{-- Upload Image --}}
        <div class="mb-3">
          <label for="image" class="form-label">
            {{ $isEdit ? __('Change Image') : __('Category Image') }}
          </label>
          <input
            type="file" name="image" id="image"
            class="form-control @error('image') is-invalid @enderror"
            accept="image/*"
          >
          @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ $btnText }}</button>
        @if($isEdit)
          <a href="" class="btn btn-secondary">
            {{ __('Cancel') }}
          </a>
        @endif
      </form>
    </div>
  </div>

  {{-- Table --}}
  <div class="card">
    <div class="card-header">
      <h3>{{ __('All Categories') }}</h3>
    </div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Image') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $cat)
            <tr>
              <td>{{ $cat->name }}</td>
              <td>
                @if($cat->image)
                  <img
                    src="{{ asset($cat->image) }}"
                    style="height:50px; object-fit:cover;"
                  >
                @else — @endif
              </td>
              <td>
                <a href="{{ route('panel.category.edit', $cat) }}" class="btn btn-sm btn-success">
                  {{ __('Edit') }}
                </a>
                <form action="{{ route('panel.category.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center py-3">{{ __('No categories found.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection