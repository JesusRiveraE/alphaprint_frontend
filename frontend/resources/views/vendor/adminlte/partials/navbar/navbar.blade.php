<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- Botón de pantalla completa -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <!-- Notificaciones -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-bell"></i>
                @if(count($navbar_notificaciones ?? []) > 0)
                    <span class="badge badge-warning navbar-badge">{{ count($navbar_notificaciones) }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    {{ count($navbar_notificaciones ?? []) }} Notificaciones
                </span>

                @foreach(array_slice($navbar_notificaciones, 0, 5) as $noti)
                    <div class="dropdown-divider"></div>
                    <a href="{{ url('notificaciones') }}" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 
                        {{ $noti['mensaje'] ?? 'Nueva notificación' }}
                        <span class="float-right text-muted text-sm">
                            {{ \Carbon\Carbon::parse($noti['fecha'])->diffForHumans() ?? '' }}
                        </span>
                    </a>
                @endforeach

                <div class="dropdown-divider"></div>
                <a href="{{ url('notificaciones') }}" class="dropdown-item dropdown-footer">
                    Ver todas
                </a>
            </div>
        </li>
    </ul>
</nav>
