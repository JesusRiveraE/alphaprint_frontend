@extends('adminlte::page')

@section('title', 'Nueva Valoración')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        <i class="fas fa-star-half-alt mr-2 brand-text"></i> Nueva Valoración
    </h1>
    <a href="{{ route('valoraciones.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-soft">
            <div class="card-header border-brand">
                <strong class="brand-text">Registrar valoración</strong>
            </div>

            <form action="{{ route('valoraciones.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="puntuacion">Puntuación</label>
                        <select name="puntuacion" id="puntuacion" class="form-control @error('puntuacion') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            @for($i=1;$i<=5;$i++)
                                <option value="{{ $i }}" {{ old('puntuacion') == $i ? 'selected' : '' }}>
                                    {{ $i }} ⭐
                                </option>
                            @endfor
                        </select>
                        @error('puntuacion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="comentario">Comentario (opcional)</label>
                        <textarea name="comentario" id="comentario" rows="4"
                                  class="form-control @error('comentario') is-invalid @enderror"
                                  placeholder="Cuéntanos tu experiencia...">{{ old('comentario') }}</textarea>
                        @error('comentario')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-brand-outline">
                        <i class="fas fa-save mr-1"></i> Guardar Valoración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
:root{
    --brand:#e24e60;
    --brand-100:#fde5e9;
}
.brand-text{ color:var(--brand); }
.card-soft{
    border:1px solid #eff1f5;
    border-radius:.6rem;
}
.border-brand{
    border-left:4px solid var(--brand);
    background:#fff;
}
.btn-brand-outline{
    border:1px solid var(--brand);
    color:var(--brand);
    background:#fff;
}
.btn-brand-outline:hover{
    background:var(--brand-100);
    color:var(--brand);
}
</style>
@stop
