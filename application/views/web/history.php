<div class="container2">

    <div class="b_title">선정산 내역 조회 <img src="/garage/images/ic_search.png" /></div>

<?php
    $current_month = date('Y-m');
    $end_month   = isset($end_date) && $end_date ? substr($end_date, 0, 7) : $current_month;
?>
    <form method="get" action="">
        <div class="n_search_bar">
            <input type="month" name="start_date" class="filter_date"
                value="<?= htmlspecialchars($end_month) ?>">
            <input type="month" name="end_date" class="filter_date"
                value="<?= htmlspecialchars($end_month) ?>">
            <a href="<?= base_url('web/history') ?>" class="n_filter_reset">초기화</a>
            <button type="submit" class="n_filter_bt">적용하기</button>
            <div class="clear"></div>
        </div>
    </form>

    <div class="tb_w_green">


    <?php if (!empty($history_by_month)): ?>
        <?php foreach ($history_by_month as $month => $records): ?>

            <div class="n_mypage_date"><?= htmlspecialchars($month) ?>월</div>
            <div class="clear"></div>

            <div class="n_mypage_date_lef">
                <span class="n_mypage_date_lef_span">총 <?= number_format($month_total) ?></span> 원<br />
                미리 정산 받았어요.
            </div>
            <div class="clear"></div>


            <div class="n_search_inner">
                <div class="tb_w_scroll">
                    <div class="tr_t">
                        <div class="td20">날짜</div>
                        <div class="td20">신청 쇼핑몰</div>
                        <div class="td20">신청금액</div>
                        <div class="td20">지급금액</div>
                        <div class="td20">비고</div>
                        <div class="clear"></div>
                    </div>

                    <?php $month_total = 0; ?>
                    <?php foreach ($records as $row): ?>
                    <?php if ((int)$row['status'] === 0) continue; // status=0인 항목은 출력하지 않음 ?>
                        <div class="td">
                            <div class="td20"><?= htmlspecialchars($row['pre_settlement_date']) ?></div>
                            <div class="td20"><?= htmlspecialchars($row['shopping_mall']) ?></div>
                            <div class="td20"><?= number_format($row['application_amount']) ?></div>
                            <div class="td20"><?= ($row['status'] == 2) ? number_format($row['application_amount']) : '-' ?></div>
                            <div class="td20">
                            <?php
                                $status = intval($row['status']);
                                echo match ($status) {
                                    1 => '신청중',
                                    2 => '승인',
                                    3 => '거절',
                                    4 => '취소',
                                    default => '-',
                                };
                            ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <?php 
                            if ($status == 2 || $status == 1) {
                                $month_total += $row['application_amount'];
                            }
                        ?>
                    <?php endforeach; ?>

                    <div class="tr_b">
                        <div class="td20"><?= $month ?> 지급총계</div>
                        <div class="td20">&nbsp;</div>
                        <div class="td20">&nbsp;</div>
                        <div class="td20"><?= number_format($month_total) ?></div>
                        <div class="td20">&nbsp;</div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    
    <?php else: ?>
        <div class="tb_inner">조회된 정산 내역이 없습니다.</div>
    <?php endif; ?>


    </div>

    <div id="popup_type_2" class="bg_bk_alert" style="display:none;">
        <?php if ($this->session->userdata('user_id')): ?>
            <div class="bg_bk_alert_x" onclick="popupType(2)"></div>
        <?php else: ?>
            <div class="bg_bk_alert_x" style="opacity: 0.3; cursor: not-allowed;"></div>
        <?php endif; ?>

        <div class="bg_bk_alert_txt">로그인 후 편리한 정산 서비스를 이용해보세요.</div>
        <div class="bg_bk_confirm_gr" onclick="location.href='<?= base_url('web/login') ?>'">로그인</div>
        <div class="bg_bk_confirm_bk" onclick="location.href='<?= base_url('web/signup') ?>'">회원가입</div>
        <div class="clear"></div>
    </div>

    <!--조회기간 초과-->
    <div id="popup_type_1" class="bg_bk_alert" style="display:none;">
        <div class="bg_bk_alert_x" onclick="popupType(1)"></div>
        <div class="bg_bk_alert_txt">조회기간은 최대 1년으로 설정해주세요.</div>
        <div class="bg_bk_alert_bt" onclick="popupType(1)">확인</div>
    </div>

</div><!--container END-->

<script>
function popupType(type) {
    const popup = document.getElementById('popup_type_' + type);
    if (!popup) return;
    popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
}

// 조회기간 초과 팝업 표시 (type=1)
<?php if (!empty($show_duration_popup)): ?>
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('popup_type_1').style.display = 'block';
    });
<?php endif; ?>

// 로그인 여부에 따른 팝업 표시 (type=2)
<?php if (!$this->session->userdata('user_id')): ?>
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('popup_type_2').style.display = 'block';
    });
<?php endif; ?>
</script>
