<div class="page-head">
    <h1>衣類一覧 <span class="count"><?php echo count($items); ?>件</span></h1>
    <?php echo \Html::anchor('clothes/create', '＋ 衣類を追加', array('class' => 'btn')); ?>
</div>

<div class="category-bar">
    <?php foreach ($chips as $chip): ?>
        <a class="chip<?php echo $chip['active'] ? ' is-active' : ''; ?>" href="<?php echo $chip['url']; ?>"><?php echo $chip['label']; ?></a>
    <?php endforeach; ?>
</div>

<form class="filter-area" method="get" action="<?php echo \Uri::create('clothes'); ?>">
    <?php if ($filters['category'] !== ''): ?>
        <input type="hidden" name="category" value="<?php echo $filters['category']; ?>">
    <?php endif; ?>

        <div class="filter-item">色
        <label class="color-choice">
            <input type="radio" name="color" value=""<?php echo $filters['color'] === '' ? ' checked' : ''; ?>>
            <span class="color-all">すべて</span>
        </label>
        <?php foreach ($colors as $key => $color): ?>
            <label class="color-choice" title="<?php echo $color[0]; ?>">
                <input type="radio" name="color" value="<?php echo $key; ?>"<?php echo $filters['color'] === $key ? ' checked' : ''; ?>>
                <span class="color-swatch" style="background: <?php echo $color[1]; ?>" aria-label="<?php echo $color[0]; ?>"></span>
            </label>
        <?php endforeach; ?>
    </div>

    <div class="filter-item">季節
        <?php foreach ($seasons as $key => $label): ?>
            <label>
                <input type="checkbox" name="seasons[]" value="<?php echo $key; ?>"<?php echo in_array($key, $filters['seasons'], true) ? ' checked' : ''; ?>>
                <?php echo $label; ?>
            </label>
        <?php endforeach; ?>
    </div>

    <label class="filter-item">購入時期
        <select name="period">
            <option value="">すべて</option>
            <?php foreach ($periods as $key => $label): ?>
                <option value="<?php echo $key; ?>"<?php echo $filters['period'] === $key ? ' selected' : ''; ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label class="filter-item">並び順
        <select name="sort">
            <option value="desc"<?php echo $filters['sort'] === 'desc' ? ' selected' : ''; ?>>新しい順</option>
            <option value="asc"<?php echo $filters['sort'] === 'asc' ? ' selected' : ''; ?>>古い順</option>
        </select>
    </label>

    <button type="submit" class="btn">絞り込む</button>
    <?php echo \Html::anchor('clothes', 'リセット'); ?>
</form>

<?php if (count($items) === 0): ?>
    <p class="empty"><?php echo $is_filtered ? '条件に合う衣類がありません' : 'まだ衣類が登録されていません'; ?></p>
<?php else: ?>
    <div class="card-grid">
        <?php foreach ($items as $item): ?>
            <a class="card" href="<?php echo \Uri::create('clothes/view/'.$item['id']); ?>">
                <div class="card-image">
                    <?php if ($item['image_url']): ?>
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#9CA3AF" stroke-width="1.5" aria-label="画像なし">
                            <path d="M12 6a2 2 0 1 1 2 2c-1 .5-2 1.2-2 2v1M12 11L3 17h18L12 11z"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="card-name"><?php echo $item['name']; ?></div>
                <div class="card-category"><?php echo $item['category_label']; ?></div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php echo \Html::anchor('clothes/create', '＋', array('class' => 'fab', 'aria-label' => '衣類を追加')); ?>