<x-layout>
    @section('head')
        <meta name="title" content="{{ $post->meta_title }}">
        <meta name="description" content="{{ $post->meta_description }}">
        <meta name="keywords" content="{{ $post->keywords }}">
    @endsection

    @section('title', $post->title)

    <body class="bg-gray-100">

        <div class="container mx-auto p-6 bg-white shadow-md rounded-lg mt-6 max-w-3xl">
            <!-- Post information -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
            <div class="text-sm text-gray-500 mb-4">
                <span class="font-semibold">Edited By:</span> {{ $post->edited_by }}
                <br>
                <span class="font-semibold">Last Updated:</span> {{ $post->published_at }}
                <br>
            </div>
            <div class="text-lg mb-6">{!! $post->description !!}</div>

            <!-- Back to Posts Link -->
            <div class="mt-6">
                <a href="{{ route('home') }}" class="text-blue-500 hover:underline">Back to Posts</a>
            </div>
        </div>
    </body>
</x-layout>