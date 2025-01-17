<x-layout>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <h2>Login to your account</h2>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required></input>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required></input>
        
        <button type="submit" class="btn mt-4">Login</button>

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