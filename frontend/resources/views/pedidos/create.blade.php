@extends('adminlte::page')

@section('title','Nuevo Pedido')

@section('content_header')
<h1 class="m-0" style="font-size:1.7rem;">
    <i class="fas fa-plus-circle mr-2 brand-text"></i> Nuevo Pedido
</h1>
@stop

@section('content')
<div class="card card-soft shadow-sm">
    <div class="card-header border-brand">
        <strong class="brand-text" style="font-size:1.25rem;">Registrar Pedido</strong>
    </div>

    <form method="POST" action="{{ route('pedidos.store') }}">
        @csrf
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🧑 Selección de cliente --}}
            <div class="form-group">
                <label for="id_cliente">Cliente</label>
                <select id="id_cliente" name="id_cliente" class="form-control" required>
                    <option value="">Seleccione un cliente...</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente['id_cliente'] }}"
                            {{ old('id_cliente') == $cliente['id_cliente'] ? 'selected' : '' }}>
                            #{{ $cliente['id_cliente'] }} — {{ $cliente['nombre'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="descripcion">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion"
                           value="{{ old('descripcion') }}" placeholder="Descripción del pedido">
                </div>

                <div class="form-group col-md-4">
                    <label for="total">Total (Lps)</label>
                    <input type="number" step="0.01" class="form-control" id="total" name="total"
                           value="{{ old('total') }}" placeholder="0.00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" class="form-control">
                        <option value="Pendiente"   {{ old('estado')=='Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="En Progreso" {{ old('estado')=='En Progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="Completado"  {{ old('estado')=='Completado' ? 'selected' : '' }}>Completado</option>
                    </select>
                </div>

                {{-- 📅 Fecha + 🕒 Hora de entrega --}}
                <div class="form-group col-md-8">
                    <label>Entrega</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega"
                               value="{{ old('fecha_entrega') }}" aria-label="Fecha de entrega">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btn-hora-entrega" title="Seleccionar hora">
                                <i class="fas fa-clock"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2 d-flex align-items-center">
                        <input type="time" class="form-control form-control-sm" style="max-width: 150px;"
                               id="hora_entrega" name="hora_entrega" step="60" value="{{ old('hora_entrega') }}">
                        <span id="preview_entrega" class="ml-2 badge badge-chip d-none"></span>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-brand-outline">
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>
    </form>
</div>
@stop

@section('css')
<style>
:root{ --brand:#e24e60; --brand-100:#fde5e9; }
.brand-text{ color:var(--brand); }
.card-soft{ border:1px solid #eff1f5; border-radius:.6rem; }
.card-soft:hover{ box-shadow:0 0 15px rgba(226,78,96,.08); }
.border-brand{ border-left:4px solid var(--brand); background:#fff; }
.btn-brand-outline{ border:1px solid var(--brand); color:var(--brand); background:#fff; }
.btn-brand-outline:hover{ background:#fde5e9; color:var(--brand); }
.badge-chip{ background: var(--brand-100); color: var(--brand); font-weight:600; border-radius:999px; padding:.35rem .6rem; }
</style>
@stop

@section('js')
<script>
document.getElementById('btn-hora-entrega').addEventListener('click', function(){
    document.getElementById('hora_entrega').focus();
});

const fecha = document.getElementById('fecha_entrega');
const hora  = document.getElementById('hora_entrega');
const prev  = document.getElementById('preview_entrega');

function renderPreview(){
    if(!fecha.value && !hora.value){
        prev.classList.add('d-none');
        return;
    }
    let ddmmyy = '';
    if(fecha.value){
        const d = new Date(fecha.value + 'T00:00:00');
        ddmmyy = d.toLocaleDateString('es-HN', { timeZone:'America/Tegucigalpa' });
    }
    const hm = hora.value ? hora.value.substring(0,5) : '--:--';
    prev.textContent = `${ddmmyy} ${hm}`;
    prev.classList.remove('d-none');
}
fecha.addEventListener('change', renderPreview);
hora.addEventListener('change', renderPreview);
renderPreview();
</script>
@stop
