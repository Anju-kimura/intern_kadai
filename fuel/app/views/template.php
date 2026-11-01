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
        <?php // flashは、次の画面で1回だけ読める、sessionの値。読むと消える ?>
        <?php $flash = \Session::get_flash('success'); ?>
        <?php if ($flash): ?>
            <p class="flash" role="status"><?php echo \Security::htmlentities($flash); ?></p>
        <?php endif; ?>
        <?php echo $content; ?>
    </main>
</body>
</html>