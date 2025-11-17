<nav class="main-header navbar navbar-expand nav-elevated">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link nav-toggle" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Botón de pantalla completa -->
        <li class="nav-item">
            <a class="nav-link nav-icon" data-widget="fullscreen" href="#" role="button" title="Pantalla completa">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <!-- 🔔 Notificaciones -->
        <li class="nav-item dropdown">
            <a class="nav-link nav-icon" data-toggle="dropdown" href="#" title="Notificaciones">
                <i class="fas fa-bell"></i>

                @php $pendientes = $navbar_notificaciones_badge ?? 0; @endphp
                @if($pendientes > 0)
                    <span class="badge badge-danger navbar-badge navbar-badge-noti">
                        {{ $pendientes }}
                    </span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right dropdown-menu-notifications">
                <span class="dropdown-item dropdown-header">
                    {{ $pendientes }} Notificación{{ $pendientes !== 1 ? 'es' : '' }} sin leer
                </span>

                @forelse($navbar_notificaciones ?? [] as $noti)
                    <div class="dropdown-divider m-0"></div>

                    <div class="dropdown-item dropdown-item-noti d-flex align-items-start">
                        <i class="fas fa-envelope mr-2 {{ empty($noti['leido']) ? 'text-danger' : 'text-muted' }}"></i>

                        <div class="flex-fill">
                            <div>{{ $noti['mensaje'] ?? 'Nueva notificación' }}</div>
                            <div class="small text-muted">
                                {{ isset($noti['fecha']) ? \Carbon\Carbon::parse($noti['fecha'])->diffForHumans() : '' }}
                            </div>
                        </div>

                        <button
                            type="button"
                            class="btn btn-sm btn-link text-success btn-mark-read"
                            data-id="{{ $noti['id_notificacion'] ?? '' }}"
                            title="Marcar como leída">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                @empty
                    <div class="dropdown-divider m-0"></div>
                    <div class="dropdown-item text-center text-muted small py-3">
                        Sin notificaciones nuevas
                    </div>
                @endforelse

                <div class="dropdown-divider m-0"></div>
                <a href="{{ url('notificaciones') }}" class="dropdown-item dropdown-footer">
                    Ver todas las notificaciones
                </a>
            </div>
        </li>

        <!-- 📅 CALENDARIO: Próximas entregas -->
        <li class="nav-item dropdown">
            <a class="nav-link nav-icon" data-toggle="dropdown" href="#" title="Próximas entregas">
                <i class="fas fa-calendar-alt"></i>
                @php $countCalendar = count($navbar_entregas ?? []); @endphp
                @if($countCalendar > 0)
                    <span class="badge navbar-badge badge-info">{{ $countCalendar }}</span>
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
                        $fecha   = !empty($it['fecha_entrega']) ? Carbon::parse($it['fecha_entrega']) : null;
                        $isToday = $fecha && $fecha->isSameDay($hoy);
                        $badgeClass = 'badge-success';
                        if ($isToday) $badgeClass = 'badge-warning';
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

                <!-- 📦 BOTÓN ROJO – VER PEDIDOS -->
                <a href="{{ route('pedidos.index') }}" class="dropdown-item dropdown-footer text-center btn-cal rojo">
                    <i class="fas fa-box mr-2"></i> Ver todos los pedidos
                </a>

                <!-- 📅 BOTÓN ROJO – VER CALENDARIO -->
                <a href="{{ route('calendario.index') }}" class="dropdown-item dropdown-footer text-center btn-cal rojo">
                    <i class="fas fa-calendar-alt mr-2"></i> Ver Calendario
                </a>
            </div>
        </li>

        <!-- 👤 Usuario (Firebase) dropdown mejorado -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link nav-user-toggle dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-user-circle brand-text mr-1"></i>
                <span class="d-none d-md-inline nav-user-email">
                    {{ session('firebase_user.displayName') ?? session('firebase_user.email') ?? 'Usuario' }}
                </span>
            </a>

            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right user-dropdown-menu">
                <!-- Cabecera con gradiente de marca -->
                <li class="user-header user-header-brand text-center">
                    <div class="user-avatar-wrapper mb-2">
                        <i class="fas fa-user-circle fa-4x"></i>
                    </div>
                    <p class="mb-0">
                        <strong>{{ session('firebase_user.email') ?? 'Usuario' }}</strong>
                    </p>
                    <small>Sesión activa</small>
                </li>

                <!-- Botón Perfil -->
                <li class="user-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <a href="{{ route('perfil') }}"
                               class="btn btn-sm btn-brand-outline-perfil">
                                <i class="fas fa-id-badge mr-1"></i> Perfil
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Botón Cerrar sesión -->
                <li class="user-footer">
                    <a href="#"
                       id="btn-logout"
                       class="btn btn-brand-logout btn-block">
                        <i class="fas fa-sign-out-alt mr-1"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

