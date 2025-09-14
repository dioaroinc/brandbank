
<div class="container2">

    <div class="n_c_title">
        <div class="n_c_title_name"><?= htmlspecialchars($user->ceo_name); ?>(<?= htmlspecialchars($user->brand_name); ?>)님 </div>
        <div class="n_c_title_logout"><a href="javascript:void(0);" onclick="confirmLogout()"><img src="/garage/images/img_logout.png" /></a></div>
        <div class="n_c_title_myinfo"><a href="/web/myinfo"><img src="/garage/images/img_myinfo.png" /></a></div>
        <div class="clear"></div>
    </div>

    <div class="n_c_title_img"><img src="/garage/images/text_type_5.png" /></div>

    <div class="n_mypage_m1">
        <div class="n_mypage_m_l">이번 달 전체 정산액</div>
        <div class="n_mypage_m_r"><?= number_format($total_requested) ?> <span>원</span></div>
        <div class="clear"></div>
    </div>
    <div class="n_mypage_m2">
        <div class="n_mypage_m_l">이번 달 선정산 정산액</div>
        <div class="n_mypage_m_r"><?= number_format($total_paid) ?> <span>원</span></div>
        <div class="clear"></div>
    </div>
<!--removed by DJK 250818-->
    <!-- <div class="n_mypage_m3">
        <div class="n_mypage_m_l">이번 달 선정산 지급액</div>
        <div class="n_mypage_m_r">0 <span>원</span></div>
        <div class="clear"></div>        
    </div>
    <div class="n_mypage_m4">
        <div class="n_mypage_m_l">이번 달 남은 정산액</div>
        <div class="n_mypage_m_r">0 <span>원</span></div>
        <div class="clear"></div>        
    </div> -->
    <div class="clear"></div>


    <div class="n_c_title_img"><img src="/garage/images/text_type_6.png" /></div>

    <!--2-->
    <div class="tb_w_green">

        <div class="n_mypage_date">
            <?=substr($settlements_all[0] -> pre_settlement_date, 5,2)?>월
            <?=substr($settlements_all[0] -> pre_settlement_date, 8,2)?>일
        </div>
        <div class="clear"></div>

        <div class="n_mypage_date_lef">
            <span class="n_mypage_date_lef_span">총 <?=number_format($settlements_all[0] -> application_amount)?></span> 원<br />
            정산 받았어요.
        </div>
        <div class="clear"></div>

        <div class="tr_t">
            <div class="td20">날짜</div>
            <div class="td20">신청 쇼핑몰</div>
            <div class="td20">신청 금액</div>
            <div class="td20">지급 금액</div>
            <div class="td20">비고</div>
            <div class="clear"></div>
        </div>
        <?php foreach ($settlements_all as $item): ?>
            <?php if ($item->status == 0 || $item->status == 4) continue; ?>
            <div class="td">
                <div class="td20"><?= $item->pre_settlement_date ?></div>
                <div class="td20"><?= $item->shopping_mall ?></div>
                <div class="td20"><?= number_format($item->application_amount) ?></div>
                <div class="td20">
                    <?php
                        if ($item->status == 3) {
                            echo '거절';
                        } elseif ($item->status == 1) {
                            echo '정산중';
                        } else {
                            echo number_format($item->application_amount);
                        }
                    ?>
                </div>
                <div class="td20">
                    <?php if ($item->status == 1): ?>
                        <div class="td_bt" onclick="cancelSettlement(<?= $item->num ?>)">신청취소</div>
                    <?php elseif ($item->status == 2): ?>
                        <div class="td_bt" style="background-color: #ccc; cursor: default;">지급완료</div>
                    <?php elseif ($item->status == 3): ?>
                        <div class="td_bt" style="background:#f44336;color:#fff;cursor:pointer;"
                            onclick="showRejectReason('<?= htmlspecialchars(addslashes($item->reject_reason)) ?>')">
                            <span style="vertical-align:middle;">거절사유</span>
                            <span style="font-size:18px;vertical-align:middle;margin-left:3px;">&#9432;</span>
                        </div>
                    <?php else: ?>
                        &nbsp;
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>

    </div><!--tb_w END-->

    <!-- 팝업 -->
    <div id="popup_type_2" class="bg_bk_alert" style="display:none;">
        <div class="bg_bk_alert_x" onclick="popupType(2)"></div>
        <div class="bg_bk_alert_txt" id="popup_message">팝업내용입니다.</div>
        <div class="bg_bk_confirm_gr" onclick="confirmAction()">예</div>
        <div class="bg_bk_confirm_bk" onclick="popupType(2)">아니오</div>
        <div class="clear"></div>
    </div>

    <div id="popup_reject_reason" class="bg_bk_alert" style="display:none;">
        <div class="bg_bk_alert_x" onclick="closeRejectReasonPopup()"></div>
        <div class="bg_bk_alert_txt" id="reject_reason_text"></div>
        <div class="bg_bk_confirm_gr" onclick="closeRejectReasonPopup()">확인</div>
        <div class="clear"></div>
    </div>


</div><!--container END-->

<script>
let selectedCancelId = null;
let currentAction = null;

function cancelSettlement(num) {
    selectedCancelNum = num;
    currentAction = 'cancel';
    document.getElementById('popup_message').textContent = "신청을 취소하시겠습니까?";
    popupType(2);
}

function confirmLogout() {
    currentAction = 'logout';
    document.getElementById('popup_message').textContent = "로그아웃 하시겠습니까?";
    popupType(2);
}

function confirmAction() {
    if (currentAction === 'cancel' && selectedCancelNum !== null) {
        $.post("<?= base_url('web/cancel_settlement') ?>", { num: selectedCancelNum }, function(response) {
            alert(response.message);
            if (response.status === "success") {
                location.reload();
            }
        }, 'json');
    } else if (currentAction === 'logout') {
        location.href = "<?= base_url('web/logout'); ?>";
    }
    popupType(2);  // 팝업 닫기
}

function popupType(type) {
    if (type === 2) {
        $('#popup_type_2').toggle();
    }
}

function showRejectReason(reason) {
    document.getElementById('reject_reason_text').textContent = reason;
    document.getElementById('popup_reject_reason').style.display = 'block';
}
function closeRejectReasonPopup() {
    document.getElementById('popup_reject_reason').style.display = 'none';
}

</script>
