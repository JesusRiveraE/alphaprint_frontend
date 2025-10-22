@extends('adminlte::page')

@section('title', 'Nueva Valoración')

@section('content_header')
    <h1>Registrar Nueva Valoración</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('valoraciones.store') }}" method="POST">
            @csrf

            {{-- ⭐ Sistema de estrellas interactivo --}}
            <div class="mb-4 text-center">
                <label class="form-label fw-bold d-block mb-2">Puntuación</label>
                <div class="star-rating" id="rating-stars">
                    <i class="fas fa-star star" data-value="1"></i>
                    <i class="fas fa-star star" data-value="2"></i>
                    <i class="fas fa-star star" data-value="3"></i>
                    <i class="fas fa-star star" data-value="4"></i>
                    <i class="fas fa-star star" data-value="5"></i>
                </div>
                <input type="hidden" name="puntuacion" id="puntuacion" value="{{ old('puntuacion', 0) }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Comentario</label>
                <textarea name="comentario" class="form-control" rows="3">{{ old('comentario') }}</textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('valoraciones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Valoración
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ⭐ Script para estrellas dinámicas --}}
@section('js')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const stars = document.querySelectorAll("#rating-stars .star");
        const ratingInput = document.getElementById("puntuacion");
        let selected = parseInt(ratingInput.value);

        // Función para actualizar el color de las estrellas
        const updateStars = (hoverValue = 0) => {
            stars.forEach(star => {
                const val = parseInt(star.getAttribute("data-value"));
                if (hoverValue >= val || selected >= val) {
                    star.classList.add("text-warning");
                    star.classList.remove("text-muted");
                } else {
                    star.classList.remove("text-warning");
                    star.classList.add("text-muted");
                }
            });
        };

        // Eventos de hover y click
        stars.forEach(star => {
            star.addEventListener("mouseover", () => updateStars(star.getAttribute("data-value")));
            star.addEventListener("mouseout", () => updateStars());
            star.addEventListener("click", () => {
                selected = parseInt(star.getAttribute("data-value"));
                ratingInput.value = selected;
                updateStars();
            });
        });

        // Inicializa las estrellas
        updateStars();
    });
</script>
@stop

{{-- ⭐ Estilos para estrellas --}}
@section('css')
<style>
    .star-rating {
        font-size: 2rem;
        cursor: pointer;
        user-select: none;
    }
    .star {
        transition: color 0.2s;
        margin: 0 4px;
    }
</style>
@stop
@endsection
