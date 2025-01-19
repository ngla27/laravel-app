<x-layout>
    @section('title','Create User')
    
    <form action="{{ route('createUser') }}" method="POST">
        @csrf
        <h2>Create a user</h2>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required></input>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="{{ old('email') }}" required></input>

        <label for="role">Role:</label>
        <select name="role" id="role">
            <option value="admin" {{ old('status') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="editor" {{ old('status') == 'editor' ? 'selected' : '' }}>Editor</option>
            <option value="author" {{ old('status') == 'author' ? 'selected' : '' }}>Author</option>
        </select>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required></input>

        <label for="confirm_password">Confirm password:</label>
        <input type="password" id="confirm_password" name="password_confirmation" required></input>

        <button type="submit" class="btn">Create</button>

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