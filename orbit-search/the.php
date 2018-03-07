<?php
	
	function the_sb_tags($slug){
		global $post, $post_type;
		$terms = wp_get_post_terms($post->ID, $slug);
		_e("<div class='tagcloud'>");
		foreach($terms as $term):
			$term_url = sb_get_term_url($post_type, $term->taxonomy, $term->slug);
			_e("<a href='".$term_url."'>".$term->name."</a>");
		endforeach;	
		_e("</div>");
	}
	
	function the_sb_location($slug){
		global $post, $post_type;
		$terms = wp_get_post_terms($post->ID, $slug);
		foreach($terms as $term):
			$term_url = sb_get_term_url($post_type, $term->taxonomy, $term->slug);
			$parent = get_term_by('id', $term->parent, $slug);
			$location = $term->name;
			if($parent){
				$location .= ", ".$parent->name;
			}
			_e("<a class='text-muted' href='".$term_url."'><i class='fa fa-map-marker'></i>&nbsp;".$location."</a>");
		endforeach;
	}
	
	function the_sb_img($size){
		include "templates/img.php";
	}
	
	function the_sb_availability($slug){
		global $post;
		$terms = wp_get_post_terms($post->ID, $slug);
		$w_days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		$w_no = array();
		foreach($terms as $term){
			$key = array_search($term->name, $w_days);
			if($key > -1){array_push($w_no, $key);}
		}
		/* sorted array of the available week day numbers */
		sort($w_no);
		$final_arr_no = array();	
		$i = 0;
		$cnt = 0;
		foreach($w_no as $no){
			if($cnt == 0){$final_arr_no[$i] = array();	}
			elseif($prev == $no-1){ /* Do nothing */}
			else{$i = $i + 1;$final_arr_no[$i] = array();}
			array_push($final_arr_no[$i], $no);
			$prev = $no;
			$cnt += 1;
		}
		
		$html = "";
		$cnt = 0;
		
		/* get consequtive week days into disjoint sets */
		foreach($final_arr_no as $disjoint_arr){
			$temp_size = sizeof($disjoint_arr);
			if($temp_size > 1){
				$html .= $w_days[$disjoint_arr[0]]." to ".$w_days[$disjoint_arr[$temp_size - 1]];
			}
			else{
				$html .= $w_days[$disjoint_arr[0]];
			}
			if(!($cnt == (sizeof($final_arr_no) - 1))){
				$html .= ", ";
			}
			$cnt += 1;
		}
		
		if($html){
			_e("<i class='fa fa-clock-o'></i>&nbsp;".$html);	
		}
		
	}
	
	
	
	function the_sb_tagslist($slug){
		global $post;
		$terms = wp_get_post_terms($post->ID, $slug);
		$term_names = array();
		foreach($terms as $term){
			array_push($term_names, $term->name);
		}
		_e(implode( ', ', $term_names ));
	}
	
	
	function the_sb_search_form(){
		global $post_type, $sb_post_types;
		if(!$post_type){ return 1;}
		
		
		$searchform = dirname( __FILE__ ) . '/templates/'.$post_type.'/searchform.php';
		if(file_exists($searchform))
			include $searchform;
	}
	
	
	function the_sb_input_field($r){
		include "templates/typeahead_input_field.php";
	}
	
	function the_sb_list_field($r){
		$terms = sb_filter_terms($r);
		include "templates/list_field.php";
	}
	function the_sb_availability_field($r){
		$terms = sb_filter_terms($r);
		include "templates/availability_field.php";
	}
	
	
	
	function the_sb_contact_info(){
		global $post, $sb_contact_form;
		include "templates/contact_info.php";
	}