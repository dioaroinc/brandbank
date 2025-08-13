<div class="container">
    <div class="c_title">선정산 신청내역</div>

    <!-- 검색 영역 -->
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

            <input type="text" name="search_keyword" placeholder="검색어" value="<?= htmlspecialchars($search_keyword) ?>"
                style="height: 33px; padding: 0px 8px; font-size: 14px; width: 150px;" />

            <button type="submit" style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; border: none; font-size: 14px; cursor: pointer;">
                검색
            </button>

            <a href="/manage/pre_settlement" style="height: 36px; padding: 0 14px; background-color: #777; color: white; text-decoration: none; display: inline-flex; align-items: center; font-size: 14px; margin-left: 6px;">
                초기화
            </a>
        </div>
    </form>

    <!-- 리스트 영역 -->
    <div class="tb_w_green" style="
        text-align: center;
        width: 1500px;
        margin: 0 auto;  /* 추가 */
    ">
        <div class="tr_t", style="text-align: center;">
            <div class="td5">No</div>
            <div class="td10">ID</div>
            <div class="td15">브랜드명</div>
            <div class="td10">신청일</div>
            <div class="td10">산정기간</div>
            <div class="td10">대상매출</div>
            <div class="td10">신청금액</div>
            <div class="td10">상태</div>
            <div class="td10">자세히</div>
            <div class="td5">승인</div>
            <div class="td5">거절</div>
            <div class="clear"></div>
        </div>

        <?php $no = 1; foreach ($pre_settlements as $row): ?>
            <?php
                // 산정기간 계산
                $key = "{$row->member_id}_{$row->brand_name}_{$row->pre_settlement_date}_{$row->pre_settlement_updated}";
                $prev_date = isset($previous_dates[$key]) ? $previous_dates[$key] : null;

                if ($prev_date) {
                    $start_date_obj = new DateTime($prev_date);
                    $end_date_obj = new DateTime($row->pre_settlement_date);
                } else {
                    $start_date_obj = (new DateTime($row->pre_settlement_date))->modify('first day of this month');
                    $end_date_obj = (new DateTime($row->pre_settlement_date))->modify('-1 day');
                }

                $display_period = $start_date_obj->format('n/j') . ' ~ ' . $end_date_obj->format('n/j');
            ?>
            <div class="td" style="text-align: center;">
                <div class="td5"><?= $no++ ?></div>
                <div class="td10"><?= htmlspecialchars($row->member_id) ?></div>
                <div class="td15"><?= htmlspecialchars($row->brand_name) ?></div>
                <div class="td10"><?= htmlspecialchars($row->pre_settlement_date) ?></div>
                <div class="td10"><?= $display_period ?></div>
                <div class="td10" style="text-align: right;"><?= number_format($row->total_sales_amount) ?>원</div>
                <div class="td10" style="text-align: right;"><?= number_format($row->total_application_amount) ?>원</div>
                <div class="td10">
                    <?php
                        switch ($row->status) {
                            case '1': echo '신청중'; break;
                            case '2': echo '승인됨'; break;
                            case '3': echo '거절됨'; break;
                            case '4': echo '취소됨'; break;
                            default: echo '알수없음';
                        }
                    ?>
                </div>
                <div class="td10">
                    <button type="button" class="detail-btn" style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; border: none; font-size: 14px; cursor: pointer;"
                        data-member-id="<?= htmlspecialchars($row->member_id) ?>"
                        data-brand-name="<?= htmlspecialchars($row->brand_name) ?>"
                        data-pre-settlement-date="<?= htmlspecialchars($row->pre_settlement_date) ?>"
                        data-pre-settlement-updated="<?= htmlspecialchars($row->pre_settlement_updated) ?>"
                    >
                        자세히
                    </button>
                </div>
                <div class="td5">
                    <button type="button" class="approve-btn" style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; border: none; font-size: 14px; cursor: pointer;"
                        data-member-id="<?= htmlspecialchars($row->member_id) ?>"
                        data-brand-name="<?= htmlspecialchars($row->brand_name) ?>"
                        data-pre-settlement-date="<?= htmlspecialchars($row->pre_settlement_date) ?>"
                        data-pre-settlement-updated="<?= htmlspecialchars($row->pre_settlement_updated) ?>"
                        data-status="<?= $row->status ?>"
                    >
                        승인
                    </button>
                </div>
                <div class="td5">
                    <button type="button" class="reject-btn" style="height: 36px; padding: 0 14px; background-color: #1a8754; color: white; border: none; font-size: 14px; cursor: pointer;"
                        data-member-id="<?= htmlspecialchars($row->member_id) ?>"
                        data-brand-name="<?= htmlspecialchars($row->brand_name) ?>"
                        data-pre-settlement-date="<?= htmlspecialchars($row->pre_settlement_date) ?>"
                        data-pre-settlement-updated="<?= htmlspecialchars($row->pre_settlement_updated) ?>"
                        data-status="<?= $row->status ?>"
                    >
                        거절
                    </button>
                </div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 승인/거절 처리용 hidden form -->
    <form id="actionForm" method="POST" action="/manage/pre_settlement_action" style="display:none;">
        <input type="hidden" name="member_id" value="">
        <input type="hidden" name="brand_name" value="">
        <input type="hidden" name="pre_settlement_date" value="">
        <input type="hidden" name="pre_settlement_updated" value="">
        <input type="hidden" name="action" value="">
        <input type="hidden" name="reject_reason" value="">
    </form>

