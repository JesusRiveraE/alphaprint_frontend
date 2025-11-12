@extends('adminlte::page')

@section('title', 'Calendario de Entregas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title m-0">
            <i class="fas fa-calendar-alt mr-2"></i> Calendario de Entregas
        </h1>
    </div>
@stop

@section('content')
{{-- Cambié card-primary por card-brand para usar nuestro color --}}
<div class="card card-outline card-brand shadow-sm">
    <div class="card-body">
        <div id="calendar"></div>
        <div id="calError" class="alert alert-danger d-none mt-3"></div>
    </div>
</div>

<!-- Modal Detalle del Pedido -->
<div class="modal fade" id="pedidoModal" tabindex="-1" role="dialog" aria-labelledby="pedidoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header modal-brand">
        <h5 class="modal-title" id="pedidoModalLabel">
          <i class="fas fa-box-open mr-2"></i> Detalle del Pedido
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body py-3">
        <div class="row small">
            <div class="col-6 mb-2">
                <span class="text-muted d-block">ID Pedido</span>
                <strong id="modal-id">—</strong>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block">Cliente</span>
                <strong id="modal-cliente">—</strong>
            </div>
            <div class="col-12 mb-2">
                <span class="text-muted d-block">Descripción</span>
                <strong id="modal-desc">—</strong>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block">Total (L)</span>
                <strong id="modal-total">—</strong>
            </div>
            <div class="col-6 mb-2">
                <span class="text-muted d-block">Fecha de Entrega</span>
                <strong id="modal-fecha">—</strong>
            </div>
            <div class="col-12 mb-2">
                <span class="text-muted d-block">Estado</span>
                <span id="modal-estado" class="badge badge-state">—</span>
            </div>
        </div>
      </div>

      <div class="modal-footer py-2">
        <a id="verPedidoBtn" href="#" class="btn btn-brand">
            <i class="fas fa-eye mr-1"></i> Ver Pedido
        </a>
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
<style>
  /* =========================
     Marca y tokens de diseño
     ========================= */
  :root{
    --brand:#e24e60;               /* color principal */
    --brand-600:#cc4656;
    --brand-700:#b23d4b;
    --brand-100:#fde5e9;
    --ink:#4b5563;                 /* gris para títulos y números */
    --muted:#6b7280;               /* gris secundario */
    --radius:.65rem;
    --shadow:0 8px 24px rgba(226,78,96,.08);
  }

  .page-title{
    font-weight:700;
    letter-spacing:.2px;
    color:var(--ink);
  }

  /* Card contenedor */
  .card{
    border-radius:var(--radius);
    border:1px solid #eef0f3;
    box-shadow:var(--shadow);
  }
  .card .card-body{
    padding:1rem 1rem 1.25rem;
  }

  /* ====== Card outline con color de marca ====== */
  /* Emula .card-primary.card-outline pero con nuestra marca */
  .card-brand.card-outline{
    border-top:3px solid var(--brand);
  }

  /* =========================
     FullCalendar estilos
     ========================= */
  #calendar{
    width:100%;
    min-height:620px;
  }

  /* Toolbar */
  .fc .fc-toolbar.fc-header-toolbar{
    margin-bottom:.75rem;
  }
  .fc .fc-toolbar-title{
    font-size:1.15rem;
    font-weight:700;
    color:var(--ink);              /* gris en título */
  }
  .fc .fc-button{
    border-radius:.5rem;
    border:1px solid var(--brand);
    background:#fff;
    color:var(--brand);
    box-shadow:none !important;
    padding:.375rem .65rem;
    font-weight:600;
  }
  .fc .fc-button:hover{
    background:var(--brand-100);
    color:var(--brand);
  }
  .fc .fc-button-primary:not(:disabled).fc-button-active,
  .fc .fc-button-primary:not(:disabled):active{
    background:var(--brand);
    border-color:var(--brand);
    color:#fff;
  }
  .fc .fc-button-primary:disabled{
    background:#f3f4f6;
    border-color:#e5e7eb;
    color:#9ca3af;
  }

  /* Encabezados de días (Lun, Mar, ...) y números de día en gris */
  .fc .fc-col-header-cell-cushion{
    color:var(--ink);              /* gris para letras del header */
    font-weight:600;
  }
  .fc .fc-daygrid-day-number{
    color:var(--ink);              /* gris para números del calendario */
    font-weight:600;
  }

  /* Celdas y eventos */
  .fc .fc-daygrid-day{
    transition:background .15s ease;
  }
  .fc .fc-daygrid-day:hover{
    background:#fafafa;
  }
  .fc .fc-daygrid-event{
    border-radius:.5rem;
    padding:.15rem .4rem;
    border:0;
    font-weight:600;
  }
  .fc .fc-daygrid-event:hover{
    filter:brightness(.96);
  }

  /* Hoy */
  .fc .fc-daygrid-day.fc-day-today{
    background:#fff8f9 !important;
    outline:2px dashed var(--brand-100);
    outline-offset:-6px;
  }

  /* =========================
     Modal y badges
     ========================= */
  .modal-brand{
    background:var(--brand);
    color:#fff;
    border-top-left-radius:var(--radius);
    border-top-right-radius:var(--radius);
  }
  .btn-brand{
    background:var(--brand);
    border-color:var(--brand);
    color:#fff;
  }
  .btn-brand:hover{
    background:var(--brand-600);
    border-color:var(--brand-600);
    color:#fff;
  }
  .badge-state{
    background:var(--brand-100);
    color:var(--brand-700);
    padding:.45rem .6rem;
    font-weight:700;
    border-radius:.5rem;
  }

  /* Estados coherentes con AdminLTE pero adaptados a la marca */
  .badge-pendiente{ background:#ffefc2; color:#8a6d1d; }
  .badge-progreso{  background:#dff3f8; color:#0b647a; }
  .badge-done{      background:#e6f6ea; color:#1e7b39; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales-all.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    try {
        const eventos = @json($eventos ?? []);
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            height: 'auto',
            dayMaxEventRows: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: Array.isArray(eventos) ? eventos : [],
            eventDisplay: 'block',
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                const p = info.event.extendedProps || {};

                // Rellenar modal
                document.getElementById('modal-id').textContent      = p.id_pedido ?? '—';
                document.getElementById('modal-cliente').textContent = p.cliente_nombre ?? '—';
                document.getElementById('modal-desc').textContent    = p.descripcion ?? '—';
                document.getElementById('modal-total').textContent   = p.total ?? '—';
                document.getElementById('modal-fecha').textContent   = p.fecha_entrega ?? '—';

                const badge = document.getElementById('modal-estado');
                badge.textContent = p.estado ?? '—';
                badge.className = 'badge badge-state ' + (
                    p.estado === 'Pendiente' ? 'badge-pendiente' :
                    p.estado === 'En Progreso' ? 'badge-progreso' : 'badge-done'
                );

                const verPedidoBtn = document.getElementById('verPedidoBtn');
                if (p.id_pedido) {
                    verPedidoBtn.href = `/pedidos/${p.id_pedido}/show`;
                    verPedidoBtn.style.display = 'inline-block';
                } else {
                    verPedidoBtn.style.display = 'none';
                }

                $('#pedidoModal').modal('show');
            }
        });

        calendar.render();

    } catch (e) {
        const box = document.getElementById('calError');
        if (box) {
            box.textContent = 'No se pudo inicializar el calendario: ' + (e.message || e);
            box.classList.remove('d-none');
        }
        console.error(e);
    }
});
</script>
@stop
