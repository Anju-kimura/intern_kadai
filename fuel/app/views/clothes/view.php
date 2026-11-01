<p><?php echo \Html::anchor('clothes', '← 衣類一覧へ戻る'); ?></p>

<div class="detail">
    <div class="detail-image">
        <?php if ($item['image_url']): ?>
            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
        <?php else: ?>
            <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="#9CA3AF" stroke-width="1.5" aria-label="画像なし">
                <path d="M12 6a2 2 0 1 1 2 2c-1 .5-2 1.2-2 2v1M12 11L3 17h18L12 11z"/>
            </svg>
        <?php endif; ?>
    </div>

    <div class="detail-body">
        <h1><?php echo $item['name']; ?></h1>

        <dl class="detail-list">
            <dt>カテゴリ</dt>
            <dd><?php echo $item['category_label']; ?></dd>

            <dt>ブランド</dt>
            <dd><?php echo $item['brand']; ?></dd>

            <dt>色</dt>
            <dd>
                <span class="color-chip"
                      style="background: <?php echo $item['color_css']; ?>"
                      aria-label="<?php echo $item['color_name']; ?>"></span>
            </dd>

            <dt>購入時期</dt>
            <dd><?php echo $item['purchase']; ?></dd>

            <dt>季節</dt>
            <dd><?php echo $item['seasons'] !== '' ? $item['seasons'] : '未設定'; ?></dd>

            <dt>メモ</dt>
            <dd><?php echo $item['memo'] !== '' ? nl2br($item['memo']) : 'なし'; ?></dd>
        </dl>

        <div class="detail-actions">
            <?php echo \Html::anchor('clothes/edit/'.$item['id'], '編集', array('class' => 'btn')); ?>
            <button type="button" class="btn btn-danger" disabled>削除</button>
        </div>
    </div>
</div>