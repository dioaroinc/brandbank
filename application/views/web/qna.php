<?php
$page_url = 'web/qna';
?>
<div class="container">
    
    <div class="b_title">문의하기<img src="/garage/images/ic_chat.png" /></div>

    <div class="notice_filter">
        <div class="flt_l">총 <?= $total ?>건</div>
        <div class="flt_r">
            <form method="get" action="<?= base_url('web/qna') ?>" style="display: flex; align-items: center;">
                <select name="field" class="notice_select">
                    <option value="all" <?= ($field == 'all') ? 'selected' : '' ?>>전체</option>
                    <option value="title" <?= ($field == 'title') ? 'selected' : '' ?>>제목</option>
                    <option value="content" <?= ($field == 'content') ? 'selected' : '' ?>>내용</option>
                </select>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="검색어를 입력하세요." class="notice_search">
            </form>
        </div>
        <div class="clear"></div>
    </div>

    <?php if (!empty($qnas)) : ?>
        <?php foreach ($qnas as $qna) : ?>
            <div class="noti_t">
                <div class="td80" onclick="location.href='<?= base_url('web/qna_view/' . $qna->num) ?>'">
                    <span>Q.</span> <?= htmlspecialchars($qna->title) ?>
                </div>
                
                <div class="td20">
                    <?php if (!empty($qna->answer)) : ?>
                        답변 완료
                    <?php else : ?>
                        미답변
                    <?php endif; ?>
                    <?= date('Y-m-d H:i', strtotime($qna->created_at)) ?>
                </div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>

        <!-- 페이지네이션 -->
        <div class="pagenate">
        <!-- 처음 페이지로 이동 -->
        <?php if ($current_page > 1): ?>
            <div class="page_m">
                <a href="<?= base_url($page_url) ?>?page=1&search=<?= urlencode($search) ?>&field=<?= $field ?>">≪</a>
            </div>
            <div class="page_m">
                <a href="<?= base_url($page_url) ?>?page=<?= $current_page - 1 ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>"><</a>
            </div>
        <?php else: ?>
            <div class="page_m">≪</div>
            <div class="page_m"><</div>
        <?php endif; ?>

        <!-- 페이지 번호 출력 -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $current_page): ?>
                <div class="page_m_s"><?= $i ?></div>
            <?php else: ?>
                <div class="page_m">
                    <a href="<?= base_url($page_url) ?>?page=<?= $i ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>"><?= $i ?></a>
                </div>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- 다음 페이지로 이동 -->
        <?php if ($current_page < $total_pages): ?>
            <div class="page_m">
                <a href="<?= base_url($page_url) ?>?page=<?= $current_page + 1 ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>">></a>
            </div>
            <div class="page_m">
                <a href="<?= base_url($page_url) ?>?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>&field=<?= $field ?>">≫</a>
            </div>
        <?php else: ?>
            <div class="page_m">></div>
            <div class="page_m">≫</div>
        <?php endif; ?>

        <div class="clear"></div>
    </div>

    <div class="button_box">
        <button class="qna" onclick="location.href='<?= base_url('web/qna_main') ?>'">뒤로</button>
    </div>

    <?php else : ?>
        <div class="noti_no">ⓧ 검색결과가 없습니다.</div>
    <?php endif; ?>
</div>



</div><!--container END-->

<style>
    .qna_status {
        margin-top: 4px;
        font-size: 12px;
        font-weight: 500;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }

    .button_box {
        text-align: center;
        margin-top: 20px;
    }

    button.qna {
        padding: 10px 20px;
        font-size: 14px;
        border: 1px solid #333;
        background: #fff;
        color: #333;
        border-radius: 4px;
        cursor: pointer;
    }
    button.qna:hover {
        background-color: #f2f2f2;
    }
</style>