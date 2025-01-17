<x-layout>
  @auth
    Role: {{ Auth::user()->role }}
  @endauth
</x-layout>