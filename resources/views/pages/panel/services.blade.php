@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Edit / Add Services</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('panel.vendor.services.update') }}">
        @csrf

        @error('subcategories')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        @error('subcategories.*')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <div class="accordion" id="serviceCategories">
            @forelse($categories as $catIndex => $category)
                <div class="card mb-2">
                    <div class="card-header d-flex align-items-center justify-content-between" id="heading-{{ $catIndex }}">
                        <h5 class="mb-0">{{ $category->name }}</h5>
                        <div>
                            <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                onclick="toggleCategory('{{ $catIndex }}', true)">
                                Select All
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onclick="toggleCategory('{{ $catIndex }}', false)">
                                Clear
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="category-body-{{ $catIndex }}">
                        @if($category->subCategories->isEmpty())
                            <p class="text-muted mb-0">No sub categories.</p>
                        @else
                            <div class="row">
                                @foreach($category->subCategories as $sub)
                                    @php
                                        $isChecked = in_array((string)$sub->id, $selected, true);
                                    @endphp
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input cat-{{ $catIndex }}"
                                                   type="checkbox"
                                                   id="sub-{{ $sub->id }}"
                                                   name="subcategories[]"
                                                   value="{{ $sub->id }}"
                                                   @checked($isChecked)>
                                            <label class="form-check-label" for="sub-{{ $sub->id }}">
                                                {{ $sub->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p>No categories found.</p>
            @endforelse
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Set</button>
        </div>
    </form>
</div>

<script>
function toggleCategory(catIndex, check) {
    document.querySelectorAll('.cat-' + catIndex).forEach(cb => {
        cb.checked = !!check;
    });
}
</script>
@endsection
