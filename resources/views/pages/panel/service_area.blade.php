@extends('layouts.dashboard')

@section('content')
<style>
  /* Page look & feel */
  body { background: linear-gradient(180deg,#f7f9fc, #eef3f8); }
  .page-wrap { max-width: 1100px; margin: 0 auto; }
  .card-surface { border: 0; border-radius: 18px; box-shadow: 0 10px 30px rgba(16,24,40,.06); }
  .title-row h3 { font-weight: 700; letter-spacing: .2px; }
  .muted { color:#667085; }
  /* Search */
  .search-wrap { position: relative; }
  .search-wrap .icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); }
  .search-input { padding-left: 40px; border-radius: 12px; }
  /* Pincode pill checkbox */
  .pin-pill { 
    display:flex; align-items:center; gap:.6rem;
    padding:.65rem .85rem; border-radius: 12px;
    border:1px solid #e6e9ef; background:#fff;
    transition: all .15s ease; cursor:pointer; user-select:none;
  }
  .pin-pill:hover { box-shadow: 0 6px 18px rgba(17,24,39,.06); transform: translateY(-1px); }
  .pin-pill input { width:18px; height:18px; }
  .pin-pill.active { border-color:#c8d9ff; background:#f3f7ff; }
  .pin-pill .pin { font-weight:600; letter-spacing:.6px; }
  /* Selected chips */
  .chip { display:inline-flex; align-items:center; gap:.35rem; background:#eef2ff; color:#344054;
          border-radius:999px; padding:.25rem .6rem; font-size:.85rem; font-weight:600; margin:.25rem; }
  .chip .x { cursor:pointer; font-weight:700; opacity:.7; }
  /* Sticky save bar */
  .save-bar {
    position: sticky; bottom: 0; z-index: 5; backdrop-filter: blur(6px);
    background: rgba(255,255,255,.8); border-top:1px solid #e6e9ef;
  }
</style>

<div class="page-wrap py-4">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="mb-3 title-row d-flex align-items-center justify-content-between">
    <div>
      <h3 class="mb-1">Service PIN Codes</h3>
      <div class="muted">Choose the PIN codes where you provide service.</div>
    </div>
    <div class="text-end">
      <span class="badge rounded-pill text-bg-primary p-2 me-1">
        <span class="small">Total</span> <span id="totalCount">{{ $pincodes->count() }}</span>
      </span>
      <span class="badge rounded-pill text-bg-success p-2">
        <span class="small">Selected</span> <span id="selectedCount">{{ count($selected ?? []) }}</span>
      </span>
    </div>
  </div>

  <form method="POST" action="{{ route('panel.vendor.services.area.save') }}" id="serviceAreaForm">
    @csrf

    <div class="card card-surface">
      <div class="card-body">
        {{-- Controls --}}
        <div class="row g-2 align-items-center mb-3">
          <div class="col-md-6">
            <div class="search-wrap">
              <i class="icon bi bi-search text-secondary"></i>
              <input type="text" id="searchBox" class="form-control search-input" placeholder="Search pincode…">
            </div>
          </div>
          <div class="col-md-6 text-md-end">
            <button type="button" class="btn btn-outline-secondary me-2" id="selectAllVisible">Select all (visible)</button>
            <button type="button" class="btn btn-outline-secondary" id="clearAllVisible">Clear (visible)</button>
          </div>
        </div>

        {{-- Selected chips --}}
        <div class="mb-3" id="chipsWrap">
          @foreach(($selected ?? []) as $pin)
            <span class="chip" data-pin="{{ $pin }}">{{ $pin }} <span class="x" aria-label="Remove" title="Remove" data-remove="{{ $pin }}">×</span></span>
          @endforeach
        </div>

        {{-- Errors --}}
        @error('pincodes') <div class="text-danger small">{{ $message }}</div> @enderror
        @error('pincodes.*') <div class="text-danger small">{{ $message }}</div> @enderror

        {{-- Grid --}}
        <div id="pinGrid" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-2">
          @foreach($pincodes as $pin)
            @php $checked = in_array($pin, $selected ?? [], true); @endphp
            <div class="col pin-item" data-pin="{{ $pin }}">
              <label class="pin-pill {{ $checked ? 'active' : '' }}" for="pin-{{ $pin }}">
                <input class="form-check-input pin-check me-1" type="checkbox"
                  name="pincodes[]"
                  value="{{ $pin }}"
                  id="pin-{{ $pin }}"
                  {{ $checked ? 'checked' : '' }}>
                <span class="pin">{{ $pin }}</span>
              </label>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Sticky save bar --}}
      <div class="save-bar p-3 d-flex justify-content-between align-items-center">
        <div class="muted small">
          <span id="footerSelection">{{ count($selected ?? []) }}</span> selected ·
          Use search to filter and “Select all (visible)” to bulk-select.
        </div>
        <div>
          <a href="{{ route('panel.vendor.services.area') }}" class="btn btn-light me-2">Reset</a>
          <button class="btn btn-primary">Save Service Area</button>
        </div>
      </div>
    </div>
  </form>
