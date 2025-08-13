<div class="myinfo_box">
    <form id="myinfo_form" onsubmit="event.preventDefault(); updateMyInfo();">
        <div class="">
            <div class="signup_box_title">내 정보수정</div>

            <div class="signup_box_t"><span>*</span>&nbsp;아이디</div>
            <input type="text" class="signup_box_c" value="<?= htmlspecialchars($user->user_id); ?>" readonly />

            <div class="signup_box_t"><span>*</span>&nbsp;비밀번호</div>

            <input type="password" name="password" class="signup_box_c" placeholder="비밀번호를 입력해 주세요." />
            <input type="password" name="password_confirm" class="signup_box_c signup_box_c_normal" placeholder="비밀번호 재입력" />
            <div class="signup_box_guide_error" style="display:none; color:red;">다시 입력해주세요.</div>

            <div class="signup_box_t"><span>*</span>&nbsp;사업자 번호</div>
            <input type="text" class="signup_box_c_se" value="<?= htmlspecialchars($user->business_number); ?>" readonly />
            <div class="clear"></div>

            <div class="signup_box_t"><span>*</span>&nbsp;대표자명</div>
            <input type="text" class="signup_box_c" name="ceo_name" value="<?= htmlspecialchars($user->ceo_name); ?>" />

            <div class="signup_box_t"><span>*</span>&nbsp;대표자 연락처</div>
            <input type="text" class="signup_box_c" name="ceo_contact" value="<?= htmlspecialchars($user->ceo_contact); ?>" />
            <div id="ceo_contact_error" class="signup_box_guide_error" style="display:none; color:red;">
                전화번호는 숫자로만 입력해주세요.
            </div>

            <div class="signup_box_t"><span>*</span>&nbsp;대표자 이메일</div>
            <input type="text" class="signup_box_c" name="ceo_email" value="<?= htmlspecialchars($user->ceo_email); ?>" />
            <div id="ceo_email_error" class="signup_box_guide_error" style="display:none; color:red;">
                이메일 형식에 맞지 않습니다.
            </div>

            <div class="signup_box_t">회원 유형</div>
            <select class="signup_box_c_sel_se" name="member_type">
                <option value="">회원유형을 선택해주세요.</option>
                <option value="1" <?= $user->member_type == "1" ? 'selected' : '' ?>>위탁판매</option>
                <option value="2" <?= $user->member_type == "2" ? 'selected' : '' ?>>직접판매</option>
            </select>
            <input type="button" class="signup_box_c_bt_se" value="?" />
            <div class="clear"></div>

            <div class="signup_box_guide">위탁항목 : 브랜드집을 통해 위탁판매를 희망하는 업체 / 직접판매 : 브랜드집을 활용해 직접판매를 희망하는 업체</div>

            <div class="signup_box_t">홈페이지</div>
            <input type="text" class="signup_box_c" name="website" value="<?= htmlspecialchars($user->website); ?>" />

            <div class="signup_box_t">브랜드 명</div>
            <input type="text" class="signup_box_c" name="brand_name" value="<?= htmlspecialchars($user->brand_name); ?>" readonly />

            <div class="signup_box_t"><span>*</span>&nbsp;선정산 받을 계좌번호</div>
            <input type="text" class="signup_box_c_se" value="<?= htmlspecialchars($user->settlement_account_number); ?>" readonly />
            <div class="clear"></div>

            <div class="signup_box_t"><span>*</span>&nbsp;선정산 받을 계좌 사본</div>
            <input type="text" class="signup_box_c_se" value="<?= htmlspecialchars($user->settlement_account_copy); ?>" readonly />
            <div class="clear"></div>
            <div class="signup_box_guide">계좌번호 변경은 1:1 문의를 이용해주세요.</div>

            <br /><br />
            <div class="signup_box_bt_green" onclick="updateMyInfo()">저장하기</div>
            <div class="signup_box_bt_black" onclick="window.location.href='<?= base_url('web/mypage'); ?>'">취소</div>
            <div class="clear"></div>
        </div>
    </form>
    <div class="clear"></div>

    <div id="popup_type_1" class="bg_bk_alert" style="display: none;">
        <div class="bg_bk_alert_x" onclick="popupType(1)"></div>
        <div class="bg_bk_alert_txt">팝업내용입니다.</div>
        <div class="bg_bk_alert_bt" onclick="popupType(1)">확인</div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const pwInput = document.querySelector("input[name='password']");
    const pwConfirmInput = document.querySelector("input[name='password_confirm']");
    const contactInput = document.querySelector("input[name='ceo_contact']");
    const emailInput = document.querySelector("input[name='ceo_email']");
    const submitBtn = document.querySelector(".signup_box_bt_green");

    pwInput.addEventListener("input", validateForm);
    pwConfirmInput.addEventListener("input", validateForm);
    contactInput.addEventListener("input", validateForm);
    emailInput.addEventListener("input", validateForm);

    function validateForm() {
        const pw = pwInput.value.trim();
        const pwConfirm = pwConfirmInput.value.trim();
        const ceoContact = contactInput.value.trim();
        const ceoEmail = emailInput.value.trim();

        const pwErrorBox = document.querySelector(".signup_box_guide_error");
        const contactError = document.getElementById("ceo_contact_error");
        const emailError = document.getElementById("ceo_email_error");

        let valid = true;

        // 비밀번호 유효성
        if (pw === "" && pwConfirm === "") {
            pwErrorBox.style.display = "none";
            pwConfirmInput.classList.remove("signup_box_c_error");
            pwConfirmInput.classList.add("signup_box_c_normal");
        } else if (pw.length < 8 || pw.length > 20) {
            pwErrorBox.textContent = "비밀번호는 8자 이상 20자 이하로 입력해주세요.";
            pwErrorBox.style.display = "block";
            valid = false;
        } else if (pw !== pwConfirm) {
            pwErrorBox.textContent = "비밀번호가 일치하지 않습니다.";
            pwErrorBox.style.display = "block";
            pwConfirmInput.classList.add("signup_box_c_error");
            valid = false;
        } else {
            pwErrorBox.style.display = "none";
            pwConfirmInput.classList.remove("signup_box_c_error");
            pwConfirmInput.classList.add("signup_box_c_normal");
        }

        // 연락처 검사
        if (ceoContact !== "" && !/^[0-9]+$/.test(ceoContact)) {
            contactError.style.display = "block";
            valid = false;
        } else {
            contactError.style.display = "none";
        }

        // 이메일 검사
        if (ceoEmail !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ceoEmail)) {
            emailError.style.display = "block";
            valid = false;
        } else {
            emailError.style.display = "none";
        }

        // 버튼 상태 조정
        submitBtn.style.opacity = valid ? "1" : "0.5";
        submitBtn.style.pointerEvents = valid ? "auto" : "none";
    }
});
</script>

