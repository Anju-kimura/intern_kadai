<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?> | Wardrobe</title>
    <?php echo \Asset::css('style.css'); ?>
</head>
<body>
    <header class="site-header">
        <div class="site-header__inner">
            <span class="site-header__logo">Wardrobe</span>
            <?php if (\Auth::check()): ?>
                <?php echo \Html::anchor('clothes', '衣類'); ?>
                <span class="site-header__user">
                    <?php echo \Security::htmlentities(\Auth::get('username')); ?>
                    <?php echo \Form::open(array('action' => 'user/logout', 'class' => 'logout-form')); ?>
                        <button type="submit" class="link-button">ログアウト</button>
                    <?php echo \Form::close(); ?>
                </span>
            <?php endif; ?>
        </div>
    </header>
    <main class="container">
        <?php echo $content; ?>
    </main>
</body>
</html>