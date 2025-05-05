@extends('template')

@section('contents')

<!-- Hidden fields for chart data -->
<input type="hidden" id="login-stats-today" value="{{ $loginStats['today'] }}">
<input type="hidden" id="login-stats-week" value="{{ $loginStats['week'] }}">
<input type="hidden" id="login-stats-month" value="{{ $loginStats['month'] }}">
<input type="hidden" id="reminders-by-month" value="{{ json_encode($remindersByMonth ?? []) }}">
<input type="hidden" id="notes-by-month" value="{{ json_encode($notesByMonth ?? []) }}">

<!-- Summary Statistics Cards -->
<div class="row">
  <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-9">
            <div class="d-flex align-items-center align-self-start">
              <h3 class="mb-0">{{ $totalCompanies }}</h3>
            </div>
          </div>
          <div class="col-3">
            <div class="icon icon-box-success">
              <span class="mdi mdi-office-building"></span>
            </div>
          </div>
        </div>
        <h6 class="text-muted font-weight-normal">Total Companies</h6>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-9">
            <div class="d-flex align-items-center align-self-start">
              <h3 class="mb-0">{{ $totalNotes }}</h3>
            </div>
          </div>
          <div class="col-3">
            <div class="icon icon-box-primary">
              <span class="mdi mdi-note-text"></span>
            </div>
          </div>
        </div>
        <h6 class="text-muted font-weight-normal">Total Notes</h6>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-9">
            <div class="d-flex align-items-center align-self-start">
              <h3 class="mb-0">{{ $totalReminders }}</h3>
              @if($overdueReminders > 0)
                <p class="text-danger ms-2 mb-0 font-weight-medium">{{ $overdueReminders }} Overdue</p>
              @endif
            </div>
          </div>
          <div class="col-3">
            <div class="icon icon-box-warning">
              <span class="mdi mdi-bell-ring"></span>
            </div>
          </div>
        </div>
        <h6 class="text-muted font-weight-normal">Total Reminders</h6>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-9">
            <div class="d-flex align-items-center align-self-start">
              <h3 class="mb-0">{{ $totalUsers }}</h3>
            </div>
          </div>
          <div class="col-3">
            <div class="icon icon-box-info">
              <span class="mdi mdi-account-multiple"></span>
            </div>
          </div>
        </div>
        <h6 class="text-muted font-weight-normal">Total Users</h6>
      </div>
    </div>
  </div>
</div>

