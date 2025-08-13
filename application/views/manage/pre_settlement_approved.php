<?php
$total_sum = 0;
foreach ($pre_settlements as $row) {
    $total_sum += $row->total_application_amount;
}
?>
<div class="container">
    <div class="c_title">선정산 승인내역</div>

    <!-- 검색 -->
    <form method="GET" style="margin-bottom: 12px;">
        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" style="height: 36px; padding: 4px 8px; font-size: 14px;">
            ~
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" style="height: 36px; padding: 4px 8px; font-size: 14px;">

            <select name="search_type" style="height: 36px; padding: 4px 8px; font-size: 14px;">
                <option value="전체" <?= ($search_type === '전체') ? 'selected' : '' ?>>전체</option>
                <option value="ID" <?= ($search_type === 'ID') ? 'selected' : '' ?>>ID</option>
                <option value="브랜드명" <?= ($search_type === '브랜드명') ? 'selected' : '' ?>>브랜드명</option>
            </select>

            <input type="text" name="search_keyword" placeholder="검색어" value="<?= htmlspecialchars($search_keyword) ?>" style="height: 33px; padding: 0px 8px; font-size: 14px; width: 150px;" />

            <button type="submit" style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; border: none; font-size: 14px; cursor: pointer;">검색</button>
            <a href="/manage/pre_settlement_approved" style="height: 36px; padding: 0 14px; background-color: #777; color: white; text-decoration: none; display: inline-flex; align-items: center; font-size: 14px; margin-left: 6px;">초기화</a>
        </div>
    </form>

    <!-- 리스트 -->
    <div class="tb_w_green" style="text-align: center; width: 1500px;">
        <div class="tr_t">
            <div class="td5">No</div>
            <div class="td10">ID</div>
            <div class="td15">브랜드명</div>
            <div class="td10">신청일</div>
            <div class="td10">산정기간</div>
            <div class="td10">대상매출</div>
            <div class="td10">신청금액</div>
            <div class="td10">상태</div>
            <div class="td10">자세히</div>
            <div class="clear"></div>
        </div>

        <?php $no = 1; foreach ($pre_settlements as $row): ?>
            <?php
                $start = (new DateTime($row->pre_settlement_date))->modify('first day of this month');
                $end = (new DateTime($row->pre_settlement_date))->modify('-1 day');
                $display_period = $start->format('n/j') . ' ~ ' . $end->format('n/j');
            ?>
            <div class="td">
                <div class="td5"><?= $no++ ?></div>
                <div class="td10"><?= htmlspecialchars($row->member_id) ?></div>
                <div class="td15"><?= htmlspecialchars($row->brand_name) ?></div>
                <div class="td10"><?= htmlspecialchars($row->pre_settlement_date) ?></div>
                <div class="td10"><?= $display_period ?></div>
                <div class="td10" style="text-align:right;"><?= number_format($row->total_sales_amount) ?>원</div>
                <div class="td10" style="text-align:right;"><?= number_format($row->total_application_amount) ?>원</div>
                <div class="td10">승인됨</div>
                <div class="td10">
                    <button type="button" class="detail-btn" data-member-id="<?= htmlspecialchars($row->member_id) ?>" data-brand-name="<?= htmlspecialchars($row->brand_name) ?>" data-pre-settlement-date="<?= htmlspecialchars($row->pre_settlement_date) ?>"data-pre-settlement-updated="<?= htmlspecialchars($row->pre_settlement_updated) ?>" style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; border: none; font-size: 14px;">자세히</button>
                </div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>
        <div style="margin-top:10px; text-align:right; font-weight:bold; font-size:16px;">
            승인된 신청금액 합계: <?= number_format($total_sum) ?>원
        </div>
    </div>
</div>

<div id="detailModal" style="
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border: 1px solid #ccc;
    padding: 20px;
    z-index: 9999;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    text-align: center;
">
    <div class="tb_w_green" style="width: 90%; text-align: center;">
        <div class="tr_t">
            <div class="td25">채널명</div>
            <div class="td25">대상매출</div>
            <div class="td25">정산가능금액</div>
            <div class="td25">신청금액</div>
            <div class="clear"></div>
        </div>
        <div id="detailModalBody"></div>
    </div>
    <button type="button" id="closeDetailModal" style="
        margin-top: 12px;
        padding: 6px 12px;
        background-color: #1a8754;
        color: white;
        border: none;
        cursor: pointer;
    ">닫기</button>
</div>

<script>
    document.querySelectorAll('.detail-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var memberId = this.getAttribute('data-member-id');
            var brandName = this.getAttribute('data-brand-name');
            var preSettlementDate = this.getAttribute('data-pre-settlement-date');
            var preSettlementUpdated = this.getAttribute('data-pre-settlement-updated');
            fetch('/manage/get_pre_settlement_detail?member_id=' + encodeURIComponent(memberId)
                + '&brand_name=' + encodeURIComponent(brandName)
                + '&pre_settlement_date=' + encodeURIComponent(preSettlementDate)
                + '&pre_settlement_updated=' + encodeURIComponent(preSettlementUpdated))
            .then(response => response.json())
            .then(data => {
                var tbody = document.getElementById('detailModalBody');
                tbody.innerHTML = '';
                data.forEach(function(row) {
                    const salesAmount = Math.floor(Number(row.sales_amount));
                    const preSettlementAmount = Math.floor(Number(row.pre_settlement_amount));
                    const applicationAmount = Math.floor(Number(row.application_amount));
                    const tr = document.createElement('div');
                    tr.className = 'td';
                    tr.style.textAlign = 'center';
                    tr.innerHTML = `
                        <div class="td25">${row.shopping_mall}</div>
                        <div class="td25" style="text-align:right;">${salesAmount.toLocaleString()}원</div>
                        <div class="td25" style="text-align:right;">${preSettlementAmount.toLocaleString()}원</div>
                        <div class="td25" style="text-align:right;">${applicationAmount.toLocaleString()}원</div>
                        <div class="clear"></div>
                    `;
                    tbody.appendChild(tr);
                });
                document.getElementById('detailModal').style.display = 'block';
            });
        });
    });
    document.getElementById('closeDetailModal').addEventListener('click', function() {
        document.getElementById('detailModal').style.display = 'none';
    });
</script>