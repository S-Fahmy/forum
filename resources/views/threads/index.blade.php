@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-left">

        <div class="col-md-8">
            @forelse ($threads as $thread)

            <a href="#">
                <div class="card thread-card">

                    {{-- <div class="card-header">

                </div> --}}

                    <div class="card-body">
                        <div class="row">
                            <div class="col-10">
                                <h5 class="thread-header">
                                    @if (auth()->check() && $thread->hasUpdatesFor)
                                    <a style="color:cyan;" href={{$thread->path()}}>{{$thread->title}}</a>


                                    @else
                                    <a href={{$thread->path()}}>{{$thread->title}}</a>

                                    @endif


                                </h5>
                            </div>
                            <div class="col-2 comments">
                                <span>{{ $thread->replies_count }} <i class="fas fa-comments"></i>
                                    {{-- {{ str_plural('Reply', $thread->replies_count) }} --}}

                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 postedBy">
                                Posted by:
                                <a href="{{ route('profile', $thread->owner) }}">{{ $thread->owner->name }}</a>, last
                                updated {{$thread->updated_at->diffForHumans()}}

                            </div>
                        </div>
                        @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                        @endif



                        <article>


                            <div class="body">
                                {!! $thread->body !!}
                            </div>



                        </article>

                    </div>

                    <div class="card-footer">

                        <strong><i class="fas fa-eye"> </i></strong>0  {{-- {{$thread->visits()->count()}} --}}
                    </div>

                </div>
            </a>
            @empty
            <p><strong>There are no records in this channel at this time.</strong></p>


            @endforelse
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Search
                </div>

                <div class="card-body">
                    <form method="GET" action="/forum/threads/search">
                        <div class="form-group">
                            <input type="text" placeholder="Search threads..." name="query" class="form-control">
                        </div>

                        <div class="form-group">
                            <button class="btn btn-dark" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- <div class="card trending-card" style="margin-bottom: 40px;">
                <div class="card-header trending-header">
                    <h6>Trending threads</h6>

                </div>
                <div class="card-body trending-body">

                    @foreach ($trending as $key=> $trendingObject)
                    <div class="row justify-content-center trending-list">
                        <div class="col-12 ">
                            <a href="{{$trendingObject->path}}">{{$trendingObject->title}}</a>
                        </div>
                    </div>
                    {{-- bad code design yes, todo: impelemnt a counter helper class later OR use a ul here --}}
                    {{-- <span style="display: none">{{++$key}}</span>
                    @if ($key< 5)
                     <hr />
                    @endif


                    @endforeach
                </div>
            </div> --}}

        </div>
        <div class="row justify-content-center">
            {{$threads->render()}}
        </div>
    </div>
    @endsection