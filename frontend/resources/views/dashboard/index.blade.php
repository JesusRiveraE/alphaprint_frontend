@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Panel General</h1>
@stop

@section('content')

<!-- 📊 Tarjetas de estadísticas -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalClientes }}</h3>
                <p>Clientes Registrados</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ url('clientes') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalPedidos }}</h3>
                <p>Pedidos Totales</p>
            </div>
            <div class="icon"><i class="fas fa-box"></i></div>
            <a href="{{ url('pedidos') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner text-white">
                <h3>{{ $promedioValoracion }}</h3>
                <p>Promedio Valoraciones</p>
            </div>
            <div class="icon"><i class="fas fa-star"></i></div>
            <a href="{{ url('valoraciones') }}" class="small-box-footer text-white">Ver más <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalNotificaciones }}</h3>
                <p>Notificaciones</p>
            </div>
            <div class="icon"><i class="fas fa-bell"></i></div>
            <a href="{{ url('notificaciones') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- 📈 Gráficos más compactos -->
<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header py-2">
                <h3 class="card-title">Pedidos por Estado</h3>
            </div>
            <div class="card-body p-3">
                <canvas id="pedidosChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-info">
            <div class="card-header py-2">
                <h3 class="card-title">Empleados por Área</h3>
            </div>
            <div class="card-body p-3">
                <canvas id="empleadosChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-success">
            <div class="card-header py-2">
                <h3 class="card-title">Resumen General</h3>
            </div>
            <div class="card-body p-3">
                <ul class="list-group text-sm">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Clientes <span class="badge bg-info">{{ $totalClientes }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Pedidos <span class="badge bg-success">{{ $totalPedidos }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Valoraciones <span class="badge bg-warning text-dark">{{ $totalValoraciones }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Notificaciones <span class="badge bg-danger">{{ $totalNotificaciones }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 🧾 Actividad reciente -->
<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header py-2"><h3 class="card-title">Últimos Pedidos</h3></div>
            <div class="card-body p-2">
                <table class="table table-sm table-striped">
                    <thead><tr><th>ID</th><th>Cliente</th><th>Estado</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @foreach($ultimosPedidos as $p)
                        <tr>
                            <td>{{ $p['id_pedido'] ?? '' }}</td>
                            <td>{{ $p['cliente_nombre'] ?? '' }}</td>
                            <td><span class="badge 
                                @if($p['estado'] == 'Pendiente') bg-warning 
                                @elseif($p['estado'] == 'En Progreso') bg-info 
                                @else bg-success @endif">
                                {{ $p['estado'] ?? '' }}</span></td>
                            <td>{{ $p['fecha_creacion'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header py-2"><h3 class="card-title">Últimas Valoraciones</h3></div>
            <div class="card-body p-2">
                <table class="table table-sm table-striped">
                    <thead><tr><th>ID</th><th>Puntuación</th><th>Comentario</th></tr></thead>
                    <tbody>
                        @foreach($ultimasValoraciones as $v)
                        <tr>
                            <td>{{ $v['id_valoracion'] ?? '' }}</td>
                            <td>
                                @for($i=1;$i<=5;$i++)
                                    <i class="fas fa-star {{ $i <= ($v['puntuacion'] ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </td>
                            <td>{{ Str::limit($v['comentario'] ?? '', 40) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 💬 Chat + Notificaciones -->
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header py-2">
                <h3 class="card-title"><i class="fas fa-comments"></i> Chat Interno</h3>
            </div>
            <div class="card-body p-2" id="chat-body" style="height: 200px; overflow-y: scroll;">
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

    <!-- 📅 Notificaciones -->
    <div class="col-md-4">
        <div class="card card-outline card-danger">
            <div class="card-header py-2"><h3 class="card-title">Últimas Notificaciones</h3></div>
            <div class="card-body p-2">
                <ul class="list-group list-group-flush small">
                    @foreach(array_slice($notificaciones ?? [], 0, 5) as $n)
                        <li class="list-group-item">
                            <i class="fas fa-bell text-danger"></i> {{ $n['mensaje'] ?? 'Nueva notificación' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const pedidosData = @json($pedidosPorEstado);
new Chart(document.getElementById('pedidosChart'), {
    type: 'doughnut',
    data: { 
        labels: Object.keys(pedidosData), 
        datasets: [{ data: Object.values(pedidosData), backgroundColor: ['#17a2b8','#ffc107','#28a745','#dc3545','#6c757d'] }] 
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

const empleadosData = @json($empleadosPorArea);
new Chart(document.getElementById('empleadosChart'), {
    type: 'bar',
    data: { 
        labels: Object.keys(empleadosData), 
        datasets: [{ data: Object.values(empleadosData), backgroundColor: '#007bff' }] 
    },
    options: { 
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Chat básico (local)
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
