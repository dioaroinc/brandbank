<style>
    table {
        table-layout: fixed;        /* 테이블 전체 레이아웃 고정 */
        width: 100%;
        max-width: 800px;
        border-collapse: collapse;
    }

    td, th {
        white-space: nowrap;        /* 기본은 줄바꿈 방지 */
        overflow: hidden;           /* 넘친 내용 숨김 */
        text-overflow: ellipsis;    /* ... 표시 */
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
        vertical-align: middle;
    }

    /* 긴 내용을 허용하고 싶을 때 사용하는 class */
    .long-text {
        white-space: normal !important;
        word-wrap: break-word;
        overflow: visible;
        text-overflow: clip;
    }
</style>


<div class="container">
    <form method="POST" action="/manage/memberapprove_sumit" enctype="multipart/form-data">
    
        <!-- user_id Hidden 으로 추가 -->
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($member->user_id) ?>">

        <table style="margin: 0 auto; border-collapse: collapse; width: 100%; max-width: 800px; text-align: left;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ccc; width:20%;">ID</th>
                <td style="padding: 10px; border: 1px solid #ccc; width:30%;">
                    <?= htmlspecialchars($member->user_id) ?>
                </td>
                <th style="padding: 10px; border: 1px solid #ccc; width:20%;"></th>
                <td style="padding: 10px; border: 1px solid #ccc; width:30%;"></td>
            </tr>

            <tr>
                <th style="padding: 10px; border: 1px solid #ccc;">사업자번호</th>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= htmlspecialchars($member->business_number) ?>
                </td>
                <th style="padding: 10px; border: 1px solid #ccc;">대표자명</th>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= htmlspecialchars($member->ceo_name) ?>
                </td>
            </tr>
            
            <tr>
                <th style="padding: 10px; border: 1px solid #ccc;">대표자연락처</th>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= htmlspecialchars($member->ceo_contact) ?>
                </td>
                <th style="padding: 10px; border: 1px solid #ccc;">대표자이메일</th>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= htmlspecialchars($member->ceo_email) ?>
                </td>
            </tr>

            <tr>
                <th style="padding: 10px; border: 1px solid #ccc;">사업자등록증</th>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?php if (!empty($member->business_license)): ?>
                        <a href="<?= base_url('garage/attachment/1_Business_registration_certificate/' . $member->business_license) ?>" target="_blank" download>
                            <?= htmlspecialchars($member->business_license) ?>
                        </a>
                    <?php else: ?>
                        없음
                    <?php endif; ?>
                </td>
                <th style="padding: 10px; border: 1px solid #ccc;">회원유형</th>
                <td style="padding: 10px; border: 1px solid #ccc;">
                        <?= ($member->member_type == '1') ? '위탁판매' : (($member->member_type == '2') ? '직접판매' : '-') ?>
                </td>
            </tr>

            <tr>
                <th style="padding: 10px; border: 1px solid #ccc;">홈페이지</th>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= htmlspecialchars($member->website) ?>
                </td>
                <th style="padding: 10px; border: 1px solid #ccc;">브랜드명</th>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <?= htmlspecialchars($member->brand_name) ?>
                </td>
                <input type="hidden" name="brand_name" value="<?= htmlspecialchars($member->brand_name) ?>">
            </tr>

            <tr>
                <th style="padding: 10px; border: 1px solid #ccc;">선정산받을계좌번호</th>
                <td colspan="3" style="padding: 10px; border: 1px solid #ccc;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <?= htmlspecialchars($member->settlement_account_bank) ?>
                        <?= htmlspecialchars($member->settlement_account_number) ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th style="padding: 10px; border: 1px solid #ccc;">선정산받을계좌사본</th>
                <td colspan="3" style="padding: 10px; border: 1px solid #ccc;">
                    <?php if (!empty($member->settlement_account_copy)): ?>
                        <div style="margin-bottom: 6px;">
                            <?php if (!empty($member->settlement_account_copy)): ?>
                                <div style="margin-bottom: 6px;">
                                    <a href="<?= base_url('garage/attachment/2_copy_of_account/' . $member->settlement_account_copy) ?>" target="_blank" download>
                                        <?= htmlspecialchars($member->settlement_account_copy) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div style="margin-top: 20px; text-align: center;">
            <?php if (!in_array($member->status, [1, 2])): ?>
                <button type="submit" name="action" value="approve" style="padding: 8px 24px; background-color: #1a8754; color: white; font-size: 14px; border: none; cursor: pointer;">
                    승인하기
                </button>
                <button type="button" id="rejectButton" style="padding: 8px 24px; background-color: #1a8754; color: white; font-size: 14px; border: none; cursor: pointer; margin-left: 12px;">
                    거절하기
                </button>
            <?php endif; ?>
            <a href="/manage/memberapprove" style="display: inline-block; padding: 8px 24px; background-color: #777; color: white; text-decoration: none; font-size: 14px; margin-left: 12px;">
                돌아가기
            </a>
        </div>
    </form>

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
                    window.location.href = '/manage/memberapprove';
                });
            });
            </script>
    <?php endif; ?>


    <!-- 거절사유 입력 팝업 -->
    <div id="rejectModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
        background:white; border:1px solid #ccc; padding:20px; z-index:9999; box-shadow:0 0 10px rgba(0,0,0,0.3); width:400px;">
        
        <h3 style="margin-top:0;">거절사유를 입력해 주세요</h3>
        <textarea id="rejectReason" name="reject_reason" style="width:100%; height:100px; margin-bottom:10px;"></textarea>
        <div style="text-align:right;">
            <button type="button" id="confirmReject" style="padding:6px 12px; background-color:#1a8754; color:white; border:none;">거절완료</button>
            <button type="button" id="cancelReject" style="padding:6px 12px; background-color:#777; color:white; border:none; margin-left:8px;">취소하기</button>
        </div>
    </div>

</div>

<script>
    document.getElementById('rejectButton').addEventListener('click', function() {
        document.getElementById('rejectModal').style.display = 'block';
    });

    document.getElementById('cancelReject').addEventListener('click', function() {
        document.getElementById('rejectModal').style.display = 'none';
    });

    document.getElementById('confirmReject').addEventListener('click', function() {
        // 동적으로 hidden input 추가
        var form = document.querySelector('form');
        
        var actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'reject';
        form.appendChild(actionInput);

        var reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reject_reason';
        reasonInput.value = document.getElementById('rejectReason').value;
        form.appendChild(reasonInput);

        // submit
        form.submit();
    });
</script>