</div>

<!-- 메시지 출력 -->
<?php
$message = $this->session->flashdata('message');
$show_message = $this->input->get('show_message');
if ($message && $show_message == '1'): ?>
    <div id="messageModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
        background:white; border:1px solid #ccc; padding:20px; z-index:9999; box-shadow:0 0 10px rgba(0,0,0,0.3); width:400px; text-align:center;">
        <p style="font-size:16px;"><?= htmlspecialchars($message) ?></p>
        <button type="button" id="closeMessageModal" style="margin-top:10px; padding:6px 12px; background-color:#1a8754; color:white; border:none;">확인</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('messageModal').style.display = 'block';
        document.getElementById('closeMessageModal').addEventListener('click', function() {
            document.getElementById('messageModal').style.display = 'none';
            window.location.href = '/manage/pre_settlement';
        });
    });
    </script>
<?php endif; ?>


<div id="approveModal" style="
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
    width: 400px;
    text-align: center;
">
    <p style="font-size: 16px;">해당 신청건을 승인하시겠습니까?</p>
    <div style="margin-top: 10px;">
        <button type="button" id="confirmApprove" style="
            padding: 6px 12px;
            background-color: #1a8754;
            color: white;
            border: none;
            cursor: pointer;
        ">확인</button>
        <button type="button" id="cancelApprove" style="
            padding: 6px 12px;
            background-color: #777;
            color: white;
            border: none;
            cursor: pointer;
            margin-left: 8px;
        ">취소</button>
    </div>
</div>

<div id="rejectModal" style="
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
    width: 400px;
    text-align: center;
">
    <h3 style="margin-top: 0;">거절 사유를 입력해 주세요</h3>
    <textarea id="rejectReason" name="reject_reason" style="
        width: 100%;
        height: 100px;
        margin-bottom: 10px;
    "></textarea>
    <div>
        <button type="button" id="confirmReject" style="
            padding: 6px 12px;
            background-color: #1a8754;
            color: white;
            border: none;
            cursor: pointer;
        ">확인</button>
        <button type="button" id="cancelReject" style="
            padding: 6px 12px;
            background-color: #777;
            color: white;
            border: none;
            cursor: pointer;
            margin-left: 8px;
        ">취소</button>
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
    width: 1000px;
    max-height: 90vh;
    overflow-y: auto;
    text-align: center;
">
    <div class="tb_w_green" style="width: 90%; text-align: center;">
        <!-- 헤더 -->
        <div class="tr_t">
            <div class="td25">채널명</div>
            <div class="td25">대상매출</div>
            <div class="td25">정산가능금액</div>
            <div class="td25">신청금액</div>
            <div class="clear"></div>
        </div>

        <!-- 데이터 영역 -->
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

<!-- 메시지용 modal (취소 시 표시) -->
<div id="cancelMessageModal" style="
    display:none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border: 1px solid #ccc;
    padding: 20px;
    z-index: 10000; /* 여기! 기존 9999 → 10000 으로 높여줌 */
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    width: 400px;
    text-align: center;
">
    <p style="font-size:16px;">사용자에 의해 취소되었습니다.</p>
    <button type="button" id="closeCancelMessageModal" style="
        margin-top: 10px;
        padding: 6px 12px;
        background-color: #1a8754;
        color: white;
        border: none;
    ">확인</button>
</div>

