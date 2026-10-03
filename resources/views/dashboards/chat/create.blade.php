@csrf
<div class="msg-top">
    <h4>{{ $user->name }}</h4>
</div>
<div class="message-wrapper">
    <ul class="messages">
        @foreach ($messages as $message)
            @php
                $isAdmin = $message->from == 0;
            @endphp
            <li class="message clearfix">
                <div class="{{ $isAdmin ? 'sent' : 'received' }}">
                    <p class="color">
                        @if ($isAdmin)
                            <div style="font-weight:bold;">{{ $message->admin }}-(admin)</div>
                        @else
                            <div style="font-weight:bold;">{{ $message->mobileuser->name }}</div>
                        @endif
                        @if ($message->message)
                            <br>
                            {{ $message->message }}
                        @endif
                        @if ($message->messageImages)
                            <div class="message-images-grid">
                                @foreach ($message->messageImages as $img)
                                    <span class="image_container">
                                        <img src="{{ asset('storage/' . $img->image) }}" class="imgages" alt=""
                                            height="150px;">
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </p>
                    <p class="date">
                        {{ date('d M y, h:i a', strtotime($message->created_at)) }} &nbsp;&nbsp;
                    </p>
                </div>
            </li>
        @endforeach
    </ul>
</div>
<div class="popup" id="popup">
    <img src="" class="zoom-img" id="zoom-img" alt="">
    <div id="caption"></div>
</div>
<div id="gallery">

</div>
<div class="input-text">
    <input type="text" name="message" class="submit" placeholder="Aa">
    <label for="file-input" style="display:inline-block;">
        <i class="fa fa-upload" aria-hidden="true" style="font-size: 28px;"></i>
    </label>
    <input type="file" id="file-input" name="files[]" style="display:inline-block;display:none;" onchange="preview()"
        multiple>
    <button type="submit" class="btn btn-primary" style="display:inline-block">
        Send
    </button>
</div>
<script>
    function preview() {
        let preview = document.getElementById('gallery');
        let file = document.getElementById('file-input').files;
        preview.innerHTML = ""; // Clear previous previews
        for (let i = 0; i < file.length; i++) {
            var reader = new FileReader();

            reader.addEventListener("load", function() {
                let image = new Image();
                image.height = 100;
                image.title = file[i].name;
                image.src = this.result;
                image.className = "preview-img"
                preview.appendChild(image);
            });
            reader.readAsDataURL(file[i]);
        }
    }

    function clearGallery() {
        document.getElementById('gallery').innerHTML = "";
        document.getElementById('file-input').value = "";
    }
</script>
<style>
    .msg-top {
        height: 50px;
        background: #0d2444;
        border: 1px solid;
        border-radius: 6px;
    }

    .msg-top h4 {
        color: white;
        margin-left: 50px;
        padding-top: 3px;
        font-weight: 500;
    }

    .message-images-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    .image_container {
        padding: 0;
        display: block;
    }

    .image_container img {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .image_container:hover {
        opacity: 0.7;
    }

    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 10;
        padding-top: 20px;
        padding-bottom: 10px;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
    }

    .zoom-img {
        display: block;
        margin: auto;
        height: 90vh;
    }

    @-webkit-keyframes zoom {
        from {
            -webkit-transform: scale(1)
        }

        to {
            -webkit-transfer: scale(2)
        }
    }

    @keyframes zoom {
        from {
            transform: scale(0.4)
        }

        to {
            transform: scale(1)
        }
    }

    @-webkit-keyframes zoom-out {
        from {
            transform: scale(1)
        }

        to {
            transform: scale(0)
        }
    }

    @keyframes zoom-out {
        from {
            transform: scale(1)
        }

        to {
            transform: scale(0)
        }
    }

    .zoom-img {
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.6s;
        animation-name: zoom;
        animation-duration: 0.6s
    }

    .out {
        animation-name: zoom-out;
        animation-duration: 0.6s;
    }

    #gallery {
        max-height: 150px;
        max-width: 100%;
        overflow-y: auto;
    }

    .preview-img {
        margin: 3px;
    }
</style>
<script>
    // Get the modal
    var modal = document.getElementById('popup');
    var modalImg = document.getElementById("zoom-img");

    let imgages = document.querySelectorAll('.imgages');
    imgages.forEach((data) => {
        data.addEventListener('click', () => {
            modal.style.display = "block";
            modalImg.src = data.src;
            modalImg.alt = data.alt;
        })
    })
    modal.onclick = function() {
        modalImg.className += " out";
        setTimeout(function() {
            modal.style.display = "none";
            modalImg.className = "zoom-img";
        }, 400);

    }
</script>
