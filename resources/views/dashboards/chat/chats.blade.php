@extends('dashboards.blank')

@section('content')
    <style>
        #search {
            width: 100%;
            position: sticky;
            top: 63px;
            z-index: 2;
        }
    </style>
    <div class="container-fluid chat-container">
        <div class="row">
            <div class="col-md-4 user-section">
                <div class="user-wrapper">
                    <p id="chats-head">Mobile Users</p>
                    <div style="display:flex;position:sticky;top:63px;z-index:2;">
                        <input name="search" class="form-control" placeholder="Search here ..." id="search"><i
                            class="fas fa-search"
                            style="z-index:2;cursor:pointer;font-size:18px;margin-left:-21px;margin-top:10px;"
                            id="search-icon"></i>
                    </div>
                    @include('dashboards.chat.users')
                </div>
            </div>

            <div class="col-md-8" id="messages">
                @csrf
            </div>

        </div>
    </div>
@endsection
