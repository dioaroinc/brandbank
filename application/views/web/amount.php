<?php
$showTwoWeekPopup = false;

$reg_date_raw = $this->session->userdata('registration_date');
if ($reg_date_raw) {
    $reg_date = new DateTime($reg_date_raw);
    $today = new DateTime();
    $interval = $reg_date->diff($today)->days;

    if ($interval < 14) {
        $showTwoWeekPopup = true;
    }
}
?>

<div class="container">
    <div class="b_title">선정산 가능금액 조회 <img src="/garage/images/ic_search.png" /></div>
    <div class="b_title_se">미리 정산받을 수 있는 쇼핑몰의 정산금을 조회해보세요.</div>

    <form id="settlementForm" method="post" action="/web/amount_submit">

        <div class="filter_se">
            <div class="flt_l"><input type="checkbox" id="check_all" onclick="toggleAll(this)"></div>
            <div class="flt_l">&nbsp;&nbsp;&nbsp;전체선택</div>
            <div class="tb2_bt flt_r" onclick="runSettlement()" style="margin-left:10px;">조회하기</div>
            <div class="clear"></div>
        </div>

        <div class="tb2_t">
            <div class="td25">선택</div>
            <div class="td25">채널명</div>
            <div class="td25">가능금액<span class="info_icon" onclick="showInfoPopup()">?</span></div>
            <div class="td25">신청금액</div>
            <div class="clear"></div>
        </div>

        <?php foreach ($settlements as $row): ?>

            <div class="tb2_tr">
                <div class="td25">
                    <input type="checkbox" name="settlement_ids[]" value="<?= $row["num"] ?>" class="tb2_chk">
                    <input type="hidden" name="max_amount_<?= $row["num"] ?>" value="<?= $row["pre_settlement_amount"] ?>">
                    <input type="hidden" name="brand_name_<?= $row["num"] ?>" value="<?= htmlspecialchars($row["brand_name"]) ?>">
                    <input type="hidden" name="shopping_mall_<?= $row["num"] ?>" value="<?= htmlspecialchars($row["shopping_mall"]) ?>">
                    <input type="hidden" name="max_amount_<?= $row["num"] ?>" value="<?= $row["pre_settlement_amount"] ?>">
                </div>
                <div class="td25"><?= htmlspecialchars($row["shopping_mall"]) ?></div>
<!--                
                <div class="td25">
                    <?php
                        // 산정기간 계산
                        $start_date_obj = new DateTime($row["first_day"]);
                        $end_date_obj = new DateTime();
                        $end_date_obj->modify('-1 day');

                        $display_period = $start_date_obj->format('n/j') . ' ~ ' . $end_date_obj->format('n/j');
                        echo $display_period;
                    ?>
                </div>
