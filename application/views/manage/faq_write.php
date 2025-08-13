<div class="container">
    <div class="c_title">FAQ 등록</div>

    <form action="<?= base_url('manage/faq_save'); ?>" method="post" enctype="multipart/form-data">
        <div class="noti_view">
            <div class="noti_write_t">
                <span>*</span> 카테고리
            </div>
            <div class="noti_write_c">
                <select name="category" required>
                    <option value="">카테고리 선택</option>
                    <option value="서비스">서비스</option>
                    <option value="조회/신청">조회/신청</option>
                    <option value="가입및회원정보">가입 및 회원정보</option>
                    <option value="기타">기타</option>
                </select>
            </div>

            <div class="noti_write_t">
                <span>*</span> 제목
            </div>
            <div class="noti_write_c">
                <input type="text" name="title" required />
            </div>

            <div class="noti_write_t">
                <span>*</span> 내용
            </div>
            <div class="noti_write_c_lar">
                <textarea name="contents" required style="width: 100%; height: 200px; font-size: 16px;"></textarea>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <div class="noti_view_bt">
                    <a href="#" onclick="document.querySelector('form').submit();" class="btn_submit">FAQ 등록하기</a>
                </div>
                <div class="noti_view_bt">
                    <a href="<?= base_url('manage/faq'); ?>" class="cancel_btn">취소</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleClearButton() {
        const input = document.getElementById('attachmentInput');
        const clearBtn = document.getElementById('clearBtn');
        clearBtn.style.display = input.files.length > 0 ? 'inline-block' : 'none';
    }

    function clearFile() {
        const input = document.getElementById('attachmentInput');
        const clearBtn = document.getElementById('clearBtn');

        input.value = '';
        clearBtn.style.display = 'none';
    }
</script>