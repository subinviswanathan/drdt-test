<?php
/*
Plugin Name: TMBI Bounce Exchange
Version: 1.2
Description: Adds the BX simple tag to the header <a href='https://readersdigest.atlassian.net/browse/WPDT-3669' target='_blank'>Read more at WPDT-3669 ...</a>
Author: Santhosh Kumar MJ
Text Domain: bounce-exchange
License: BSD(3 Clause)
License URI: http://opensource.org/licenses/BSD-3-Clause

	Copyright (C) 2017, Santhosh Kumar, ness.com, (santhosh.kumar AT ness DOT com)
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

		* Redistributions of source code must retain the above copyright notice, this
		  list of conditions and the following disclaimer.

		* Redistributions in binary form must reproduce the above copyright notice,
		  this list of conditions and the following disclaimer in the documentation
		  and/or other materials provided with the distribution.

		* Neither the name of the {organization} nor the names of its
		  contributors may be used to endorse or promote products derived from
		  this software without specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
	FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
	DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
	SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
	CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
	OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
	OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

require( 'inc/cpt-bx.php' );

class BX_Controller extends BX_Base  {
	const VERSION     = '1.2';
	public $bx;

	public function __construct() {
		$vb = new Variant_Base();
		if ( ! $vb->is_ad_blocked() ) {
			$this->bx_loader();
			if ( $this->bx ) {
				$this->bx->set_version( self::VERSION );
				$this->bx->init();
			} else {
				$this->render_bx_load_failure();
			}
		} else {
			$this->render_bx_is_blocked();
		}
	}

	public function bx_loader() {
		$this->bx = CPT_BX::get_instance();
	}
}

$bxc = BX_Controller::get_instance();
