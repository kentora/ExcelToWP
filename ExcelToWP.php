<?php
/**
 * Plugin Name: Excel To WP
 * Description: Import Excel spreadsheet to Wordpress user. Specific for RinkenæsSpejderne
 * Version: 0.1
 * Author: Kentora
 * License: GPL2
 */
 
 /*  Copyright 2013  Kentora  (email : kentora@kentora.dk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?php
	require_once('excel_reader2.php');
	
	/** Register admin menu hook */
	add_action('admin_menu', 'ExcelToWP_menu');
	
	/** Build the menu */
	function ExcelToWP_menu(){
		add_users_page('Import users from Excel', 'ExcelToWP', 'add_users', 'ExcelToWPAddUser', 'ExcelToWP_showPage');
	}
	
	/** The page */
	function ExcelToWP_showPage(){
		if(!current_user_can('add_users')){
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		if($_POST['stage'] == 1){
			if(!$_FILES['excelUpload']['error'] > 0){
				echo $_FILES['excelUpload']['name'] . ' uploaded as ' . $_FILES['excelUpload']['tmp_name'];
				echo '<br />';
				echo 'Type is: ' . $_FILES['excelUpload']['type'] . ' and extension is ' . end(explode('.', $_FILES['excelUpload' ]['name']));
				echo '<br />';
				echo 'dump:<br />';
				error_reporting(E_ALL ^ E_NOTICE);
				wp_upload_bits("ExcelToWP.xls", null, file_get_contents($_FILES['excelUpload']['tmp_name']));
				$data = new Spreadsheet_Excel_Reader(trailingslashit(wp_upload_dir()['path']) . 'ExcelToWP.xls');
				echo $data->dump(true, true);
			} else {
				echo 'Error on upload...<br />' . $_FILES['excelUpload']['error'];
			}
		} else if($_POST['stage'] == 2){
		
		} else {
			echo '<form method="post" action="" enctype="multipart/form-data">';
			echo '<input type="file" name="excelUpload" id="excelUpload" />';
			echo '<input type="hidden" name="stage" value="1" />';
			echo '<input type="submit" value="import" />';
			echo '</form>';
		}
	}
?>