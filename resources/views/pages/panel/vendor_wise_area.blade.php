@extends('layouts.dashboard')

@section('content')
<style>
  .table-card { border:0; border-radius:16px; box-shadow:0 10px 30px rgba(16,24,40,.06); }
  .search-input { max-width: 380px; border-radius: 12px; }
  .badge-soft { background:#eef2ff; color:#344054; }
</style>

<div class="container py-4">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h3 class="mb-1">Vendor Wise Area</h3>
      <div class="text-muted">Personal name, comma-separated PIN codes, approve to verify.</div>
    </div>
    <input id="tableSearch" class="form-control search-input" placeholder="Search vendor/pincode…">
  </div>

  <div class="card table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0" id="vwAreaTable">
        <thead class="table-light">
          <tr>
            <th>Vendor</th>
            <th>Vendor Pincodes</th>
            <th style="width:160px;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            <tr>
              <td class="fw-semibold">{{ $r['vendor'] }}</td>
              <td><span class="badge badge-soft rounded-pill">{{ $r['pincodes'] }}</span></td>
              
              <td>
                @if(!$r['verified'])
                  <form method="POST" action="{{ route('panel.vendor.area.approve', $r['id']) }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-sm btn-primary"
                            onclick="return confirm('Approve this vendor area?')">
                      Approve
                    </button>
                  </form>
                @else
                  <button class="btn btn-sm btn-outline-secondary" disabled>Approved</button>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No data found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
// simple client-side filter
const q = document.getElementById('tableSearch');
q.addEventListener('input', () => {
  const needle = q.value.toLowerCase();
  document.querySelectorAll('#vwAreaTable tbody tr').forEach(tr => {
    const text = tr.innerText.toLowerCase();
    tr.style.display = text.includes(needle) ? '' : 'none';
  });
});
</script>
@endsection
