<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //관리자 등록
    public function check_admin_id_exists($admin_id) {
        return $this->db->where('admin_id', $admin_id)->count_all_results('admin_users') > 0;
    }
    
    // 로그인
    public function get_admin_by_id($admin_id) {
        return $this->db->get_where('admin_users', ['admin_id' => $admin_id])->row();
    }
    
    public function admin_login($id, $pw) {
        return $this->db->get_where('admin_users', [
            'admin_id' => $id,
            'password' => md5($pw)
        ])->row();
    }

    public function get_all_qna() {
        return $this->db->get('qna')->result();
    }

    // 공지사항
    // 공지사항 리스트 가져오기 (검색 + 페이징)
    public function get_notices($search = '', $field = 'all', $limit = 10, $offset = 0) {
        if (!empty($search)) {
            if ($field == 'title') {
                $this->db->like('title', $search);
            } elseif ($field == 'contents') {
                $this->db->like('contents', $search);
            } else {  // 전체 검색
                $this->db->group_start();
                $this->db->like('title', $search);
                $this->db->or_like('contents', $search);
                $this->db->group_end();
            }
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('notices', $limit, $offset)->result();
    }
    
    public function get_notices_count($search = '', $field = 'all') {
        if (!empty($search)) {
            if ($field == 'title') {
                $this->db->like('title', $search);
            } elseif ($field == 'contents') {
                $this->db->like('contents', $search);
            } else {
                $this->db->group_start();
                $this->db->like('title', $search);
                $this->db->or_like('contents', $search);
                $this->db->group_end();
            }
        }
        return $this->db->count_all_results('notices');
    }
    
    public function get_notice($num) {
        return $this->db->get_where('notices', ['num' => $num])->row();
    }

    // 공지사항 등록
    public function insert_notice($admin_id, $title, $contents, $attachment = NULL) {
        $data = [
            'admin_id'   => $admin_id,
            'title'      => $title,
            'contents'   => $contents,
            'attachment' => $attachment,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('notices', $data);
    }

    // 공지사항 가져오기
    public function get_notice_by_num($num) {
        return $this->db->get_where('notices', ['num' => $num])->row();
    }

    // 공지사항 업데이트
    public function update_notice($num, $data) {
        $this->db->where('num', $num);
        return $this->db->update('notices', $data);
    }
    
    // 공지사항 삭제
    public function delete_notice($num) {
        return $this->db->delete('notices', ['num' => $num]);
    }

    // 공지사항 다음글 이전글
    public function get_prev_notice($num) {
        return $this->db
            ->where('num <', $num)
            ->order_by('num', 'DESC')
            ->limit(1)
            ->get('notices')
            ->row();
    }

    public function get_next_notice($num) {
        return $this->db
            ->where('num >', $num)
            ->order_by('num', 'ASC')
            ->limit(1)
            ->get('notices')
            ->row();
    }

    // faq
    // 전체 FAQ 목록 가져오기
    public function get_faq_list($search = '', $field = 'all', $limit = 10, $offset = 0) {
        if (!empty($search)) {
            if ($field == 'title') {
                $this->db->like('title', $search);
            } elseif ($field == 'contents') {
                $this->db->like('contents', $search);
            } else {
                $this->db->group_start();
                $this->db->like('title', $search);
                $this->db->or_like('contents', $search);
                $this->db->group_end();
            }
        }
        $this->db->order_by('num', 'DESC');
        return $this->db->get('faq', $limit, $offset)->result();
    }

    public function get_faq_count($search = '', $field = 'all') {
        if (!empty($search)) {
            if ($field == 'title') {
                $this->db->like('title', $search);
            } elseif ($field == 'contents') {
                $this->db->like('contents', $search);
            } else {
                $this->db->group_start();
                $this->db->like('title', $search);
                $this->db->or_like('contents', $search);
                $this->db->group_end();
            }
        }
        return $this->db->count_all_results('faq');
    }

    // 특정 FAQ 가져오기
    public function get_faq($num) {
        return $this->db->get_where('faq', ['num' => $num])->row();
    }

    // 이전 FAQ 가져오기
    public function get_prev_faq($num) {
        $this->db->where('num <', $num);
        $this->db->order_by('num', 'DESC');
        $this->db->limit(1);
        return $this->db->get('faq')->row();
    }

    // 다음 FAQ 가져오기
    public function get_next_faq($num) {
        $this->db->where('num >', $num);
        $this->db->order_by('num', 'ASC');
        $this->db->limit(1);
        return $this->db->get('faq')->row();
    }

    // faq 등록
    public function insert_faq($admin_id, $category, $title, $contents) {
        $data = [
            'admin_id'   => $admin_id,
            'category'   => $category,
            'title'      => $title,
            'contents'   => $contents,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('faq', $data);
    }

    // Q&A 답변 수정
    public function update_qna_answer($num, $admin_id, $answer) {
        $data = [
            'answer'      => $answer,
            'admin_id'    => $admin_id,
            'answer_date' => date('Y-m-d H:i:s'),
        ];

        return $this->db->where('num', $num)->update('qna', $data);
    }
    
    // 제목 중복 확인
    public function is_faq_title_exists($title)
    {
        return $this->db->where('title', $title)->count_all_results('faq') > 0;
    }

    // faq 수정
    public function update_faq($num, $category, $title, $contents) {
        $this->db->where('num', $num);
        return $this->db->update('faq', [
            'category' => $category,
            'title' => $title,
            'contents' => $contents
        ]);
    }
    
    // faq 삭제
    public function delete_faq($num) {
        return $this->db->delete('faq', ['num' => $num]);
    }

    // QnA
    public function get_qna_list($search = '', $field = 'all', $limit = 10, $offset = 0) {
        if (!empty($search)) {
            if ($field == 'title') {
                $this->db->like('title', $search);
            } elseif ($field == 'contents') {
                $this->db->like('contents', $search);
            } else {
                $this->db->group_start();
                $this->db->like('title', $search);
                $this->db->or_like('contents', $search);
                $this->db->group_end();
            }
        }
        $this->db->order_by('num', 'DESC');
        return $this->db->get('qna', $limit, $offset)->result();  // qna 테이블 기준
    }

    public function get_qna_count($search = '', $field = 'all') {
        if (!empty($search)) {
            if ($field == 'title') {
                $this->db->like('title', $search);
            } elseif ($field == 'contents') {
                $this->db->like('contents', $search);
            } else {
                $this->db->group_start();
                $this->db->like('title', $search);
                $this->db->or_like('contents', $search);
                $this->db->group_end();
            }
        }
        return $this->db->count_all_results('qna');
    }

    public function get_qna($num) {
        return $this->db->get_where('qna', ['num' => $num])->row();
    }

    // QnA 답변
    public function insert_qna_answer($num, $admin_id, $answer) {
        $this->db->where('num', $num);
        return $this->db->update('qna', [
            'admin_id'    => $admin_id,
            'answer'      => $answer,
            'answer_date' => date('Y-m-d H:i:s')
        ]);
    }

    // 회원 정보
    public function get_all_members() {
        $this->db->order_by('registration_date', 'DESC');
        return $this->db->get('company_members')->result();
    }

    // 회원 승인
    public function get_pending_members() {
        return $this->db->get_where('company_members', ['status' => 0])->result();
    }
    
    // 회원 상태 승인 또는 거절 처리
    public function update_member_status($user_id, $status, $reject_reason = null) {
        $data = ['status' => $status];
        if ($status == 2 && $reject_reason) {
            $data['reject_reason'] = $reject_reason;
        }
        $this->db->where('user_id', $user_id);
        return $this->db->update('company_members', $data);
    }

    // 선정산
    public function get_pending_settlements() {
        return $this->db->get_where('pre_settlements', ['status' => 1])->result();
    }
    
    // 선정산 상태 업데이트
    public function update_settlement_status($num, $status, $reject_reason = null) {
        $data = ['status' => $status];
        if ($status == 3 && $reject_reason) {
            $data['reject_reason'] = $reject_reason;
        }
    
        $this->db->where('num', $num);
        return $this->db->update('pre_settlements', $data);
    }
    
    // 월정산
    public function get_company_members() {
        return $this->db
            ->select('user_id, brand_name, ceo_name')
            ->order_by('registration_date', 'DESC')
            ->get('company_members')
            ->result();
    }

    public function insert_monthly_settlement($data) {
        return $this->db->insert('monthly_settlements', $data);
    }

    // 월정산 체크
    public function get_recent_months($count = 3) {
        $months = [];
        for ($i = 0; $i < $count; $i++) {
            $months[] = date('Y-m', strtotime("-$i months")) . '-01';
        }
        return array_reverse($months); // 오래된 순서부터
    }

    // 모든 회원(user_id) 기준으로 최근 N개월 정산 업로드 여부 가져오기
    public function get_all_members_with_monthly_status($months) {
        $this->db->select('user_id, brand_name, ceo_name');
        $members = $this->db->get('company_members')->result();

        foreach ($members as &$member) {
            $member->status = [];

            foreach ($months as $month) {
                $exists = $this->db->where('member_id', $member->user_id)
                                ->where('settlement_month', $month)
                                ->get('monthly_settlements')
                                ->num_rows() > 0;
                $member->status[$month] = $exists;
            }
        }

        return $members;
    }
    
}
