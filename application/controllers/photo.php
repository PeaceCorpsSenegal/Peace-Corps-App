<?php
#   Copyright (c) 2011, John F. Brown  This file is
#   licensed under the Affero General Public License version 3 or later.  See
#   the COPYRIGHT file.

class Photo extends MY_Controller {

	function __construct() {
	    parent::__construct();
		$this->load->library('photo_class');
	    $this->load->helper('form');
	    $this->load->helper('url');
	}

	public function add()
	{
		if (! $this->userdata['is_user'])
		{
			$this->session->set_flashdata('error', 'You do not have appropriate permissions for this action. [upload photo]');
			redirect('photo/gallery');
		}
		$data['form_title'] = 'Upload Photo';
		$data['user_id'] = $this->userdata['id'];

		$this->load->view('head', array('page_title' => 'Upload Photo', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css')));
		$this->load->view('header');
		$this->load->view('main_open');
		$this->load->view('left_column');
		$this->load->view('right_column');
		$this->load->view('photo_form', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}

	public function upload()
	{
		if (! $this->userdata['is_user'])
		{
			$this->session->set_flashdata('error', 'You do not have appropriate permissions for this action. [upload photo]');
			redirect('photo/gallery');
		}
		ini_set("memory_limit","128M");
		$this->load->config('photo');
		$this->load->library('upload');

		if ( ! $this->upload->do_upload())
		{
			$this->session->set_flashdata('error', $this->upload->display_errors());
			redirect('photo/add');
		}
		else
		{
			$caption = $this->input->post('caption');
			//print $caption;
			$this->load->library('image_lib');

			// set an array of sizes to be created
			$photos = array(
							array('width' => 50, 'height' => 50, 'name' => '_thumb'),
							array('width' => 180, 'height' => 180, 'name' => '_sm'),
							array('width' => 180, 'height' => null, 'name' => '_180w'),
							array('width' => null, 'height' => 180, 'name' => '_180h'),
							array('width' => 658, 'height' => 390, 'name' => '_splash'),
							array('width' => 980, 'height' => null, 'name' => '_lrg')
							);

			// process each photo
			foreach ($photos as $photo)
			{
				$success[] = $this->photo_class->create($this->upload->data(), $photo, $caption);
			}

			$this->session->set_flashdata('success', 'Your photo was successfully uploaded.');
			redirect('photo/add');
		}
	}

	public function gallery()
	{
		$this->load->model('photo_model');
		if (is_numeric($this->uri->segment(3)))
		{
			$owner_id = $this->uri->segment(3);
		}
		elseif ($this->uri->segment(3) == 'me')
		{
			$owner_id = $this->userdata['id'];
		}
		else
		{
			$owner_id = '%';
		}
		$results = $this->photo_model->read(array('fields' => 'width, filename, extension, height, caption, imagename', 'where' => array('height' => 180, 'owner_id like' => $owner_id), 'order_by' => array('column' => 'width', 'order' => 'desc')));
		foreach ($results as $result)
		{
			if ($result['width'] != 180)
			{
				$count = floor(980 / $result['width']);
				$margin = (980 - $result['width'] * $count) / $count / 2;
				if ($margin > 5) {
					$margin = 3;
				}
				$data['photos'][] = array('data' => array('src' => '/uploads/'.$result['filename'].$result['extension'], 'height' => '180px', 'width' => $result['width'], 'style' => 'margin: 3px '.$margin.'px;', 'alt' => $result['caption']), 'link' => '/uploads/'.$result['imagename'].'_lrg'.$result['extension']);
			}
		}

		$data['title'] = 'Photo Gallery';
		$data['backtrack'] = array('feed/page' => 'Home', 'photo/gallery' => 'Photos', 'photo/gallery/' => 'Gallery');

		if ($this->userdata['group']['name'] = 'admin')
		{
			$data['controls'] = anchor('photo/add/', img('img/upload_icon_black.png'), array('class' => 'upload'));
		}
		else
		{
			$data['controls'] = null;
		}
		$this->load->view('head', array('page_title' => 'Photo Gallery', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css'), 'scripts' => array('basic.js', 'jquery.url.js')));
		$this->load->view('header');
		$this->load->view('main_open');
		//$this->load->view('left_column');
		//$this->load->view('right_column');
		$this->load->view('gallery_view', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}

	// this is like the gallery function, but displays all photos, at 180px height
	public function show_all()
	{
		if (! $this->userdata['is_admin'])
		{
			$this->session->set_flashdata('error', 'You do not have appropriate permissions for this action. [upload photo]');
			redirect('photo/gallery');
		}

		$this->load->model('photo_model');
		$results = $this->photo_model->read();
		foreach ($results as $result)
		{
			$data['photos'][] = array('src' => base_url().'uploads/'.$result['filename'].$result['extension'], 'height' => '180px', 'alt' => $result['caption']);
		}

		$data['title'] = 'Photo Gallery';

		if ($this->userdata['group']['name'] = 'admin')
		{
			$data['controls'] = anchor('photo/add/', img('img/upload_icon_black.png'), array('class' => 'upload'));
		}
		else
		{
			$data['controls'] = null;
		}
		$this->load->view('head', array('page_title' => 'Photo Gallery', 'stylesheets' => array('layout_outer.css', 'layout_inner.css', 'theme.css'), 'scripts' => array('basic.js', 'jquery.url.js')));
		$this->load->view('header');
		$this->load->view('main_open');
		//$this->load->view('left_column');
		//$this->load->view('right_column');
		$this->load->view('gallery_view', $data);
		$this->load->view('main_close');
		$this->load->view('footer', array('footer' => 'Footer Here'));
	}

	// for use in profile photo selection modal, is a basic gallery, no header, footer, etc.
	public function select_profile_photo()
	{
		if (! $this->userdata['is_user'])
		{
			$this->session->set_flashdata('error', 'You do not have appropriate permissions for this action. [upload photo]');
			redirect('photo/gallery');
		}

		$this->load->model('photo_model');

		$results = $this->photo_model->read(array('where' => array('width' => 180, 'height' => 180)));
		foreach ($results as $result)
		{
			$count = floor(980 / $result['width']);
			$margin = (980 - $result['width'] * $count) / $count / 2;
			if ($margin > 5) {
				$margin = 3;
			}
			$data['photos'][] = array('src' => base_url().'uploads/'.$result['filename'].$result['extension'], 'height' => '180px', 'width' => '180px', 'id' => $result['filename'].$result['extension'], 'class' => 'gallery_photo', 'onClick' => "profile_photo('".$result['imagename']."');", 'style' => 'margin:'.$margin.'px;');
		}

		$data['controls'] = null;
		$data['title'] = 'Select a Photo';

		$this->load->view('photo_modal_view', $data);
	}

	// for use in the embedded photo modal, is a basic gallery, no header, footer, etc.
	public function select_embedded_photo()
	{
		if (! $this->userdata['is_user'])
		{
			$this->session->set_flashdata('error', 'You do not have appropriate permissions for this action. [upload photo]');
			redirect('photo/gallery');
		}

		$this->load->model('photo_model');

		$results = $this->photo_model->read(array('where' => array('height' => 180), 'order_by' => array('column' => 'width', 'order' => 'desc')));
		foreach ($results as $result)
		{
			if ($result['width'] != 180)
			{
				if ($result['width'] > 180) {
					$tail = '_180h';
				} else {
					$tail = '_180w';
				}
				$count = floor(980 / $result['width']);
				$margin = (980 - $result['width'] * $count) / $count / 2;
				if ($margin > 5) {
					$margin = 3;
				}
				$data['photos'][] = array('src' => base_url().'uploads/'.$result['filename'].$result['extension'], 'height' => $result['height'], 'width' => $result['width'], 'id' => $result['filename'].$result['extension'], 'class' => 'gallery_photo', 'onClick' => "embed_photo('/uploads/".$result['imagename'].$tail.$result['extension']."');", 'style' => 'margin:'.$margin.'px;');
			}
		}

		$data['controls'] = null;
		$data['title'] = 'Select a Photo';

		$this->load->view('photo_modal_view', $data);
	}

	// for ajax requests using the page edit photo select tool
	function ajax_image()
	{
		if (! $this->userdata['is_user'])
		{
			$this->session->set_flashdata('error', 'You do not have appropriate permissions for this action. [upload photo]');
			redirect('photo/gallery');
		}

		$imagename = $this->input->post('img');
		$this->load->model('photo_model');
		$result = $this->photo_model->read(array('where' => array('imagename' => $imagename), 'limit' => 1, 'width' => 50, 'height' => 50));
		$this->output->set_output('/uploads/'.$result['filename'].$result['extension']);
	}
}