<script>
    function popupType(type, message = "") {
        const popup = document.getElementById(`popup_type_${type}`);
        const popupText = popup.querySelector(".bg_bk_alert_txt");

        if (popup.style.display === "block") {
            popup.style.display = "none";

            // type 1 팝업 닫을 때 mypage로 이동
            if (type === 1) {
                window.location.href = "<?= base_url('web/mypage'); ?>";
            }
        } else {
            popupText.textContent = message || "팝업내용입니다.";
            popup.style.display = "block";
        }
    }
</script>

<script>
function updateMyInfo() {
    const formData = {
        ceo_name: $("input[name='ceo_name']").val(),
        ceo_contact: $("input[name='ceo_contact']").val(),
        ceo_email: $("input[name='ceo_email']").val(),
        business_license: $("input[name='business_license']").val(),
        member_type: $("select[name='member_type']").val(),
        website: $("input[name='website']").val(),
        brand_name: $("input[name='brand_name']").val(),
    };

    const password = $("input[name='password']").val();
    const passwordConfirm = $("input[name='password_confirm']").val();

    if (password && password === passwordConfirm) {
        formData.password = password;
    }

    $.ajax({
        type: "POST",
        url: "<?= base_url('web/update_myinfo'); ?>",
        data: formData,
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                popupType(1, "변경한 정보가 저장되었습니다.");
            } else {
                popupType(1, "수정에 실패했습니다.");
            }
        },
        error: function () {
            alert("서버 오류가 발생했습니다.");
        }
    });
}
</script>

<style>
input[readonly] {
    background-color: #f5f5f5;
    color: #555;
    cursor: not-allowed;
}
</style>