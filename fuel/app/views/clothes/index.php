<div id="closet"
     data-api-url="<?php echo \Uri::create('clothes/api_list'); ?>"
     data-login-url="<?php echo \Uri::create('user/login'); ?>">
    <div class="page-head">
        <h1>衣類一覧 <span class="count"><span data-bind="text: count"></span>件</span></h1>
        <?php echo \Html::anchor('clothes/create', '＋ 衣類を追加', array('class' => 'btn')); ?>
    </div>

    <div class="category-bar">
        <button type="button" class="chip"
                data-bind="click: function () { category(''); }, css: { 'is-active': category() === '' }">すべて</button>
        <?php foreach ($categories as $key => $label): ?>
            <button type="button" class="chip"
                    data-bind="click: function () { category('<?php echo $key; ?>'); }, css: { 'is-active': category() === '<?php echo $key; ?>' }"><?php echo $label; ?></button>
        <?php endforeach; ?>
    </div>

    <div class="filter-area">
        <div class="filter-item">色
            <label class="color-choice">
                <input type="radio" name="color" value="" data-bind="checked: color">
                <span class="color-all">すべて</span>
            </label>
            <?php foreach ($colors as $key => $color): ?>
                <label class="color-choice" title="<?php echo $color[0]; ?>">
                    <input type="radio" name="color" value="<?php echo $key; ?>" data-bind="checked: color">
                    <span class="color-swatch" style="background: <?php echo $color[1]; ?>" aria-label="<?php echo $color[0]; ?>"></span>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="filter-item">季節
            <?php foreach ($seasons as $key => $label): ?>
                <label>
                    <input type="checkbox" value="<?php echo $key; ?>" data-bind="checked: seasons">
                    <?php echo $label; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <label class="filter-item">購入時期
            <select data-bind="value: period">
                <option value="">すべて</option>
                <?php foreach ($periods as $key => $label): ?>
                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="filter-item">並び順
            <select data-bind="value: sort">
                <option value="desc">新しい順</option>
                <option value="asc">古い順</option>
            </select>
        </label>

        <button type="button" class="btn" data-bind="click: reset">リセット</button>
    </div>

    <p class="loading" data-bind="visible: loading">読み込み中…</p>
    <p class="error" data-bind="visible: errorMessage() !== '', text: errorMessage"></p>
    <p class="empty" data-bind="visible: !loading() && errorMessage() === '' && count() === 0">条件に合う衣類がありません</p>

    <div class="card-grid" data-bind="foreach: items">
        <a class="card" data-bind="attr: { href: view_url }">
            <div class="card-image">
                <!-- ko if: image_url !== '' -->
                <img data-bind="attr: { src: image_url, alt: name }">
                <!-- /ko -->
                <!-- ko if: image_url === '' -->
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#9CA3AF" stroke-width="1.5" aria-label="画像なし">
                    <path d="M12 6a2 2 0 1 1 2 2c-1 .5-2 1.2-2 2v1M12 11L3 17h18L12 11z"/>
                </svg>
                <!-- /ko -->
            </div>
            <div class="card-name" data-bind="text: name"></div>
            <div class="card-category">
                <span class="color-chip color-chip--small" data-bind="style: { background: color_css }, attr: { 'aria-label': color_name, title: color_name }"></span>
                <span data-bind="text: category_label"></span>
            </div>
        </a>
    </div>

    <?php echo \Html::anchor('clothes/create', '＋', array('class' => 'fab', 'aria-label' => '衣類を追加')); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/knockout@3.5.1/build/output/knockout-latest.js"></script>
<?php echo \Asset::js('clothes_list.js'); ?>