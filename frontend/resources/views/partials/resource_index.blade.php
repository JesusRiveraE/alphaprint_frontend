@php
    $rows = $rows ?? [];
    $title = $title ?? 'Listado';
    $headers = collect($rows)->first() ? array_keys(collect($rows)->first()) : [];
@endphp

@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    @if(empty($rows) || count($rows) === 0)
        <div class="alert alert-info">No hay datos para mostrar.</div>
    @else
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    @foreach($headers as $h)
                        <th class="text-capitalize">{{ str_replace('_', ' ', strtolower($h)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                    <tr>
                        @foreach($headers as $h)
                            <td>
                                @php $val = $r[$h] ?? ''; @endphp
                                @if(is_array($val))
                                    {{ json_encode($val, JSON_UNESCAPED_UNICODE) }}
                                @else
                                    {{ $val }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
@stop
