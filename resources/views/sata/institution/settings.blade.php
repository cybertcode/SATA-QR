@extends('layouts.vertical', ['title' => 'Configuración Institucional'])

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Administración',
        'title' => 'Configuración de la I.E.',
    ])

    <livewire:sata.institution-settings-manager />
@endsection
