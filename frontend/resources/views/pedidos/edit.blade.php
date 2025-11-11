@extends('adminlte::page')

@section('title', 'Editar Pedido')

@section('content_header')
<h1 class="m-0" style="font-size:1.7rem;">
    <i class="fas fa-edit mr-2 brand-text"></i> Editar Pedido
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand d-flex justify-content-between align-items-center">
        <strong class="brand-text" style="font-size:1.25rem;">Actualizar Pedido #{{ $pedido['id_pedido'] }}</strong>
    </div>

    <div class="card-body">
        <form action="{{ route('pedidos.update', $pedido['id_pedido']) }}" method="POST" id="form-edit-pedido">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="id_cliente" class="brand-text font-weight-bold">Cliente</label>
                <select name="id_cliente" id="id_cliente" class="form-control" required>
                    <option value="">Seleccione un cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente['id_cliente'] }}"
                            {{ $pedido['id_cliente'] == $cliente['id_cliente'] ? 'selected' : '' }}>
                            {{ $cliente['id_cliente'] }} - {{ $cliente['nombre'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="descripcion" class="brand-text font-weight-bold">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="3">{{ $pedido['descripcion'] ?? '' }}</textarea>
            </div>

            <div class="form-group">
                <label for="total" class="brand-text font-weight-bold">Total (Lps)</label>
                <input type="number" name="total" id="total" class="form-control" step="0.01" min="0" value="{{ $pedido['total'] ?? '' }}" required>
            </div>

            <!-- 🔹 Fecha y hora de entrega (visibles) + campo oculto que se envía -->
            <div class="form-group">
                <label class="brand-text font-weight-bold">Fecha de Entrega</label>
                <div class="input-group">
                    {{-- visibles (SIN name) --}}
                    <input type="date" id="fecha_entrega_d" class="form-control"
                           value="{{ isset($pedido['fecha_entrega']) ? \Carbon\Carbon::parse($pedido['fecha_entrega'])->timezone('America/Tegucigalpa')->format('Y-m-d') : '' }}">
                    <input type="time" id="hora_entrega_d" class="form-control"
                           value="{{ isset($pedido['fecha_entrega']) ? \Carbon\Carbon::parse($pedido['fecha_entrega'])->timezone('America/Tegucigalpa')->format('H:i') : '' }}">

                    <div class="input-group-append">
                        <button type="button" id="btnHoraEntrega" class="btn btn-brand-outline">
                            <i class="fas fa-clock"></i> Actualizar hora
                        </button>
                    </div>
                </div>

                {{-- oculto: ESTE es el que viaja al backend --}}
                <input type="hidden" name="fecha_entrega" id="fecha_entrega_full"
                       value="{{ isset($pedido['fecha_entrega']) ? \Carbon\Carbon::parse($pedido['fecha_entrega'])->timezone('America/Tegucigalpa')->format('Y-m-d H:i:s') : '' }}">

                <small class="form-text text-muted">Selecciona fecha y hora; se guardará como una marca completa.</small>
            </div>

            <div class="form-group">
                <label for="estado" class="brand-text font-weight-bold">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="Pendiente" {{ $pedido['estado']=='Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="En Progreso" {{ $pedido['estado']=='En Progreso' ? 'selected' : '' }}>En Progreso</option>
                    <option value="Completado" {{ $pedido['estado']=='Completado' ? 'selected' : '' }}>Completado</option>
                </select>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-brand-outline mr-2">
                    <i class="fas fa-save mr-1"></i> Actualizar Pedido
                </button>
                <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
:root { --brand: #e24e60; --brand-100: #fde5e9; }
.brand-text { color: var(--brand); }
.card-soft { border: 1px solid #eff1f5; border-radius: .6rem; }
.card-soft:hover { box-shadow: 0 0 15px rgba(226, 78, 96, .08); }
.border-brand { border-left: 4px solid var(--brand); background: #fff; }
.btn-brand-outline { border: 1px solid var(--brand); color: var(--brand); background: #fff; }
.btn-brand-outline:hover { background: #fde5e9; color: var(--brand); }
</style>
@stop

@section('js')
<script>
function combineDateTime() {
    const f = document.getElementById('fecha_entrega_d').value; // YYYY-MM-DD
    const h = document.getElementById('hora_entrega_d').value;  // HH:MM
    const target = document.getElementById('fecha_entrega_full');
    if (f && h) {
        target.value = `${f} ${h}:00`; // Y-m-d H:i:s
        return true;
    } else if (f && !h) {
        // Si hay fecha pero no hora, enviamos solo fecha con 00:00:00 (o vaciamos si prefieres)
        target.value = `${f} 00:00:00`;
        return true;
    } else {
        target.value = ''; // nulo
        return true;
    }
}

document.getElementById('btnHoraEntrega').addEventListener('click', function() {
    combineDateTime();
    this.classList.add('disabled');
    setTimeout(()=>this.classList.remove('disabled'), 500);
    alert('Hora de entrega preparada para guardar.');
});

// Aseguramos combinar ANTES de enviar
document.getElementById('form-edit-pedido').addEventListener('submit', function() {
    combineDateTime();
});
</script>
@stop
