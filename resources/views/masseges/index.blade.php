<div class="message-wrapper">
    <ui class="messages">
        @foreach ($messeges as $message)
        <li class="message clearfix">
            <div class="{{ ($message->from == Auth::id()) ? 'sent' :'received' }}">
            <p>{{ $message->message }}</p>
            <p class="date">{{ date('d M y , h:i a' , strtotime($message->created_at)) }}</p> 
            </div>
        </li>
        @endforeach
    </ui>
</div>
<div class="input-text">
        <input type="text" class="submit" name="message" placeholder="Type a text">
</div>