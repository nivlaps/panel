<?php

class UsersController extends Controller {

  public function index() {

    $users = $this->users()->map(function($user) {
      return api::user($user);
    });

    return response::json(array_values($users->toArray()));

  }

  public function create() {

    $form = $this->form();
    $data = $form->toArray();

    if($data['password'] !== $data['passwordConfirmation']) {
      return response::error(l('users.form.error.password.confirm'));
    }

    unset($data['passwordConfirmation']);

    try {
      $user = $this->users()->create($data);
      return response::success('success');
    } catch(Exception $e) {
      return response::error(l('users.form.error.create'));
    }

  }

  public function update($username) {

    $user = $this->user($username);

    if(!$user) {
      return response::error(l('users.edit.error.missing'));
    } else {

      $form = $this->form($user);
      $data = $form->toArray();

      if(str::length($data['password']) > 0) {
        if($data['password'] !== $data['passwordConfirmation']) {
          return response::error(l('users.form.error.password.confirm'));
        }
      } else {
        unset($data['password']);
      }

      unset($data['passwordConfirmation']);

      if($user->update($data)) {
        return response::success('success');
      } else {
        return response::error(l('users.form.error.update'));
      }

    }

  }

  public function delete($username) {

    $user = $this->user($username);

    if(!$user) {
      return response::error(l('users.error.missing'));
    }

    try {
      $user->delete();
      return response::success('success');
    } catch(Exception $e) {
      return response::error(l('users.delete.error'));
    }

  }

  protected function users() {
    return app::$site->users();
  }

  protected function user($username) {
    return app::$site->users()->find($username);
  }

  protected function form($user = null) {
    $mode = $user ? 'edit' : 'add';
    return app::form('user.' . $mode);
  }

}