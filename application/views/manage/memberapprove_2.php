<div class="container">
    <div class="c_title">회원 승인 요청</div>

    <!-- 🔍 검색 영역 -->
    <form method="get" action="<?= base_url('manage/memberapprove') ?>" style="margin-bottom: 20px;">
        <select name="filter_type">
            <option value="all" <?= $filter_type == 'all' ? 'selected' : '' ?>>전체</option>
            <option value="business_number" <?= $filter_type == 'business_number' ? 'selected' : '' ?>>사업자번호</option>
            <option value="ceo_name" <?= $filter_type == 'ceo_name' ? 'selected' : '' ?>>대표자명</option>
            <option value="user_id" <?= $filter_type == 'user_id' ? 'selected' : '' ?>>아이디</option>
        </select>
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="검색어 입력" />
        <button type="submit">검색</button>
    </form>

    <!-- 📋 결과 테이블 -->
    <?php if (!empty($members)) : ?>
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background:#f5f5f5;">
                    <th style="padding:10px;">아이디</th>
                    <th>사업자번호</th>
                    <th>대표자명</th>
                    <th>상태</th>
                    <th>자세히</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member) : ?>
                    <tr style="border-bottom:1px solid #ccc;">
                        <td style="padding:10px;"><?= htmlspecialchars($member->user_id) ?></td>
                        <td><?= htmlspecialchars($member->business_number) ?></td>
                        <td><?= htmlspecialchars($member->ceo_name) ?></td>
                        <td>
                            <?php
                                switch ($member->status) {
                                    case 1: echo '승인'; break;
                                    case 2: echo '거절'; break;
                                    default: echo '승인 대기';
                                }
                            ?>
                        </td>
                        <td>
                            <button onclick="location.href='<?= base_url('manage/member_detail/' . $member->user_id) ?>'">자세히</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div>검색 결과가 없습니다.</div>
    <?php endif; ?>
</div>
