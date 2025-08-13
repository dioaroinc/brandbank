<div class="container">
    <div class="b_title">일반 정산 내역 조회 <img src="/garage/images/ic_search.png" /></div>

    <form method="get" action="">
        <div class="n_search_bar">
            <input type="month" name="start_date" class="filter_date"
                value="<?= htmlspecialchars($end_month) ?>">
            <input type="month" name="end_date" class="filter_date"
                value="<?= htmlspecialchars($end_month) ?>">
            <a href="<?= base_url('web/logs') ?>" class="n_filter_reset">초기화</a>
            <button type="submit" class="n_filter_bt">적용하기</button>
            <div class="clear"></div>
        </div>
    </form>

    <div class="tb_w_green">

        <div class="n_mypage_date"><?= htmlspecialchars($month) ?>월</div>
        <div class="clear"></div>

        <div class="n_mypage_date_lef">
            <span class="n_mypage_date_lef_span">총 <?= number_format($month_total) ?></span> 원<br />
            정산 받았어요.
        </div>
        <div class="clear"></div>


    <?php if (!empty($logs_by_month)): ?>
        <?php foreach ($logs_by_month as $month_label => $rows): ?>
            <div class="tb_title"><?= $month_label ?></div>
            <div class="">
                <!-- 헤더 -->
                <div class="tr_t">
                    <div class="td10">업로드일</div>
                    <div class="td10">채널명</div>
                    <div class="td15">총 매출</div>
                    <div class="td15">브랜드수수료</div>
                    <div class="td15">브랜드 정산액</div>
                    <div class="td10">배송비</div>
                    <div class="td15">최종 정산지급액</div>
                    <div class="td10">다운로드</div>
                    <div class="clear"></div>
                </div>

                <?php 
                $sum_settlement = 0;
                foreach ($rows as $row):
                    $sum_settlement += $row->final_settlement;
                ?>
                <!-- 데이터 행 -->
                <div class="td">
                    <div class="td10"><?= date('Y.m.d', strtotime($row->uploaded_at)) ?></div>
                    <div class="td10"><?= htmlspecialchars($row->channel) ?></div>
                    <div class="td15"><?= number_format($row->total_sales) ?></div>
                    <div class="td15">
                        <?= number_format(
                            round($row->total_sales * ($row->commission_rate ?? 0))
                        ) ?>
                    </div>
                    <div class="td15"><?= number_format($row->total_settlement) ?></div>
                    <div class="td10"><?= number_format($row->total_shipping) ?></div>
                    <div class="td15"><?= number_format($row->final_settlement) ?></div>
                    <div class="td10">
                        <?php if (!empty($row->excel_filename)): ?>
                            <a href="<?= base_url("web/download_brand_excel"
                                . "?month="   . date('Y_m', strtotime($row->settlement_month))
                                . "&channel=" . urlencode($row->channel)
                                . "&file="    . urlencode($row->excel_filename)
                            ) ?>"
                            class="td_download" download>
                                엑셀다운
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php endforeach; ?>

                <!-- 요약 행: 브랜드 정산액 합계만 표시 -->
                <div class="td summary" style="font-weight:bold;">
                    <div class="td10"></div>
                    <div class="td10">합계</div>
                    <div class="td15"></div>
                    <div class="td15"></div>
                    <div class="td15"></div>
                    <div class="td10"></div>
                    <div class="td15"><?= number_format($sum_settlement) ?></div>
                    <div class="td10"></div>
                    <div class="clear"></div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <p style="padding:20px;">정산 내역이 없습니다.</p>
    <?php endif; ?>
    </div>

</div>

<script>
function popupType(type) {
    if (type === 2) {
        const popup = document.getElementById('popup_type_2');
        popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
    }
}
<?php if (!$this->session->userdata('user_id')): ?>
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('popup_type_2').style.display = 'block';
    });
<?php endif; ?>
</script>

<style>
/* 간결화된 flex 레이아웃 */
.tb_w .tr_t,
.tb_w .td {
    display: flex; align-items: center;
    font-size: 13px;
}
.tb_w .tr_t > div,
.tb_w .td > div {
    padding: 6px 4px;
    overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
}
/* 셀 비율 재조정 */
.tb_w .td10 { width: 50%; }
.tb_w .td15 { width: 50%; }
.clear { flex-basis: 100%; height: 0; }
.filter_date { margin-right: 8px; }

/* 다운로드 버튼 스타일 (필요시 수정) */
.td_bt {
    display: inline-block;
    padding: 4px 10px;
    background: #1a8754;
    color: white;
    border-radius: 4px;
    font-size: 12px;
    text-decoration: none;
}
</style>
