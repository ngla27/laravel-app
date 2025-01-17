<x-layout>
  <form action="{{ route('createUser') }}" method="POST">
    @csrf
    <h2>Create a user</h2>
    <label for="name">Name:</label>
    <input type="text" name="name" required></input>

    <label for="email">Email:</label>
    <input type="text" name="email" required></input>

    <label for="password">Password:</label>
    <input type="password" name="password" required></input>

    <label for="confirm_password">Confirm password:</label>
    <input type="password" name="password_confirmation" required></input>

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