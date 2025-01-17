<x-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    @vite('resources/css/app.css')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Dynamic Meta Tags for SEO -->
    <meta name="title" content="{{ $post->meta_title }}">
    <meta name="description" content="{{ $post->meta_description }}">
    <meta name="keywords" content="{{ $post->keywords }}">
    
    <title>{{ $post->title }}</title>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-6 bg-white shadow-md rounded-lg mt-6 max-w-3xl">
        <!-- Post information -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>
        <div class="text-sm text-gray-500 mb-4">
            <span class="font-semibold">Published By:</span> {{ $post->published_by }}
            <br>
            <span class="font-semibold">Last Updated:</span> {{ $post->published_at }}
            <br>
        </div>
        <div class="text-lg text-gray-700 mb-6">{{ $post->description }}</div>

        <!-- Back to Posts Link -->
        <div class="mt-6">
            <a href="{{ route('home') }}" class="text-blue-500 hover:underline">Back to Posts</a>
        </div>
    </div>

</body>
</html>
</x-layout>