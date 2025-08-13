<style>
    table {
        table-layout: fixed;        /* 테이블 전체 레이아웃 고정 */
        width: 100%;
        max-width: 800px;
        border-collapse: collapse;
    }

    td, th {
        white-space: nowrap;        /* 기본은 줄바꿈 방지 */
        overflow: hidden;           /* 넘친 내용 숨김 */
        text-overflow: ellipsis;    /* ... 표시 */
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
        vertical-align: middle;
    }

    /* 긴 내용을 허용하고 싶을 때 사용하는 class */
    .long-text {
        white-space: normal !important;
        word-wrap: break-word;
        overflow: visible;
        text-overflow: clip;
    }
</style>

<div class="container">
    <form method="POST" action="/manage/member_info_update" enctype="multipart/form-data">
    
    <!-- user_id Hidden 으로 추가 -->
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($member->user_id) ?>">

    <table style="margin: 0 auto; border-collapse: collapse; width: 100%; max-width: 800px; text-align: left;">
        <tr>
            <th style="padding: 10px; border: 1px solid #ccc; width:20%;">ID</th>
            <td style="padding: 10px; border: 1px solid #ccc; width:30%;">
                <?= htmlspecialchars($member->user_id) ?>
            </td>
            <th style="padding: 10px; border: 1px solid #ccc; width:20%;"></th>
            <td style="padding: 10px; border: 1px solid #ccc; width:30%;"></td>
        </tr>

        <tr>
            <th style="padding: 10px; border: 1px solid #ccc;">PASS</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    width: 100%;    /* 셀 너비에 맞게 맞춤 */
                    box-sizing: border-box;
                ">
                    <button type="button"
                        style="
                            flex: 0 0 auto;             /* 고정 크기 유지 */
                            height: 34px;
                            padding: 0 12px;
                            min-width: 70px;
                            background-color: #1a8754;
                            color: white;
                            border: none;
                            cursor: pointer;
                            font-size: 14px;
                            line-height: 1;
                            box-sizing: border-box;
                        "
                        onclick="resetMemberPassword('<?= htmlspecialchars($member->user_id) ?>')">
                        초기화
                    </button>
                </div>
            </td>
            <th style="padding: 10px; border: 1px solid #ccc;"></th>
            <td style="padding: 10px; border: 1px solid #ccc;"></td>
        </tr>

        <tr>
            <th style="padding: 10px; border: 1px solid #ccc;">사업자번호</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                <?= htmlspecialchars($member->business_number) ?>
            </td>
            <th style="padding: 10px; border: 1px solid #ccc;">대표자명</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                <?= htmlspecialchars($member->ceo_name) ?>
            </td>
        </tr>
        
        <tr>
            <th style="padding: 10px; border: 1px solid #ccc;">대표자연락처</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                <input type="text" name="ceo_phone" value="<?= htmlspecialchars($member->ceo_contact) ?>" style="width: 100%; box-sizing: border-box; height: 32px; padding: 0 8px; font-size: 14px;" />
            </td>
            <th style="padding: 10px; border: 1px solid #ccc;">대표자이메일</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                <input type="text" name="ceo_email" value="<?= htmlspecialchars($member->ceo_email) ?>" style="width: 100%; box-sizing: border-box; height: 32px; padding: 0 8px; font-size: 14px;" />
            </td>
        </tr>

        <tr>
            <th style="padding: 10px; border: 1px solid #ccc;">사업자등록증</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                    <?php if (!empty($member->business_license)): ?>
                        <a href="<?= base_url('garage/attachment/1_Business_registration_certificate/' . $member->business_license) ?>" target="_blank" download>
                            <?= htmlspecialchars($member->business_license) ?>
                        </a>
                    <?php else: ?>
                        없음
                    <?php endif; ?>
            </td>
            <th style="padding: 10px; border: 1px solid #ccc;">회원유형</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                <?= htmlspecialchars($member->member_type) ?>
            </td>
        </tr>

        <tr>
            <th style="padding: 10px; border: 1px solid #ccc;">홈페이지</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                <input type="text" name="website" value="<?= htmlspecialchars($member->website) ?>" style="width: 100%; box-sizing: border-box; height: 32px; padding: 0 8px; font-size: 14px;" />
            </td>
            <th style="padding: 10px; border: 1px solid #ccc;">브랜드명</th>
            <td style="padding: 10px; border: 1px solid #ccc;">
                <input type="text" name="brand_name" value="<?= htmlspecialchars($member->brand_name) ?>" style="width: 100%; box-sizing: border-box; height: 32px; padding: 0 8px; font-size: 14px;" />
            </td>
        </tr>

        <tr>
            <th style="padding: 10px; border: 1px solid #ccc;">선정산받을계좌번호</th>
            <td colspan="3" style="padding: 10px; border: 1px solid #ccc;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <select name="settlement_account_bank" style="height: 32px; padding: 0 8px; font-size: 14px;">
                        <option value="우리은행" <?= ($member->settlement_account_bank == '우리은행') ? 'selected' : '' ?>>우리은행</option>
                        <option value="국민은행" <?= ($member->settlement_account_bank == '국민은행') ? 'selected' : '' ?>>국민은행</option>
                        <option value="신한은행" <?= ($member->settlement_account_bank == '신한은행') ? 'selected' : '' ?>>신한은행</option>
                        <option value="하나은행" <?= ($member->settlement_account_bank == '하나은행') ? 'selected' : '' ?>>하나은행</option>
                    </select>
                    <input type="text" name="settlement_account_number" value="<?= htmlspecialchars($member->settlement_account_number) ?>"
                        style="flex: 1; box-sizing: border-box; height: 32px; padding: 0 8px; font-size: 14px;" />
                </div>
            </td>
        </tr>

        <tr>
            <th style="padding: 10px; border: 1px solid #ccc;">선정산받을계좌사본</th>
            <td colspan="3" style="padding: 10px; border: 1px solid #ccc;">
                <?php if (!empty($member->settlement_account_copy)): ?>
                    <div style="margin-bottom: 6px;">
                        <?php if (!empty($member->settlement_account_copy)): ?>
                            <div style="margin-bottom: 6px;">
                                <a href="<?= base_url('garage/attachment/2_copy_of_account/' . $member->settlement_account_copy) ?>" target="_blank" download>
                                    <?= htmlspecialchars($member->settlement_account_copy) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <h2 style="text-align: center; margin-top: 20px;"></h2>
    <h2 style="text-align: center; margin-top: 20px;">
    정산율
    <span style="font-size: 14px; color: #888; margin-left: 10px;">(예: 35% → 0.35로 입력)</span>
    </h2>
    <table style="margin: 0 auto; border-collapse: collapse; width: 100%; max-width: 800px; text-align: left;">
        <tr>
            <th style="padding: 10px; border: 1px solid #ccc;">채널명</th>
            <th style="padding: 10px; border: 1px solid #ccc;">정산율</th>
        </tr>
        <?php foreach ($channels as $channel): ?>
            <tr>
                <td style="padding: 10px; border: 1px solid #ccc;"><?= htmlspecialchars($channel) ?></td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <input type="text" class="fee-input" data-channel="<?= htmlspecialchars($channel) ?>"
                        value="<?= isset($fees[$channel]) && $fees[$channel] !== null ? htmlspecialchars($fees[$channel]) : '' ?>"
                        style="width: 100%; box-sizing: border-box; height: 32px; padding: 0 8px; font-size: 14px;" />
                </td>
            </tr>
        <?php endforeach; ?>
        <input type="hidden" id="fee_json" name="fee_json" value="">
        <input type="hidden" name="channels" value='<?= json_encode($channels) ?>'>
    </table>

    <div style="margin-top: 20px; text-align: center;">
        <button type="submit" style="padding: 8px 24px; background-color: #1a8754; color: white; font-size: 14px; border: none; cursor: pointer;">
            수정완료
        </button>
        <a href="/manage/memberinfo" style="display: inline-block; padding: 8px 24px; background-color: #777; color: white; text-decoration: none; font-size: 14px; margin-left: 12px;">
            돌아가기
        </a>
        <?php if ($member->status == 1): // 활성화 ?>
            <!-- 정지하기 버튼만 -->
            <button type="button"
                style="padding:8px 24px; background:#d32f2f; color:#fff; border:none; font-size:14px; margin-left:12px; cursor:pointer;"
                onclick="stopMember('<?= htmlspecialchars($member->user_id) ?>')"
            >중지하기</button>
        <?php elseif ($member->status == 3): // 정지 ?>
            <!-- 계속하기 버튼만 -->
            <button type="button"
                style="padding:8px 24px; background:#1a8754; color:#fff; border:none; font-size:14px; margin-left:12px; cursor:pointer;"
                onclick="resumeMember('<?= htmlspecialchars($member->user_id) ?>')"
            >계속하기</button>
        <?php endif; ?>
    </div>
    </form>
