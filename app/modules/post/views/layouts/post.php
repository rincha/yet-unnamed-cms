<?php

use app\modules\post\assets\PostAsset;

/* @var $this \app\common\web\View */
/* @var $content string */

PostAsset::register($this);
?>
<?php $this->beginContent('@app/views/layouts/columns.php'); ?>
<?= $content ?>
<?php $this->endContent(); ?>
