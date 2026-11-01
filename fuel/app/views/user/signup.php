<h1>新規登録</h1>

<?php echo \Form::open('user/signup'); ?>
    <div class="form-field">
        <?php echo \Form::label('ユーザー名', 'username'); ?>
        <?php echo \Form::input('username', \Arr::get($old, 'username', ''), array('id' => 'username')); ?>
        <?php if (isset($errors['username'])): ?>
            <p class="form-error"><?php echo $errors['username']; ?></p>
        <?php endif; ?>
    </div>
    <div class="form-field">
        <?php echo \Form::label('メールアドレス', 'email'); ?>
        <?php echo \Form::input('email', \Arr::get($old, 'email', ''), array('id' => 'email', 'type' => 'email')); ?>
        <?php if (isset($errors['email'])): ?>
            <p class="form-error"><?php echo $errors['email']; ?></p>
        <?php endif; ?>
    </div>
    <div class="form-field">
        <?php echo \Form::label('パスワード', 'password'); ?>
        <?php echo \Form::password('password', '', array('id' => 'password')); ?>
        <p class="form-hint">8文字以上で、英字と数字を含めてください</p>
        <?php if (isset($errors['password'])): ?>
            <p class="form-error"><?php echo $errors['password']; ?></p>
        <?php endif; ?>
    </div>
    <div class="form-field">
        <?php echo \Form::label('パスワード（確認）', 'password_confirm'); ?>
        <?php echo \Form::password('password_confirm', '', array('id' => 'password_confirm')); ?>
        <?php if (isset($errors['password_confirm'])): ?>
            <p class="form-error"><?php echo $errors['password_confirm']; ?></p>
        <?php endif; ?>
    </div>
    <?php echo \Form::submit('submit', '登録', array('class' => 'btn')); ?>
<?php echo \Form::close(); ?>

<p><?php echo \Html::anchor('user/login', 'ログイン画面へ'); ?></p>