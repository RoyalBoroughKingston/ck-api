@extends('layout')

@section('css')
  <link rel="stylesheet" href="{{ mix('/css/docs.css') }}">
@endsection

@section('content')
  <div id="docs"></div>
@endsection

@section('js')
  <script src="{{ mix('/js/docs.js') }}"></script>
@endsection
