<div class="container">
    <div class="c_title">FAQ 수정</div>

    <form id="faqForm" action="<?= base_url('manage/faq_update/' . $faq->num); ?>" method="post">
        <div class="noti_view">

            <div class="noti_write_t">
                <span>*</span> 카테고리
            </div>
            <div class="noti_write_c">
                <select name="category" required>
                    <option value="">카테고리 선택</option>
                    <option value="서비스" <?= ($faq->category == '서비스') ? 'selected' : '' ?>>서비스</option>
                    <option value="조회/신청" <?= ($faq->category == '조회/신청') ? 'selected' : '' ?>>조회/신청</option>
                    <option value="가입 및 회원정보" <?= ($faq->category == '가입 및 회원정보') ? 'selected' : '' ?>>가입 및 회원정보</option>
                    <option value="기타" <?= ($faq->category == '기타') ? 'selected' : '' ?>>기타</option>
                </select>
            </div>

            <div class="noti_write_t">
                <span>*</span> 제목
            </div>
            <div class="noti_write_c">
                <input type="text" name="title" value="<?= htmlspecialchars($faq->title) ?>" required />
            </div>

            <div class="noti_write_t">
                <span>*</span> 내용
            </div>
            <div class="noti_write_c_lar">
                <textarea name="contents" rows="12" required style="width: 100%; height: 200px; font-size: 16px;"><?= htmlspecialchars($faq->contents) ?></textarea>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <div class="noti_view_bt">
                    <a href="javascript:void(0);" onclick="document.getElementById('faqForm').submit();" class="btn_submit">수정완료</a>
                </div>
                <div class="noti_view_bt">
                    <a href="<?= base_url('manage/faq'); ?>" class="cancel_btn">취소</a>
                </div>
            </div>

        </div>
    </form>
</div>
