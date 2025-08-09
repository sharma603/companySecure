@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <h4 class="card-title mb-0">Sub-Users</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="input-group input-group-sm" style="width: 260px;">
                          <span class="input-group-text bg-transparent text-muted"><i class="mdi mdi-magnify"></i></span>
                          <input type="text" id="userSearch" class="form-control" placeholder="Search name or email...">
                        </div>
                        <select class="form-select form-select-sm" id="roleFilter" style="width: 160px;">
                          <option value="">All roles</option>
                          <option value="admin">Admin</option>
                          <option value="user">User</option>
                        </select>
                        @if(!empty($isAdmin) && $isAdmin)
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="mdi mdi-shield-account"></i>
                                <span class="d-none d-sm-inline">Manage Roles</span>
                            </a>
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-account-plus"></i>
                                <span class="d-none d-sm-inline">Add User</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="usersTable">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:36%">User</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                      <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-weight:600;">
                                        {{ strtoupper(substr($user->name,0,1)) }}
                                      </div>
                                      <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="text-muted small">#{{ $user->id }}</div>
                                      </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @forelse($user->roles as $role)
                                        <span class="badge {{ $role->name === 'admin' ? 'bg-danger' : 'bg-info' }} text-uppercase">{{ $role->name }}</span>
                                    @empty
                                        <span class="badge bg-secondary">none</span>
                                    @endforelse
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" title="View details" data-bs-toggle="modal" data-bs-target="#userModal-{{ $user->id }}">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    @if(!empty($isAdmin) && $isAdmin)
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($users as $user)
<!-- User Details Modal -->
<div class="modal fade" id="userModal-{{ $user->id }}" tabindex="-1" aria-labelledby="userModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel-{{ $user->id }}">User Details - {{ $user->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6>Basic Info</h6>
            <table class="table table-sm">
              <tr><th>Name</th><td>{{ $user->name }}</td></tr>
              <tr><th>Email</th><td>{{ $user->email }}</td></tr>
              <tr><th>Created</th><td>{{ $user->created_at->format('M d, Y') }}</td></tr>
            </table>
            <h6 class="mt-3">Roles</h6>
            @forelse($user->roles as $role)
              <span class="badge bg-info me-1">{{ $role->name }}</span>
            @empty
              <p class="text-muted mb-0">No roles assigned</p>
            @endforelse
          </div>
          <div class="col-md-6">
            <h6>Permissions</h6>
            <div class="row g-2">
              @php
                $permViaRoles = collect();
                foreach ($user->roles as $r) { $permViaRoles = $permViaRoles->merge($r->permissions); }
                $permViaRoles = $permViaRoles->unique('id');
                $directPerms = (\Illuminate\Support\Facades\Schema::hasTable('permission_user') && method_exists($user, 'directPermissions'))
                    ? $user->directPermissions
                    : collect();
                $allPerms = $permViaRoles->merge($directPerms)->unique('id');
              @endphp
              @forelse($allPerms as $perm)
                <div class="col-md-6">
                  <div class="border rounded p-2 d-flex align-items-center">
                    <i class="mdi mdi-check-circle text-success me-2"></i>
                    <span>{{ $perm->name }}</span>
                  </div>
                </div>
              @empty
                <p class="text-muted">No permissions</p>
              @endforelse
            </div>
            @if(!empty($isAdmin) && $isAdmin && isset($allPermissions))
            <hr/>
            <h6>Set Direct Permissions</h6>
            <form class="d-block" method="POST" action="{{ route('users.permissions.update', $user) }}" onsubmit="event.preventDefault(); submitPerms{{ $user->id }}(this)">
              @csrf
              <div class="row">
                @foreach($allPermissions as $perm)
                  <div class="col-md-6 mb-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="perm-{{ $user->id }}-{{ $perm->id }}" name="permission_ids[]" value="{{ $perm->id }}"
                        {{ (isset($directPerms) && $directPerms->contains('id', $perm->id)) ? 'checked' : '' }}>
                      <label class="form-check-label" for="perm-{{ $user->id }}-{{ $perm->id }}">{{ $perm->name }}</label>
                    </div>
                  </div>
                @endforeach
              </div>
              <button type="submit" class="btn btn-sm btn-success mt-2">Save Permissions</button>
              <span class="text-muted small ms-2" id="perm-status-{{ $user->id }}"></span>
            </form>
            <script>
              function submitPerms{{ $user->id }}(form){
                const url = form.action;
                const formData = new FormData(form);
                fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, body: formData})
                  .then(r=>r.json()).then(data=>{
                    document.getElementById('perm-status-{{ $user->id }}').textContent = data.success ? 'Saved' : (data.message||'Failed');
                  }).catch(()=>{
                    document.getElementById('perm-status-{{ $user->id }}').textContent = 'Failed';
                  });
              }
            </script>
            @endif
          </div>
        </div>

        @if(!empty($isAdmin) && $isAdmin)
        <hr/>
        <h6>Access Control</h6>
        <div class="alert alert-secondary py-2">
          <small>Use the actions below to grant/remove access for this user.</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-pencil"></i> Edit Roles
          </a>
          <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
              <i class="mdi mdi-delete"></i> Delete User
            </button>
          </form>
        </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach

@section('script')
<script>
  (function(){
    var search = document.getElementById('userSearch');
    var filter = document.getElementById('roleFilter');
    var table = document.getElementById('usersTable');
    function normalize(s){ return (s || '').toLowerCase(); }
    function text(el){ return el ? (el.textContent || '') : ''; }
    function apply(){
      var q = normalize(search && search.value);
      var role = normalize(filter && filter.value);
      var rows = table ? table.querySelectorAll('tbody tr') : [];
      for (var i = 0; i < rows.length; i++){
        var row = rows[i];
        var nameEl = row.querySelector('td:nth-child(1) .fw-semibold');
        var emailEl = row.querySelector('td:nth-child(2)');
        var rolesCell = row.querySelector('td:nth-child(3)');
        var name = normalize(text(nameEl));
        var email = normalize(text(emailEl));
        var rolesText = normalize(text(rolesCell));
        var matchText = !q || (name && name.indexOf(q) !== -1) || (email && email.indexOf(q) !== -1);
        var matchRole = !role || (rolesText.indexOf(role) !== -1);
        row.style.display = (matchText && matchRole) ? '' : 'none';
      }
    }
    if (search) { search.addEventListener('input', apply); }
    if (filter) { filter.addEventListener('change', apply); }
  })();
</script>
@endsection