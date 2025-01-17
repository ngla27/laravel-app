<x-layout>
<div class="container">
    <h1 class="my-4 text-3xl font-bold text-center">All Posts</h1>

    @if($posts->isEmpty())
        <p class="text-center">No posts available.</p>
    @else
        <div class="postWrapper">
            @foreach($posts as $post)
                <div class="postContainer">
                    <div class="postTitle">{{ $post->title }}</div>
                    <div class="postMeta">
                        <span class="font-semibold">Published By:</span> {{ $post->updated_by }}
                        <br>
                        <span class="font-semibold">Last Updated:</span> {{ $post->published_at }}
                        <br>
                    </div>
                    <div class="postContent" id="content-{{ $post->id }}">{{ $post->description }}</div>

                    <!-- See full post -->
                    <a href="{{ route('show.showPost', ['id' => $post->id]) }}" class="seeMoreLink">See more</a>
                </div>
            @endforeach
        </div>
    @endif
</div>
</x-layout>