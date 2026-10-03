<ul class="users">
    @foreach ($users as $user)
        <li class="user" id="@if ($user->type == 'group') group_{{ $user->id }}@else{{ $user->id }} @endif"
            role="{{ $user->type }}">

            @if ($user->unread)
                <span class="pending" style=" margin-left:80%;">{{ $user->unread }}</span>
            @endif
            <div clas="media">
                <div class="media-left">
                </div>
                <div class="media-left media-body">
                    <h6 class="name">{{ $user->name }}</h6>
                </div>
            </div>
        </li>
    @endforeach

</ul>
