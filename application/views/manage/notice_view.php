<div class="container">
    <div class="c_title">공지사항</div>

    <div class="noti_view">
        <div class="noti_view_t"><?= htmlspecialchars($notice->title) ?></div>
        <div class="noti_view_c"><?= date('Y-m-d H:i', strtotime($notice->created_at)) ?></div>

        <?php if ($notice->attachment) : ?>
            <div class="noti_attach" style="margin-top: 20px; display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 500;">첨부파일</span>
                <a class="download_btn small"
                    href="<?= base_url('garage/attachment/3_notice/' . $notice->attachment) ?>"
                    download>
                    <?= htmlspecialchars($notice->attachment) ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="noti_content">
            <?= nl2br(htmlspecialchars($notice->contents)) ?>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <div class="noti_view_bt">
                <a href="<?= base_url('manage/notice_edit/' . $notice->num); ?>">수정하기</a>
            </div>
            <div class="noti_view_bt">
                <a href="<?= base_url('manage/notice'); ?>">확인</a>
            </div>
        </div>
    </div>

    <div class="noti_prev">
        <?php if (isset($prev_notice)) : ?>
            <span>이전글</span><br />
            <a href="<?= base_url('manage/notice_view/' . $prev_notice->num) ?>">
                <?= htmlspecialchars($prev_notice->title) ?>
            </a>
        <?php else : ?>
            <span>이전글 없음</span>
        <?php endif; ?>
    </div>

    <div class="noti_next">
        <?php if (isset($next_notice)) : ?>
            <span>다음글</span><br />
            <a href="<?= base_url('manage/notice_view/' . $next_notice->num) ?>">
                <?= htmlspecialchars($next_notice->title) ?>
            </a>
        <?php else : ?>
            <span>다음글 없음</span>
        <?php endif; ?>
    </div>

    <div class="clear"></div>
</div>

<style>
    .download_btn {
        display: inline-block;
        padding: 10px 16px;
        background-color: #f0f0f5;
        color: #333;
        text-decoration: none;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .download_btn:hover {
        background-color: #e6e6f0;
        border-color: #999;
        color: #000;
    }

    .download_btn.small {
        padding: 6px 10px;
        font-size: 13px;
    }
</style>