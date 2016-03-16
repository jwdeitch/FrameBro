<?php if ( isset($username) ): ?>

	<div class="user-container">
		<div class="user-profile well">
			<p class="username"><h2><?= $username ?></h2></p>
			<p class="name"> Name: <?= $fname ?></p>
			<p class="email"> Email: <?=$email ?></p>
		</div>
	<?php if ($username !== self::getUser('username') && self::is_user_role(['Super Admin', 'Admin'])) : ?>
		<div class="delete-user-button">
			<?php if (!self::is_user_role('Super Admin') && !in_array('Super Admin', $roles) || self::is_user_role('Super Admin')) : ?>
				<button type="button" class="btn btn-danger delete-user" data-collect="<?= $username ?>">Delete this user</button>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<?php if ($username === self::getUser('username')) : ?>
		@partial("user.password_form")
	<?php endif; ?>

<?php else : ?>
	<?= renderUsers($data) ?>
<?php endif; ?>

<?php

function renderUsers($users) {

	$result = '<div class="items-directory"><ul class="item-list">';
	$result .= '<li class="labels"><span>Username</span><span>First Name</span><span>Last Name</span><span>Email</span></li><hr>';
	$order = 0;
	foreach ($users as $object) {
		$order++;
		$class = ($order % 2 == 0)? "item-even" : "item-odd";
		$result .= '<li class="' . $class . '">';
		$result .= '<span><a class="item-link" href="/users/' . $object->username . '"">' . $object->username . '</a></span>';
		$result .= '<span>' . $object->fname . '</span><span>' . $object->lname . '</span><span>' . $object->email . '</span>';
		$result .= '</li>';
	}
	$result .= "</ul></div>";

	return $result;
}
?>