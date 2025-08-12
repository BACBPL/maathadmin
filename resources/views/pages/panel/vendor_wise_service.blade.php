@extends('layouts.dashboard')

@section('content')
<style>
  .table-card { border:0; border-radius:16px; box-shadow:0 10px 30px rgba(16,24,40,.06); }
  .search-input { max-width: 380px; border-radius: 12px; }
  .badge-soft { background:#eef2ff; color:#344054; }
</style>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h3 class="mb-1">Vendor Wise Service</h3>
      <div class="text-muted">List of vendors with their main categories and subcategories.</div>
    </div>
    <input id="tableSearch" class="form-control search-input" placeholder="Search vendor/category/subcategory…">
  </div>

  <div class="card table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0" id="vwsTable">
        <thead class="table-light">
          <tr>
            <th>Vendor</th>
            <th>Main Categories</th>
            <th>Subcategories</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            <tr>
              <td><span class="fw-semibold">{{ $r['vendor'] ?? '—' }}</span></td>
              <td><span class="badge badge-soft rounded-pill">{{ $r['categories'] ?? '—' }}</span></td>
              <td>{{ $r['subcategories'] ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted py-4">No data found.</td></tr>
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
  document.querySelectorAll('#vwsTable tbody tr').forEach(tr => {
    const text = tr.innerText.toLowerCase();
    tr.style.display = text.includes(needle) ? '' : 'none';
  });
});
</script>
@endsection
