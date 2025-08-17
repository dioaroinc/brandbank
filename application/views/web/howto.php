<div class="howto">
    <img src="/garage/images/howto1.png" /><div class="clear"></div>
    <img src="/garage/images/howto2.png" /><div class="clear"></div>
    <img src="/garage/images/howto3.png" /><div class="clear"></div>
    <img src="/garage/images/howto4.png" /><div class="clear"></div>
    <img src="/garage/images/howto5.png" /><div class="clear"></div>
    <img src="/garage/images/howto6.png" /><div class="clear"></div>
</div>
<div class="howtoslide">

    <div class="howtoslide_t">선정산 서비스 이용 방법 🔍️</div>
    
    <div class="howtoslide_bt_s" id="howtoslide_bt_1" onclick="howtoslide(1)">01 조회하기</div>
    <div class="howtoslide_bt" id="howtoslide_bt_2" onclick="howtoslide(2)">02 신청하기</div>
    <div class="howtoslide_bt" id="howtoslide_bt_3" onclick="howtoslide(3)">03 신청 확인하기</div>
    <div class="howtoslide_bt" id="howtoslide_bt_4" onclick="howtoslide(4)">04 지급받기</div>
    <div class="clear"></div>

    <div class="howtoslide_txt" id="howtoslidetxt_1">
        선정산 조회 페이지에서 선정산을 원하는 채널을 선택하고 조회하기 버튼을 클릭해<br />
        받을 수 있는 선정산금을 확인해 보세요.
    </div>
    <div class="howtoslide_txt" id="howtoslidetxt_2" style="display: none">
        받을 수 있는 선정산금 안에서 원하는 액수를 직접 입력한 뒤,<br />
        신청하기 버튼을 눌러보세요.
    </div>
    <div class="howtoslide_txt" id="howtoslidetxt_3" style="display: none">
        신청이 완료되면 열리는 팝업창에서 내가 신청한 정산금의 잔액을 확인하고<br />
        채널별 신청 내역을 확인해보세요.
    </div>
    <div class="howtoslide_txt" id="howtoslidetxt_4" style="display: none">
        신청한 선정산금은 회원가입 시 입력한 계좌로 일주일 이내에 입금돼요.<br />
        입금된 선정산금을 확인해 보세요!
    </div>

    <img src="/garage/images/step_1.png" class="howtoslide_img" id="howtoslideimg_1" /><div class="clear"></div>
    <img src="/garage/images/step_2.png" class="howtoslide_img" id="howtoslideimg_2" style="display: none" /><div class="clear"></div>
    <img src="/garage/images/step_3.png" class="howtoslide_img" id="howtoslideimg_3" style="display: none" /><div class="clear"></div>
    <img src="/garage/images/step_4.png" class="howtoslide_img" id="howtoslideimg_4" style="display: none" /><div class="clear"></div>
    
    
</div>

<script>
    function howtoslide(num){

        $("div.howtoslide_bt_s").attr("class","howtoslide_bt");
        $("div#howtoslide_bt_"+num).attr("class","howtoslide_bt_s");

        $("div.howtoslide_txt").hide();
        $("div#howtoslidetxt_"+num).show();

        $(".howtoslide_img").hide();
        $("#howtoslideimg_"+num).show();
    }
</script>