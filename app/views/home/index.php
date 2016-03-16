<?php if (isset($data->docs)): ?>
	<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
		<?= $this->renderDocuments($data->docs, $options = true) ?>
	<?php else: ?>
		<?= $this->renderDocuments($data->docs) ?>
	<?php endif; ?>
<?php endif; ?>
