@extends('layouts.app')

@section('content')

{{-- now th whole thread display page is an vue inline template --}}

<thread-view :thread="{{$thread}}" inline-template v-cloak>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card" v-if="! editing">
                    <div class="card-header">
                        <img src="/{{ $thread->owner->avatar_path }}" alt="{{ $thread->owner->name }}" width="45"
                            height="auto" class="mr-1">

                        <a href="{{ route('profile', $thread->owner) }}">{{ $thread->owner->name }}</a>
                        posted: <span v-text="form.title"></span>

                    </div>

                    <div class="card-body" v-html="form.body">

                    </div>

                    {{-- @can('update', Thread::class) --}}


                    <div class="card-footer" v-if="authorize('owns', {{$thread}})" v-cloak>
                        <button class="btn-sm btn-dark" @click="editing = true"><i class="fa fa-pencil-square-o"
                                aria-hidden="true"></i> Edit your post</button>
                    </div>

                    {{-- @endcan --}}
                </div>


                <div class="editing card" v-else>
                    <div class="card-header">
                        <img src="/{{ $thread->owner->avatar_path }}" alt="{{ $thread->owner->name }}" width="45"
                            height="auto" class="mr-1">

                        <div class="form-group">

                            <input class="form-control" type="text" v-model="form.title">



                        </div>

                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            {{-- <textarea class="form-control" v-model="form.body"></textarea> --}}
                            {{-- text change in the trix component is synced with the thread component body element --}}
                            <wysiwyg v-model="form.body"  @attachmentAdded="loadAttachment"></wysiwyg>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn-sm" @click="cancel">Cancel</button>
                        <button class="btn-sm btn-primary" @click="update">Update Thread</button>

                    </div>


                </div>
                <hr />

                {{-- load the replies with vue instead of blade syntax --}}

                <replies @added="repliesCount++" @removed="repliesCount--"></replies>

                {{-- <replies :data="{{$thread->replies}}" @added = "repliesCount++" @removed = "repliesCount--">
                </replies> --}}

                {{-- @foreach ($replies as $reply)
            @include ('threads.reply')
            @endforeach --}}

                {{-- {{ $replies->links() }} --}}

                {{-- @if (auth()->check())
                <form method="POST" action="{{ $thread->path() . '/replies' }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <textarea name="body" id="body" class="form-control" placeholder="Have something to say?"
                        rows="5"></textarea>
                </div>

                <button type="submit" class="btn btn-default">Post</button>
                </form>
                @else
                <p class="text-center">Please <a href="{{ route('login') }}">sign in</a> to participate in this
                    discussion.</p>
                @endif --}}
            </div>

            <div class="col-md-4">
                <div class="card ">
                    <div class="card-body">
                        <div class="row">
                            <p>
                                This thread was published {{ $thread->created_at->diffForHumans() }} by
                                <a href="#">{{ $thread->owner->name }}</a>, and currently
                                has <span v-text="repliesCount"></span>
                                {{ str_plural('comment', $thread->replies_count) }}.
                            </p>
                        </div>
                        <br />

                        <div class="row">
                            <subscribe-button :active={{json_encode($thread->isSubscribed) }} v-if="signedIn">
                            </subscribe-button>

                            <button class="btn lockBtn btn-dark" v-if="authorize('isAdmin')" @click="toggleLock">
                                <span v-show="locked"> <i class="fas fa-unlock-alt"></i> Unlock</span>
                                <span v-show="! locked"><i class="fas fa-lock"></i> Lock</span>
                            </button>


                            @can('update', $thread)

                            <form action="{{$thread->path()}}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-link" type="submit">Delete thread</button>
                            </form>


                            @endcan

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
@endsection