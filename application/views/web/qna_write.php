<div class="container">
    <div class="c_title">문의 작성</div>

    <!-- 안내 문구 -->
    <div class="qna_info_text" style="margin-bottom: 30px; font-size: 16px; color: #444; text-align: center;">
        궁금한 사항을 문의해 주세요.<br>
        운영진이 확인 후 답변을 달아 드립니다.
    </div>

    <form method="post" action="<?= base_url('web/qna_submit') ?>" enctype="multipart/form-data">
        <div class="form_row">
            <label>제목</label>
            <input type="text" name="title" required style="width:100%; padding:8px;">
        </div>

        <div class="form_row" style="margin-top:15px;">
            <label>내용</label>
            <textarea name="contents" rows="10" required style="width:100%; padding:8px;"></textarea>
        </div>

        <div class="form_row" style="margin-top:20px; text-align:right;">
            <button type="submit" style="padding:10px 20px;">등록하기</button>
            <a href="<?= base_url('web/qna') ?>" style="margin-left:10px;">취소</a>
        </div>
    </form>
</div>