{{-- 🎨 Estilos personalizados --}}
<style>
    :root{
        --brand:#e24e60;
        --brand-100:#fde5e9;
        --brand-600:#c23c4e;
        --ink:#2b2f33;
    }

    .nav-elevated{
        background: linear-gradient(90deg, #ffffff, #fff7f8);
        border-bottom:1px solid #e5e7eb;
        box-shadow:0 2px 8px rgba(15,23,42,0.06);
        padding-top:0;
        padding-bottom:0;
        min-height: 56px;
        display:flex;
        align-items:center;
    }
    .nav-elevated .nav-link{
        color:#6b7280;
        font-size:.95rem;
        padding:.55rem .75rem;
        transition: color .2s ease, background-color .2s ease, transform .15s ease;
    }
    .nav-elevated .nav-link .fas{
        font-size:1rem;
        color:#9ca3af;
        transition: color .2s ease, transform .15s ease;
    }
    .nav-elevated .nav-link:hover{
        color:var(--brand);
        background-color:rgba(226,78,96,0.04);
    }
    .nav-elevated .nav-link:hover .fas{
        color:var(--brand);
        transform:translateY(-1px);
    }

    .nav-toggle .fas{
        font-size:1.1rem;
    }

    .navbar-badge{
        font-size:.7rem;
        padding:.15rem .3rem;
        border-radius:999px;
        transform:translateY(-3px);
    }

    .nav-user-email{
        max-width:180px;
        display:inline-block;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .dropdown-menu-notifications {
        width: 450px !important;
        max-height: 480px !important;
        overflow-y: auto !important;
        overflow-x: hidden;
        border-radius:.75rem;
        box-shadow:0 10px 24px rgba(15,23,42,0.25);
    }

    .user-dropdown-menu{
        padding:0;
        overflow:hidden;
        border-radius:.75rem;
        box-shadow:0 10px 24px rgba(15,23,42,0.25);
    }
    .user-header-brand{
        background: linear-gradient(135deg, var(--brand), var(--brand-600));
        color:#fff;
        padding:1.25rem 1rem 1rem;
        border-bottom:1px solid rgba(255,255,255,0.22);
    }

    .btn-brand-outline-perfil{
        border:1px solid var(--brand);
        color:var(--brand);
        background:#fff;
        font-weight:600;
        border-radius:999px;
        padding:.35rem 1.2rem;
        transition:
            background-color 0.2s ease,
            color 0.2s ease,
            box-shadow 0.15s ease,
            transform 0.15s ease;
    }
    .btn-brand-outline-perfil:hover{
        background:var(--brand-100);
        color:var(--brand-600);
        box-shadow:0 4px 10px rgba(226,78,96,0.25);
        transform:translateY(-1px);
    }

    .btn-brand-logout{
        background:var(--brand);
        border-color:var(--brand);
        color:#fff;
        font-weight:600;
        border-radius:.6rem;
        padding:.45rem 1rem;
        transition: background-color .2s ease, box-shadow .15s ease, transform .15s ease;
    }
    .btn-brand-logout:hover{
        background:var(--brand-600);
        border-color:var(--brand-600);
        transform:translateY(-1px);
        box-shadow:0 4px 12px rgba(226,78,96,0.35);
    }

    .dropdown-item-noti {
        white-space: normal !important;
        overflow-wrap: break-word !important;
        word-wrap: break-word !important;
        max-width: 100% !important;
        line-height: 1.2rem;
        align-items: flex-start !important;
        padding-top: .75rem;
        padding-bottom: .75rem;
    }

    .dropdown-item-noti .flex-fill {
        white-space: normal !important;
        overflow: visible !important;
    }
</style>

<script>
/**
 * Marcar notificaciones como leídas (AJAX)
 */
document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-mark-read')) {
        const btn = e.target.closest('.btn-mark-read');
        const id  = btn.dataset.id;

        if (!id) return;

        fetch(`{{ url('/notificaciones') }}/${id}/leido`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(() => {
            const item = btn.closest('.dropdown-item-noti');
            if (item) item.remove();

            const badge = document.querySelector('.navbar-badge-noti');
            if (badge) {
                let current = parseInt(badge.textContent) || 0;
                current = Math.max(current - 1, 0);
                if (current <= 0) badge.remove();
                else badge.textContent = current;
            }
        })
        .catch(err => {
            console.error(err);
            alert('No se pudo marcar la notificación como leída.');
        });
    }
});

/**
 * Cerrar sesión: Firebase + Laravel
 */
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-logout');
    if (!btn) return;

    btn.addEventListener('click', async (e) => {
        e.preventDefault();

        try {
            if (window.firebaseSignOut) {
                await window.firebaseSignOut();
            } else {
                window.location.href = "{{ route('logout') }}";
            }
        } catch (err) {
            console.error('Error al cerrar sesión:', err);
            window.location.href = "{{ route('logout') }}";
        }
    });
});
</script>
