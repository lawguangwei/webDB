<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="css/user_login.css" rel="stylesheet"/>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div id="logo">
    <div id="logo-web">WEB</div>
    <div id="logo-db">网盘</div>
</div>
<br/><br/><br/><br/>
<?=$content?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
