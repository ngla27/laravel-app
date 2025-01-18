<x-layout>
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

    <label class="required" for="description">Description</label>
    <input type="textarea" id="description" name="description" value="{{ old('description', isset($post) ? $post->description : '') }}" required></input>

    <label class="required" for="start_timestamp">Start Date Time</label>
    <input type="datetime-local" id="start_timestamp" name="start_timestamp" value="{{ old('start_timestamp', isset($post) ? \Carbon\Carbon::parse($post->start_timestamp)->format('Y-m-d\TH:i') : '') }}" required>
    
    <br>
    <div class="metaContainer">
        <div class="metaHeader">
            <h2>Meta data</h2>
            @can('editPost', Auth::user())
                <button type="button" class="btn" onclick="generateSuggestions()">Generate</button>
            @endcan
        </div>
        <label class="required" for="meta_title">Meta title</label>
        <input class="bg-gray-200" type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', isset($post) ? $post->meta_title : '') }}" required></input>

        <label class="required" for="meta_description">Meta Description</label>
        <input class="bg-gray-200" type="text" id="meta_description" name="meta_description" value="{{ old('meta_description', isset($post) ? $post->meta_description : '') }}" required></input>

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
        // Function to trigger an AJAX request to generate meta information
        function generateSuggestions() {
            // Get form data
            const title = document.getElementById('title').value;
            const description = document.getElementById('description').value;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const APP_URL = "http://localhost/laravel-app/public"
            fetch(`${APP_URL}/generateMeta`, {
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
        }
    </script>
</x-layout>