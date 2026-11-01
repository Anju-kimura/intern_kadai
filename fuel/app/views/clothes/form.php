<div id="clothes-form"
     data-initial="<?php echo $initial; ?>"
     data-rules="<?php echo $rules; ?>"
     data-save-url="<?php echo \Uri::create('clothes/api_save'); ?>"
     data-back-url="<?php echo $back_url; ?>"
     data-csrf-key="<?php echo $csrf_key; ?>"
     data-csrf-token="<?php echo $csrf_token; ?>">

    <h1><?php echo $heading; ?></h1>
    <p class="error" data-bind="text: errors()._all"></p>

    <form data-bind="submit: save">
        <div class="form-row">
            <label for="name">名称</label>
            <input type="text" id="name" data-bind="textInput: name">
            <span class="error" data-bind="text: errors().name"></span>
        </div>

        <div class="form-row">
            <label for="category">カテゴリ</label>
            <select id="category" data-bind="value: category">
                <option value="">選択してください</option>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
            <span class="error" data-bind="text: errors().category"></span>
        </div>

        <div class="form-row">
            <label for="brand">ブランド</label>
            <input type="text" id="brand" data-bind="textInput: brand">
            <span class="error" data-bind="text: errors().brand"></span>
        </div>

        <div class="form-row">色
            <?php foreach ($colors as $key => $color): ?>
                <label class="color-choice" title="<?php echo $color[0]; ?>">
                    <input type="radio" name="color" value="<?php echo $key; ?>" data-bind="checked: color">
                    <span class="color-swatch" style="background: <?php echo $color[1]; ?>" aria-label="<?php echo $color[0]; ?>"></span>
                </label>
            <?php endforeach; ?>
            <span class="error" data-bind="text: errors().color"></span>
        </div>

        <div class="form-row">
            <label for="purchase_date">購入日</label>
            <input type="date" id="purchase_date" data-bind="value: purchaseDate, enable: !purchaseUnknown()">
            <label>
                <input type="checkbox" data-bind="checked: purchaseUnknown">
                購入時期は不明
            </label>
            <span class="error" data-bind="text: errors().purchase_date"></span>
        </div>

        <div class="form-row">季節
            <?php foreach ($seasons as $key => $label): ?>
                <label>
                    <input type="checkbox" value="<?php echo $key; ?>" data-bind="checked: seasons">
                    <?php echo $label; ?>
                </label>
            <?php endforeach; ?>
            <span class="error" data-bind="text: errors().seasons"></span>
        </div>

        <div class="form-row">
            <label for="memo">メモ</label>
            <textarea id="memo" rows="4" data-bind="textInput: memo"></textarea>
            <span class="error" data-bind="text: errors().memo"></span>
        </div>

        <div class="form-row">
            <label for="image">画像（任意）</label>
            <input type="file" id="image" accept="image/*" data-bind="event: { change: onImageChange }">
            <span class="error" data-bind="text: errors().image"></span>
            <div class="image-preview">
                <!-- ko if: previewUrl() !== '' -->
                <img alt="選んだ画像" data-bind="attr: { src: previewUrl }">
                <!-- /ko -->
            </div>
        </div>

        <div class="detail-actions">
            <button type="submit" class="btn" data-bind="enable: !submitting(), text: submitting() ? '保存中…' : '<?php echo $submit_label; ?>'"><?php echo $submit_label; ?></button>
            <a class="btn" href="<?php echo $back_url; ?>">キャンセル</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/knockout@3.5.1/build/output/knockout-latest.js"></script>
<?php echo \Asset::js('clothes_form.js'); ?>