<!-- Roles & Permissions Summary -->
<div class="row">
  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h4 class="card-title">Role & Permission Stats</h4>
          <div>
            <a href="{{ route('roles.create') }}" class="btn btn-outline-success btn-sm me-2">
              <i class="mdi mdi-plus"></i> Create Role
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-info btn-sm">
              <i class="mdi mdi-shield-account"></i> Manage Roles
            </a>
          </div>
        </div>
        <div class="d-flex flex-wrap mb-4">
          <div class="me-5 mt-3">
            <p class="text-muted">Total Roles</p>
            <h3 class="text-primary fs-30 font-weight-medium">{{ $totalRoles }}</h3>
          </div>
          <div class="mt-3">
            <p class="text-muted">Total Permissions</p>
            <h3 class="text-primary fs-30 font-weight-medium">{{ $totalPermissions }}</h3>
          </div>
        </div>
        <div class="mt-4" style="height: 225px; overflow-y: auto;">
          <h5 class="mb-3">Most Used Roles</h5>
          @foreach($roles as $role)
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
              <p class="mb-0 text-capitalize">{{ $role->name }}</p>
              <span class="badge bg-primary text-white">{{ $role->registers_count }} users</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Login Statistics</h4>
        <div style="height: 180px;">
          <canvas id="login-stats-chart"></canvas>
        </div>
        <div class="mt-3 d-flex justify-content-between">
          <div class="text-center p-2 bg-gray-dark rounded">
            <h6 class="mb-1">Today</h6>
            <p class="mb-0 font-weight-bold">{{ $loginStats['today'] }}</p>
          </div>
          <div class="text-center p-2 bg-gray-dark rounded">
            <h6 class="mb-1">Weekly</h6>
            <p class="mb-0 font-weight-bold">{{ $loginStats['week'] }}</p>
          </div>
          <div class="text-center p-2 bg-gray-dark rounded">
            <h6 class="mb-1">Monthly</h6>
            <p class="mb-0 font-weight-bold">{{ $loginStats['month'] }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Activity Summary</h4>
        <div style="height: 278px;">
          <canvas id="activity-chart"></canvas>
        </div>
        <div class="text-center mt-3">
          <small class="text-muted">Activity data for {{ date('Y') }}</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Companies & Upcoming Reminders -->
<div class="row">
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="card-title mb-0">Recent Companies</h4>
          <a href="{{ route('companies.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="mdi mdi-eye"></i> View All
          </a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentCompanies as $company)
                <tr>
                  <td>
                    <a href="{{ route('companies.show', $company) }}" class="text-decoration-none">
                      {{ $company->name }}
                    </a>
                  </td>
                  <td>{{ $company->contact }}</td>
                  <td>{{ $company->created_at->diffForHumans() }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center">No companies found</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="card-title mb-0">Upcoming Reminders</h4>
          <a href="{{ route('reminders.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="mdi mdi-bell-ring"></i> View All
          </a>
        </div>
        
        @if(isset($upcomingReminders) && count($upcomingReminders) > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Reminder</th>
                  <th>Company</th>
                  <th>Due</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($upcomingReminders as $reminder)
                  <tr class="{{ $reminder->reminder_date->isPast() && !$reminder->is_completed ? 'bg-danger bg-opacity-10' : '' }}">
                    <td>
                      <span class="fw-bold">{{ $reminder->title }}</span>
                      <p class="text-muted mb-0 small">{{ Str::limit($reminder->description, 30) }}</p>
                    </td>
                    <td>{{ $reminder->company->name ?? 'N/A' }}</td>
                    <td>{{ $reminder->reminder_date->format('M d, Y') }}</td>
                    <td>
                      @if($reminder->is_completed)
                        <span class="badge badge-success">Completed</span>
                      @elseif($reminder->reminder_date->isPast())
                        <span class="badge badge-danger">Overdue</span>
                      @else
                        <span class="badge badge-warning">Pending</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-3">
            <p>No upcoming reminders found.</p>
            <a href="{{ route('reminders.create') }}" class="btn btn-primary btn-sm">
              <i class="mdi mdi-plus"></i> Create Reminder
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Recent Notes & Users -->
<div class="row">
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="card-title mb-0">Recent Notes</h4>
          <a href="{{ route('notes.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="mdi mdi-note-text"></i> View All
          </a>
        </div>
        
        @if(isset($recentNotes) && count($recentNotes) > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Company</th>
                  <th>Created By</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentNotes as $note)
                  <tr>
                    <td>{{ $note->company->name }}</td>
                    <td>{{ $note->user->name ?? 'System' }}</td>
                    <td>{{ $note->created_at->diffForHumans() }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-3">
            <p>No notes found.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
  
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="card-title mb-0">Recent Users</h4>
          <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="mdi mdi-account-multiple"></i> View All
          </a>
        </div>
        
        @if(isset($recentUsers) && count($recentUsers) > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentUsers as $user)
                  <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->diffForHumans() }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-3">
            <p>No users found.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  $(document).ready(function() {
    // Get data from hidden fields
    var loginStatsToday = parseInt(document.getElementById('login-stats-today').value);
    var loginStatsWeek = parseInt(document.getElementById('login-stats-week').value);
    var loginStatsMonth = parseInt(document.getElementById('login-stats-month').value);
    
    var remindersByMonthData = JSON.parse(document.getElementById('reminders-by-month').value);
    var notesByMonthData = JSON.parse(document.getElementById('notes-by-month').value);
    
    // Login Stats Chart
    var loginStatsCtx = document.getElementById('login-stats-chart').getContext('2d');
    
    var loginStatsChart = new Chart(loginStatsCtx, {
      type: 'pie',
      data: {
        labels: ['Today', 'This Week', 'This Month'],
        datasets: [{
          data: [loginStatsToday, loginStatsWeek, loginStatsMonth],
          backgroundColor: ['#007bff', '#6f42c1', '#17a2b8'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        layout: {
          padding: {
            left: 10,
            right: 10,
            top: 0,
            bottom: 0
          }
        },
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#ccc',
              font: {
                size: 10
              },
              padding: 5
            }
          }
        }
      }
    });

    // Activity Chart - Reminders vs Notes by Month
    var activityCtx = document.getElementById('activity-chart').getContext('2d');
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    var reminderData = [];
    var noteData = [];
    
    // Fill data arrays for all 12 months
    for (var i = 1; i <= 12; i++) {
      reminderData.push(remindersByMonthData[i] || 0);
      noteData.push(notesByMonthData[i] || 0);
    }
    
    var activityChart = new Chart(activityCtx, {
      type: 'line',
      data: {
        labels: months,
        datasets: [
          {
            label: 'Reminders',
            data: reminderData,
            backgroundColor: 'rgba(255, 193, 7, 0.2)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1,
            tension: 0.4
          },
          {
            label: 'Notes',
            data: noteData,
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 1,
            tension: 0.4
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        layout: {
          padding: {
            left: 10,
            right: 20,
            top: 10,
            bottom: 10
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            },
            ticks: {
              color: '#ccc',
              font: {
                size: 10
              }
            }
          },
          x: {
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            },
            ticks: {
              color: '#ccc',
              font: {
                size: 10
              }
            }
          }
        },
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#ccc',
              font: {
                size: 10
              },
              padding: 5
            }
          }
        }
      }
    });
  });
</script>
@endsection