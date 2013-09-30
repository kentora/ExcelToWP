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
			$err = false;
			if(!$_FILES['excelUpload']['error'] > 0){
				error_reporting(E_ALL ^ E_NOTICE);
				$wp_upload = wp_upload_bits("ExcelToWP.xls", null, file_get_contents($_FILES['excelUpload']['tmp_name']));
				if(!$wp_upload['error']){
					$data = new Spreadsheet_Excel_Reader($wp_upload['file'], false);
					
					$row = 2;
						
						$names = explode(' ', $data->val($row, 1));
						$namesCount = count($names);
						
						$first_name = getFirstName($names, $namesCount -1);
						
						if($namesCount > 1){
							$last_name = $names[$namesCount -1];
						} else {
							$last_name = '';
						}
					
					while($data->val($row, 2) != ""){
						$userdat = array(
							'user_pass' => $data->val($row, 4),
							'user_login' => $data->val($row, 2),
							'user_email' => $data->val($row, 3),
							'display_name' => $data->val($row, 1),
							'first_name' => $first_name,
							'last_name' => $last_name,
							'role' => 'author'
						);
						
						echo 'Trying to add user ' . $userdat['user_login'] . '... ';
						
						$insertResult = wp_insert_user($userdat);
						
						if(is_wp_error($insertResult)){
							echo '<b style="color: red"> Error: ' . $insertResult->get_error_message($insertResult->get_error_code()) . '</b>';
						} else {
							echo '<b style="color: green">Succes</b>';
						}
						
						echo '<br />';
						$row++;
					}
					
					unlink($wp_upload['file']);
				} else {
					$err = true;
				}
			} else {
				$err = true;
			}
			
			if($err){
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
	
	function getFirstName($arr, $count){
		$name = '';
		
		for($i = 0; $i < $count; $i++){
			$name .= $arr[$i];
		}
		
		return $name;
	}
?>