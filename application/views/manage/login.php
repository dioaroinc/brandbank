<div class="login_box">
    <div class="login_box_manage">
        <div class="login_box_t">아이디</div>
        <input type="text" id="admin_id" class="login_box_c" placeholder="아이디를 입력하세요." />
        <div class="login_box_t">비밀번호</div>
        <input type="password" id="password" class="login_box_c" placeholder="비밀번호를 입력하세요." />

        <div class="login_box_bt" onclick="loginUser()">로그인</div>

    </div>
    <div class="clear"></div>
</div>

<div id="login_box_alert" class="bg_bk_alert" style="display: none;">
    <div class="bg_bk_alert_x" onclick="shutLoginAlert()"></div>
    <div class="bg_bk_alert_txt" id="alert_message">아이디 혹은 비밀번호가 맞지 않습니다. 문제가 지속될 경우 관리자에게 문의해주세요. (010-0000-0000)</div>
    <div class="bg_bk_alert_bt" onclick="shutLoginAlert()">확인</div>
</div>

<script>
    function loginUser() {
        let admin_id = $("#admin_id").val();
        let password = $("#password").val();

        if (!admin_id || !password) {
            $("#alert_message").text("아이디와 비밀번호를 입력하세요.");
            showLoginAlert();
            return;
        }

        $.ajax({
            type: "POST",
            url: "<?= base_url('manage/login_process'); ?>",
            data: {admin_id: admin_id, password: password },
            dataType: "json",
            success: function(response) {
                console.log(response);

                if (response.status === "success") {
                    window.location.href = response.redirect;
                } else {
                    $("#alert_message").text(response.message);
                    showLoginAlert();
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX 오류:", xhr, status, error);
                $("#alert_message").text("서버 오류가 발생했습니다.");
                showLoginAlert();
            }
        });
    }

    function showLoginAlert() {
        $("#login_box_alert").fadeIn();
    }

    function shutLoginAlert() {
        $("#login_box_alert").fadeOut();
    }
</script>
