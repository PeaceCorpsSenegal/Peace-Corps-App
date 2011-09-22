<?php
class Feed extends MY_Controller {
	
	function __construct() {
	    parent::__construct();
		$this->load->library('page_class');
		$this->load->library('menu_class');
		$this->load->library('common_class');
	}

	public function index()
	{
		redirect('feed/page');
		$this->load->library('blog_class');
		
	    $pages = $this->page_class->feed();
		$blogs = $this->blog_class->feed();
	    //echo '<pre>'; echo print_r($feed['data']); echo '</pre>';
		
		krsort($blogs);
		$blogs_chunks = array_chunk($blogs, 10, true);
		$pages_chunks = array_chunk($pages, 10, true);
		
		$data['feed'] = $pages_chunks[0] + $blogs_chunks[0];
		//print_r($data['feed']);
		krsort($data['feed']);
		//print_r($data['feed']);
	    
		$data['backtrack'] = array('feed' => 'Feed');
		$left_col['menu'] = $this->menu_class->menu(1, 0);
		
		$this->load->view('head', array('page_title' => 'Updates', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css'), 'scripts' => array('basic.js', 'jquery.url.js')));
		$this->load->view('header');
		$this->load->view('main_open');
		$this->load->view('left_column', $left_col);
		$this->load->view('right_column');
		$this->load->view('feed_view', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}

	public function blog()
	{
		$this->load->library('blog_class');
		
		$blogs = $this->blog_class->feed();
	    //echo '<pre>'; echo print_r($feed['data']); echo '</pre>';
		
		krsort($blogs);
		$blogs_chunks = array_chunk($blogs, 10, true);
		
		$data['feed'] = $blogs_chunks[0];
	    
		$data['backtrack'] = array('feed' => 'Feed', 'feed/blog' => 'Blogs');
		$left_col['menu'] = $this->menu_class->menu(1, 0);
		
		$this->load->view('head', array('page_title' => 'Recently Updated Blogs', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css'), 'scripts' => array('basic.js', 'jquery.url.js')));
		$this->load->view('header');
		$this->load->view('main_open');
		$this->load->view('left_column', $left_col);
		$this->load->view('right_column');
		$this->load->view('feed_view', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}

	public function page()
	{
	    $data['feed'] = $this->page_class->feed();
	    //echo '<pre>'; echo print_r($feed['data']); echo '</pre>';
	    
		$tags['tags'] = array();
		foreach ($data['feed'] as $feed)
		{
			//print_r($tags['tags']);
			//print_r($feed['tags']);
			$tags['tags'] = array_merge($tags['tags'], $feed['tags']);
		}
		
		$tags['tags'] = array_unique($tags['tags']);
	    
		$data['backtrack'] = array('feed' => 'Feed', 'feed/page' => 'Pages');
		$left_col['menu'] = $this->menu_class->menu(1, 0);
		
		$this->load->view('head', array('page_title' => 'Page Updates', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css'), 'scripts' => array('basic.js', 'jquery.url.js')));
		$this->load->view('header');
		$this->load->view('main_open');
		$this->load->view('left_column', $left_col);
		$this->load->view('right_column', $tags);
		$this->load->view('feed_view', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}
	
	public function profile()
	{
	    $data['feed'] = $this->page_class->feed();
	    //echo '<pre>'; echo print_r($feed['data']); echo '</pre>';
	    
		$tags['tags'] = array();
		foreach ($data['feed'] as $feed)
		{
			//print_r($tags['tags']);
			//print_r($feed['tags']);
			$tags['tags'] = array_merge($tags['tags'], $feed['tags']);
		}
		
		$tags['tags'] = array_unique($tags['tags']);
	    
		$data['backtrack'] = array('feed' => 'Feed', 'feed/page' => 'Pages');
		$left_col['menu'] = $this->menu_class->menu(1, 0);
		
		$this->load->view('head', array('page_title' => 'Page Updates', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css'), 'scripts' => array('basic.js', 'jquery.url.js')));
		$this->load->view('header');
		$this->load->view('main_open');
		$this->load->view('left_column', $left_col);
		$this->load->view('right_column', $tags);
		$this->load->view('feed_view', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}

	public function tag()
	{
		$this->load->library('tag_class');
		
	    $data['feed'] = $this->tag_class->feed(urldecode($this->uri->segment(3, null)));
	    //echo '<pre>'; echo print_r($data); echo '</pre>';
	    
		$tags['tags'] = array();
		foreach ($data['feed'] as $feed)
		{
			//print_r($tags['tags']);
			//print_r($feed['tags']);
			$tags['tags'] = array_merge($tags['tags'], $feed['tags']);
		}
		
		$tags['tags'] = array_unique($tags['tags']);
		
		$data['backtrack'] = array('feed' => 'Feed', 'feed/tag' => 'Tags');
		$left_col['menu'] = $this->menu_class->menu(1, 0);
		
		$this->load->view('head', array('page_title' => 'Tag Updates', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css'), 'scripts' => array('basic.js', 'jquery.url.js')));
		$this->load->view('header');
		$this->load->view('main_open');
		$this->load->view('left_column', $left_col);
		$this->load->view('right_column', $tags);
		$this->load->view('feed_view', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}
	
	public function video() {
		
		$data['backtrack'] = array('feed' => 'Feed', 'feed/video' => 'Videos');
		$data['data'] = "<h1>Coming Soon: Video Updates</h1><p>Sorry, but we haven't quite finished this part yet! Come back soon to check out the latest videos from the production houses of Peace Corps Senegal.</p>";
		$left_col['menu'] = $this->menu_class->menu(1, 0);
		
		$this->load->view('head', array('page_title' => 'Coming Soon: Video Updates', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css'), 'scripts' => array('basic.js', 'jquery.url.js')));
		$this->load->view('header');
		$this->load->view('main_open');
		$this->load->view('left_column', $left_col);
		$this->load->view('right_column');
		$this->load->view('basic_view', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}
}