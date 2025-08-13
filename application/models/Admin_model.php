<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

	function __construct(){
    	parent::__construct();
    }


	// 각종 이름 찾아주는
	public function rowfinder($table ='', $columns= '', $search=''){

		$sql = "select $columns from $table $search";
		$query = $this -> db -> query($sql);
		$row = $query -> row();
		return($row);
	}


	// 각종 리스트 뽑아주는
	public function make_list($table ='', $columns= '', $search = ''){

		$sql = "select $columns from $table $search";
		$query = $this -> db -> query($sql);
		$result = $query -> result();
		return($result);
	}


	// 각종 갯수 뽑아주는
	public function count_list($table ='', $columns= '', $search = ''){

		$sql = "select $columns from $table $search";

		$query = $this -> db -> query($sql);
		$count = $query -> num_rows();
		return($count);

	}

	function cgroup_list($search='',$start='',$limit='', $order=''){
		
		if(!$search){
			$search = "";
		}
		
		if(!$start){
			$start = 0;
		}
		
		if(!$limit){
			$limit = 20;
		}
		
		if(!$order){
			$order = " num desc ";
		}
		
		$list = $this -> make_list("c_group"," *"," $search order by $order limit $start, $limit");
		
		foreach($list as $ls){
			
			$num = $ls -> num;
			
			$fol = $this -> count_list("c_group_fol","*"," WHERE isActive = 0 and c_group = $num");
			$cbbs = $this -> count_list("c_bbs","*"," WHERE status = 0 and c_group = $num");
			$sell = $this -> count_list("c_sell","*"," WHERE isActive = 0 and c_group = $num");
			
			$owner = $ls -> owner;
			$cate = $ls -> cate;
			
			$lists[] = array(
				
				"info" => $ls,
				"follower" => $fol,
				"leader" => $this -> rowfinder("user","*"," WHERE num = $owner"),
				"cbbs" => $cbbs,
				"cate" => $this -> rowfinder("cate_cbbs","*"," WHERE num = $cate"),
				"sell" => $sell				
			);
			
		};
		
		return $lists;
	}
	
	
	function cbbs_list($search='',$start='',$limit='', $order=''){
		
	
		if(!$search){
			$search = "";
		}
		
		if(!$start){
			$start = 0;
		}
		
		if(!$limit){
			$limit = 20;
		}
		
		if(!$order){
			$order = " num desc ";
		}
		
		$list = $this -> make_list("c_bbs"," *"," $search order by $order limit $start, $limit");
		
		foreach($list as $ls){
			
			$num = $ls -> num;
			$uploader = $ls -> user;
			
			$likes = $this -> count_list("c_bbs_likes","*"," WHERE isActive = 0 and c_bbs = $num");
			$comment = $this -> count_list("c_bbs_cmt","*"," WHERE isActive = 0 and c_bbs = $num");
			$comment_re = $this -> count_list("c_bbs_cmt_re","*"," WHERE isActive = 0 and c_bbs = $num");
			
			$uploader_info = $this -> rowfinder("user","*"," where num = $uploader ");
			
			$photo = $this -> make_list("c_bbs_pic","*"," WHERE c_bbs = $num");
		
			$lists[] = array(
				
				"info" => $ls,
				"likes" => $likes,
				"comment" => $comment,
				"comment_re" => $comment_re,
				"uploader" => $uploader_info,
				"photo" => $photo	
			);
			
		};
		
		return $lists;
	}

	function follower_list($search='',$start='',$limit='', $order=''){
		
	
		if(!$search){
			$search = "";
		}
		
		if(!$start){
			$start = 0;
		}
		
		if(!$limit){
			$limit = 20;
		}
		
		if(!$order){
			$order = " level desc, num desc ";
		}
		
		$list = $this -> make_list("c_group_fol"," *"," $search order by $order limit $start, $limit");
		
		foreach($list as $ls){
			
			$num = $ls -> num;
			$uploader = $ls -> user;
			$uploader_info = $this -> rowfinder("user","*"," where num = $uploader");
		
			$lists[] = array(
				
				"info" => $ls,
				"uploader" => $uploader_info	
			);
			
		};
		
		return $lists;
	}



}
