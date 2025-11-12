@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Panel General</h1>
@stop

@section('content')

<!-- 🧩 FILA 1: Métricas globales -->
<div class="row">
    <div class="col-md-3 col-6">
        <x-adminlte-small-box title="{{ $totalClientes }}" text="Clientes" icon="fas fa-users" theme="info" url="clientes" url-text="Ver más"/>
    </div>
    <div class="col-md-3 col-6">
        <x-adminlte-small-box title="{{ $totalPedidos }}" text="Pedidos" icon="fas fa-box" theme="success" url="pedidos" url-text="Ver más"/>
    </div>
    <div class="col-md-3 col-6">
        <x-adminlte-small-box title="{{ $promedioValoracion }}" text="Promedio Valoraciones" icon="fas fa-star" theme="warning" url="valoraciones" url-text="Ver más"/>
    </div>
    <div class="col-md-3 col-6">
        <x-adminlte-small-box title="{{ $totalNotificaciones }}" text="Notificaciones" icon="fas fa-bell" theme="danger" url="notificaciones" url-text="Ver más"/>
    </div>
</div>

<!-- 📦 FILA 2: Porcentajes de pedidos -->
<div class="row">
    <div class="col-md-4 col-12">
        <x-adminlte-small-box title="{{ $porcentajePendientes }}%" text="Pedidos Pendientes" icon="fas fa-hourglass-half" theme="warning"/>
    </div>
    <div class="col-md-4 col-12">
        <x-adminlte-small-box title="{{ $porcentajeProgreso }}%" text="Pedidos en Progreso" icon="fas fa-spinner" theme="info"/>
    </div>
    <div class="col-md-4 col-12">
        <x-adminlte-small-box title="{{ $porcentajeCompletados }}%" text="Pedidos Completados" icon="fas fa-check-circle" theme="success"/>
    </div>
</div>

<!-- 📊 FILA 3: Gráficos -->
<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-primary h-100 text-center" style="min-height: 240px;">
            <div class="card-header py-2">
                <h3 class="card-title"><i class="fas fa-chart-pie"></i> Pedidos por Estado</h3>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="padding: 0.25rem;">
                <canvas id="pedidosChart" style="max-height: 160px; width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-warning h-100 text-center" style="min-height: 240px;">
            <div class="card-header py-2">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Valoraciones por Puntuación</h3>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="padding: 0.25rem;">
                <canvas id="valoracionesChart" style="max-height: 160px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- 🧾 FILA 4: Actividad reciente -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-box"></i> Últimos Pedidos</h3></div>
            <div class="card-body p-2" style="max-height:240px; overflow-y:auto;">
                <table class="table table-sm table-striped text-sm">
                    <thead><tr><th>ID</th><th>Cliente</th><th>Estado</th><th>Entrega</th></tr></thead>
                    <tbody>
                        @foreach($ultimosPedidos as $p)
                        <tr>
                            <td>{{ $p['id_pedido'] }}</td>
                            <td>{{ $p['cliente_nombre'] ?? '-' }}</td>
                            <td><span class="badge
                                @if($p['estado']=='Pendiente') bg-warning
                                @elseif($p['estado']=='En Progreso') bg-info
                                @else bg-success @endif">
                                {{ $p['estado'] }}
                            </span></td>
                            <td>{{ $p['fecha_entrega'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-clipboard-list"></i> Últimas Bitácoras</h3></div>
            <div class="card-body p-2" style="max-height:240px; overflow-y:auto;">
                <table class="table table-sm table-striped text-sm">
                    <thead><tr><th>Módulo</th><th>Acción</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @foreach($ultimasBitacora as $b)
                        <tr>
                            <td>{{ $b['modulo'] ?? '' }}</td>
                            <td>{{ Str::limit($b['accion'] ?? '', 35) }}</td>
                            <td>{{ $b['fecha'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 💬 FILA 5: Notificaciones + Chat -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card card-outline card-danger">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-bell"></i> Últimas Notificaciones</h3></div>
            <div class="card-body p-2" style="max-height:220px; overflow-y:auto;">
                <ul class="list-group small">
                    @forelse($ultimasNotificaciones as $n)
                    <li class="list-group-item">
                        <i class="fas fa-bell text-danger"></i> {{ $n['mensaje'] ?? 'Nueva notificación' }}
                        <br><small class="text-muted">{{ $n['fecha'] ?? '' }}</small>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">Sin notificaciones recientes</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header py-2">
                <h3 class="card-title"><i class="fas fa-comments"></i> Chat Interno (Empleados)</h3>
            </div>
            <div class="card-body p-2" id="chat-body" style="height:220px; overflow-y:auto;">
                <div id="messages"></div>
            </div>
            <div class="card-footer p-2">
                <div class="input-group input-group-sm">
                    <input type="text" id="chat-input" class="form-control" placeholder="Escribe un mensaje...">
                    <div class="input-group-append">
                        <button id="send-btn" class="btn btn-primary btn-sm">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* 🔹 Ajustes de estilo para un dashboard equilibrado */
.card {
    border-radius: 0.5rem;
}
.card-body canvas {
    max-height: 160px !important;
}
.small-box {
    margin-bottom: 1rem !important;
}
.table-sm th, .table-sm td {
    padding: 0.35rem !important;
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 🔸 Gráfico pastel reducido
const pedidosData = @json($pedidosPorEstado);
new Chart(document.getElementById('pedidosChart'), {
    type: 'doughnut',
    data: { 
        labels: Object.keys(pedidosData), 
        datasets: [{ 
            data: Object.values(pedidosData), 
            backgroundColor: ['#ffc107','#17a2b8','#28a745','#6c757d'] 
        }] 
    },
    options: { 
        responsive: true, 
        plugins: { legend: { position: 'bottom' } },
        cutout: '75%'
    }
});

// 🔸 Histograma valoraciones
const valoracionesData = @json($valoracionesPorPuntuacion);
new Chart(document.getElementById('valoracionesChart'), {
    type: 'bar',
    data: { 
        labels: Object.keys(valoracionesData),
        datasets: [{ 
            label: 'Cantidad', 
            data: Object.values(valoracionesData), 
            backgroundColor: '#f0ad4e' 
        }]
    },
    options: { 
        responsive: true, 
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// 🔸 Chat local
const chatBody = document.getElementById('chat-body');
const chatInput = document.getElementById('chat-input');
const messages = document.getElementById('messages');
document.getElementById('send-btn').addEventListener('click', () => {
    const text = chatInput.value.trim();
    if (!text) return;
    const msg = document.createElement('div');
    msg.className = 'alert alert-primary py-1 mb-2';
    msg.textContent = text;
    messages.appendChild(msg);
    chatInput.value = '';
    chatBody.scrollTop = chatBody.scrollHeight;
});
</script>
@stop
