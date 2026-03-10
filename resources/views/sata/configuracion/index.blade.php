@extends('layouts.vertical', ['title' => 'Configuración General'])

@section('page-title')
    @include('layouts.partials.page-title', [
        'subtitle' => 'Administración',
        'title' => 'Configuración General del Sistema',
    ])
@endsection

@section('content')
    <livewire:sata.configuracion-general-manager />
@endsection
