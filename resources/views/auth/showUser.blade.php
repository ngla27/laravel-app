<x-layout>
<div class="container">
    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('show.account') }}" class="seeMoreLink">Back to Account</a>
    <div class="metaHeader">
        <h1 class="my-4 text-3xl font-bold text-center">Users</h1>
        @can('createUser', Auth::user())
            <a href="{{ route('show.createUser') }}" class="btn">Create User</a>
        @endcan
    </div>

    @if($users->isEmpty())
        <p class="text-center">No users available.</p>
    @else
        <div class="postWrapper">
            @foreach($users as $user)
                <div class="postContainer">
                    <span class="font-semibold">Name:</span> {{ $user->name }}
                    <br>
                    <span class="font-semibold">Email:</span> {{ $user->email }}
                    <br>
                    <span class="font-semibold">Role:</span> {{ $user->role }}
                </div>
            @endforeach
        </div>
    @endif
</div>
</x-layout>