{{-- <reply :attributes="{{$reply}}" inline-template v-cloak>

    <div class="reply-card" style="margin-bottom: 30px">
        <div id="reply-{{ $reply->id }}" class="card-header">
            <div class="row">
                <div class="col-9">
                    <a href="{{ route('profile', $reply->owner) }}">
                        {{ $reply->owner->name }}
                    </a>
                    {{$reply->created_at->diffForHumans()}}
                </div>

                @if (Auth::check())

                <div class="offset-1 col-2">
                    <favorite :reply="{{$reply}}"></favorite>
                    {{-- <form method="POST" action="/replies/{{$reply->id}}/favorites">
                    @csrf
                    <button class="btn btn-dark btn-sm"
                        {{$reply->isFavorited() ? 'disabled' :''}}>{{$reply->getFavoritesCountAttribute()}}
                        Favorites</button>
                    </form> --}}
                {{-- </div>
                @endif

            </div>
        </div>

        <div class="card">

            <div class="card-body">
                <div v-if="editing">
                    <div class="form-group">
                        <textarea class="form-control" v-model="body"></textarea>
                    </div>
                    <button class="btn btn-xs btn-primary" @click="update">Update</button>

                    <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
                </div>

                <!-- the body attribute in the vue file, which is the same as saying $reply->body-->
                <div v-else v-text="body"> </div>


            </div>

            @can('update', $reply)

            <div class="card-footer">
                <div class="row">
                    <button class="btn-sm btn btn-success mr-2" @click="editing = true">Edit</button>

                    <button class="btn-sm btn btn-danger mr-2" @click="destroy">Delete</button>

                </div>
            </div>
            @endcan
        </div>
    </div>

</reply> --}}