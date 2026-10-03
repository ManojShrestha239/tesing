@extends('dashboards.tintBlank')

@section('content')
    <div class="container-fluid chat-container">
        <div class="row">
            <div class="col-md-4 user-section">
            
                <div class="user-wrapper">
                    <p id = "chats-head">Dealer User</p>
                    <ul class="users">
                        @foreach($users as $user)
                        {{-- <input type = "hidden" class = "chatAvatar" id = "{{$user->full_name}}">
                        <script>
                            $(document).ready(function(){
                               var id = $(".chatAvatar").attr('id');
                                console.log(id);
                            });
                        </script> --}}
                            <li class="user" id="{{$user->id}}">

                                @if($user->unread)
                                    <span class="pending" style=" margin-left:80%;">{{ $user->unread }}</span>
                                @endif
                                <div clas="media">
                                    <div class="media-left">
                                   
                                        {{-- <img src="{{$user->avatar}}" alt="" srcset="" class="media-object"> --}}
                                    </div>
                                    <div class="media-left media-body">
                                        <h6  class="name">{{$user->full_name}}</h6>
                                    </div>
                                </div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>

            <div class="col-md-8" id="messages">

            @csrf
            </div>

        </div>
    </div>
@endsection
