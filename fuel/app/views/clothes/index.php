<div class="page-head">
    <h1>衣類一覧 <span class="count"><?php echo count($items); ?>件</span></h1>
    <?php echo \Html::anchor('clothes/create', '＋ 衣類を追加', array('class' => 'btn')); ?>
</div>

<div class="category-bar">
    <button type="button" class="chip is-active">すべて</button>
    <?php foreach ($categories as $label): ?>
        <button type="button" class="chip"><?php echo $label; ?></button>
    <?php endforeach; ?>
</div>

<div class="filter-area">
    絞り込み（色・季節・購入時期・並び替え）は、Step 7で作ります
</div>

<?php if (count($items) === 0): ?>
    <p class="empty">まだ衣類が登録されていません</p>
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