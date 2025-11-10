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

                @foreach(array_slice($navbar_notificaciones ?? [], 0, 5) as $noti)
                    <div class="dropdown-divider"></div>
                    <a href="{{ url('notificaciones') }}" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i>
                        {{ $noti['mensaje'] ?? 'Nueva notificación' }}
                        <span class="float-right text-muted text-sm">
                            {{ isset($noti['fecha']) ? \Carbon\Carbon::parse($noti['fecha'])->diffForHumans() : '' }}
                        </span>
                    </a>
                @endforeach

                <div class="dropdown-divider"></div>
                <a href="{{ url('notificaciones') }}" class="dropdown-item dropdown-footer">
                    Ver todas
                </a>
            </div>
        </li>

        <!-- 📅 CALENDARIO: Próximas entregas -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Próximas entregas">
                <i class="fas fa-calendar-alt"></i>
                @php $countCalendar = count($navbar_entregas ?? []); @endphp
                @if($countCalendar > 0)
                    <span class="badge badge-info navbar-badge">{{ $countCalendar }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" style="min-width: 320px;">
                <div class="dropdown-header bg-light border-bottom">
                    <strong>{{ $countCalendar }}</strong> próximas entregas
                </div>

                @php
                    use Carbon\Carbon;
                    $hoy = Carbon::today();
                @endphp

                @forelse($navbar_entregas ?? [] as $it)
                    @php
                        $fecha = !empty($it['fecha_entrega']) ? Carbon::parse($it['fecha_entrega']) : null;
                        $isToday = $fecha && $fecha->isSameDay($hoy);
                        $badgeClass = 'badge-success'; // futuro
                        if ($isToday) $badgeClass = 'badge-warning'; // hoy
                    @endphp

                    <a href="{{ route('pedidos.show', ['id' => $it['id_pedido'] ?? 0]) }}" class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <i class="far fa-calendar-check mr-2 text-primary"></i>
                            <div class="flex-fill">
                                <div class="d-flex justify-content-between">
                                    <strong>#{{ $it['id_pedido'] ?? '' }}</strong>
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $fecha ? $fecha->format('d/m/Y') : '-' }}
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    {{ $it['descripcion'] ?? 'Pedido' }}
                                    @if(!empty($it['cliente_nombre']))
                                        — {{ $it['cliente_nombre'] }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider m-0"></div>
                @empty
                    <div class="dropdown-item text-center text-muted small py-3">
                        No hay entregas próximas
                    </div>
                    <div class="dropdown-divider m-0"></div>
                @endforelse

                <a href="{{ route('pedidos.index') }}" class="dropdown-item dropdown-footer text-center">
                    Ver todos los pedidos
                </a>
            </div>
        </li>

        <!-- Usuario (Firebase) dropdown -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-user-circle"></i>
                <span class="d-none d-md-inline">
                    {{ session('firebase_user.displayName') ?? session('firebase_user.email') ?? 'Usuario' }}
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- Encabezado -->
                <li class="user-header bg-primary text-center">
                    <i class="fas fa-user-circle fa-3x mb-2"></i>
                    <p>
                        {{ session('firebase_user.displayName') ?? session('firebase_user.email') ?? 'Usuario' }}
                        <small>Sesión activa</small>
                    </p>
                </li>

                <!-- Cuerpo -->
                <li class="user-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <a href="#" class="btn btn-default btn-flat disabled">Perfil</a>
                        </div>
                    </div>
                </li>

                <!-- Pie -->
                <li class="user-footer">
                    <a href="{{ route('logout') }}" class="btn btn-danger btn-block">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
