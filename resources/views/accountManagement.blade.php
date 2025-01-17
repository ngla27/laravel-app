<x-layout>
    <span>Role: {{ Auth::user()->role }}</span>
    <br><br>

    @can('createUser', Auth::user())
        <a href="{{ route('createUser') }}" class="btn">Create User</a>
    @endcan

    @can('createPost', Auth::user())
        <a href="{{ route('createPost') }}" class="btn">Create User</a>
    @endcan
</x-layout>