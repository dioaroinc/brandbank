<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="description" content="브랜드뱅크 - Brand Bank" />
  <meta name="keywords" content="브랜드뱅크 - Brand Bank" />
  <meta name="author" content="브랜드뱅크 - Brand Bank" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- 호환성 버튼 없애는 코드 -->
  <meta http-equiv="Page-exit" content="BlendTrans(Duration=0.3)">
  <meta property="og:title" content="브랜드뱅크 - Brand Bank">
  <meta property="og:description" content="브랜드뱅크 - Brand Bank">
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="브랜드뱅크 - Brand Bank" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" type="text/css" media="(min-width: 600px)" href="/garage/index.css?ver=<?= date('ymdHis') ?>" />
  <link rel="stylesheet" type="text/css" media="(max-width: 600px)" href="/garage/index_m.css?ver=<?= date('ymdHis') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&family=Noto+Serif+KR:wght@200;300;400;500;600;700;900&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <title>브랜드뱅크 - Brand Bank</title>
</head>
<body>

<div class="header">
    <div class="header_logo_wrapper" style="display: inline-block;">
        <div class="header_logo bgcontain" onclick="location.href='/manage/dashboard'" style="display: inline-block; vertical-align: middle;"></div>
        <span class="header_logo_text" style="display: inline-block; vertical-align: middle; font-size: 18px; font-weight: bold; color: #333; margin-left: 8px;">
            관리자페이지
        </span>
    </div>

    <div class="header_burger bgcontain" onclick="showBurgerMenu()"></div>
    <!-- 로그인/로그아웃 버튼 -->
    <div class="header_logout">
        <?php if ($this->session->userdata('admin_id')): ?>
            <a href="/manage/logout">로그아웃</a>
        <?php else: ?>
            <a href="/manage/login">로그인</a>
        <?php endif; ?>
    </div>
    
    <!-- 고객센터 메뉴 -->
    <div class="header_m">
        고객센터
        <div class="header_m_inner" id="header_menu_1" style="top:100%;left:0;">
            <div class="<?php echo (isset($active_menu) && $active_menu == 'notice') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/notice'">공지사항</div>
            <div class="<?php echo (isset($active_menu) && $active_menu == 'faq') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/faq'">FAQ</div>
            <div class="<?php echo (isset($active_menu) && $active_menu == 'qna') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/qna'">문의사항</div>
        </div>
    </div>
    
    <!-- 정산 조회 -->
    <div class="header_m">
        정산 조회
        <div class="header_m_inner" id="header_menu_2" style="top:100%;left:0;">
            <div class="<?php echo (isset($active_menu) && $active_menu == 'amount') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/amount'">월정산 자료 입력</div>
            <div class="<?php echo (isset($active_menu) && $active_menu == 'amount_check') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/amount_check'">브랜드별 월 정산 내역</div>
            <div class="<?php echo (isset($active_menu) && $active_menu == 'pre_settlement') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/pre_settlement'">선정산 신청 내역</div>
            <div class="<?php echo (isset($active_menu) && $active_menu == 'pre_settlement_approved') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/pre_settlement_approved'">선정산 승인 내역</div>
        </div>
    </div>
    
    <!-- 회원관리 메뉴 -->
    <div class="header_m">
        회원관리
        <div class="header_m_inner" id="header_menu_3" style="top:100%;left:0;">
            <div class="<?php echo (isset($active_menu) && $active_menu == 'memberapprove') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/memberapprove'">회원신청 리스트</div>    
            <div class="<?php echo (isset($active_menu) && $active_menu == 'memberinfo') ? 'header_m_inner_m_s' : 'header_m_inner_m'; ?>" onclick="location.href='/manage/memberinfo'">회원 리스트</div>  
        </div>
    </div>

    <div class="clear"></div>
</div>

<script>
$(function(){
    // 모든 하위메뉴 초기 감춤
    $('.header_m_inner').hide();
    // hover시 하위메뉴 show/hide (상위/하위 모두에 적용)
    $('.header_m').each(function(){
        var timer;
        $(this).on('mouseenter', function(){
            clearTimeout(timer);
            $(this).find('.header_m_inner').stop(true, true).show();
        });
        $(this).on('mouseleave', function(){
            var self = this;
            timer = setTimeout(function(){
                $(self).find('.header_m_inner').stop(true, true).hide();
            }, 80);
        });
        $(this).find('.header_m_inner').on('mouseenter', function(){
            clearTimeout(timer);
            $(this).show();
        }).on('mouseleave', function(){
            var self = this;
            timer = setTimeout(function(){
                $(self).hide();
            }, 80);
        });
    });
});
</script>
</body>
</html>
