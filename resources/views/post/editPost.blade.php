<x-layout>
    <form action="{{ route('createPost') }}" method="POST">
    @csrf
        <h2>Create/Edit Post</h2>

    <label class="required" for="title">Title</label>
    <input type="text" id="title" name="title" value="{{ old('title') }}" required></input>

    <label class="required" for="description">Description</label>
    <input type="textarea" id="description" name="description" value="{{ old('description') }}" required></input>

    <label class="required" for="start_timestamp">Start Date Time</label>
    <input type="datetime-local" id="start_timestamp" name="start_timestamp" value="{{ old('start_timestamp') }}" required>
    
    <br>
    <div class="metaContainer">
        <div class="metaHeader">
            <h2>Meta data</h2><a href="#" class="btn">Generate</a>
        </div>
        <label class="required" for="meta_title">Meta title</label>
        <input class="bg-gray-200" type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" required></input>

        <label class="required" for="meta_description">Meta Description</label>
        <input class="bg-gray-200" type="text" id="meta_description" name="meta_description" value="{{ old('meta_description') }}" required></input>

        <label class="required" for="keywords">Keywords</label>
        <input class="bg-gray-200" type="text" id="keywords" name="keywords" value="{{ old('keywords') }}" required></input>
    </div>
    
    @can('createPost', Auth::user())
        <button type="submit" class="btn" value="create">Create</button>
    @endcan
    @can('publishPost', Auth::user())
        <button type="submit" class="btn" value="createAndPublish">Create and Publish</button>
    @endcan

    <!-- validation -->
    @if($errors->any())
        <ul class="px-4 py-2 bg-red-100">
            @foreach($errors->all() as $error)
            <li class="my-2 text-red-500">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
  </form>
</x-layout>