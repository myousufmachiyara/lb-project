<aside id="sidebar-left" class="sidebar-left">
  <div class="sidebar-header">
    <div class="sidebar-title pt-2 d-flex justify-content-between">
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
              <i class="fa fa-home"></i><span>Dashboard</span>
            </a>
          </li>

          @if(auth()->user()->can('coa.index') || auth()->user()->can('shoa.index'))
            <li class="nav-parent">
              <a class="nav-link" href="#"><i class="fa fa-book"></i><span>Accounts</span></a>
              <ul class="nav nav-children">
                @can('coa.index')
                  <li><a class="nav-link" href="{{ route('coa.index') }}">Chart of Accounts</a></li>
                @endcan
                @can('shoa.index')
                  <li><a class="nav-link" href="{{ route('shoa.index') }}">Subheads</a></li>
                @endcan
              </ul>
            </li>
          @endif

          @if(auth()->user()->can('user_roles.index') || auth()->user()->can('users.index'))
          <li class="nav-parent">
            <a class="nav-link" href="#"><i class="fa fa-user-shield"></i><span>User Management</span></a>
            <ul class="nav nav-children">
              @can('user_roles.index')
              <li><a class="nav-link" href="{{ route('roles.index') }}">Roles & Permissions</a></li>
              @endcan
              @can('users.index')
              <li><a class="nav-link" href="{{ route('users.index') }}">Users</a></li>
              @endcan
            </ul>
          </li>
          @endif

          @if(auth()->user()->can('services.index'))
          <li>
            <a class="nav-link" href="{{ route('services.index') }}">
              <i class="fa fa-object-ungroup"></i><span>Services</span>
            </a>
          </li>
          @endif

          @if(auth()->user()->can('projects.index'))
          <li>
            <a class="nav-link" href="{{ route('projects.index') }}">
              <i class="fa fa-layer-group"></i><span>Projects</span>
            </a>
          </li>
          @endif

          @if(auth()->user()->can('tasks.index') || auth()->user()->can('tasks_categories.index') )
          <li class="nav-parent">
            <a class="nav-link" href="#"><i class="fa fa-list"></i><span>Tasks</span></a>
            <ul class="nav nav-children">
              @can('tasks_categories.index')
                <li><a class="nav-link" href="{{ route('task-categories.index') }}">Categories</a></li>
              @endcan
              @can('tasks.index')
                <li><a class="nav-link" href="{{ route('tasks.index') }}">All Tasks</a></li>
              @endcan
            </ul>
          </li>
          @endif

          @if(auth()->user()->can('quotations.index'))
          <li>
            <a class="nav-link" href="{{ route('quotations.index') }}">
              <i class="fa fa-file"></i><span>Quotations</span>
            </a>
          </li>
          @endif

          @if(auth()->user()->can('purchase_vouchers.index'))
          <li>
            <a class="nav-link" href="{{ route('purchase-vouchers.index') }}">
              <i class="fa fa-file-import"></i><span>Purchase Vouchers</span>
            </a>
          </li>
          @endif

          @if(auth()->user()->can('sale_vouchers.index'))
          <li>
            <a class="nav-link" href="{{ route('sale-vouchers.index') }}">
              <i class="fa fa-file-export"></i><span>Sale Vouchers</span>
            </a>
          </li>
          @endif 
          
          @if(auth()->user()->can('gate_pass.index'))
            <li>
              <a class="nav-link" href="">
                <i class="fa fa-clipboard"></i><span>GatePass</span>
              </a>
            </li>
          @endif      

          @if(auth()->user()->can('payment_vouchers.index'))
          <li>
            <a class="nav-link" href="{{ route('payment-vouchers.index') }}">
              <i class="fa fa-money-bill"></i><span>Payment Vouchers</span>
            </a>
          </li>
          @endif

          <li class="nav-parent">
            <a class="nav-link" href="#"><i class="fa fa-sun"></i><span>Others</span></a>
            <ul class="nav nav-children">
              @can('status_management.index')
                <li><a class="nav-link" href="{{ route('status.index') }}">Status</a></li>
              @endcan
            </ul>
          </li>
        </ul>
      </nav>
    </div>

    <script>
      // Maintain Scroll Position
      if (typeof localStorage !== 'undefined') {
        const sidebar = document.querySelector('#sidebar-left .nano-content');
        const position = localStorage.getItem('sidebar-left-position');
        if (position !== null && sidebar) sidebar.scrollTop = position;
      }
    </script>
  </div>
</aside>
