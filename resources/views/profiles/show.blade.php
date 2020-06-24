@extends('layouts.app')

@section('content')
<div class="container ">
    <div class="row">

       
        <div class="col-md-8 ml-auto mr-auto">
          
                {{-- <h1>
                    {{ $profileUser->name }}
                </h1>
                <hr />
                @can('update', $profileUser)
                <form method="POST" action="{{ route('avatar', $profileUser) }}" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <input type="file" name="avatar">
                    <button type="submit">add</button>

                </form>
                @endcan
                <img src='/storage/{{$profileUser->avatar_path}}' width="200px" height="auto"> --}}


           

            @forelse ($activities as $date => $activity)
            <h6 class="page-header">{{ $date }}</h6>

            @foreach ($activity as $record)
            @include ("profiles.activities.{$record->type}", ['activity' => $record])
            @endforeach
            @empty

            <p>User hasn't participated any activities yet</p>

            @endforelse
        </div>

        <div class="col-4">
            <avatar-form :user-profile="{{$profileUser}}"> </avatar-form>
        </div>
    </div>
</div>
@endsection