<h1>ログイン</h1>

<?php if ($error !== ''): ?>
    <p class="form-error"><?php echo $error; ?></p>
<?php endif; ?>

<?php echo \Form::open('user/login'); ?>
    <div class="form-field">
        <?php echo \Form::label('メールアドレス', 'email'); ?>
        <?php echo \Form::input('email', $email, array('id' => 'email', 'type' => 'email')); ?>
    </div>
    <div class="form-field">
        <?php echo \Form::label('パスワード', 'password'); ?>
        <?php echo \Form::password('password', '', array('id' => 'password')); ?>
    </div>
    <?php echo \Form::submit('submit', 'ログイン', array('class' => 'btn')); ?>
<?php echo \Form::close(); ?>

<p><?php echo \Html::anchor('user/signup', '新規登録'); ?></p>