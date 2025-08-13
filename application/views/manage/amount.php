<?php if (isset($_GET['upload_result']) && $_GET['upload_result'] == '1' && $this->session->flashdata('upload_message')): ?>   
    <div id="popup_type_1" class="bg_bk_alert" style="display:none;">
        <div class="bg_bk_alert_x" onclick="popupType(1)"></div>
        <div class="bg_bk_alert_txt"><?= $this->session->flashdata('upload_message') ?></div>
        <div class="bg_bk_alert_bt" onclick="popupType(1)">확인</div>
    </div>
    
    <script>
        // 페이지 로드 시 팝업 표시
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById('popup_type_1').style.display = 'block';
        });

        // URL에서 upload_result=1 제거
        if (history.replaceState) {
        const url = new URL(window.location.href);
        url.searchParams.delete('upload_result');
        window.history.replaceState({}, document.title, url.toString());
        }
    </script>
<?php endif; ?>

<!-- 자료 월 선택 -->
<div class="container">
    <div class="c_title">월정산 자료 입력</div>
    <div style="text-align: center;">
        <form method="GET" action="/manage/amount" style="display: inline-block;">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <!-- 연도 선택 -->
                <select name="year" style="padding: 6px 10px;">
                <?php for ($y = $min_year; $y <= $current_year; $y++): ?>
                    <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>>
                    <?= $y ?>년
                    </option>
                <?php endfor; ?>
                </select>

                <!-- 월 선택 -->
                <select name="month" style="padding: 6px 10px;">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <?php $mm = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                    <option value="<?= $mm ?>" <?= $selected_month == $mm ? 'selected' : '' ?>>
                    <?= $m ?>월
                    </option>
                <?php endfor; ?>
                </select>

                <!-- 확인 버튼 -->
                <input type="hidden" name="confirm" value="1" />
                <button type="submit" style="padding: 6px 18px; background: #1a8754; color: white; border: none;">
                확인
                </button>

                <!-- 초기화 -->
                <a href="/manage/amount"
                style="padding: 6px 18px; background: #ccc; color: black; text-decoration: none;">
                초기화
                </a>
            </div>
        </form>
    </div>

    <!-- 채널별 파일입력 UI -->
    <?php if ($is_selected): ?>
        <hr style="margin: 20px 0;" />
        <div style="text-align: center; font-weight: bold; margin-bottom: 12px;">
            <?= $selected_year ?>년 <?= $selected_month ?>월 자료 입력
        </div>

        <form method="POST" action="/manage/input_upload?year=<?= $selected_year ?>&month=<?= $selected_month ?>" enctype="multipart/form-data">
            <table style="margin: 0 auto; border-collapse: collapse; width: 120%; min-width: 600px;">
            <thead>
                <tr>
                    <th style="width: 8%; padding: 8px; border: 1px solid #ccc; text-align: center;">채널명</th>
                    <th style="width: 17%; padding: 8px; border: 1px solid #ccc; text-align: center;">업로드 일자</th>
                    <th style="width: 25%; padding: 8px; border: 1px solid #ccc; text-align: center;">파일</th>
                    <th style="width: 8%; padding: 8px; border: 1px solid #ccc; text-align: center;">채널명</th>
                    <th style="width: 17%; padding: 8px; border: 1px solid #ccc; text-align: center;">업로드 일자</th>
                    <th style="width: 25%; padding: 8px; border: 1px solid #ccc; text-align: center;">파일</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $total_channels = count($channels);
                    for ($i = 0; $i < $total_channels; $i += 2):
                ?>
                <tr>
                    <!-- Left Column -->
                    <td style="padding: 8px; border: 1px solid #ccc; text-align: center;"><?= htmlspecialchars($channels[$i]) ?></td>
                    <td style="padding: 8px; border: 1px solid #ccc; text-align: center;">
                        <?= htmlspecialchars($existing_files[$channels[$i]]['uploaded_at'] ?? '') ?>
                    </td>
                    <td style="padding: 8px; border: 1px solid #ccc;">
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <input type="text" readonly id="filename_<?= md5($channels[$i]) ?>"
                                placeholder="파일을 선택하세요"
                                value="<?= htmlspecialchars($existing_files[$channels[$i]]['filename'] ?? '') ?>"
                                style="flex-grow: 1; min-width: 0; padding: 6px; font-size: 13px;">

                            <label for="file_<?= md5($channels[$i]) ?>"
                                style="padding: 6px 14px; background-color: #1a8754; color: white;
                                        border-radius: 4px; cursor: pointer; display: inline-block;">
                            찾아보기
                            </label>

                            <input type="file" name="files[<?= htmlspecialchars($channels[$i]) ?>]"
                                id="file_<?= md5($channels[$i]) ?>" style="display: none;"
                                accept=".xlsx,.xlsm"
                                onchange="document.getElementById('filename_<?= md5($channels[$i]) ?>').value = this.files[0]?.name || '';">
                        </div>
                    </td>

                    <!-- Right Column -->
                    <?php if ($i + 1 < $total_channels): ?>
                    <td style="padding: 8px; border: 1px solid #ccc; text-align: center;"><?= htmlspecialchars($channels[$i+1]) ?></td>
                    <td style="padding: 8px; border: 1px solid #ccc; text-align: center;">
                        <?= htmlspecialchars($existing_files[$channels[$i+1]]['uploaded_at'] ?? '') ?>
                    </td>
                    <td style="padding: 8px; border: 1px solid #ccc;">
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <input type="text" readonly id="filename_<?= md5($channels[$i+1]) ?>"
                                placeholder="파일을 선택하세요"
                                value="<?= htmlspecialchars($existing_files[$channels[$i+1]]['filename'] ?? '') ?>"
                                style="flex-grow: 1; min-width: 0; padding: 6px; font-size: 13px;">

                            <label for="file_<?= md5($channels[$i+1]) ?>"
                                    style="padding: 6px 14px; background-color: #1a8754; color: white;
                                        border-radius: 4px; cursor: pointer; display: inline-block;">
                            찾아보기
                            </label>

                            <input type="file" name="files[<?= htmlspecialchars($channels[$i+1]) ?>]"
                                    id="file_<?= md5($channels[$i+1]) ?>" style="display: none;"
                                    accept=".xlsx,.xlsm"
                                    onchange="document.getElementById('filename_<?= md5($channels[$i+1]) ?>').value = this.files[0]?.name || '';">
                        </div>
                    </td>
                    <?php else: ?>
                    <td colspan="2" style="padding: 8px; border: 1px solid #ccc;"></td>
                    <?php endif; ?>

                </tr>
                <?php endfor; ?>
            </tbody>
            </table>

            <!-- 등록 버튼 -->
            <div style="margin-top: 20px; text-align: center;">
                <button type="submit" id="uploadButton"
                        style="padding: 8px 20px; background-color: #1a8754; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;"
                        disabled>
                등록
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    function checkFilesSelected() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        let hasFile = false;

        fileInputs.forEach(input => {
            if (input.files.length > 0) {
                hasFile = true;
            }
        });

        const uploadButton = document.getElementById('uploadButton');

        if (hasFile) {
            uploadButton.disabled = false;
            uploadButton.style.opacity = '1';
            uploadButton.style.cursor = 'pointer';
            uploadButton.style.backgroundColor = '#1a8754'; // 활성화 색상
        } else {
            uploadButton.disabled = true;
            uploadButton.style.opacity = '0.5';
            uploadButton.style.cursor = 'not-allowed';
            uploadButton.style.backgroundColor = '#1a8754'; // 유지
        }
    }

    // 모든 파일 input 에 change 이벤트 추가
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', checkFilesSelected);
        });

        // 초기 상태 확인
        checkFilesSelected();
    });

    function popupType(type) {
        const popup = document.getElementById('popup_type_' + type);
        if (!popup) return;
        popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
    }

</script>

