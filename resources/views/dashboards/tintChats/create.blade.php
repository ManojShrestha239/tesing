@csrf
<div class="msg-top"><h4>{{$user->full_name}}</h4></div>
<div class="message-wrapper">
    <ul class="messages">
        @foreach($messages as $message)
            <li class="message clearfix">

                <div class="{{ ($message->from == 0) ? 'sent' : 'received' }}">
                    <p class="color">{{ $message->message }}</p>
                    <p class="date">
                        {{ date('d M y, h:i a', strtotime($message->created_at)) }} &nbsp;&nbsp;
                        {{ (($message->admin))}}
                    </p>
                </div>
            </li>
        @endforeach
    </ul>
</div>

<div class="input-text">
    <input type="text" name="message" class="submit" placeholder="Aa">
    <button type="submit" class="btn btn-primary" >
          Send
    </button>
    
</div>
<style>
    .msg-top{
        height:50px;
        background: #0d2444;
        border: 1px solid;
        border-radius: 6px;
    }
    .msg-top h4{
        color:white;
        margin-left: 50px;
        padding-top: 3px;
        font-weight: 500;
    }
</style>
