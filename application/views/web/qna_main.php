<div class="container">
    <div class="b_title">문의하기<img src="/garage/images/ic_chat.png" /></div>

    <!-- 버튼 영역 -->
    <div class="qna_button_box">
        <div class="qna_btn" onclick="goQnaWrite()" style="background-image: url('/garage/images/qna_1.png')"></div>
        <div class="qna_btn" onclick="goQnaList()" style="background-image: url('/garage/images/qna_2.png')"></div>
    </div>

    <div id="popup_type_1" class="bg_bk_alert" style="display: none;">
        <div class="bg_bk_alert_x" onclick="popupType(1)"></div>
        <div class="bg_bk_alert_txt">팝업내용입니다.</div>
        <div class="bg_bk_alert_bt" onclick="popupType(1)">확인</div>
    </div>

</div><!--container END-->

<script>
    const isLoggedIn = <?= $this->session->userdata('user_id') ? 'true' : 'false' ?>;
</script>

<script>
function popupType(type) {
    const popup = document.getElementById('popup_type_' + type);
    popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
}

function goQnaWrite() {
    if (!isLoggedIn) {
        document.querySelector('#popup_type_1 .bg_bk_alert_txt').innerText = "로그인 후 이용해주세요.";
        popupType(1);
        return;
    }
    location.href = "<?= base_url('web/qna_write') ?>";
}

function goQnaList() {
    if (!isLoggedIn) {
        document.querySelector('#popup_type_1 .bg_bk_alert_txt').innerText = "로그인 후 이용해주세요.";
        popupType(1);
        return;
    }
    location.href = "<?= base_url('web/qna') ?>";
}
</script>

<style>
    .qna_button_box {width: 810px; height: 360px; margin: 170px auto}
    .qna_btn {float: left; width: 400px; height: 360px; margin: 0px 5px 0px 0px; background-size: contain; background-position: center; background-repeat: no-repeat}
</style>