-->
                <div class="td25"><?= number_format(floor($row["pre_settlement_amount"])) ?>원</div>
                <div class="td25">
                    <input
                        type="text"
                        name="application_amount_<?= $row["num"] ?>"
                        min="0"
                        max="<?= $row["pre_settlement_amount"] ?>"
                        placeholder="액수 입력"
                        class="td_amount_input"
                        data-id="<?= $row["num"] ?>"
                        disabled
                        onkeyup="readyToRegister()"
                    >
                </div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>

        <div style="margin-top: 20px; text-align: center;">
            <div class="tb4_bt" onclick="showSubmitPopup()" style="display: inline-block;" id="bt_amount_register">신청하기</div>
        </div>
    </form>

    <div id="popup_type_2" class="bg_bk_alert_new2">
        <div class="bg_bk_alert_new_title">
            <img src="/garage/images/text_type_3.png" />
        </div>
        <div class="bg_bk_alert_new_x" onclick="popupType(2)"></div>
        <div class="clear"></div>
        <div class="bg_bk_alert_new_body">로그인 후 브랜드집의 선정산 서비스를 이용해 보세요.</div>

        <div class="bg_bk_confirm_new_orange" onclick="location.href='<?= base_url('web/login') ?>'">로그인</div>
        <div class="bg_bk_confirm_new_gray" onclick="location.href='<?= base_url('web/signup') ?>'">회원가입</div>
        <div class="clear"></div>
    </div>

    <!-- 정산 확인 팝업 -->
    <div id="popup_settlement_confirm" class="bg_bk_alert_new2">
        <div class="bg_bk_alert_new_title">
            <!-- <img src="/garage/images/text_type_3.png" /> -->
        </div>
        <div class="bg_bk_alert_new_x" onclick="closeSettlementPopup()"></div>
        <div class="clear"></div>
        
        
        <div class="bg_bk_alert_new_body">선정산 가능 금액을 조회하시겠습니까?</div>

        <div class="bg_bk_confirm_new_orange" onclick="confirmRunSettlement()">예</div>
        <div class="bg_bk_confirm_new_gray" onclick="closeSettlementPopup()">아니오</div>

        <div class="clear"></div>
    </div>

    <!-- 신청 확인 팝업 -->
    <div id="popup_submit_confirm" class="bg_bk_alert" style="display: none">
        <div class="bg_bk_alert_x" onclick="closeSubmitPopup()"></div>

        <div class="bg_bk_alert_txt" style="white-space: pre-line;">
            <div style="font-weight: 600; margin-bottom: 10px;">신청 내역</div>
            <div id="popup_application_list" style="font-size: 14px; margin-bottom: 12px;"></div>
            <div style="font-weight: bold;">합계: <span id="popup_application_total"></span>원</div>
        </div>

        <div class="bg_bk_confirm_gr" onclick="confirmSubmit()">예</div>
        <div class="bg_bk_confirm_bk" onclick="closeSubmitPopup()">아니오</div>
        <div class="clear"></div>
    </div>

    <!-- 가능금액 설명 팝업 -->
    <div id="popup_amount_info" class="bg_bk_alert_new">
        <div class="bg_bk_alert_new_title">
            <img src="/garage/images/text_type_1.png" />
        </div>
        <div class="bg_bk_alert_new_x" onclick="closeInfoPopup()"></div>
        <div class="clear"></div>

        <div class="bg_bk_alert_new_txt">
            <img src="/garage/images/text_type_2.png" />
        </div>
        <div class="bg_bk_alert_new_txt_gray">
            Ex ) 매출액이 1,000,000원일 경우  <br />
            정산요율 반영 : 1,000,000 x 65% = 650,000원  <br />
            지급율 : 알고리즘을 통해 80%로 산정 <br /> 
            가능금액 : 650,000 x 80% = 520,000
        </div>
    </div>

    <!-- 가입 2주 미만 알림 팝업 -->
    <div id="popup_two_week_limit" class="bg_bk_alert" style="display:none;">
        <div class="bg_bk_alert_txt" style="white-space: pre-line;">
            분석 데이터 수집 중입니다.
            최초 선정산 가능금액 조회는  
            가입승인 후 최소 2주간의 매출데이터 집계가 필요합니다.
        </div>
        <div class="bg_bk_confirm_gr" onclick="location.href='<?= base_url('web/mypage') ?>'">확인</div>
        <div class="clear"></div>
    </div>

    <!-- 체크박스 미선택 경고 팝업 -->
    <div id="popup_check_required" class="bg_bk_alert" style="display:none;">
        <div class="bg_bk_alert_txt">최소 하나 이상의 선택이 필요합니다.</div>
        <div class="bg_bk_confirm_gr" onclick="closeCheckRequiredPopup()">확인</div>
        <div class="clear"></div>
    </div>

    <!-- 신청 완료 안내 팝업 -->
    <div id="popup_submit_success" class="bg_bk_alert_new3">

        <div class="bg_bk_alert_new_title">
            <img src="/garage/images/text_type_4.png" />
        </div>
        <div class="bg_bk_alert_new_x" onclick="location.reload()"></div>
        <div class="clear"></div>
        <div class="bg_bk_alert_new_body">지금 바로 선정산 신청 내역을 확인해 보세요.</div>
        
        <div class="bg_bk_alert_new_img">
            <img src="/garage/images/img_type_1.png" />
        </div>

        <div class="bg_bk_confirm_new_orange_long" onclick="location.href='/web/nhistory'">신청내역 확인하기</div>
    </div>

    <!-- 신청금액 유효성 실패 팝업 -->
    <div id="popup_amount_invalid" class="bg_bk_alert" style="display:none;">
        <div class="bg_bk_alert_txt">금액을 확인해주세요.</div>
        <div class="bg_bk_confirm_gr" onclick="closeAmountInvalidPopup()">확인</div>
        <div class="clear"></div>
    </div>

</div>

<!-- 로딩 오버레이 (반투명) -->
<div id="popup_loading_overlay" style="display:none; position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.12); z-index:9998;"></div>

<!-- 작고 심플한 로딩 팝업 -->
<div id="popup_loading" class="bg_bk_alert"
     style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);min-width:220px;max-width:340px;z-index:9999;padding:18px 12px 18px 12px;box-shadow:0 2px 10px rgba(0,0,0,0.12);">
    <div class="bg_bk_alert_txt" style="text-align:center; margin:0; display:flex; flex-direction:column; align-items:center; justify-content:center;">
        <div style="font-size:17px;font-weight:600;margin-bottom:7px;">정산 처리 중</div>
        <div class="spinner" style="margin:7px auto 6px;width:28px;height:28px;border:4px solid #eee;border-top:4px solid #1a8754;border-radius:50%;animation:spin 0.9s linear infinite;"></div>
        <div style="font-size:14px;color:#888;margin-top:2px;">잠시만 기다려 주세요</div>
    </div>
