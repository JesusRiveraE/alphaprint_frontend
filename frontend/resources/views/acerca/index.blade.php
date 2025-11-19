@extends('adminlte::page')

@section('title', 'Acerca de AlphaPrint')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title m-0">
            <i class="fas fa-info-circle mr-2"></i> Acerca de AlphaPrint
        </h1>
    </div>
@stop

@section('content')
    <div class="card card-outline card-brand shadow-sm">
        <div class="card-header border-0">
            <h3 class="card-title font-weight-bold mb-0">
                Información del Proyecto
            </h3>
        </div>

        <div class="card-body">
            <p class="mb-4">
                SIGOP es un sistema integral de gestión diseñado para administrar pedidos,
                clientes, personal, bitácoras, archivos y notificaciones dentro de un entorno
                de imprenta moderna. Esta plataforma surge como un proyecto académico
                orientado a la optimización de procesos administrativos y operativos.
            </p>

            <div class="row">
                <!-- COL 1: INTEGRANTES -->
                <div class="col-md-6">
                    <h5 class="text-muted text-uppercase mb-2" style="letter-spacing: .08em;">
                        Equipo Desarrollador
                    </h5>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <i class="fas fa-user mr-2 text-muted"></i>
                            <strong>Jesus Alberto Rivera Espino</strong><br>
                            <i class="fas fa-envelope mr-2 text-muted"></i>
                            <a href="mailto:jesusriveraespino@yahoo.es">
                                jesusriveraespino@yahoo.es
                            </a>
                        </li>

                        <li class="list-group-item px-0">
                            <i class="fas fa-user mr-2 text-muted"></i>
                            <strong>Maria Jose Daniel Amaya</strong><br>
                            <i class="fas fa-envelope mr-2 text-muted"></i>
                            <a href="mailto:mariadaniel7c@gmail.com">
                                mariadaniel7c@gmail.com
                            </a>
                        </li>

                        <li class="list-group-item px-0">
                            <i class="fas fa-user mr-2 text-muted"></i>
                            <strong>Nellelin Marili Mejia Fugon</strong><br>
                            <i class="fas fa-envelope mr-2 text-muted"></i>
                            <a href="mailto:nellelinmejia7@gmail.com">
                                nellelinmejia7@gmail.com
                            </a>
                        </li>

                        <li class="list-group-item px-0">
                            <i class="fas fa-user mr-2 text-muted"></i>
                            <strong>Brandon Aldair Rodriguez Escalante</strong><br>
                            <i class="fas fa-envelope mr-2 text-muted"></i>
                            <a href="mailto:aldairescalanteescalante8@gmail.com">
                                aldairescalanteescalante8@gmail.com
                            </a>
                        </li>

                        <li class="list-group-item px-0">
                            <i class="fas fa-user mr-2 text-muted"></i>
                            <strong>Oscar Daniel Ramirez Garmendia</strong><br>
                            <i class="fas fa-envelope mr-2 text-muted"></i>
                            <a href="mailto:osdanir99@gmail.com">
                                osdanir99@gmail.com
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- COL 2: INFO GENERAL -->
                <div class="col-md-6">
                    <h5 class="text-muted text-uppercase mb-2" style="letter-spacing: .08em;">
                        Acerca del Sistema
                    </h5>
                    <p>
                        AlphaPrint centraliza la administración de pedidos y la gestión interna
                        del negocio, permitiendo a los usuarios llevar control de clientes,
                        historial de estados, notificaciones automáticas, bitácoras de actividad,
                        archivos adjuntos y calendario de entregas.
                    </p>

                    <h5 class="text-muted text-uppercase mt-4 mb-2" style="letter-spacing: .08em;">
                        Objetivo del Proyecto
                    </h5>
                    <p>
                        Proporcionar una herramienta eficiente, moderna y escalable que facilite
                        la toma de decisiones, mejore el flujo de trabajo y permita una experiencia
                        organizada tanto para empleados como para administradores.
                    </p>

                    <h5 class="text-muted text-uppercase mt-4 mb-2" style="letter-spacing: .08em;">
                        Tecnologías Utilizadas
                    </h5>

                    <ul>
                        <li>Laravel 10 + Blade (Frontend)</li>
                        <li>AdminLTE 3 (UI/UX)</li>
                        <li>Node.js + Express (Backend API)</li>
                        <li>Firebase Authentication (Login y seguridad)</li>
                        <li>MySQL (Base de datos principal)</li>
                        <li>jQuery, Axios, FullCalendar, FontAwesome</li>
                    </ul>

                    <h5 class="text-muted text-uppercase mt-4 mb-2" style="letter-spacing: .08em;">
                        Alcance Funcional
                    </h5>

                    <ul>
                        <li>Gestión completa de pedidos</li>
                        <li>Control de clientes y empleados</li>
                        <li>Bitácora con auditoría de acciones</li>
                        <li>Sistema de notificaciones internas</li>
                        <li>Calendario de entregas sincronizado</li>
                        <li>Gestión de archivos por pedido</li>
                        <li>Roles y permisos basados en Firebase</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
