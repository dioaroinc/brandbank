<div class="container">
    <div class="c_title">공지사항 수정</div>

    <form id="noticeForm" action="<?= base_url('manage/notice_update/' . $notice->num); ?>" method="post" enctype="multipart/form-data">
        <div class="noti_view">

            <div class="noti_write_t">
                <span style="color: red;">*</span> 제목
            </div>
            <div class="noti_write_c">
                <input type="text" name="title" value="<?= htmlspecialchars($notice->title) ?>" required />
            </div>

            <div class="noti_write_t">
                첨부파일
            </div>
            <div class="noti_write_c" style="display: flex; flex-direction: column; gap: 10px;">
                <?php if ($notice->attachment): ?>
                    <div>
                        <a class="download_btn small" href="<?= base_url('uploads/' . $notice->attachment) ?>" download>
                            <?= htmlspecialchars($notice->attachment) ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="file" name="attachment" id="attachmentInput" onchange="toggleClearButton()" />
                    <button type="button" onclick="clearFile()" class="file_clear_btn" id="clearBtn" style="display: none;">✕</button>
                </div>

                <input type="hidden" name="existing_attachment" value="<?= htmlspecialchars($notice->attachment) ?>">
            </div>

            <div class="noti_write_t">
                <span style="color: red;">*</span> 내용
            </div>
            <div class="noti_write_c_lar">
                <textarea name="contents" rows="12" required style="width: 100%; height: 200px; font-size: 16px;"><?= htmlspecialchars($notice->contents) ?></textarea>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <div class="noti_view_bt">
                    <a href="javascript:void(0);" onclick="document.getElementById('noticeForm').submit();" class="submit_btn">수정완료</a>
                </div>
                <div class="noti_view_bt">
                    <a href="<?= base_url('manage/notice'); ?>" class="cancel_btn">취소</a>
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

<style>
    .download_btn {
        display: inline-block;
        padding: 6px 10px;
        background-color: #f0f0f5;
        color: #333;
        text-decoration: none;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-weight: 500;
        font-size: 13px;
    }

    .download_btn:hover {
        background-color: #e6e6f0;
        border-color: #999;
    }
</style>
