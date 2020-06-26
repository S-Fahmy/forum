@extends('layouts.app')
@section('head')

<script src="https://www.google.com/recaptcha/api.js" async defer></script>


@endsection


@section('content')

<div class="container">


    @if (auth()->check())




    <div class="row justify-content-center" style="padding-top: 40px;">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6>Create a new thread</h6>
                </div>

                <div class="card-body">
                    <form method="POST" action="/forum/threads">
                        @csrf

                        <div class="form-group"><label for="channel_id">Choose a channel:</label>

                            <select name="channel_id" id="channel_id" class="form-control" required>
                                <option value="">Choose one</option>

                                @foreach ($channels as $channel)
                                <option value="{{$channel->id}}" {{old('channel_id')==$channel->id? 'selected':''}}>
                                    {{$channel->name}}</option>

                                @endforeach

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Thread title:</label>

                            <input type="text" id="title" class="form-control" name="title" value="{{old('title')}}"
                                required><br><br>
                        </div>

                        <div class="form-group">

                            <label for="replyText">Thread content:</label>
                            
                            {{-- <textarea required name="body" class="body form-control" id="replyText"
                                rows="3">{{old('body')}}</textarea> --}}

                                <wysiwyg name = "body"></wysiwyg>
                                
                        </div>

                        <div class="g-recaptcha" data-sitekey="6Lf4-6kZAAAAACbd3r_QfxOcUi_2JqQt_Cwy83Jc"></div>



                        <button type="submit" class="btn btn-primary mb-2">Post thread!</>

                        @if (count($errors))
                        <div class="alert alert-danger">
                            <ul>

                                @foreach ($errors->all() as $error)
                                <li>
                                    {{$error}}
                                </li>

                                @endforeach
                            </ul>

                        </div>

                        @endif
                    </form>
                </div>
            </div>

        </div>
    </div>

    @else
    <p class="text-center">Please <a href="{{ route('login')}}"> log in </a> to add a new thread.</p>
    @endif
</div>

@endsection