<div class="container">
    <div class="c_title">FAQ</div>

    <div class="noti_view">
        <div class="noti_view_t"><?= htmlspecialchars($faq->title) ?></div>
        <div class="noti_view_c"><?= date('Y-m-d H:i', strtotime($faq->created_at)) ?></div>

        <?php if (!empty($faq->category)) : ?>
            <div class="noti_view_c">카테고리: <?= htmlspecialchars($faq->category) ?></div>
        <?php endif; ?>

        <div class="noti_content">
            <?= nl2br(htmlspecialchars($faq->contents)) ?>
        </div>
    </div>

    <div class="noti_prev">
        <?php if ($prev_faq) : ?>
            <span>이전글</span><br />
            <a href="<?= base_url('manage/faq_view/' . $prev_faq->num) ?>">
                <?= htmlspecialchars($prev_faq->title) ?>
            </a>
        <?php else : ?>
            <span>이전글 없음</span>
        <?php endif; ?>
    </div>

    <div class="noti_next">
        <?php if ($next_faq) : ?>
            <span>다음글</span><br />
            <a href="<?= base_url('manage/faq_view/' . $next_faq->num) ?>">
                <?= htmlspecialchars($next_faq->title) ?>
            </a>
        <?php else : ?>
            <span>다음글 없음</span>
        <?php endif; ?>
    </div>

    <div style="margin-top: 20px; display: flex; gap: 10px;">
        <div class="noti_view_bt">
            <a href="<?= base_url('manage/faq_edit/' . $faq->num); ?>">수정하기</a>
        </div>
        <div class="noti_view_bt">
            <a href="<?= base_url('manage/faq'); ?>">확인</a>
        </div>
    </div>
    <div class="clear"></div>
</div>

