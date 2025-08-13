<div class="container">
    <div class="b_title">문의하기<img src="/garage/images/ic_chat.png" /></div>
    <div class="noti_view">
        <div class="noti_view_t"><?= htmlspecialchars($qna->title) ?></div>

        <div class="noti_view_c">작성일: <?= date('Y-m-d H:i', strtotime($qna->created_at)) ?></div>

        <div class="noti_content">
            <?= nl2br(htmlspecialchars($qna->contents)) ?>
        </div>

        <!-- 답변 영역 -->
        <div class="qna_answer_section" style="margin-top:30px; padding:15px; background:#f9f9f9; border:1px solid #ddd;">
            <h4>📢 답변</h4>

            <?php if (!empty($qna->answer)) : ?>
                <div><strong>담당자:</strong> <?= htmlspecialchars($qna->admin_id) ?></div>
                <div><strong>답변일:</strong> <?= date('Y-m-d H:i', strtotime($qna->answer_date)) ?></div>
                <div style="margin-top:10px;">
                    <?= nl2br(htmlspecialchars($qna->answer)) ?>
                </div>
            <?php else : ?>
                <div>❗ 아직 답변이 등록되지 않았습니다.</div>
            <?php endif; ?>
        </div>

        <div class="noti_view_bt" style="margin-top:20px;">
            <a href="<?= base_url('web/qna'); ?>">목록으로</a>
        </div>

        <div class="noti_prev" style="float:left; margin-top: 30px;">
            <?php if ($prev_qna) : ?>
                <span>이전글</span><br />
                <a href="<?= base_url('web/qna_view/' . $prev_qna->num) ?>">
                    <?= htmlspecialchars($prev_qna->title) ?>
                </a>
            <?php else : ?>
                <span>이전글 없음</span>
            <?php endif; ?>
        </div>

        <div class="noti_next" style="float:right; margin-top: 30px;">
            <?php if ($next_qna) : ?>
                <span>다음글</span><br />
                <a href="<?= base_url('web/qna_view/' . $next_qna->num) ?>">
                    <?= htmlspecialchars($next_qna->title) ?>
                </a>
            <?php else : ?>
                <span>다음글 없음</span>
            <?php endif; ?>
        </div>

        <div class="clear"></div>

    </div>
</div>