@extends('layouts.app')
@section('content')
<title>Bienvenue</title>
<ul>
@foreach($hellos as $hello)
<li>{{ $hello->mots}}</li>
@endforeach
</ul>
@endsection
