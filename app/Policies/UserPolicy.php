<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy {
  public function createUser(User $user): bool {
    return $user->role === 'admin';
  }

  public function editPost(User $user): bool {
    return in_array($user->role, ['author', 'editor']);
  }

  public function createPost(User $user): bool {
    return in_array($user->role, ['author', 'editor']);
  }

  public function publishPost(User $user): bool {
    return in_array($user->role, ['admin', 'editor']);
  }
}
