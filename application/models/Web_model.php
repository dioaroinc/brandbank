<?php
class Web_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // 사용자 정보 조회 (로그인)
    public function get_user_by_id($user_id) {
        return $this->db->select('*')
                        ->where('user_id', $user_id)
                        ->order_by('registration_date', 'DESC')
                        ->limit(1)
                        ->get('company_members')
                        ->row();
    }

    //회원수정
    public function update_user_info($user_id, $data) {
        $this->db->where('user_id', $user_id);
        return $this->db->update('company_members', $data);
    }

    //회원가입
    public function insert_member($data) {
        return $this->db->insert('company_members', $data);
    }

    // 아이디 중복 확인 (status != 2만 확인)
    public function is_user_id_exists($user_id) {
        return $this->db
                    ->where('user_id', $user_id)
                    ->where_in('status', [0, 1, 3])
                    ->count_all_results('company_members') > 0;
    }

    // 사업자번호 중복 확인 (status != 2만 확인)
    public function is_business_number_exists($biz_number) {
        return $this->db
                    ->where('business_number', $biz_number)
                    ->where_in('status', [0, 1, 3])
                    ->count_all_results('company_members') > 0;
    }
    
    // 아이디 찾기
    public function get_user_id_by_info($business_number, $ceo_email) {
        $this->db->where('business_number', $business_number);
        $this->db->where('ceo_email', $ceo_email);
        $query = $this->db->get('company_members');
    
        if ($query->num_rows() > 0) {
            return $query->row()->user_id;
        } else {
            return false;
        }
    }

    // 비밀번호 찾기
    public function check_user_email($user_id, $ceo_email) {
        $this->db->where('user_id', $user_id);
        $this->db->where('ceo_email', $ceo_email);
        $query = $this->db->get('company_members');
    
        return $query->num_rows() > 0;
    }

    public function update_password($user_id, $hashed_pass) {
        $this->db->where('user_id', $user_id);
        return $this->db->update('company_members', ['password' => $hashed_pass]);
    }

    //신청내역 관련 부분
    public function get_settlements_by_period($member_id, $start_date, $end_date) {
        $this->db->select('pre_settlement_date, shopping_mall, sales_amount, pre_settlement_amount, application_amount, reject_reason, status');
        $this->db->from('pre_settlements');
        $this->db->where('member_id', $member_id);
        $this->db->where('DATE(pre_settlement_date) >=', $start_date);
        $this->db->where('DATE(pre_settlement_date) <=', $end_date);
        $this->db->order_by('pre_settlement_date', 'ASC');

        $results = $this->db->get()->result_array();

        $grouped = [];
        foreach ($results as $row) {
            $month_key = date('Y-m', strtotime($row['pre_settlement_date']));
            $grouped[$month_key][] = $row;
        }

        return $grouped;
    }

    // 월별 정산 및 엑셀
    // 단일 멤버 정보 조회 (company_members 테이블에서)
    public function get_company_member($member_id)
    {
        return $this->db
            ->get_where('company_members', ['user_id' => $member_id])
            ->row();
    }

    // monthly_total 테이블에서 브랜드별, 기간별 집계 불러오기
    public function get_monthly_totals($brand, $start_date, $end_date)
    {
        $this->db
            ->select('*')
            ->from('monthly_total')
            ->where('brand_name', $brand)
            ->where("DATE_FORMAT(settlement_month, '%Y-%m') >=", $start_date)
            ->where("DATE_FORMAT(settlement_month, '%Y-%m') <=", $end_date)
            ->order_by('settlement_month', 'DESC');

        $rows = $this->db->get()->result();

        // "6월", "5월" … 형태로 그룹핑
        $grouped = [];
        foreach ($rows as $r) {
            $label = date('n월', strtotime($r->settlement_month));
            $grouped[$label][] = $r;
        }
        return $grouped;
    }

    //공지사항
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

    // 이전 공지사항
    public function get_prev_notice($num) {
        $this->db->where('num <', $num);
        $this->db->order_by('num', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('notices');
        return $query->row();
    }

    // 다음 공지사항
    public function get_next_notice($num) {
        $this->db->where('num >', $num);
        $this->db->order_by('num', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get('notices');
        return $query->row();
    }

    public function get_notice($num) {
        return $this->db->get_where('notices', ['num' => $num])->row();
    }
    
    //faq
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

    // QnA
    // QnA 리스트
    public function get_qna_list($search = '', $field = 'all', $limit = 10, $offset = 0, $member_id = null) {
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

        if (!empty($member_id)) {
            $this->db->where('member_id', $member_id);
        }

        $this->db->order_by('num', 'DESC');
        return $this->db->get('qna', $limit, $offset)->result();
    }

    public function get_qna_count($search = '', $field = 'all', $member_id = null) {
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

        if (!empty($member_id)) {
            $this->db->where('member_id', $member_id);
        }

        return $this->db->count_all_results('qna');
    }

    // 이전 Q&A
    public function get_prev_qna($num) {
        $this->db->where('num <', $num);
        $this->db->order_by('num', 'DESC');
        $this->db->limit(1);
        return $this->db->get('qna')->row();
    }

    // 다음 Q&A
    public function get_next_qna($num) {
        $this->db->where('num >', $num);
        $this->db->order_by('num', 'ASC');
        $this->db->limit(1);
        return $this->db->get('qna')->row();
    }

    public function get_qna($num) {
        return $this->db->get_where('qna', ['num' => $num])->row();
    }

    public function insert_qna($data) {
        return $this->db->insert('qna', $data);
    }

    //mypage
    // 월 계산
    public function get_monthly_total_amount($member_id, $status, $year, $month) {
        $this->db->select_sum('application_amount');
        $this->db->from('pre_settlements');
        $this->db->where('member_id', $member_id);
        $this->db->where_in('status', $status);
        $this->db->where('YEAR(pre_settlement_date)', $year);
        $this->db->where('MONTH(pre_settlement_date)', $month);

        $query = $this->db->get();
        return $query->row()->application_amount ?? 0;
    }
    // 총 개수
    public function get_product_count_by_member($brand_name) {
        return $this->db->where('brand_name', $brand_name)
                        ->count_all_results('product_info');
    }
    // product_info 페이지용
    public function get_product_info_by_member($brand_name, $limit, $offset) {
        return $this->db->where('brand_name', $brand_name)
                        ->limit($limit, $offset)
                        ->get('product_info')
                        ->result();
    }
    // 모든 pre_settlements
    public function get_settlements_by_member($member_id) {
        return $this->db->where('member_id', $member_id)
                        ->get('pre_settlements')
                        ->result();
    }
    // 신청 중 상태값만 (status = 0 가정)
    public function get_pending_settlements_by_member($member_id) {
        return $this->db->where('member_id', $member_id)
                        ->where('status', 1)
                        ->get('pre_settlements')
                        ->result();
    }
    // 선정산
    public function get_available_settlements($member_id) {
        $firstDay = date('Y-m-01');

        # 쇼핑몰 찾기
        $select_shopping_mall = "
            SELECT DISTINCT shopping_mall
            FROM pre_settlements
            WHERE member_id = ?";
        $shopping_malls = $this->db->query($select_shopping_mall, [$member_id])->result_array();
        
        # 쇼핑몰 단위로 수행. 
        $num = 1;
        foreach ($shopping_malls as $shopping_mall) {
            # 쇼핑몰별 선정산 날짜를 찾기
            $select_first_day = "
                SELECT MAX(pre_settlement_date) AS shopping_mall
                FROM pre_settlements
                WHERE member_id = ?
                AND shopping_mall = ?
                AND status IN (1, 2)
                AND pre_settlement_date > ?
                order by pre_settlement_date ASC";
            $settlement_day = $this->db->query($select_first_day, [$member_id, $shopping_mall['shopping_mall'], $firstDay])->row()->shopping_mall;
            $first_day = max($firstDay, $settlement_day);

            # 쇼핑몰별 선정산 금액을 찾기
            $select_pre_settlement_amount = "
                SELECT COALESCE(pre_settlement_amount, 0) AS pre_settlement_amount
                FROM pre_settlements
                WHERE member_id = ?
                AND shopping_mall = ?
                AND pre_settlement_date > ?
                limit 1";
            $pre_settlement_amount = $this->db->query($select_pre_settlement_amount, [$member_id, $shopping_mall['shopping_mall'], $first_day])->row()->pre_settlement_amount;
            //var_dump($shopping_mall['shopping_mall'], $first_day, $pre_settlement_amount); // 쇼핑몰과 첫 선정산 날짜, 선정산 금액 출력

            # 선정산 금액이 0보다 크면 결과에 추가
            if ($pre_settlement_amount > 0) {
                $result[] = [
                    'num' => $num++,
                    'shopping_mall' => $shopping_mall['shopping_mall'],
                    'first_day' => $first_day,
                    'pre_settlement_amount' => $pre_settlement_amount
                ];
            }
        }
        // var_dump($result); exit;

        return $result ?? [];
    }

    // 선정산 조회
    public function submit_settlements($data) {
        foreach ($data as $row) {
            // var_dump($row); exit;
            // 1. 최신 pre_settlement_date 기준 num 조회
            $latest = $this->db->select('num')
                ->from('pre_settlements')
                ->where('brand_name', $row['brand_name'])
                ->where('shopping_mall', $row['shopping_mall'])
                ->order_by('pre_settlement_date', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            if ($latest) {
                // 2. 해당 num만 update
                $this->db->where('num', $latest->num)
                        ->update('pre_settlements', [
                            'application_amount' => $row['application_amount'],
                            'status' => 1
                        ]);
            }
        }
    }

}
?>