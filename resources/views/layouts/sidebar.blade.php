<aside id="sidebar-left" class="sidebar-left">
  <div class="sidebar-header">
    <div class="sidebar-title pt-2" style="display: flex;justify-content: space-between;">
      <a href="/" class="logo col-11">						
        <img src="/assets/img/lb-logo.jpg" class="sidebar-logo" alt="Brand Logo" height="12%" />
      </a>
      <div class="d-md-none toggle-sidebar-left col-1" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
        <i class="fas fa-times" aria-label="Toggle sidebar"></i>
      </div>
    </div>
    <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
      <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
    </div>
  </div>

  <div class="nano">
    <div class="nano-content">
      <nav id="menu" class="nav-main" role="navigation">
        <ul class="nav nav-main">
          <li class="active">
            <a class="nav-link" href="/">
              <i class="fa fa-home" aria-hidden="true"></i><span>Dashboard</span>
            </a>    
          </li>

          @if(auth()->user()->can('modules.index') || auth()->user()->can('roles.index') || auth()->user()->can('users.index'))
          <li class="nav-parent">
            <a class="nav-link" href="#">
              <i class="fa fa-user-shield" aria-hidden="true"></i>
              <span>User Management</span>
            </a>
            <ul class="nav nav-children">
              @can('modules.index')
              <li><a class="nav-link" href="{{ route('modules.index') }}">Modules</a></li>
              @endcan

              @can('roles.index')
              <li><a class="nav-link" href="{{ route('roles.index') }}">Roles</a></li>
              @endcan

              @can('users.index')
              <li><a class="nav-link" href="{{ route('users.index') }}">Users</a></li>
              @endcan
            </ul>
          </li>
          @endif

          @if(auth()->user()->can('projects.index') || auth()->user()->can('project-status.index'))
          <li class="nav-parent">
            <a class="nav-link" href="#">
              <i class="fa fa-layer-group" style="font-size:16px" aria-hidden="true"></i>
              <span>Projects</span>
            </a>
            <ul class="nav nav-children">
              @can('project-status.index')
              <li><a class="nav-link" href="{{ route('project-status.index') }}">Status</a></li>
              @endcan

              @can('projects.index')
              <li><a class="nav-link" href="{{ route('projects.index') }}">All Projects</a></li>
              @endcan
            </ul>
          </li>
          @endif

          @if(auth()->user()->can('tasks.index') || auth()->user()->can('task-categories.index'))
          <li class="nav-parent">
            <a class="nav-link" href="#">
              <i class="fa fa-list" style="font-size:16px" aria-hidden="true"></i>
              <span>Task</span>
            </a>
            <ul class="nav nav-children">
              @can('task-categories.index')
              <li><a class="nav-link" href="{{ route('task-categories.index') }}">Categories</a></li>
              @endcan

              @can('tasks.index')
              <li><a class="nav-link" href="{{ route('tasks.index') }}">All Task</a></li>
              @endcan
            </ul>
          </li>
          @endif
        </ul>
      </nav>
		</div>

    <script>
      // Maintain Scroll Position
      if (typeof localStorage !== 'undefined') {
        if (localStorage.getItem('sidebar-left-position') !== null) {
          var initialPosition = localStorage.getItem('sidebar-left-position'),
          sidebarLeft = document.querySelector('#sidebar-left .nano-content');

          sidebarLeft.scrollTop = initialPosition;
        }
      }
    </script>
  </div>
</aside>