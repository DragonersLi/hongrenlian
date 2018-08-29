<header class="main-header">

  <!-- Logo -->
  <a href="/" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>后台</b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b>红人链</b>后台</span>
  </a>

  <!-- Header Navbar -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <!-- Navbar Right Menu -->
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">


        <!-- User Account Menu -->
        <li class="dropdown user user-menu">
          <!-- Menu Toggle Button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <img src="/dist/img/default.jpg" class="img-circle" alt="User Image" style="width:40px">

            <span class="hidden-xs">{{auth('admin')->user()->name}}</span>
          </a>




          <ul class="dropdown-menu">
            <!-- The user image in the menu -->
            <li class="user-header">
              <img src="/dist/img/default.jpg" class="img-circle" alt="User Image">
              <p>
                {{auth('admin')->user()->name}} - 系统管理员
                <small>最后登录:{{date('Y-m-d H:i',strtotime(auth('admin')->user()->updated_at))}}</small>
              </p>
            </li>

            </li>
            <!-- Menu Footer-->
            <li class="user-footer">

              <div class="pull-left">
                <a href="{{url('/admin/resetPwd')}}" class="btn btn-default btn-flat">重置密码</a>
              </div>
              <div class="pull-right">
                <a href="{{url('/admin/logout')}}" class="btn btn-default btn-flat">退出登陆</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>