@extends('layouts.app')

@section('content')

<search :trending = "{{$trendingThreads}}"></search>

 @endsection