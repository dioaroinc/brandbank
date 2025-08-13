<div class="signup_box">
    <div class="signup_box_r">
        <div class="signup_box_title">관리자 등록</div>
        <form id="admin_signup_form">
            <div class="signup_box_t"><span>*</span>&nbsp;관리자 아이디</div>
            <input type="text" class="signup_box_c" name="admin_id" placeholder="아이디를 입력해 주세요." required />
            <input type="button" class="signup_box_c_bt" id="check_admin_id_btn" value="중복 확인" />
            <div class="input_guide_text">아이디는 영문+숫자 조합, 5자 이상 20자 미만</div>

            <div class="signup_box_t"><span>*</span>&nbsp;이름</div>
            <input type="text" class="signup_box_c" name="name" placeholder="이름을 입력해 주세요." required />

            <div class="signup_box_t"><span>*</span>&nbsp;비밀번호</div>
            <input type="password" id="password" class="signup_box_c" name="password" placeholder="비밀번호를 입력해 주세요." required />
            <div class="input_guide_text">비밀번호는 영문+숫자 조합, 8자 이상</div>

            <div class="signup_box_t"><span>*</span>&nbsp;비밀번호 재입력</div>
            <input type="password" id="password_confirm" class="signup_box_c" placeholder="비밀번호를 재입력 해주세요." onkeyup="checkPasswordMatch()" />
            <div id="password_match_error" class="signup_box_guide_error" style="display: none; color: red;">ⓘ 비밀번호가 일치하지 않습니다.</div>

            <br /><br />
            <div class="signup_box_bt" id="admin_signup_button" onclick="submitAdminSignup()" style="opacity: 0.5; pointer-events: none;">등록하기</div>
        </form>
    </div>
    <div class="clear"></div>
</div>

<div id="popup_type_1" class="bg_bk_alert" style="display: none;">
    <div class="bg_bk_alert_txt">팝업내용입니다.</div>
    <div class="bg_bk_alert_bt" onclick="popupType(1)">확인</div>
</div>

<script>
    let adminIdChecked = false;

    document.getElementById("check_admin_id_btn").addEventListener("click", function () {
        const adminId = document.querySelector("input[name='admin_id']").value;
        if (!/^[a-zA-Z0-9]{5,19}$/.test(adminId)) {
            popupType(1, "아이디는 영문+숫자 조합, 5자 이상 20자 미만이어야 합니다.");
            return;
        }
        $.post("<?= base_url('manage/check_admin_id') ?>", { admin_id: adminId }, function (res) {
            if (res.exists) {
                popupType(1, "이미 사용 중인 아이디입니다.");
                adminIdChecked = false;
            } else {
                popupType(1, "사용 가능한 아이디입니다.");
                adminIdChecked = true;
            }
            validateForm();
        }, "json");
    });

    function checkPasswordMatch() {
        const password = document.getElementById("password").value;
        const passwordConfirm = document.getElementById("password_confirm").value;
        const errorMessage = document.getElementById("password_match_error");

        if (password !== passwordConfirm || password === "" || passwordConfirm === "") {
            errorMessage.style.display = "block";
        } else {
            errorMessage.style.display = "none";
        }
        validateForm();
    }

    function validateForm() {
        const adminId = document.querySelector("input[name='admin_id']").value;
        const password = document.getElementById("password").value;
        const passwordConfirm = document.getElementById("password_confirm").value;
        const signupButton = document.getElementById("admin_signup_button");

        const idValid = /^[a-zA-Z0-9]{5,19}$/.test(adminId);
        const pwValid = /^(?=.*[a-zA-Z])(?=.*[0-9])[A-Za-z0-9]{8,}$/.test(password);
        const pwMatch = (password === passwordConfirm) && password !== "";

        if (idValid && pwValid && pwMatch && adminIdChecked) {
            signupButton.style.opacity = "1";
            signupButton.style.pointerEvents = "auto";
        } else {
            signupButton.style.opacity = "0.5";
            signupButton.style.pointerEvents = "none";
        }
    }

    function submitAdminSignup() {
        let formData = $("#admin_signup_form").serialize();

        $.ajax({
            type: "POST",
            url: "<?= base_url('manage/admin_signup_submit'); ?>",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    popupType(1, "관리자 등록이 완료되었습니다.");
                    setTimeout(function() {
                        window.location.href = "<?= base_url('manage/login'); ?>";
                    }, 10);
                } else {
                    popupType(1, response.message);
                }
            },
            error: function() {
                popupType(1, "서버 오류가 발생했습니다.");
            }
        });
    }

    function popupType(type, message = "") {
        const popup = document.getElementById(`popup_type_${type}`);
        const popupText = popup.querySelector(".bg_bk_alert_txt");

        if (popup.style.display === "block") {
            popup.style.display = "none";   // 팝업 닫기
        } else {
            popupText.textContent = message || "팝업내용입니다.";
            popup.style.display = "block";  // 팝업 열기
        }
    }

</script>
