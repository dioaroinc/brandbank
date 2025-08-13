<div class="find_id_box">

    <div class="find_id_title">아이디/비밀번호 찾기</div>
    
    <div class="find_id_tab_s" onclick="location.href='/web/find_id'">아이디 찾기</div>
    <div class="find_id_tab" onclick="location.href='/web/find_pass'">비밀번호 찾기</div>
    <div class="clear"></div>

    <div id="find_id_req">
        <div class="login_box_t">사업자번호</div>
        <input type="text" id="business_number" class="login_box_c" placeholder="사업자번호를 입력하세요." />
        <div class="login_box_t">이메일주소</div>
        <input type="text" id="ceo_email" class="login_box_c" placeholder="이메일주소를 입력하세요." />

        <br /><br />
        <div class="login_box_bt"  onclick="findId()">확인</div>
    </div>
    <div id="find_id_res" class="no_dis">
    <div class="find_id_res" id="result_message">
        해당 정보에 해당하는 아이디는 <br />
        <span></span><br />
        입니다.
    </div>

    <br /><br />
    <div class="login_box_bt" onclick="location.href='/web/login'">확인</div>
</div>

<div id="popup_type_1" class="bg_bk_alert" style="display: none;">
    <div class="bg_bk_alert_x" onclick="popupType(1)"></div>
    <div class="bg_bk_alert_txt">팝업내용입니다.</div>
    <div class="bg_bk_alert_bt" onclick="popupType(1)">확인</div>
</div>

</div>

<script>
    function findId(){
        let business_number = $(".login_box_c").eq(0).val().trim();
        let ceo_email = $(".login_box_c").eq(1).val().trim();

        if(business_number === "" || ceo_email === ""){
            showPopup("사업자번호와 이메일을 모두 입력하세요.");
            return;
        }

        $.ajax({
            url: '/web/find_id_check',
            type: 'POST',
            data: {
                business_number: business_number,
                ceo_email: ceo_email
            },
            dataType: 'json',
            success: function(response){
                $("#find_id_req").hide();
                $("#find_id_res").show();

                if(response.status === 'success'){
                    $("#result_message").html(`
                        해당 정보에 해당하는 아이디는 <br />
                        <span>${response.user_id}</span><br />
                        입니다.
                    `);
                } else {
                    $("#result_message").html(`
                        해당 정보로 가입된 아이디가 없습니다.<br />
                        <span></span>
                    `);
                }
            },
            error: function(){
                alert("오류가 발생했습니다. 다시 시도해주세요.");
            }
        });
    }
</script>

<script>
    function showPopup(message) {
        $(".bg_bk_alert_txt").text(message);
        $("#popup_type_1").show();
    }

    function popupType(type){
        if(type === 1){
            $("#popup_type_1").hide();
        }
    }
</script>