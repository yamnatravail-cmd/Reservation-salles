@extends('layout')
@section('content')
<form method="{{route('hello.index')}}">
    @csrf
 <p>Ajouter un nom :</p>
  <input type="text" name="nom" placeholder="Ajouter un nom" required />
  <button type="submit" >Envoyer</button>
</form>
@endsection