<!-- 승인/거절 JS 처리 -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let selectedMemberId = '';
        let selectedBrandName = '';
        let selectedDate = '';
        let selectedUpdated = '';
        let selectedAction = '';

        // 승인 버튼
        document.querySelectorAll('.approve-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var status = this.getAttribute('data-status');

                if (status == '3') {
                    // 취소됨 → 팝업만 표시
                    document.getElementById('cancelMessageModal').style.display = 'block';
                    return;
                }

                // 정상 승인 flow
                selectedMemberId = this.getAttribute('data-member-id');
                selectedBrandName = this.getAttribute('data-brand-name');
                selectedDate = this.getAttribute('data-pre-settlement-date');
                selectedUpdated = this.getAttribute('data-pre-settlement-updated');
                selectedAction = 'approve';

                document.getElementById('approveModal').style.display = 'block';
            });
        });

        document.getElementById('confirmApprove').addEventListener('click', function() {
            var form = document.getElementById('actionForm');
            form.member_id.value = selectedMemberId;
            form.brand_name.value = selectedBrandName;
            form.pre_settlement_date.value = selectedDate;
            form.pre_settlement_updated.value = selectedUpdated;
            form.action.value = 'approve';
            form.reject_reason.value = '';
            form.submit();
        });

        document.getElementById('cancelApprove').addEventListener('click', function() {
            document.getElementById('approveModal').style.display = 'none';

            // 사용자에 의해 취소되었습니다. 표시
            setTimeout(function() {
                document.getElementById('cancelMessageModal').style.display = 'block';
            }, 100);
        });

        // 거절 버튼
        document.querySelectorAll('.reject-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var status = this.getAttribute('data-status');

                if (status == '3') {
                    // 취소됨 → 팝업만 표시
                    document.getElementById('cancelMessageModal').style.display = 'block';
                    return;
                }

                // 정상 거절 flow
                selectedMemberId = this.getAttribute('data-member-id');
                selectedBrandName = this.getAttribute('data-brand-name');
                selectedDate = this.getAttribute('data-pre-settlement-date');
                selectedUpdated = this.getAttribute('data-pre-settlement-updated');
                selectedAction = 'reject';

                document.getElementById('rejectModal').style.display = 'block';
            });
        });

        document.getElementById('confirmReject').addEventListener('click', function() {
            var reason = document.getElementById('rejectReason').value;
            if (reason.trim() === '') {
                alert('거절 사유를 입력해 주세요.');
                return;
            }
            var form = document.getElementById('actionForm');
            form.member_id.value = selectedMemberId;
            form.brand_name.value = selectedBrandName;
            form.pre_settlement_date.value = selectedDate;
            form.pre_settlement_updated.value = selectedUpdated;
            form.action.value = 'reject';
            form.reject_reason.value = reason;
            form.submit();
        });

        document.getElementById('cancelReject').addEventListener('click', function() {
            document.getElementById('rejectModal').style.display = 'none';

            // 사용자에 의해 취소되었습니다. 표시
            setTimeout(function() {
                document.getElementById('cancelMessageModal').style.display = 'block';
            }, 100);
        });

        // detail modal close
        document.getElementById('closeDetailModal').addEventListener('click', function() {
            document.getElementById('detailModal').style.display = 'none';
        });

        // 취소 팝업 확인 버튼
        document.getElementById('closeCancelMessageModal').addEventListener('click', function() {
            console.log('취소확인 버튼 클릭됨'); // 디버그용
            document.getElementById('cancelMessageModal').style.display = 'none';
        });

        // 자세히 보기 버튼
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
                    tbody.innerHTML = ''; // 초기화

                    data.forEach(function(row) {
                        var tr = document.createElement('div');
                        tr.className = 'td';
                        tr.style.textAlign = 'center';

                        tr.innerHTML = `
                            <div class="td25">${row.shopping_mall}</div>
                            <div class="td25" style="text-align:right;">${Number(row.sales_amount).toLocaleString()}원</div>
                            <div class="td25" style="text-align:right;">${Math.floor(row.pre_settlement_amount).toLocaleString()}원</div>
                            <div class="td25" style="text-align:right;">${Number(row.application_amount).toLocaleString()}원</div>
                            <div class="clear"></div>
                        `;

                        tbody.appendChild(tr);
                    });
                    document.getElementById('detailModal').style.display = 'block';
                });
                console.log(memberId, brandName, preSettlementDate, preSettlementUpdated);

            });
        });

    });

</script>
