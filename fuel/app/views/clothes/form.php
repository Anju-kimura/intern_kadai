<h1>衣類を登録する</h1>

<?php foreach ($errors as $message): ?>
    <p class="error"><?php echo $message; ?></p>
<?php endforeach; ?>

<?php echo \Form::open(array('action' => 'clothes/create', 'enctype' => 'multipart/form-data')); ?>

    <div class="form-row">
        <label for="name">名称（必須）</label>
        <input type="text" id="name" name="name" maxlength="100" value="<?php echo isset($old['name']) ? $old['name'] : ''; ?>">
    </div>

    <div class="form-row">
        <label for="category">カテゴリ（必須）</label>
        <select id="category" name="category">
            <option value="">選択してください</option>
            <?php foreach ($categories as $key => $label): ?>
                <option value="<?php echo $key; ?>"<?php echo (isset($old['category']) and $old['category'] === $key) ? ' selected' : ''; ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-row">
        <label for="brand">ブランド</label>
        <input type="text" id="brand" name="brand" maxlength="100" value="<?php echo isset($old['brand']) ? $old['brand'] : ''; ?>">
    </div>

    <div class="form-row">
        <span>色（必須）</span>
        <div class="color-list">
            <?php foreach ($colors as $key => $color): ?>
                <label class="color-dot" title="<?php echo $color[0]; ?>">
                    <input type="radio" name="color" value="<?php echo $key; ?>"<?php echo (isset($old['color']) and $old['color'] === $key) ? ' checked' : ''; ?>>
                    <span style="background: <?php echo $color[1]; ?>"></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-row">
        <label for="purchase_date">購入日</label>
        <input type="date" id="purchase_date" name="purchase_date" value="<?php echo isset($old['purchase_date']) ? $old['purchase_date'] : ''; ?>">
        <label>
            <input type="checkbox" name="purchase_unknown" value="1"<?php echo ! empty($old['purchase_unknown']) ? ' checked' : ''; ?>>
            購入時期は不明
        </label>
    </div>

    <div class="form-row">
        <span>季節</span>
        <?php $old_seasons = (isset($old['seasons']) and is_array($old['seasons'])) ? $old['seasons'] : array(); ?>
        <?php foreach ($seasons as $key => $label): ?>
            <label>
                <input type="checkbox" name="seasons[]" value="<?php echo $key; ?>"<?php echo in_array($key, $old_seasons, true) ? ' checked' : ''; ?>>
                <?php echo $label; ?>
            </label>
        <?php endforeach; ?>
    </div>

    <div class="form-row">
        <label for="memo">メモ</label>
        <textarea id="memo" name="memo" rows="4" maxlength="500"><?php echo isset($old['memo']) ? $old['memo'] : ''; ?></textarea>
    </div>

    <div class="form-row">
        <label for="image">画像（任意）</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($image_error)): ?>
          <p class="error"><?php echo $image_error; ?></p>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn">登録する</button>

<?php echo \Form::close(); ?>