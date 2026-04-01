@extends('layouts.app')

@section('titulo')
    Página Principal
@endsection

@section('contenido')
    <x-list-post :posts="$posts" />
@endsection