</div>
<style>
@keyframes spin {
  0% { transform:rotate(0deg);}
  100% { transform:rotate(360deg);}
}
</style>

<script>
    const isLoggedIn = <?= $this->session->userdata('user_id') ? 'true' : 'false' ?>;
    const registrationDate = <?= $this->session->userdata('registration_date') 
    ? '"' . $this->session->userdata('registration_date') . '"' 
    : 'null' ?>;

    function toggleAll(source) {

        const checkboxes = document.querySelectorAll('.tb2_chk');
        checkboxes.forEach(cb => {
            cb.checked = source.checked;
            const num = cb.value;
            const input = document.querySelector(`input.td_amount_input[data-id="${num}"]`);
            if (source.checked) {
                console.log('checked');
                input.removeAttribute('disabled');
            } else {
                console.log('unchecked');
                input.setAttribute('disabled', 'disabled');
                input.value = '';
            }
        });
    }

    // 1. 정산 버튼 클릭 시 팝업 표시
    function runSettlement() {
        if (!isLoggedIn) {
            // 비로그인 → 로그인 유도 팝업
            document.getElementById('popup_type_2').style.display = 'block';
            return;
        }

        // 로그인 상태 → 가입일 기준 2주 체크
        if (registrationDate) {
            const regDate = new Date(registrationDate);
            const today = new Date();

            // 가입일과 오늘의 차이 (밀리초 → 일수)
            const diffTime = today - regDate;
            const diffDays = diffTime / (1000 * 60 * 60 * 24);

            if (diffDays < 14) {
                // 2주 미만 → 팝업
                document.getElementById('popup_two_week_limit').style.display = 'block';
                return;
            }
        }

        // 2주 이상 → 기존 로직
        document.getElementById('popup_settlement_confirm').style.display = 'block';
    }

    // 2. 정산 팝업 닫기
    function closeSettlementPopup() {
        document.getElementById('popup_settlement_confirm').style.display = 'none';
    }

    // 3. 실제 정산 요청
    function confirmRunSettlement() {
        closeSettlementPopup();

        showLoadingPopup(); // ← 로딩 시작

        const form = document.getElementById('settlementForm');
        const formData = new FormData(form);

        fetch("<?= base_url('web/run_settlement') ?>", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(response => {
            hideLoadingPopup(); // ← 무조건 닫기
            alert(response.message);
            if (response.status === "success") {
                location.reload();
            }
        })
        .catch(error => {
            hideLoadingPopup(); // ← 무조건 닫기
            alert("정산 요청 중 오류가 발생했습니다.");
        });
    }


// 신청 팝업 열기
function showSubmitPopup() {
    const checked = document.querySelectorAll('.tb2_chk:checked');
    if (checked.length === 0) {
        showCheckRequiredPopup();
        return;
    }

    let listHTML = '';
    let total = 0;
    let validCount = 0;

    checked.forEach(cb => {
        const num = cb.value;
        const input = document.querySelector(`input.td_amount_input[data-id="${num}"]`);
        const max = parseFloat(document.querySelector(`input[name="max_amount_${num}"]`).value);
        const val = parseFloat(input.value.replace(/,/g, '')); // 콤마 제거!
        const mall = cb.closest('.tb2_tr').querySelectorAll('div')[1].innerText;

        if (!isNaN(val) && val > 0 && val <= max) {
            listHTML += `<div style="margin-bottom: 5px;">
                ${mall} &nbsp;&nbsp;&nbsp; ${parseInt(max).toLocaleString()}원 → <strong>${parseInt(val).toLocaleString()}원</strong>
            </div>`;
            total += parseInt(val);
            validCount++;
        }
    });

    if (validCount === 0) {
        document.getElementById('popup_amount_invalid').style.display = 'block';
        return;
    }

    document.getElementById('popup_application_list').innerHTML = listHTML;
    document.getElementById('popup_application_total').innerText = total.toLocaleString();
    document.getElementById('popup_submit_confirm').style.display = 'block';
}

// 체크박스 확인
function showCheckRequiredPopup() {
    document.getElementById('popup_check_required').style.display = 'block';
}
function closeCheckRequiredPopup() {
    document.getElementById('popup_check_required').style.display = 'none';
}

// 신청 팝업 닫기
function closeSubmitPopup() {
    document.getElementById('popup_submit_confirm').style.display = 'none';
}

// 실제 폼 제출 실행
function confirmSubmit() {
    closeSubmitPopup();

    let isValid = true;
    const checked = document.querySelectorAll('.tb2_chk:checked');

    checked.forEach(cb => {
        const num = cb.value;
        const input = document.querySelector(`input.td_amount_input[data-id="${num}"]`);
        const max = parseFloat(document.querySelector(`input[name="max_amount_${num}"]`).value);
        const val = parseFloat(input.value.replace(/,/g, ''));
        // 숫자 아님 or 0 이하 or 초과 시 에러
        if (isNaN(val) || val <= 0 || val > max) {
            isValid = false;
        }
    });

    if (!isValid) {
        document.getElementById('popup_amount_invalid').style.display = 'block';
        return;
    }

    // 금액 유효 → 성공 팝업
    // 순서를 바꿔봄 (블록처리)
    //document.getElementById('popup_submit_success').style.display = 'block';

    submitFormAfterSuccess();


}

function submitFormAfterSuccess() {
    document.getElementById('popup_submit_success').style.display = 'none';

    // 모든 신청금액 input에서 콤마 제거
    document.querySelectorAll('.td_amount_input').forEach(input => {
        if (!input.disabled) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }
    });

      const formData = $("#settlementForm").serialize(); // 폼 데이터 직렬화

        $.ajax({
            url: '/web/amount_submit', // 여기에 실제 서버 URL 입력
            type: 'POST',
            data: formData,
            success: function (response) {
                document.getElementById('popup_submit_success').style.display = 'block';
            },
            error: function (xhr, status, error) {
            console.error('에러 발생:', error);
            alert('정산 중 오류가 발생했습니다.');
            }
        });



        
    //document.getElementById('settlementForm').submit();

    // 내가 넣어놓음
    
}

