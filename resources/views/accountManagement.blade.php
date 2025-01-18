<x-layout>
    <span>Role: {{ Auth::user()->role }}</span>
    <br><br>

    @can('createUser', Auth::user())
        <a href="{{ route('show.showUsers') }}" class="btn">See all users</a>
        <br><br>
    @endcan

    <div class="metaHeader">
        <h1 class="my-4 text-3xl font-bold text-center">All Posts</h1>
        @can('createPost', Auth::user())
            <a href="{{ route('show.createPost') }}" class="btn">Create Post</a>
        @endcan
    </div>

    @if($posts->isEmpty())
        <p class="text-center">No posts available.</p>
    @else
        <div class="postWrapper">
            @foreach($posts as $post)
                <div class="postContainer">
                    <!-- Tags for Published, Draft -->
                    <div class="postTags">
                        @if($post->status == 'published')
                            <span class="tag published">Published</span>
                        @elseif($post->status == 'draft')
                            <span class="tag draft">Draft</span>
                        @endif
                    </div>
                    <div class="postTitle">{{ $post->title }}</div>
                    <div class="postMeta">
                        <span class="font-semibold">Edited By:</span> {{ $post->edited_by }}
                        <br>
                        <span class="font-semibold">Version created at:</span> {{ $post->published_at }}
                        <br>
                        @if(\Carbon\Carbon::parse($post->start_timestamp)->isFuture())
                            <span class="font-semibold">Scheduled date:</span> {{ $post->start_timestamp }}
                            <br>
                        @endif
                    </div>
                    <div class="postContent" id="content-{{ $post->id }}">{{ $post->description }}</div>

                    <!-- See full post -->
                    <a href="{{ route('show.editPost', ['id' => $post->post_version_id]) }}" class="seeMoreLink">Edit Page</a>
                </div>
            @endforeach
        </div>
    @endif
</x-layout>