</div>

<script>
// 커스텀 alert
function showAlertPopup(message, callback) {
    $('#customPopupMsg').html(message);
    $('#customPopupInput').hide();
    $('#customPopupCancel').hide();
    $('#customPopupOk').off('click').on('click', function(){
        closeCustomPopup();
        if(callback) callback();
    });
    $('#customPopup').show();
}

// 커스텀 confirm
function showConfirmPopup(message, callback) {
    $('#customPopupMsg').html(message);
    $('#customPopupInput').hide();
    $('#customPopupCancel').show();
    $('#customPopupOk').off('click').on('click', function(){
        closeCustomPopup();
        if(callback) callback(true);
    });
    $('#customPopupCancel').off('click').on('click', function(){
        closeCustomPopup();
        if(callback) callback(false);
    });
    $('#customPopup').show();
}

// 커스텀 prompt
function showPromptPopup(message, callback) {
    $('#customPopupMsg').html(message);
    $('#customPopupInput').val('').show().focus();
    $('#customPopupCancel').show();
    $('#customPopupOk').off('click').on('click', function(){
        var val = $('#customPopupInput').val();
        closeCustomPopup();
        if(callback) callback(val);
    });
    $('#customPopupCancel').off('click').on('click', function(){
        closeCustomPopup();
        if(callback) callback(null);
    });
    $('#customPopup').show();
}