//가능금액 ? 팝업
function showInfoPopup() {
    document.getElementById('popup_amount_info').style.display = 'block';
}
function closeInfoPopup() {
    document.getElementById('popup_amount_info').style.display = 'none';
}

// 신청금액 팝업
function closeAmountInvalidPopup() {
    document.getElementById('popup_amount_invalid').style.display = 'none';
}

</script>

<?php if (!$this->session->userdata('user_id')): ?>
<script>
    //window.addEventListener('DOMContentLoaded', () => {
    //    document.getElementById('popup_type_2').style.display = 'block';
    //});
</script>
<?php endif; ?>

<!-- 로그인 후 2주 처리-->
<?php if ($showTwoWeekPopup): ?>
<script>
    //window.addEventListener('DOMContentLoaded', () => {
    //    document.getElementById('popup_two_week_limit').style.display = 'block';
    //});
</script>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 3자리 콤마 자동 입력
    document.querySelectorAll('.td_amount_input').forEach(input => {
        input.addEventListener('input', function (e) {
            let value = input.value.replace(/[^0-9]/g, '');
            // 최대금액 제한 (선택)
            const max = input.getAttribute('max');
            if (max && value.length > 0 && parseInt(value, 10) > parseInt(max, 10)) {
                value = max;
            }
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        });
        input.addEventListener('blur', function () {
            let value = input.value.replace(/[^0-9]/g, '');
            input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        });
    });

    // 체크박스 활성/비활성
    const checkboxes = document.querySelectorAll('.tb2_chk');
    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            const num = checkbox.value;
            const input = document.querySelector(`input.td_amount_input[data-id="${num}"]`);
            if (checkbox.checked) {
                input.removeAttribute('disabled');
            } else {
                input.setAttribute('disabled', 'disabled');
                input.value = '';
            }
        });
    });

    // form submit 시 콤마 제거
    document.getElementById('settlementForm').addEventListener('submit', function (e) {
        document.querySelectorAll('.td_amount_input').forEach(input => {
            if (!input.disabled) {
                input.value = input.value.replace(/[^0-9]/g, '');
            }
        });
    });
});

function showLoadingPopup() {
    document.getElementById('popup_loading_overlay').style.display = 'block';
    document.getElementById('popup_loading').style.display = 'block';
    // 스크롤/키보드 포커스/클릭 차단
    document.body.style.overflow = 'hidden';
}

function hideLoadingPopup() {
    document.getElementById('popup_loading_overlay').style.display = 'none';
    document.getElementById('popup_loading').style.display = 'none';
    document.body.style.overflow = '';
}

</script>


<style>
.info_icon {
    display: inline-block;
    margin-left: 6px;
    width: 18px;
    height: 18px;
    line-height: 18px;
    border-radius: 50%;
    background-color: #555;
    color: white;
    font-size: 13px;
    text-align: center;
    cursor: pointer;
}

.tb2_tr {
    display: flex;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid #eee;
}

.tb2_tr .td20,
.tb2_tr .td30 {
    padding: 0 10px;
    box-sizing: border-box;
}

.td20 { width: 20%; }
.td30 { width: 30%; }

.tb2_tr input[type="number"] {
    width: 70%;
    box-sizing: border-box;
}

</style>



<script>
    // added by DJK 250807
    function readyToRegister(){
        $("div#bt_amount_register").attr("class","tb4_bt_s");
    }
</script>