<x-layout>
    @section('head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @endsection

    @section('title', isset($post) ? 'Edit Post' : 'Create Post')

    <form action="{{ isset($post) ? route('editPost', ['id' => $post->post_version_id]) : route('createPost') }}" id="savePost" method="POST">
        @csrf
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('show.account') }}" class="seeMoreLink">Back to Account</a>
        @if(isset($post))
            <div class="metaHeader">
                <h2>Edit Post</h2>
                <div class="postTags">
                    @if($post->status == 'published')
                        <span class="tag published">Published</span>
                    @elseif($post->status == 'draft')
                        <span class="tag draft">Draft</span>
                    @endif
                </div>
            </div>
        @else
            <h2>Create Post</h2>
        @endif

        <label class="required" for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', isset($post) ? $post->title : '') }}" required></input>

        <label>Description</label>
        <input type="hidden" id="description" name="description">
        <div>
            <button type="button" class="editor-btn" data-element="bold"><i class="fas fa-bold"></i></button>
            <button type="button" class="editor-btn" data-element="italic"><i class="fas fa-italic"></i></button>
            <button type="button" class="editor-btn" data-element="underline"><i class="fas fa-underline"></i></button>
            <button type="button" class="editor-btn" data-element="strikethrough"><i class="fas fa-strikethrough"></i></button>
            <button type="button" class="editor-btn" data-element="justifyLeft"><i class="fas fa-align-left"></i></button>
            <button type="button" class="editor-btn" data-element="justifyCenter"><i class="fas fa-align-center"></i></button>
            <button type="button" class="editor-btn" data-element="justifyRight"><i class="fas fa-align-right"></i></button>
            <button type="button" class="editor-btn" data-element="justifyFull"><i class="fas fa-align-justify"></i></button>
            <button type="button" class="editor-btn" data-element="insertMediaFile"><i class="fas fa-upload"></i></button>
            <input class="editor-input-color" type="file" id="mediaUpload">

            <div id="editorTextArea" contenteditable="true">{!! old('description', isset($post) ? $post->description : '') !!}</div>
        </div>
        <br>

        <label class="required" for="start_timestamp">Start Date Time</label>
        <input type="datetime-local" id="start_timestamp" name="start_timestamp" value="{{ old('start_timestamp', isset($post) ? \Carbon\Carbon::parse($post->start_timestamp)->format('Y-m-d\TH:i') : '') }}" required>
        
        <br>
        <div class="metaContainer">
            <div class="metaHeader">
                <h2>Meta data</h2>
                @can('editPost', Auth::user())
                    <button type="button" class="btn" id="generate-meta-btn">Generate</button>
                @endcan
            </div>
            <label class="required" for="meta_title">Meta title</label>
            <input class="bg-gray-200" type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', isset($post) ? $post->meta_title : '') }}" required></input>

            <label class="required" for="meta_description">Meta Description</label>
            <textarea class="bg-gray-200" id="meta_description" name="meta_description" required>{{ old('meta_description', isset($post) ? $post->meta_description : '') }}</textarea>

            <label class="required" for="keywords">Keywords</label>
            <input class="bg-gray-200" type="text" id="keywords" name="keywords" value="{{ old('keywords', isset($post) ? $post->keywords : '') }}" required></input>
        </div>
        
        @if(isset($post))
            <!-- Edit submission -->
            @can('editPost', Auth::user())
                <button type="submit" name="action" class="btn" value="save">Save</button>
            @endcan
        @else
            <!-- Create submission -->
            @can('createPost', Auth::user())
                <button type="submit" name="action" class="btn" value="create">Create</button>
            @endcan
        @endif
    </form>

  <!-- Publish submission -->
    @can('publishPost', Auth::user())
        @if(isset($post))
            @if($post->status == 'published')
                <form action="{{ route('unPublishPost', ['id' => $post->post_version_id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn">Unpublish</button>
                </form>
            @elseif($post->status == 'draft')
                <form action="{{ route('publishPost', ['id' => $post->post_version_id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn">Publish</button>
                </form>
            @endif
        @endif
    @endcan

    <!-- validation -->
    @if($errors->any())
        <ul class="px-4 py-2 bg-red-100">
            @foreach($errors->all() as $error)
            <li class="my-2 text-red-500">{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <script>
        /** Set editor content into description field before form submission */
        document.getElementById('savePost').addEventListener('submit', function(event) {
            const editorContent = document.getElementById('editorTextArea').innerHTML.trim();
            document.getElementById('description').value = editorContent;
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        /** Handles generate meta */
        document.getElementById('generate-meta-btn').addEventListener('click', function() {
            // Get form data
            const title = document.getElementById('title').value;
            const description = document.getElementById('editorTextArea').innerText;

            fetch("{{ route('generateMeta') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ title: title, description: description })
            })
            .then(response => response.json())
            .then(data => {
                // Populate the form fields with the generated suggestions
                document.getElementById('meta_title').value = data.meta_title || title;
                document.getElementById('meta_description').value = data.meta_description || description;
                document.getElementById('keywords').value = data.keywords || '';
            })
            .catch(error => {
                console.error('Error generating suggestions:', error);
            });
        })

        /** Uploads media and links it in editor */
        document.getElementById('mediaUpload')
            .addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const base64Media = e.target.result;
                    const fileType = file.type;

                    let routePath;
                    let mediaType = "img";
                    if (fileType.startsWith('image/')) {
                        routePath = "{{ route('uploadImage') }}";
                        mediaType = "img";
                    } else if (fileType.startsWith('video/')) {
                        routePath = "{{ route('uploadVideo') }}";
                        mediaType = "video";
                    }

                    if (routePath) {
                        // uploads media
                        fetch(routePath, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ media: base64Media })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // insert link into editor
                            insertMedia(data.path, mediaType);
                        })
                        .catch(error => {
                            console.error('Error uploading media:', error);
                        });
                    } else {
                        // insert invalid media
                        insertMedia('No media', mediaType);
                    }
                    
                }
                reader.readAsDataURL(file);
                event.target.value = "";
            }
        })

        /** Handles data-elements */
        const elements = document.querySelectorAll('.editor-btn');
        elements.forEach(element => {
            let command = element.dataset['element'];
            let value = null;
            element.addEventListener('click', () => {
                if (command === 'editorTextArea') {
                    command = 'styleWithCSS';
                    value = true;
                } else if (command === 'insertMediaFile') {
                    document.getElementById('mediaUpload').click();
                }
                document.execCommand(command, false, value);
            })
            
        })

        /** Insert media into editor */
        function insertMedia(src, mediaType) {
            const media = document.createElement(mediaType);
            media.src = src;
            media.style.maxWidth = "100%";
            media.style.height = "auto";
            media.style.display = "inline-block";
            media.style.margin = "10px 0";
            media.setAttribute("contenteditable", "false");

            if (mediaType === 'img') {
                media.classList.add("resizeable-image");
            } else if (mediaType === 'video') {
                media.classList.add("resizeable-video");
                media.controls = true;
            }
            

            const uniqueId = "media-" + Date.now();
            media.id =  uniqueId;

            const editorTextArea = document.getElementById("editorTextArea");
            const selection = window.getSelection();
            
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                if (range.startContainer === editorTextArea || range.startContainer.parentNode === editorTextArea) {
                    range.deleteContents();
                    range.insertNode(media);
                    range.setStartAfter(media);
                    range.setEndAfter(media);
                    selection.removeAllRanges();
                    selection.addRange(range);
                } else {
                    editorTextArea.appendChild(media);
                }
            } else {
                editorTextArea.appendChild(media);
            }

            media.onload = function () {
                adjustTextAreaHeight();
            }
        } 

        /** Start: Handle text area size */
        function adjustTextAreaHeight() {
            const editorTextArea = document.getElementById('editorTextArea');
            editorTextArea.style.height = "auto";
            editorTextArea.style.height = (parseInt(editorTextArea.scrollHeight) + 200) + "px";
        }

        document.getElementById('editorTextArea').addEventListener('input', function () {
            adjustTextAreaHeight();
        })

        document.getElementById('editorTextArea').addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' || e.key === 'Delete') {
                adjustTextAreaHeight();
            }
        })
        /** End: Handle text area size */
    </script>
</x-layout>