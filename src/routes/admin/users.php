<?php

$can_edit_users = curry(array($site, 'checkPermission'), 'edit users');
$can_edit_acl = curry(array($site, 'checkPermission'), 'edit acl');

$app->get('/admin/users', $can_edit_users, function () use ($site, $app, $view) {
	$acl = $site->getService('acl');
	$app->render('admin/parts/users.twig', array(
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
	$app->render('admin/parts/user-add.twig', array(
		'roles' => $site->getService('acl')->Roles->descendants(1),
		'section' => 'users',
		'page' => 'create-user',
	));
})->setName('admin/create-user');
$app->post('/admin/users/create', $can_edit_users, function () use ($site, $app, $view) {
	$user = $site->getService('user')->manageUser(1);
	$post = $app->request->post();
	try {
		$user->addNew($site->getService('users_db'), $site->getService('acl'), array(
			'Password' => $post['plainpass'],
			'Email' => $post['email'],
			'Username' => $post['username'],
		), $post);
	}
	catch (\Exception $e) {
		$error = $e->getFile().':'.$e->getLine().' - '.$e->getMessage();
		$site->getService('loggers')->error->addError($error);
		if (DEBUG) {
			d($user->log->getFullConsole(), $e->getTrace());
		}
		else {
			$app->flash('error', "User was not created.");
			$app->render('admin/parts/user-add.twig', array(
				'values' => $post,
				'roles' => $site->getService('acl')->Roles->children(1),
			));
		}
		exit(1);
	}
	$app->flash('info', "New user '$post[username]' added.");
	$app->redirect($app->urlFor('admin/users'));
});

$app->get('/admin/users/role/:id', $can_edit_acl, function ($id) use ($site, $app, $view) {
	$acl = $site->getService('acl');
	$title = $acl->Roles->getTitle($id);
	if (!$title) {
		return $app->notFound();
	}
	$app->render('admin/parts/edit-role.twig', array(
		'title' => $title,
		'id' => $id,
		'permissions' => array_map(function($perm) use ($acl, $id) {
			$perm['Selected'] = $acl->Roles->hasPermission($id, $perm['ID']);
			return $perm;
		}, $acl->Permissions->descendants(1)),
		'section' => 'users',
		'page' => 'edit-role',
	));
})->setName('admin/role');
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
	$app->render('admin/parts/create-role.twig', array(
		'roles' => $site->getService('acl')->Roles->descendants(1),
		'section' => 'users',
		'page' => 'create-role',
	));
})->setName('admin/create-role');
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
		$app->render('admin/parts/create-role.twig', array(
			'values' => $post,
		));
	}
	$app->flash('info', "ACL role '$post[name]' saved.");
	$app->redirect($app->urlFor('admin/users'));
});

$app->get('/admin/users/create-permission', $can_edit_acl, function () use ($site, $app, $view) {
	$app->render('admin/parts/create-permission.twig', array(
		'permissions' => $site->getService('acl')->Permissions->descendants(1),
		'section' => 'users',
		'page' => 'create-permission',
	));
})->setName('admin/create-permission');
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
		$app->render('admin/parts/create-permission.twig', array(
			'values' => $post,
		));
	}
	$app->flash('info', "ACL permission '$post[name]' saved.");
	$app->redirect($app->urlFor('admin/users'));
});

