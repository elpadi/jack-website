<?php

$can_edit_users = curry(array($site, 'checkPermission'), 'edit users');
$can_edit_acl = curry(array($site, 'checkPermission'), 'edit acl');

$app->get('/admin/users', $can_edit_users, function () use ($site, $app, $view) {
	$acl = $site->getService('acl');
	$app->render('admin/parts/users/users.twig', array(
		'users' => $site->fetchUsersData(),
		'roles' => array_map(function($role) use ($acl) {
			$perms = $acl->Roles->permissions($role['ID']);
			$role['Permissions'] = $perms ? array_map(function($permID) use ($acl) { return $acl->Permissions->getTitle($permID); }, $perms) : array();
			return $role;
		}, $acl->Roles->descendants(1)),
		'permissions' => $acl->Permissions->descendants(1),
		'section' => 'users',
		'page' => 'users',
	));
})->setName('admin/users');

$app->get('/admin/users/create', $can_edit_users, function () use ($site, $app, $view) {
	$app->render('admin/parts/users/add-user.twig', array(
		'roles' => $site->getService('acl')->Roles->descendants(1),
		'section' => 'users',
		'page' => 'create-user',
	));
})->setName('create-user');
$app->post('/admin/users/create', $can_edit_users, function () use ($site, $app, $view) {
	$user = $site->getService('newuser');
	$post = $app->request->post();
	$errors = array();
	try {
		if (!$user->addNew($site->getService('users_db'), $site->getService('acl'), array(
				'Password' => $post['plainpass'],
				'Email' => $post['email'],
				'Username' => $post['username'],
		), $post)) {
			foreach (array_merge($user->log->getErrors(), array_values($user->log->getFormErrors())) as $error) $errors[] = $error;
		}
	}
	catch (\Exception $e) {
		$errors[] = $e->getMessage();
	}
	if (count($errors) > 0) {
		$app->render('admin/parts/users/add-user.twig', array(
			'roles' => $site->getService('acl')->Roles->children(1),
			'section' => 'users',
			'page' => 'create-user',
			'values' => $post,
			'errors' => $errors,
		));
	}
	else {
		$app->flash('info', "New user '$post[username]' added.");
		$app->redirect($app->urlFor('admin/users'));
	}
});

$app->get('/admin/users/:id/change-password', $can_edit_users, function ($id) use ($site, $app, $view) {
	$user = $site->getService('user')->manageUser($id);
	if ($user) {
		$app->render('admin/parts/users/change-password.twig', array(
			'id' => $id,
			'username' => $user->Username,
			'section' => 'users',
			'page' => 'create-user',
		));
	}
	else {
		$app->flash('error', "User with id '$id' does not exist.");
		$app->redirect($app->urlFor('admin/users'));
	}
})->setName('passchange');
$app->post('/admin/users/:id/change-password', $can_edit_users, function ($id) use ($site, $app, $view) {
	$user = $site->getService('user')->manageUser($id);
	$post = $app->request->post();
	$errors = array();
	if ($user) {
		if ($data = $user->resetPassword($user->Email)) {
			if ($user->newPassword($data->Confirmation, array(
				'Password' => $post['plainpass'],
				'Password2' => $post['plainpass2'],
			))) {
				$app->flash('info', "Password successfully changed.");
				return $app->redirect($app->urlFor('admin/users'));
			}
		}
		foreach (array_merge($user->log->getErrors(), array_values($user->log->getFormErrors())) as $error) $errors[] = $error;
	}
	else {
		$app->flash('error', "User with id '$id' does not exist.");
	}
	$app->redirect($app->urlFor('admin/users'));
});


$app->get('/admin/users/role/:id', $can_edit_acl, function ($id) use ($site, $app, $view) {
	$acl = $site->getService('acl');
	$title = $acl->Roles->getTitle($id);
	if (!$title) {
		return $app->notFound();
	}
	$app->render('admin/parts/users/edit-role.twig', array(
		'title' => $title,
		'id' => $id,
		'permissions' => array_map(function($perm) use ($acl, $id) {
			$perm['Selected'] = $acl->Roles->hasPermission($id, $perm['ID']);
			return $perm;
		}, $acl->Permissions->descendants(1)),
		'section' => 'users',
		'page' => 'edit-role',
	));
})->setName('edit-role');
$app->post('/admin/users/role/:id', $can_edit_acl, function () use ($site, $app, $view) {
	$acl = $site->getService('acl');
	$post = $app->request->post();
	$id = $post['id'];
	$title = $acl->Roles->getTitle($id);
	if (!$title) {
		return $app->notFound();
	}
	$current = $acl->Roles->permissions($id);
	if ($current) {
		foreach ($current as $pID) {
			if (!in_array($pID, $post['permissions'])) {
				$acl->Roles->unassign($id, $pID);
			}
		}
	}
	foreach ($post['permissions'] as $pID) {
		$acl->Roles->assign($id, $pID);
	}
	$app->flash('info', "ACL role '$title' saved.");
	$app->redirect($app->urlFor('admin/users'));
});

$app->get('/admin/users/create-role', $can_edit_acl, function () use ($site, $app, $view) {
	$app->render('admin/parts/users/create-role.twig', array(
		'roles' => $site->getService('acl')->Roles->descendants(1),
		'section' => 'users',
		'page' => 'create-role',
	));
})->setName('create-role');
$app->post('/admin/users/create-role', $can_edit_acl, function () use ($site, $app, $view) {
	$acl = $site->getService('acl');
	$post = $app->request->post();
	try {
		$acl->Roles->add($post['name'], $post['description'], $post['parent']);
	}
	catch (\Exception $e) {
		$app->flash('error', "ACL role was not saved.");
		if (DEBUG) {
			$app->flash('error', $e->getFile().':'.$e->getLine().' - '.$e->getMessage());
		}
		$app->render('admin/parts/users/create-role.twig', array(
			'values' => $post,
		));
	}
	$app->flash('info', "ACL role '$post[name]' saved.");
	$app->redirect($app->urlFor('admin/users'));
});

$app->get('/admin/users/create-permission', $can_edit_acl, function () use ($site, $app, $view) {
	$app->render('admin/parts/users/create-permission.twig', array(
		'permissions' => $site->getService('acl')->Permissions->descendants(1),
		'section' => 'users',
		'page' => 'create-permission',
	));
})->setName('create-permission');
$app->post('/admin/users/create-permission', $can_edit_acl, function () use ($site, $app, $view) {
	$acl = $site->getService('acl');
	$post = $app->request->post();
	try {
		$acl->Permissions->add($post['name'], $post['description'], $post['parent']);
	}
	catch (\Exception $e) {
		$app->flash('error', "ACL permission was not saved.");
		if (DEBUG) {
			$app->flash('error', $e->getFile().':'.$e->getLine().' - '.$e->getMessage());
		}
		$app->render('admin/parts/users/create-permission.twig', array(
			'values' => $post,
		));
	}
	$app->flash('info', "ACL permission '$post[name]' saved.");
	$app->redirect($app->urlFor('admin/users'));
});