</div>

{{-- Bootstrap Icons if your layout doesn’t already include them --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script>
  const searchBox = document.getElementById('searchBox');
  const pinGrid = document.getElementById('pinGrid');
  const chipsWrap = document.getElementById('chipsWrap');
  const selectedCount = document.getElementById('selectedCount');
  const footerSelection = document.getElementById('footerSelection');

  function updateCountsAndChips() {
    const checks = Array.from(document.querySelectorAll('.pin-check'));
    const sel = checks.filter(c => c.checked).map(c => c.value);

    // Update chips
    const existing = new Set(Array.from(chipsWrap.querySelectorAll('.chip')).map(c => c.dataset.pin));
    // add missing
    sel.forEach(p => {
      if (!existing.has(p)) {
        const chip = document.createElement('span');
        chip.className = 'chip';
        chip.dataset.pin = p;
        chip.innerHTML = `${p} <span class="x" data-remove="${p}">×</span>`;
        chipsWrap.appendChild(chip);
      }
    });
    // remove extras
    chipsWrap.querySelectorAll('.chip').forEach(chip => {
      if (!sel.includes(chip.dataset.pin)) chip.remove();
    });

    selectedCount.textContent = sel.length;
    footerSelection.textContent = sel.length;
  }

  // Toggle pill active when checkbox changes
  pinGrid.addEventListener('change', (e) => {
    if (e.target.classList.contains('pin-check')) {
      const pill = e.target.closest('.pin-pill');
      pill.classList.toggle('active', e.target.checked);
      updateCountsAndChips();
    }
  });

  // Remove via chip
  chipsWrap.addEventListener('click', (e) => {
    if (e.target.dataset.remove) {
      const pin = e.target.dataset.remove;
      const input = document.querySelector(`.pin-check[value="${pin}"]`);
      if (input) {
        input.checked = false;
        input.closest('.pin-pill').classList.remove('active');
      }
      e.target.closest('.chip').remove();
      updateCountsAndChips();
    }
  });

  // Filter visible cards
  function filterPincodes() {
    const q = (searchBox.value || '').trim();
    document.querySelectorAll('#pinGrid .pin-item').forEach(el => {
      const pin = el.dataset.pin;
      el.style.display = pin.includes(q) ? '' : 'none';
    });
  }
  searchBox.addEventListener('input', () => {
    // light debounce
    clearTimeout(searchBox._t);
    searchBox._t = setTimeout(filterPincodes, 80);
  });

  // Bulk select/clear only visible
  document.getElementById('selectAllVisible').addEventListener('click', () => {
    document.querySelectorAll('#pinGrid .pin-item').forEach(el => {
      if (el.style.display !== 'none') {
        const cb = el.querySelector('.pin-check');
        cb.checked = true;
        el.querySelector('.pin-pill').classList.add('active');
      }
    });
    updateCountsAndChips();
  });
  document.getElementById('clearAllVisible').addEventListener('click', () => {
    document.querySelectorAll('#pinGrid .pin-item').forEach(el => {
      if (el.style.display !== 'none') {
        const cb = el.querySelector('.pin-check');
        cb.checked = false;
        el.querySelector('.pin-pill').classList.remove('active');
      }
    });
    updateCountsAndChips();
  });

  // Init counts
  updateCountsAndChips();
</script>
@endsection
