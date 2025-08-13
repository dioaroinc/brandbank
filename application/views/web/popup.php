
<div style="margin: 100px">

    <input type="button" value="버튼하나팝업" onclick="popupType(1)" />
    <input type="button" value="버튼두개팝업" onclick="popupType(2)" />

    <input type="button" value="상세신청내역팝업" onclick="popupType(3)" />

</div>



<div id="popup_type_1" class="bg_bk_alert">
    <div class="bg_bk_alert_x" onclick="popupType(1)"></div>
    <div class="bg_bk_alert_txt">팝업내용입니다.</div>
    <div class="bg_bk_alert_bt" onclick="popupType(1)">확인</div>
</div>

<div id="popup_type_2" class="bg_bk_alert">
    <div class="bg_bk_alert_x" onclick="popupType(2)"></div>
    <div class="bg_bk_alert_txt">팝업내용입니다.</div>
    <div class="bg_bk_confirm_gr" onclick="popupType(2)">예</div>
    <div class="bg_bk_confirm_bk" onclick="popupType(2)">아니오</div>
    <div class="clear"></div>
</div>

<div id="popup_type_3" class="bg_bk_detail">
    <div class="bg_bk_alert_x" onclick="popupType(3)"></div>
    <div class="popup_t">
        <div class="popup_t_lef">대상매출</div>
        <div class="popup_t_rig">50,000,000원</div>
        <div class="clear"></div>
    </div>

    <div class="popup_c">
        <div class="popup_t_lef">예상 정산요율</div>
        <div class="popup_t_rig">35%</div>
        <div class="clear"></div>
    </div>
    
    <div class="popup_c2">
        <div class="popup_c2_lef">
            정산요율 반영금액<br />
            <span>(정산 예정 금액)</span>
        </div>
        <div class="popup_c2_rig">30,000원</div>
        <div class="clear"></div>
    </div>

    
    <div class="popup_c">
        <div class="popup_t_lef">지급율</div>
        <div class="popup_t_rig">80%</div>
        <div class="clear"></div>
    </div>

    
    <div class="popup_c2">
        <div class="popup_c2_lef">
            지급율 적용 금액 <br />
            <span>(정산요율 반영금액에 예상반품율을 반영)</span>
        </div>
        <div class="popup_c2_rig">35%</div>
        <div class="clear"></div>
    </div>

    
    <div class="popup_c">
        <div class="popup_t_lef">선정산 가능 금액</div>
        <div class="popup_t_rig">450,000원</div>
        <div class="clear"></div>
    </div>

    <div class="popup_c">
        <div class="popup_t_lef">신청 금액</div>
        <div class="popup_t_rig">200,000원</div>
        <div class="clear"></div>
    </div>

    <div class="popup_close" onclick="popupType(3)">닫기</div>

</div>


<script>
    function popupType(type){
        
        if(type == 3){
            $("div#bg_bk").toggle();
        }

        $("div#popup_type_"+type).toggle();

    }
</script>