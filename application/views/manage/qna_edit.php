<div class="container">
    <div class="c_title">문의하기</div>

    <div class="noti_view">
        <!-- 제목 -->
        <div class="noti_view_t"><?= htmlspecialchars($qna->title) ?></div>

        <!-- 작성일 -->
        <div class="noti_view_c">작성일: <?= date('Y-m-d H:i', strtotime($qna->created_at)) ?></div>

        <!-- 질문 내용 -->
        <div class="noti_content">
            <?= nl2br(htmlspecialchars($qna->contents)) ?>
        </div>

        <!-- 답변 영역 -->
        <div class="qna_answer_section" style="margin-top:30px; padding:20px; background:#f9f9f9; border:1px solid #ddd; border-radius: 8px;">
            <h4 style="margin-bottom: 15px;">📢 답변</h4>

            <?php if (!empty($qna->answer)) : ?>
                <div style="margin-bottom: 8px;"><strong>담당자:</strong> <?= htmlspecialchars($qna->admin_id) ?></div>
                <div style="margin-bottom: 12px;"><strong>답변일:</strong> <?= date('Y-m-d H:i', strtotime($qna->answer_date)) ?></div>
                <div><?= nl2br(htmlspecialchars($qna->answer)) ?></div>
            <?php else : ?>
                <!-- 답변 작성 폼 -->
                <form id="qnaForm" action="<?= base_url('manage/qna_answer/' . $qna->num); ?>" method="post">
                    <textarea id="answerInput" name="answer" placeholder="답변을 입력하세요" required
                        style="width:100%; height:150px; margin-bottom:10px; padding:10px; font-size:14px;"></textarea>

                    <div style="margin-top:10px; display: flex; gap: 10px;">
                        <div class="noti_view_bt">
                            <a href="javascript:void(0);" onclick="submitAnswerForm()" class="submit_btn">답변 등록</a>
                        </div>
                        <div class="noti_view_bt">
                            <a href="<?= base_url('manage/qna'); ?>" class="cancel_btn">취소</a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function submitAnswerForm() {
    const answer = document.getElementById('answerInput').value.trim();
    if (answer === '') {
        alert('답변을 입력하세요.');
        return;
    }
    document.getElementById('qnaForm').submit();
}
</script>