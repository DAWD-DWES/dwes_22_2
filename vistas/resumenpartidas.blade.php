{{-- Usamos la vista app como plantilla --}}
@extends('app')
{{-- Sección aporta el título de la página --}}
@section('title', 'Introduce Jugada')
@section('navbar')
<div class="container justify-content-around">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="juego.php">Volver</a>
        </li>
    </ul>
</div>
@endsection
{{-- Sección muestra vista de juego para que el usuario elija una letra --}}
@section('content')
<div class="container">
    <h1 class="my-5 text-center">Resumen de partidas jugadas</h1>
    <div class="row">
        <div class="col-6">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Partidas ganadas</th>
                        <th scope="col"># Errores</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($partidasGanadas))
                    @php $i=1; @endphp
                    @foreach($partidasGanadas as $key => $partidaGanada)
                    <tr>
                        <th scope="row">{{ $i++ }}</th>
                        <td>{{ $key }}</td>
                        <td>{{ $partidaGanada }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr><td>No hay palabras</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Partidas perdidas</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($partidasPerdidas))
                    @php $i=1; @endphp
                    @foreach($partidasPerdidas as $key => $partidaPerdida)
                    <tr>
                        <th scope="row">{{ $i++ }}</th>
                        <td>{{ $partidaPerdida }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr><td>No hay palabras</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>    
@endsection