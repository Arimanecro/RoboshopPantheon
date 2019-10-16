<?php
/**
 * PHP Pagination Class
 * @author admin@catchmyfame.com - http://www.catchmyfame.com
 * @version 3.0.0
 * @date February 6, 2014
 * @copyright (c) admin@catchmyfame.com (www.catchmyfame.com)
 * @license CC Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0) - http://creativecommons.org/licenses/by-sa/3.0/
*/

class Paginator
{
	public $current_page;
	public $items_per_page;
	public $limit_end;
	public $limit_start;
	public $num_pages;
	public $total_items;
	protected $ipp_array;
	protected $limit;
	protected $mid_range;
	protected $querystring;
	protected $return;
	protected $get_ipp;
	static public $displayPages;

	public function __construct($total=0,$mid_range=7,$ipp_array=array(10,25,50,100,"All"), $backnext, $style) {
		$countPage = count(Http\Routes\Route::$page);
		$end = is_null(Http\Routes\Route::$page) ? false : end(Http\Routes\Route::$page);
		$typeDigit = ctype_digit($end);
		
		if( ($countPage > 1) && $typeDigit) {
			$p = $end;

		}
		elseif ( ($countPage == 1) && $typeDigit) {
			$p = $end;
		}
		else { $p = false;}
		$url = Http\Routes\Route::$urlPaginator;

		$this->total_items = (int) $total;
		if($this->total_items <= 0) exit("Unable to paginate: Invalid total value (must be an integer > 0)");
		$this->mid_range = (int) $mid_range; // midrange must be an odd int >= 1
		if($this->mid_range%2 == 0 Or $this->mid_range < 1) exit("Unable to paginate: Invalid mid_range value (must be an odd integer >= 1)");
		if(!is_array($ipp_array)) exit("Unable to paginate: Invalid ipp_array value");
		$this->ipp_array = $ipp_array;
		$this->items_per_page = $this->ipp_array[0];

		$this->default_ipp = $this->ipp_array[0];
		if($this->items_per_page == "All") {
			$this->num_pages = 1;
		} else {
			if(!is_numeric($this->items_per_page) OR $this->items_per_page <= 0) $this->items_per_page = $this->ipp_array[0];
			$this->num_pages = ceil($this->total_items/$this->items_per_page);
		}

		$this->current_page = ctype_digit($p) ? $p : 1 ; // must be numeric > 0

		if($this->num_pages > ($backnext-1)) {
			$this->return = ($this->current_page > 1 And $this->total_items >= 1) ? $style['back']($this->current_page-1) : $style['back_inactive']();
			$this->start_range = $this->current_page - floor($this->mid_range/2);
			$this->end_range = $this->current_page + floor($this->mid_range/2);
			if($this->start_range <= 0) {
				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;
			}
			if($this->end_range > $this->num_pages) {
				$this->start_range -= $this->end_range-$this->num_pages;
				$this->end_range = $this->num_pages;
			}
			$this->range = range($this->start_range,$this->end_range);

			for($i=1;$i<=$this->num_pages;$i++) {
				if($this->range[0] > 2 And $i == $this->range[0]) $this->return .= " <span style='font-size: 2.8vw;'> ... </span> ";
				// loop through all pages. if first, last, or in range, display
				if($i==1 Or $i==$this->num_pages Or in_array($i,$this->range)) $this->return .= ($i == $this->current_page And $this->items_per_page != "All") ? $style['page']($url.'/'.$i, $i) : $style['page_inactive']($url.'/'.$i, $i) ;
				if($this->range[$this->mid_range-1] < $this->num_pages-1 And $i == $this->range[$this->mid_range-1]) $this->return .= "<span style='font-size: 2.8vw;'> ... </span>";
			}

			$this->return .= (($this->current_page < $this->num_pages And $this->total_items >= 1) And $this->current_page >= 1) ? $style['next']($url.'/'.($this->current_page+1)) : $style['next_inactive']();
			//$this->return .= ($this->items_per_page == "All") ? "<a class=\"current\" style=\"margin-left:10px\" href=\"#\">All</a> \n":"<a class=\"paginate\" style=\"margin-left:10px\" href=\"$_SERVER[PHP_SELF]?page=1&ipp=All$this->querystring\">All</a> \n";
		} else	{
            //$this->return = ($this->current_page > 1 And $this->total_items >= 10) ? $style['back']($this->current_page-1, null) : $style['back_inactive']();
            $this->start_range = $this->current_page - floor($this->mid_range/2);
            $this->end_range = $this->current_page + floor($this->mid_range/2);
            if($this->start_range <= 0) {
                $this->end_range += abs($this->start_range)+1;
                $this->start_range = 1;
            }
            if($this->end_range > $this->num_pages) {
                $this->start_range -= $this->end_range-$this->num_pages;
                $this->end_range = $this->num_pages;
            }
            $this->range = range($this->start_range,$this->end_range);
            for($i=1;$i<=$this->num_pages;$i++) {
                if($this->range[0] > 2 And $i == $this->range[0]) $this->return .= " <span style='font-size: 2.8vw;'> ... </span>";
                if($i==1 Or $i==$this->num_pages Or in_array($i,$this->range)) $this->return .= ($i == $this->current_page And $this->items_per_page != "All") ? $style['page']($url.'/'.$i, $i) : $style['page_inactive']($url.'/'.$i, $i) ;
                if($this->range[$this->mid_range-1] < $this->num_pages-1 And $i == $this->range[$this->mid_range-1]) $this->return .= " <span style='font-size: 2.8vw;'> ... </span> ";
            }

			//$this->return .= "<a class=\"paginate\" href=\"$_SERVER[PHP_SELF]?page=1&ipp=All$this->querystring\">All</a> \n";
		}
		$this->return = str_replace("&","&amp;",$this->return);
		$this->limit_start = ($this->current_page <= 0) ? 0:($this->current_page-1) * $this->items_per_page;
		if($this->current_page <= 0) $this->items_per_page = 0;
		$this->limit_end = ($this->items_per_page == "All") ? (int) $this->total_items: (int) $this->items_per_page;
	}
	public function display_items_per_page() {
		$items = NULL;
		natsort($this->ipp_array); // This sorts the drop down menu options array in numeric order (with 'all' last after the default value is picked up from the first slot
		foreach($this->ipp_array as $ipp_opt) $items .= ($ipp_opt == $this->items_per_page) ? "<option selected value=\"$ipp_opt\">$ipp_opt</option>\n":"<option value=\"$ipp_opt\">$ipp_opt</option>\n";
		return "<span class=\"paginate\">Items per page:</span><select class=\"paginate\" onchange=\"window.location='$_SERVER[PHP_SELF]?page=1&amp;ipp='+this[this.selectedIndex].value+'$this->querystring';return false\">$items</select>\n";
	}
	public function display_jump_menu() {
		$option=NULL;
		for($i=1;$i<=$this->num_pages;$i++) {
			$option .= ($i==$this->current_page) ? "<option value=\"$i\" selected>$i</option>\n":"<option value=\"$i\">$i</option>\n";
		}
		return "<span class=\"paginate\">Page:</span><select class=\"paginate\" onchange=\"window.location='$_SERVER[PHP_SELF]?page='+this[this.selectedIndex].value+'&amp;ipp=$this->items_per_page$this->querystring';return false\">$option</select>\n";
	}
	public function display_pages() {
	    if(Eloquent::$rewrite) {
	        echo "<link href=\"https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300\" rel=\"stylesheet\">";
            return "<div style='display:flex;'>$this->return</div>";
        }
        else {
            return $this->return;
        }
	}
}