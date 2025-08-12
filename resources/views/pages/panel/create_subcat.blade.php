@extends('layouts.dashboard')

@section('title', isset($editSub) ? __('Edit Sub Category') : __('Create Sub Category'))

@section('content')
<div class="container">

  {{-- Success Message --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @php
    $isEdit   = isset($editSub);
    $action   = $isEdit
      ? route('panel.subcategory.update', $editSub)
      : route('panel.subcategory.store');
    $method   = $isEdit ? 'PUT' : 'POST';
    $btnLabel = $isEdit ? __('Modify') : __('Save');
  @endphp

  {{-- ── Form ───────────────────────────────────── --}}
  <div class="card mb-5">
    <div class="card-header">
      <h3>{{ $isEdit ? __('Edit Sub Category') : __('Create Sub Category') }}</h3>
    </div>
    <div class="card-body">
      <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method($method) @endif

        {{-- Category Dropdown --}}
        <div class="mb-3">
          <label for="category_id" class="form-label">{{ __('Parent Category') }}</label>
          <select name="category_id" id="category_id"
                  class="form-select @error('category_id') is-invalid @enderror"
                  required>
            <option value="">{{ __('Select one') }}</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}"
                {{ old('category_id', $isEdit ? $editSub->category_id : '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
          @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Sub-Category Name --}}
        <div class="mb-3">
          <label for="name" class="form-label">{{ __('Sub Category Name') }}</label>
          <input type="text" name="name" id="name"
                 class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name', $isEdit ? $editSub->name : '') }}"
                 required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Current Image Preview (edit only) --}}
        @if($isEdit && $editSub->image)
          <div class="mb-3">
            <strong>{{ __('Current Image') }}:</strong><br>
            <img src="{{ asset($editSub->image) }}"
                 style="height:80px; object-fit:cover; margin-top:8px;">
          </div>
        @endif

        {{-- Upload Image --}}
        <div class="mb-3">
          <label for="image" class="form-label">
            {{ $isEdit ? __('Change Image') : __('Sub Category Image') }}
          </label>
          <input type="file" name="image" id="image"
                 class="form-control @error('image') is-invalid @enderror"
                 accept="image/*">
          @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Buttons --}}
        <button type="submit" class="btn btn-primary">{{ $btnLabel }}</button>
        @if($isEdit)
          <a href="{{ route('panel.subcategory.create') }}" class="btn btn-secondary">
            {{ __('Cancel') }}
          </a>
        @endif
      </form>
    </div>
  </div>

  {{-- ── Table of Sub-Categories ─────────────────── --}}
  <div class="card">
    <div class="card-header">
      <h3>{{ __('All Sub Categories') }}</h3>
    </div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Sub Category') }}</th>
            <th>{{ __('Image') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subcategories as $sub)
            <tr>
              <td>{{ $sub->category->name }}</td>
              <td>{{ $sub->name }}</td>
              <td>
                @if($sub->image)
                  <img src="{{ asset($sub->image) }}"
                       style="height:50px; object-fit:cover;">
                @else
                  —
                @endif
              </td>
              <td>
                <a href="{{ route('panel.subcategory.edit', $sub) }}"
                   class="btn btn-sm btn-success">{{ __('Edit') }}</a>
                <form action="{{ route('panel.subcategory.destroy', $sub) }}"
                      method="POST" class="d-inline"
                      onsubmit="return confirm('{{ __('Are you sure?') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-3">
                {{ __('No sub-categories found.') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
