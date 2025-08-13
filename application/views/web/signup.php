<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="signup_box">
    
<form id="signup_form" method="post" enctype="multipart/form-data">

    <div class="nsignup_title">계정 정보</div>
    <div class="nsignup_dot"></div>
    <div class="clear"></div>

    <div class="nsignup_wrap">
        <!-- 아이디 -->
        <div class="signup_box_t">아이디</div>
        <input type="text" class="signup_box_c" name="user_id" placeholder="아이디를 입력해 주세요." required />
        <!-- <input type="button" class="signup_box_c_bt" id="check_user_id_btn" value="중복 확인" /> -->
        <!-- <div class="input_guide_text">아이디는 5자 이상</div> -->

        <!-- 비밀번호 -->
        <div class="signup_box_t">비밀번호</div>
        <div class="input_guide_text">영문,숫자,특수기호(@,!,#,_)를 포함한 8자 이상으로 설정해주세요.</div>
        <input type="password" id="password" class="signup_box_c" name="password" placeholder="비밀번호를 입력해 주세요." required />

        <!-- 비밀번호 재입력 -->
        <div class="signup_box_t">비밀번호 확인</div>
        <input type="password" id="password_confirm" class="signup_box_c signup_box_c_normal" placeholder="비밀번호를 다시 입력해 주세요." onkeyup="checkPasswordMatch()" />
        <div id="password_match_error" class="signup_box_guide_error" style="display: none; color: red;">ⓘ 비밀번호가 일치하지 않습니다.</div>
    </div>

    <div class="nsignup_title">사업자 정보</div>
    <div class="nsignup_dot"></div>
    <div class="clear"></div>


    <div class="nsignup_wrap">

        <!-- 사업자 번호 -->
        <div class="signup_box_t">사업자 번호</div>
        <input type="text" class="signup_box_c" name="business_number" id="business_number" placeholder="사업자번호를 입력해 주세요." />
        <input type="button" class="signup_box_c_bt" id="check_business_btn" value="조회하기" />
        <div class="clear"></div>

        <!-- 대표자명 -->
        <div class="signup_box_t">대표자 성명</div>
        <input type="text" class="signup_box_c" name="ceo_name" placeholder="대표자 성명을 입력해 주세요." required />

        <!-- 대표자 연락처 -->
        <div class="signup_box_t">대표자 연락처</div>
        <input type="text" class="signup_box_c" name="ceo_contact" placeholder="연락처를 입력해 주세요." required />
        <div id="ceo_contact_error" class="signup_box_guide_error" style="display: none; color: red;">전화번호는 숫자로만 입력해 주세요.</div>

        <!-- 대표자 이메일 -->
        <div class="signup_box_t">대표자 이메일</div>
        <input type="email" class="signup_box_c" name="ceo_email" placeholder="이메일을 입력해주세요." required />
        <div id="ceo_email_error" class="signup_box_guide_error" style="display: none; color: red;">이메일 형식에 맞지 않습니다.</div>

        <!-- 사업자등록증 -->
        <div class="signup_box_t">사업자등록증</div>
        <input type="file" id="business_license_input" name="business_license" style="display:none;" required  accept="image/*,.pdf" />
        <input type="text" id="license_filename_input" class="signup_box_c" placeholder="사업자등록증을 첨부해 주세요." readonly />
        <input type="button" class="signup_box_c_bt" id="browse_license_btn" value="찾아보기" />
        <div class="clear"></div>

        <!-- 회원 유형 -->
        <div class="signup_box_t">
            회원 유형 <input type="button" class="signup_box_c_bt_se" value="?" />
        </div>
        <select class="signup_box_c_sel_se" name="member_type">
            <option value="">회원유형을 선택해주세요.</option>
            <option value="1">위탁판매</option>
            <option value="2">직접판매</option>
        </select>
        <div class="clear"></div>
        <div class="signup_box_guide">
            위탁항목 : 브랜드집을 통해 위탁판매를 희망하는 업체 <br />
            직접판매 : 브랜드집을 활용해 직접판매를 희망하는 업체
        </div>
    </div>


    <div class="nsignup_title">브랜드 정보</div>
    <div class="nsignup_dot"></div>
    <div class="clear"></div>

    <div class="nsignup_wrap">
        <!-- 홈페이지 -->
        <div class="signup_box_t">홈페이지</div>
        <input type="text" class="signup_box_c" name="website" placeholder="홈페이지 주소를 입력해주세요." />

        <!-- 브랜드명 -->
        <div class="signup_box_t">브랜드 명</div>
        <input type="text" class="signup_box_c" name="brand_name" placeholder="브랜드 명을 입력해 주세요." required />

    </div>

    <div class="nsignup_title">정산 정보</div>
    <div class="nsignup_dot"></div>
    <div class="clear"></div>

    <div class="nsignup_wrap">

        <!-- 계좌번호 & 직접입력 -->
        <div class="signup_box_t">선정산 받을 계좌</div>
        <select class="signup_box_c_sel" name="settlement_account_bank">
                        <option value="">은행 선택</option>
                        <option value="씨티은행">씨티은행</option>
                        <option value="HSBC">HSBC</option>
                        <option value="LG카드">LG카드</option>
                        <option value="NH투자증권">NH투자증권</option>
                        <option value="SC은행">SC은행</option>
                        <option value="강원은행">강원은행</option>
                        <option value="경기은행">경기은행</option>
                        <option value="경남은행">경남은행</option>
                        <option value="광주은행">광주은행</option>
                        <option value="국민은행">국민은행</option>
                        <option value="농협">농협</option>
                        <option value="대구은행">대구은행</option>
                        <option value="대동은행">대동은행</option>
                        <option value="도이치뱅크">도이치뱅크</option>
                        <option value="동남은행">동남은행</option>
                        <option value="동아은행">동아은행</option>
                        <option value="미래에셋대우증권">미래에셋대우증권</option>
                        <option value="보람은행">보람은행</option>
                        <option value="부산은행">부산은행</option>
                        <option value="삼성증권">삼성증권</option>
                        <option value="삼성카드">삼성카드</option>
                        <option value="상호저축은행">상호저축은행</option>
                        <option value="새마을금고">새마을금고</option>
                        <option value="수협">수협</option>
                        <option value="신한금융투자">신한금융투자</option>
                        <option value="신한은행">신한은행</option>
                        <option value="신협중앙회">신협중앙회</option>
                        <option value="외환송금">외환송금</option>
                        <option value="우리은행">우리은행</option>
                        <option value="우체국">우체국</option>
                        <option value="유안타증권">유안타증권</option>
                        <option value="장기신용금고">장기신용금고</option>
                        <option value="전북은행">전북은행</option>
                        <option value="제주은행">제주은행</option>
                        <option value="조흥은행">조흥은행</option>
                        <option value="중소기업은행">중소기업은행</option>
                        <option value="지로">지로</option>
                        <option value="지로납부">지로납부</option>
                        <option value="축협">축협</option>
                        <option value="충북은행">충북은행</option>
                        <option value="충청은행">충청은행</option>
                        <option value="카카오뱅크">카카오뱅크</option>
                        <option value="케이뱅크">케이뱅크</option>
                        <option value="토스뱅크">토스뱅크</option>
                        <option value="평화은행">평화은행</option>
                        <option value="하나은행">하나은행</option>
                        <option value="한국산업은행">한국산업은행</option>
                        <option value="한국상업은행">한국상업은행</option>
                        <option value="한국주택은행">한국주택은행</option>
                        <option value="한국투자신탁">한국투자신탁</option>
                        <option value="한일은행">한일은행</option>
                        <option value="한화투자증권">한화투자증권</option>
                        <option value="현대증권">현대증권</option>
                        <option value="직접입력">직접입력</option>
        </select>
        <input type="text" id="bank_input" name="settlement_account_bank_custom" class="signup_box_c_se" placeholder="은행명을 직접 입력하세요." style="display:none;" />
        <input type="text" name="settlement_account_number" class="signup_box_c_se" placeholder="계좌번호를 입력해 주세요." />
        <div class="clear"></div>
        
        <br />

        <!-- 계좌 사본 -->
        <div class="signup_box_t">선정산 받을 계좌 사본</div>
        <input type="file" id="settlement_copy_input" name="settlement_account_copy" style="display:none;" required  accept="image/*,.pdf" />
        <input type="text" id="copy_filename_input" class="signup_box_c" placeholder="파일을 선택해주세요." readonly />
        <input type="button" class="signup_box_c_bt" id="browse_copy_btn" value="찾아보기" />
        <div class="clear"></div>
    </div>
        <!-- 가입하기 버튼 -->
        <br /><br />
        <div class="signup_box_bt_pre" id="signup_button" onclick="clickSignup()">회원가입</div>
</form>

</div>

<!-- 완료 팝업 -->
<div id="signup_box_alert" class="bg_bk_alert">
    <div class="bg_bk_alert_x" onclick="shutSignupAlert()"></div>
    <div class="bg_bk_alert_txt">신청이 완료되었습니다.</div>
    <div class="bg_bk_alert_bt" onclick="shutSignupAlert()">확인</div>
</div>

<!-- 알림 팝업 -->
<div id="popup_type_1" class="bg_bk_alert" style="display: none;">
    <div class="bg_bk_alert_x" onclick="popupType(1)"></div>
    <div class="bg_bk_alert_txt">팝업내용입니다.</div>
    <div class="bg_bk_alert_bt" onclick="popupType(1)">확인</div>
</div>

<!-- JavaScript -->
<script>
// 전역 플래그
let userIdChecked = false;
let bizNumberChecked = false;

// 비밀번호 일치 확인 (버튼 토글 없음)
function checkPasswordMatch() {
    const pw = document.getElementById("password").value;
    const pwConfirm = document.getElementById("password_confirm").value;
    const errorMessage = document.getElementById("password_match_error");
    const confirmInput = document.getElementById("password_confirm");

    if (pw !== pwConfirm || pw === "" || pwConfirm === "") {
        errorMessage.style.display = "block";
        confirmInput.classList.remove("signup_box_c_normal");
        confirmInput.classList.add("signup_box_c_error");
    } else {
        errorMessage.style.display = "none";
        confirmInput.classList.remove("signup_box_c_error");
        confirmInput.classList.add("signup_box_c_normal");
    }
}

// 팝업 표시 함수
function popupType(type, message = "") {
    const popup = document.getElementById(`popup_type_${type}`);
    const popupText = popup.querySelector(".bg_bk_alert_txt");
    popupText.textContent = message;
    popup.style.display = popup.style.display === "block" ? "none" : "block";
}

// DOM 로드 후 초기 설정
document.addEventListener("DOMContentLoaded", function () {
    // 사업자등록증
    document.getElementById("browse_license_btn").addEventListener("click", () => document.getElementById("business_license_input").click());
    document.getElementById("business_license_input").addEventListener("change", function () {
        document.getElementById("license_filename_input").value = this.files[0]?.name || "";
    });

    // 계좌 사본
    document.getElementById("browse_copy_btn").addEventListener("click", () => document.getElementById("settlement_copy_input").click());
    document.getElementById("settlement_copy_input").addEventListener("change", function () {
        document.getElementById("copy_filename_input").value = this.files[0]?.name || "";
    });

    // 은행 직접입력
    const bankSelect = document.querySelector("select[name='settlement_account_bank']");
    const bankInput = document.getElementById("bank_input");
    bankSelect.addEventListener("change", () => {
        if (bankSelect.value === "직접입력") {
            bankInput.style.display = "inline-block";
            bankInput.focus();
        } else {
            bankInput.style.display = "none";
            bankInput.value = "";
        }
    });

    // 사업자번호 조회 및 중복 확인
    document.getElementById("check_business_btn").addEventListener("click", function () {
        let biz = document.getElementById("business_number").value.replace(/-/g, '');
        if (!biz) return popupType(1, "사업자번호를 입력해 주세요.");
        if (!/^\d{10}$/.test(biz)) return popupType(1, "사업자번호는 10자리 숫자로 입력해야 합니다.");
        
        // API 유효성 검증
        $.post("<?= base_url('web/check_biz_api') ?>", { business_number: biz }, res => {
            if (!res.message.includes("유효한 사업자번호")) {
                bizNumberChecked = false;
                return popupType(1, res.message);
            }
            // 중복 확인
            $.post("<?= base_url('web/check_business_number') ?>", { business_number: biz }, res2 => {
                bizNumberChecked = !res2.exists;
                popupType(1, bizNumberChecked ? "사용 가능한 사업자 번호입니다." : "이미 등록된 사업자 번호입니다.");
            }, "json");
        }, "json");
    });

    // 아이디 중복 확인
    document.getElementById("check_user_id_btn").addEventListener("click", function () {
        const uid = document.querySelector("input[name='user_id']").value.trim();
        if (!/^.{5,}$/.test(uid)) return popupType(1, "아이디는 5자 이상이어야 합니다.");
        $.post("<?= base_url('web/check_user_id') ?>", { user_id: uid }, res => {
            userIdChecked = !res.exists;
            popupType(1, userIdChecked ? "사용 가능한 아이디입니다." : "이미 사용 중인 아이디입니다.");
        }, "json");
    });
});

// 순차 검증 후 제출
function clickSignup() {
    const uid   = document.querySelector("input[name='user_id']").value.trim();
    if (!uid)                   return popupType(1, "아이디를 입력해 주세요.");
    if (!userIdChecked)         return popupType(1, "아이디 중복확인을 해주세요.");

    const pw     = document.getElementById("password").value;
    if (!pw)                    return popupType(1, "비밀번호를 입력해 주세요.");
    if (!/^.{8,20}$/.test(pw))  return popupType(1, "비밀번호는 8~20자여야 합니다.");

    const pwr    = document.getElementById("password_confirm").value;
    if (!pwr)                   return popupType(1, "비밀번호 재입력을 해주세요.");
    if (pw !== pwr)             return popupType(1, "비밀번호가 일치하지 않습니다.");

    const name   = document.querySelector("input[name='ceo_name']").value.trim();
    if (!name)                  return popupType(1, "대표자명을 입력해 주세요.");

    const phone  = document.querySelector("input[name='ceo_contact']").value.trim();
    if (!phone)                 return popupType(1, "대표자 연락처를 입력해 주세요.");
    if (!/^[0-9]+$/.test(phone)) return popupType(1, "전화번호는 숫자만 입력해 주세요.");

    const email  = document.querySelector("input[name='ceo_email']").value.trim();
    if (!email)                 return popupType(1, "대표자 이메일을 입력해 주세요.");
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return popupType(1, "이메일 형식이 올바르지 않습니다.");

    const biz    = document.querySelector("input[name='business_number']").value.trim();
    if (!biz)                   return popupType(1, "사업자번호를 입력해 주세요.");
    if (!bizNumberChecked)      return popupType(1, "사업자번호 중복확인을 해주세요.");
    if (document.getElementById("business_license_input").files.length === 0)
                                return popupType(1, "사업자등록증 파일을 선택해 주세요.");

    const brand = document.querySelector("input[name='brand_name']").value.trim();
    if (!brand)                 return popupType(1, "브랜드명을 입력해 주세요.");

    const bank  = document.querySelector("select[name='settlement_account_bank']").value;
    if (!bank)                  return popupType(1, "은행을 선택해 주세요.");
    if (bank === "직접입력" && !document.getElementById("bank_input").value.trim())
                                return popupType(1, "은행명을 직접 입력해 주세요.");

    const acct  = document.querySelector("input[name='settlement_account_number']").value.trim();
    if (!acct)                  return popupType(1, "계좌번호를 입력해 주세요.");

    if (document.getElementById("settlement_copy_input").files.length === 0)
                                return popupType(1, "계좌 사본 파일을 선택해 주세요.");

    // 모두 통과하면 폼 제출
    submitSignup();
}

// 실제 AJAX 제출
function submitSignup() {
    const formData = new FormData(document.getElementById("signup_form"));
    const bankSel = document.querySelector("select[name='settlement_account_bank']");
    if (bankSel.value === "직접입력") formData.set("settlement_account_bank", document.getElementById("bank_input").value);

    $.ajax({
        type: "POST",
        url: "<?= base_url('web/signup_submit'); ?>",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success(response) {
            if (response.status === "success") showSignupAlert();
            else popupType(1, response.message);
        },
        error() { popupType(1, "서버 오류가 발생했습니다."); }
    });
}

function showSignupAlert() {
    $("div#bg_bk").fadeIn();
    $("div#signup_box_alert").fadeIn();
}

function shutSignupAlert() {
    $("div#signup_box_alert").fadeOut();
    $("div#bg_bk").fadeOut();
    window.location.href = "<?= base_url('web/login'); ?>";
}
</script>

<style>
.input_guide_text { font-size: 12px; color: #888; margin: 5px 0 10px; }
.file_name_label { margin-left: 10px; font-size: 13px; color: #444; }
</style>