// 닫기
function closeCustomPopup() {
    $('#customPopup').hide();
}

// 중지하기
function stopMember(user_id) {
    showPromptPopup('계정 중지 사유를 입력하세요.', function(reason){
        if (reason === null || reason.trim() === '') {
            showAlertPopup('사유를 입력해야 중지할 수 있습니다.');
            return;
        }
        showConfirmPopup('정말로 이 계정을 중지시키겠습니까?', function(ok){
            if(!ok) return;
            $.post('/manage/member_status_update', {
                user_id: user_id,
                status: 3,
                reject_reason: reason
            }, function(data){
                showAlertPopup(data && data.message ? data.message : '중지되었습니다.', function(){ location.reload(); });
            }, 'json').fail(function(xhr){
                showAlertPopup('중지 처리 실패\n' + xhr.responseText);
            });
        });
    });
}

// 해제(복구)
function resumeMember(user_id) {
    showConfirmPopup('이 계정의 권한을 복구(계속) 하시겠습니까?', function(ok){
        if(!ok) return;
        $.post('/manage/member_status_update', {
            user_id: user_id,
            status: 1
        }, function(data){
            showAlertPopup(data && data.message ? data.message : '복구되었습니다.', function(){ location.reload(); });
        }, 'json').fail(function(xhr){
            showAlertPopup('복구 처리 실패\n' + xhr.responseText);
        });
    });
}

function resetMemberPassword(user_id) {
    showConfirmPopup('정말 이 회원의 비밀번호를 초기화하시겠습니까?', function(ok) {
        if (!ok) return;

        $.post('/manage/member_password_reset', { user_id: user_id }, function(data) {
            if (data && data.status === 'success') {
                showAlertPopup('비밀번호가 초기화되었고 이메일로 발송되었습니다.');
            } else {
                showAlertPopup('비밀번호 초기화 실패: ' + (data.message || '알 수 없는 오류'));
            }
        }, 'json').fail(function(xhr) {
            showAlertPopup('비밀번호 초기화 요청 실패\n' + xhr.responseText);
        });
    });
}

document.querySelector('form').addEventListener('submit', function(e) {
    // ======= 유효성 체크 시작 =======
    var invalids = [];
    var inputs = document.querySelectorAll('input[name^="fees["], .fee-input');
    inputs.forEach(function(input) {
        var val = input.value.trim();
        var channel = input.dataset.channel || input.name.replace(/^fees\[(.*)\]$/, '$1');
        if (val !== '') {
            if (isNaN(val) || Number(val) < 0 || Number(val) > 1) {
                invalids.push(channel + ' (' + val + ')');
            }
        }
    });
    if (invalids.length > 0) {
        showAlertPopup('정산율 값이 올바르지 않은 채널이 있습니다:\n' + invalids.join('\n') + '\n0~1 사이의 숫자만 입력해주세요.');
        e.preventDefault();
        return false;
    }
    // ======= 유효성 체크 끝 =======

    // JSON 변환 (fee_json 사용하는 경우만!)
    if (document.querySelectorAll('.fee-input').length > 0) {
        var obj = {};
        document.querySelectorAll('.fee-input').forEach(function(input) {
            obj[input.dataset.channel] = input.value;
        });
        document.getElementById('fee_json').value = JSON.stringify(obj);
    }
});

</script>

<div id="customPopup" class="bg_bk_alert" style="display:none;z-index:9999;">
    <div class="bg_bk_alert_x" onclick="closeCustomPopup()"></div>
    <div class="bg_bk_alert_txt" id="customPopupMsg" style="white-space:pre-line"></div>
    <textarea id="customPopupInput" style="width:90%;display:none;resize:none;margin:16px auto 0;display:block;padding:10px;font-size:15px;border-radius:6px;border:1px solid #ccc;"></textarea>
    <div style="text-align:center;">
        <div class="bg_bk_confirm_gr" id="customPopupOk" style="display:inline-block;">확인</div>
        <div class="bg_bk_confirm_bk" id="customPopupCancel" style="display:none;">취소</div>
    </div>
    <div class="clear"></div>
</div>