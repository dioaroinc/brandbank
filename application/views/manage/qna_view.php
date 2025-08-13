<?php
$edit_mode = $this->input->get('edit'); // ?edit=1 여부 확인
?>
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
                <?php if ($edit_mode) : ?>
                    <!-- 수정 모드 -->
                    <form id="editForm" action="<?= base_url('manage/qna_answer_update/' . $qna->num); ?>" method="post">
                        <textarea name="answer" required style="width:100%; height:150px; padding:10px; font-size:14px;"><?= htmlspecialchars($qna->answer) ?></textarea>

                        <div style="margin-top:10px; display: flex; gap: 10px;">
                            <div class="noti_view_bt">
                                <a href="javascript:void(0);" onclick="document.getElementById('editForm').submit();" class="submit_btn">수정 완료</a>
                            </div>
                            <div class="noti_view_bt">
                                <a href="<?= base_url('manage/qna_view/' . $qna->num); ?>" class="cancel_btn">취소</a>
                            </div>
                        </div>
                    </form>
                <?php else : ?>
                    <!-- 일반 보기 모드 -->
                    <div style="margin-bottom: 8px;"><strong>담당자:</strong> <?= htmlspecialchars($qna->admin_id) ?></div>
                    <div style="margin-bottom: 12px;"><strong>답변일:</strong> <?= date('Y-m-d H:i', strtotime($qna->answer_date)) ?></div>
                    <div style="margin-bottom: 20px;"><?= nl2br(htmlspecialchars($qna->answer)) ?></div>

                    <div style="display: flex; gap: 10px;">
                        <div class="noti_view_bt">
                            <a href="<?= base_url('manage/qna_view/' . $qna->num . '?edit=1'); ?>" class="submit_btn">수정하기</a>
                        </div>
                        <div class="noti_view_bt">
                            <a href="<?= base_url('manage/qna'); ?>" class="cancel_btn">취소</a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <!-- 최초 답변 작성 -->
                <form id="qnaForm" action="<?= base_url('manage/qna_answer/' . $qna->num); ?>" method="post">
                    <textarea id="answerInput" name="answer" placeholder="답변을 입력하세요" required style="width:100%; height:150px; margin-bottom:10px; padding:10px; font-size:14px;"></textarea>

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

<?php if (empty($qna->answer)) : ?>
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
<?php endif; ?>