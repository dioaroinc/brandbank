<div class="login_box">
    <div class="login_box_l">

        <div class="login_box_title"></div>

        <input type="text" id="user_id" class="login_box_c" placeholder="아이디" />
        <input type="password" id="password" class="login_box_c" placeholder="비밀번호" />

        <div class="login_box_auto">
            <input type="hidden" id="login_checkbox_ipt" value="0" />
            <div class="custom_checkbox" id="login_checkbox" onclick="djkAutoLogin()"></div>
            <div class="flt_l">자동로그인</div>
            <div class="clear"></div>
        </div>
        <div class="login_box_link">
            <a href="/web/find_id">아이디</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="/web/find_pass">비밀번호 찾기</a>
        </div>
        <div class="clear"></div>

        <div class="login_box_bt" onclick="loginUser()">로그인</div>

        <div class="login_box_link_b">
            <a href="/web/signup">회원가입</a>
        </div>        
    </div>
    <div class="login_box_r"></div>
    <div class="clear"></div>
</div>

<div id="login_box_alert" class="bg_bk_alert" style="display: none;">
    <div class="bg_bk_alert_x" onclick="shutLoginAlert()"></div>
    <div class="bg_bk_alert_txt" id="alert_message">아이디 혹은 비밀번호가 맞지 않습니다.</div>
    <div class="bg_bk_alert_bt" onclick="shutLoginAlert()">확인</div>
</div>


<script>
    // script from DJK for GUI
    function djkAutoLogin(){
        var nowAuto = document.getElementById("login_checkbox_ipt").value;
        if(nowAuto == 0){
            $("#login_checkbox").attr("class","custom_checkbox_s");
            document.getElementById("login_checkbox_ipt").value = 1;

        }
        else{
            $("#login_checkbox").attr("class","custom_checkbox");
            document.getElementById("login_checkbox_ipt").value = 0;
        }
    }
</script>

<script>
    function loginUser() {
        let user_id = $("#user_id").val();
        let password = $("#password").val();

        if (!user_id || !password) {
            $("#alert_message").text("아이디와 비밀번호를 입력하세요.");
            showLoginAlert();
            return;
        }

        $.ajax({
            type: "POST",
            url: "<?= base_url('web/login_process'); ?>",
            data: { user_id: user_id, password: password },
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    window.location.href = response.redirect;
                } else if (response.status === "waiting") {
                    $(".bg_bk_alert_txt").text("승인 대기 중입니다.");
                    popupType(1);
                } else if (response.status === "rejected") {
                    $(".bg_bk_alert_txt").text("다음의 사유로 거절되었습니다.\n\n" + response.message);
                    popupType(1);
                } else if (response.status === "pause") {
                    $(".bg_bk_alert_txt").text("다음의 사유로 계정 사용이 제한중입니다.\n\n" + response.message);
                    popupType(1);
                } else {
                    $("#alert_message").text(response.message);
                    showLoginAlert();
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX 오류:", xhr, status, error); // 오류 확인
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

<div id="popup_type_0" class="bg_bk_alert_new5">
    <div class="bg_bk_alert_new_title_t">로그아웃</div>
    <div class="bg_bk_alert_new_x" onclick="popupType(0)"></div>
    <div class="clear"></div>

    <div class="bg_bk_alert_new_title_c">로그아웃<br />되었습니다.</div>
    <div class="bg_bk_alert_new_title_img">
        <img src="/garage/images/img_login.png" />
    </div>
    <div class="clear"></div>
</div>


<div id="popup_type_1" class="bg_bk_alert_new5">
    <div class="bg_bk_alert_new_title_t">회원가입 진행상황</div>
    <div class="bg_bk_alert_new_x" onclick="popupType(1)"></div>
    <div class="clear"></div>

    <div class="bg_bk_alert_new_title_c">현재 진행상황은<br />아래와 같습니다.</div>
    <div class="bg_bk_alert_new_title_img">
        <img src="/garage/images/img_login.png" />
    </div>
    <div class="clear"></div>
    <div class="bg_bk_alert_txt_new_w">
        <div class="bg_bk_alert_txt" style="white-space: pre-line;">팝업내용입니다.</div>
    </div>
</div>

<script>
    function popupType(type) {
        const popup = $("#popup_type_" + type);
        popup.toggle();
    }
</script>

<?php if ($this->session->flashdata('custom_popup')): ?>
<script>
window.onload = function() {

    const msg = "<?= $this->session->flashdata('custom_popup'); ?>";

    if(msg == "로그아웃 되었습니다."){
        $("#popup_type_0").fadeIn();
    }
    else{
        $("#popup_type_1 .bg_bk_alert_txt").text(msg);
        $("#popup_type_1").fadeIn();
    }
    
};
</script>
<?php endif; ?>