<div style="text-align:center; font-weight:bold; font-size:18px; margin:20px 0;">
    최근 30일 동안 이상 거래가 감지된 날짜
</div>

<table style="margin: 0 auto; border-collapse: collapse; width: 50%; min-width: 400px; text-align: center;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="padding:10px; border:1px solid #ccc;">브랜드명</th>
            <th style="padding:10px; border:1px solid #ccc;">이상치 날짜</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($anomalies)): ?>
            <?php foreach ($anomalies as $row): ?>
                <tr>
                    <td style="padding:10px; border:1px solid #ccc;"><?= htmlspecialchars($row->brand_name) ?></td>
                    <td style="padding:10px; border:1px solid #ccc;"><?= htmlspecialchars($row->anomaly_date) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2" style="padding:10px; border:1px solid #ccc;">감지된 이상 거래가 없습니다</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
