@extends('layouts.vertical', ['title' => 'Gestión de Usuarios'])

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Administración',
        'title' => 'Gestión de Usuarios',
    ])

    <livewire:sata.user-manager />
@endsection
