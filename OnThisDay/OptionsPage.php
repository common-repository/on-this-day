<?php
/*
 * Copyright 2007 Yu-Jie Lin
 * 
 * This file is part of On This Day.
 * 
 * On this day is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option)
 * any later version.
 * 
 * On this day is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Author       : Yu-Jie Lin
 * Creation Date: 7/24/2007 03:35 UTC+8
 */

function OTDOptions() {
	$options = get_option('OTDOptions');
 
	if (isset($_POST['manage'])) {
		switch($_POST['manage']) {
		case __('Reset All Options', ON_THIS_DAY_DOMAIN):
			$options = OTDGetAllDefaultOptions();
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>', __('All options are resetted!', ON_THIS_DAY_DOMAIN), '</p></div>';
			break;
		case __('Deactivate Plugin', ON_THIS_DAY_DOMAIN):
			$plugin_file = dirname(plugin_basename(__FILE__)) . '/OnThisDay.php';
			wp_redirect(str_replace('&#038;', '&', wp_nonce_url("plugins.php?action=deactivate&plugin=$plugin_file", "deactivate-plugin_$plugin_file")) . '&by=plugin');
			break;
			}
		}
	elseif (isset($_POST['updateGeneralOptions'])) {
		switch($_POST['updateGeneralOptions']) {
		case __('Save', ON_THIS_DAY_DOMAIN):
			$newOptions = array();
			$newOptions['limit'] = $_POST['limit'];
			$newOptions['limitYears'] = $_POST['limitYears'];
			$newOptions['showProtected'] = ($_POST['showProtected'] == 'true') ? true : false;
			$newOptions['excerptLength'] = $_POST['excerptLength'];
			$newOptions['enableSingle'] = isset($_POST['enableSingle']);
			$newOptions['enableMulti'] = isset($_POST['enableMulti']);
			$newOptions['includePages'] = isset($_POST['includePages']);
			$newOptions['excludeCurrentYear'] = isset($_POST['excludeCurrentYear']);
			$options = array_merge($options, $newOptions);
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('General options saved!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
		case __('Reset', ON_THIS_DAY_DOMAIN):
			$options = array_merge($options, OTDGetDefaultGeneralOptions());
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('General options reseted!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
			}
		}
	elseif (isset($_POST['updateSearchOptions'])) {
		switch($_POST['updateSearchOptions']) {
		case __('Save', ON_THIS_DAY_DOMAIN):
			$newOptions = array();
			$newOptions['formMonth'] = $_POST['formMonth'];
			$newOptions['formDay'] = $_POST['formDay'];
			$newOptions['formYear'] = $_POST['formYear'];
			$newOptions['formSearch'] = $_POST['formSearch'];
			$options = array_merge($options, $newOptions);
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('Search form options saved!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
		case __('Reset', ON_THIS_DAY_DOMAIN):
			$options = array_merge($options, OTDGetDefaultSearchFormOptions());
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('Search form options reseted!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
			}
		}
	elseif (isset($_POST['updateWidgetOptions'])) {
		switch($_POST['updateWidgetOptions']) {
		case __('Save', ON_THIS_DAY_DOMAIN):
			$newOptions = array();
			$newOptions['widgetSinglePost'] = isset($_POST['widgetSinglePost']);
			$newOptions['widgetTitle'] = $_POST['widgetTitle'];
			$newOptions['widgetPostTitle'] = $_POST['widgetPostTitle'];
			$newOptions['widgetBlock'] = $_POST['widgetBlock'];
			$newOptions['widgetItem'] = $_POST['widgetItem'];
			$newOptions['widgetNoResult'] = $_POST['widgetNoResult'];
			$newOptions['widgetPostNoResult'] = $_POST['widgetPostNoResult'];
			$options = array_merge($options, $newOptions);
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('Widget options saved!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
		case __('Reset', ON_THIS_DAY_DOMAIN):
			$options = array_merge($options, OTDGetDefaultWidgetOptions ());
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('Widget options reseted!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
			}
		}
	elseif (isset($_POST['updateSingleOptions'])) {
		switch($_POST['updateSingleOptions']) {
		case __('Save', ON_THIS_DAY_DOMAIN):
			$newOptions = array();
			$newOptions['singleTitle'] = $_POST['singleTitle'];
			$newOptions['singleBlock'] = $_POST['singleBlock'];
			$newOptions['singleItem'] = $_POST['singleItem'];
			$newOptions['singleNoResult'] = $_POST['singleNoResult'];
			$options = array_merge($options, $newOptions);
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('Single post mode options saved!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
		case __('Reset', ON_THIS_DAY_DOMAIN):
			$options = array_merge($options, OTDGetDefaultSingleOptions ());
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('Single post mode options reseted!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
			}
		}
	elseif (isset($_POST['updateMultiOptions'])) {
		switch($_POST['updateMultiOptions']) {
		case __('Save', ON_THIS_DAY_DOMAIN):
			$newOptions = array();
			$newOptions['multiTitle'] = $_POST['multiTitle'];
			$newOptions['multiBlock'] = $_POST['multiBlock'];
			$newOptions['multiItem'] = $_POST['multiItem'];
			$newOptions['multiNoResult'] = $_POST['multiNoResult'];
			$options = array_merge($options, $newOptions);
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('Multi-posts mode options saved!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
		case __('Reset', ON_THIS_DAY_DOMAIN):
			$options = array_merge($options, OTDGetDefaultMultiOptions ());
			update_option('OTDOptions', $options);
			echo '<div id="message" class="updated fade"><p>' . __('Multi-posts mode options reseted!', ON_THIS_DAY_DOMAIN) . '</p></div>';
			break;
			}
		}
	// Render option page
?>
	<div class="wrap">
		<h2><?php _e('On This Day... Options', ON_THIS_DAY_DOMAIN); ?></h2>
		<div id="poststuff">
			<div id="moremeta">
				<div id="grabit" class="dbx-group">
					<fieldset id="aboutBox" class="dbx-box">
						<h3 class="dbx-handle"><?php _e('About this plugin', ON_THIS_DAY_DOMAIN); ?></h3>
						<div class="dbx-content">
						<ul>
							<li><a href="http://www.assembla.com/wiki/show/llbbsc/wpOTD"><?php _e('Plugin\'s Website', ON_THIS_DAY_DOMAIN); ?></a></li>
							<li><a href="http://wordpress.org/extend/plugins/on-this-day/"><?php _e('WordPress Extend', ON_THIS_DAY_DOMAIN); ?></a></li>
							<li><a href="http://www.livibetter.com/it/forum/llbb-small-creations"><?php _e('Get Support', ON_THIS_DAY_DOMAIN); ?></a></li>
							<li><a href="http://www.livibetter.com/"><?php _e('Author\'s Website', ON_THIS_DAY_DOMAIN); ?></a></li>
							<li><a href="http://www.livibetter.com/blog/donate/"><?php _e('Donate', ON_THIS_DAY_DOMAIN); ?></a></li>
						</ul>
						</div>
					</fieldset>
					<fieldset id="management" class="dbx-box">
						<h3 class="dbx-handle"><?php _e('Management', ON_THIS_DAY_DOMAIN); ?></h3>
						<div class="dbx-content">
							<form method="post" action="">
								<input type="submit" name="manage" value="<?php _e('Reset All Options', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
								<p style="margin-left: 20px; margin-right: 10px;"><small><?php _e('Reverts all options to defaults.', ON_THIS_DAY_DOMAIN); ?></small></p>
								<input type="submit" name="manage" value="<?php _e('Deactivate Plugin', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
								<p style="margin-left: 20px; margin-right: 10px;"><small><?php _e('Be careful! This will remove all your settings for this plugin! If you do not want to lose settings, please use Plugins page to deactivate this plugin.', ON_THIS_DAY_DOMAIN); ?></small></p>
							</form>
						</div>
					</fieldset>
					<fieldset id="tagsForTitle" class="dbx-box">
						<h3 class="dbx-handle"><?php _e('Tags for Title', ON_THIS_DAY_DOMAIN); ?></h3>
						<div class="dbx-content">
							<dl>
							<dt><?php _e('%date:format%', ON_THIS_DAY_DOMAIN); ?></dt>
							<dd><?php _e('Only month and day of month should be used. <a href="http://php.net/date">format</a>', ON_THIS_DAY_DOMAIN); ?></dd>
							</dl>
						</div>
					</fieldset>
					<fieldset id="tagsForBlock" class="dbx-box">
						<h3 class="dbx-handle"><?php _e('Tags for Block', ON_THIS_DAY_DOMAIN); ?></h3>
						<div class="dbx-content">
							<dl>
							<dt>%items%</dt>
							<dd><?php _e('Replaces with several <b>item</b>s', ON_THIS_DAY_DOMAIN); ?></dd>
							<dt>%search%</dt>
							<dd><?php _e('Replaces with a search form', ON_THIS_DAY_DOMAIN); ?></dd>
							</dl>
						</div>
					</fieldset>
					<fieldset id="tagsForItem" class="dbx-box">
						<h3 class="dbx-handle"><?php _e('Tags for Item', ON_THIS_DAY_DOMAIN); ?></h3>
						<div class="dbx-content">
							<dl>
							<dt>%year%</dt>
							<dd><?php _e('The year of the post that matches', ON_THIS_DAY_DOMAIN); ?></dd>
							<dt>%title%</dt>
							<dd><?php _e('Title of the post', ON_THIS_DAY_DOMAIN); ?></dd>
							<dt>%permalink%</dt>
							<dd><?php _e('Permanente link to the post', ON_THIS_DAY_DOMAIN); ?></dd> 
							<dt>%excerpt%</dt>
							<dd><?php _e('Excerpt of the post', ON_THIS_DAY_DOMAIN); ?></dd>
							</dl>
						</div>
					</fieldset>
					<fieldset id="tagsForNoResult" class="dbx-box">
						<h3 class="dbx-handle"><?php _e('Tags for No Result', ON_THIS_DAY_DOMAIN); ?></h3>
						<div class="dbx-content">
							<dl>
							<dt>%search%</dt>
							<dd><?php _e('Replaces with a search form', ON_THIS_DAY_DOMAIN); ?></dd>
							</dl>
						</div>
					</fieldset>
				</div>
			</div>
			<div id="advancedstuff" class="dbx-group">
				<div class="dbx-b-ox-wrapper">
					<fieldset id="OTDgerneralOptions"class="dbx-box">
						<div class="dbx-h-andle-wrapper">
							<h3 class="dbx-handle"><?php _e('General Options', ON_THIS_DAY_DOMAIN); ?></h3>
						</div>
						<div class="dbx-c-ontent-wrapper">
							<div class="dbx-content">
								<form method="post" action="">
								<table><tbody>
									<tr>
										<td><label for="limit"><?php _e('How many posts at once?', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td><input name="limit" type="text" id="limit" value="<?php echo $options['limit']; ?>" size="2" /><em><small><?php _e('0 means unlimited.', ON_THIS_DAY_DOMAIN); ?></small></em></td>
									</tr>
									<tr>
										<td><label for="limitYears"><?php _e('How many years at once?', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td><input name="limitYears" type="text" id="limitYears" value="<?php echo $options['limitYears']; ?>" size="2" /><em><small><?php _e('0 means unlimited.', ON_THIS_DAY_DOMAIN); ?></small></em></td>
									</tr>
									<tr>
										<td><label for="showProtected"><?php _e('Show password protected posts?', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td>
											<select name="showProtected" id="showProtected">
											<option <?php if($options['showProtected']) echo 'selected'; ?> value="false"><?php _e('No', ON_THIS_DAY_DOMAIN); ?></option>
											<option <?php if($options['showProtected']) echo 'selected'; ?> value="true"><?php _e('Yes', ON_THIS_DAY_DOMAIN); ?></option>
											</select>
											<em><small><?php _e('Password protected posts\' excerpt never shown, even input password is correct.', ON_THIS_DAY_DOMAIN); ?></small></em>
										</td> 
									</tr>
									<tr>
										<td><label for="excerptLength"><?php _e('Excerpt length (Number of words):', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td><input name="excerptLength" type="text" id="excerptLength" value="<?php echo $options['excerptLength']; ?>" size="2"/></td>
									</tr>
									<tr>
										<td><label for="enableSingle"><?php _e('Show OTD list after post in single post?', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td><input type="checkbox" name="enableSingle" id="enableSingle" <?php if($options['enableSingle']) echo 'checked="checked"'; ?>/> </td>
									</tr>
									<tr>
										<td><label for="enableMulti"><?php _e('Show OTD list after each post?', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td><input type="checkbox" name="enableMulti" id="enableMulti" <?php if($options['enableMulti']) echo 'checked="checked"'; ?>/> </td>
									</tr>	
									<tr>
										<td><label for="includePages"><?php _e('Include pages?', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td><input type="checkbox" name="includePages" id="includePages" <?php if($options['includePages']) echo 'checked="checked"'; ?>/> </td>
									</tr>	
									<tr>
										<td><label for="excludeCurrentYear"><?php _e('Exclude current year\'s posts?', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td><input type="checkbox" name="excludeCurrentYear" id="excludeCurrentYear" <?php if($options['excludeCurrentYear']) echo 'checked="checked"'; ?>/> </td>
									</tr>	
								<tbody></table>
								<div class="submit">
									<input type="submit" name="updateGeneralOptions" value="<?php _e('Save', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
									<input type="submit" name="updateGeneralOptions" value="<?php _e('Reset', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>	
								</div>
								</form>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="dbx-b-ox-wrapper">
					<fieldset id="OTDsearchOptions"class="dbx-box">
						<div class="dbx-h-andle-wrapper">
							<h3 class="dbx-handle"><?php _e('Search form Options', ON_THIS_DAY_DOMAIN); ?></h3>
						</div>
						<div class="dbx-c-ontent-wrapper">
							<div class="dbx-content">
								<p><?php _e('<a href="http://php.net/date">Formats</a> should match Month, Day of Month or Year, respectively.', ON_THIS_DAY_DOMAIN); ?></p>
								<form method="post" action="">
								<table><tbody>
									<tr>
										<td><label for="formMonth"><?php _e('Month format', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td>
											<input name="formMonth" type="text" id="formMonth" value="<?php echo htmlspecialchars(stripslashes($options['formMonth'])); ?>" size="2" />
											<em><small><?php _e('Suggestions: F for January, M for Jan', ON_THIS_DAY_DOMAIN); ?></small></em>
										</td>
									</tr>
									<tr>
										<td><label for="formDay"><?php _e('Day of Month format', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td>
											<input name="formDay" type="text" id="formDay" value="<?php echo htmlspecialchars(stripslashes($options['formDay'])); ?>" size="2" />
											<em><small><?php _e('Suggestions: j for 1..31, jS for 1st..31st.', ON_THIS_DAY_DOMAIN); ?></small></em>
										</td>
									</tr>
									<tr>
										<td><label for="formYear"><?php _e('Year format', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td>
											<input name="formYear" type="text" id="formYear" value="<?php echo htmlspecialchars(stripslashes($options['formYear'])); ?>" size="2" />
											<em><small><?php _e('Suggestions: Y for 2007, y for 07', ON_THIS_DAY_DOMAIN); ?></small></em>
										</td>
									</tr>
									<tr>
										<td><label for="formSearch"><?php _e('Search button text', ON_THIS_DAY_DOMAIN); ?></label></td>
										<td>
											<input name="formSearch" type="text" id="formSearch" value="<?php echo htmlspecialchars(stripslashes($options['formSearch'])); ?>" size="10" />
											<em><small><?php _e('Suggestions: &amp;raquo; for &raquo;', ON_THIS_DAY_DOMAIN); ?></small></em>
										</td>
									</tr>
								</tbody></table>
								<div class="submit">
									<input type="submit" name="updateSearchOptions" value="<?php _e('Save', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
									<input type="submit" name="updateSearchOptions" value="<?php _e('Reset', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>	
								</div>
								</form>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="dbx-b-ox-wrapper">
					<fieldset id="OTDwidgetOptions"class="dbx-box">
						<div class="dbx-h-andle-wrapper">
							<h3 class="dbx-handle"><?php _e('Widget Options', ON_THIS_DAY_DOMAIN); ?></h3>
						</div>
						<div class="dbx-c-ontent-wrapper">
							<div class="dbx-content">
							<form method="post" action="">
							<table><tbody>
								<tr>
									<td colspan="2"><label for="widgetSinglePost"><?php _e('Lists same calendar date posts to the post in single post mode', ON_THIS_DAY_DOMAIN); ?></label>
									<input type="checkbox" name="widgetSinglePost" id="widgetSinglePost" <?php if($options['widgetSinglePost']) echo 'checked="checked"'; ?>/> </td>
								</tr>
								<tr>
									<td><?php _e('Title for today', ON_THIS_DAY_DOMAIN); ?></td>
									<td><input name="widgetTitle" type="text" id="widgetTitle" value="<?php echo htmlspecialchars(stripslashes($options['widgetTitle'])); ?>" size="50"/></td>
								</tr>
								<tr>
									<td><?php _e('Title for single post', ON_THIS_DAY_DOMAIN); ?></td>
									<td><input name="widgetPostTitle" type="text" id="widgetPostTitle" value="<?php echo htmlspecialchars(stripslashes($options['widgetPostTitle'])); ?>" size="50"/></td>
								</tr>
								<tr>
									<td><?php _e('Block', ON_THIS_DAY_DOMAIN); ?></td>
									<td><textarea name="widgetBlock" id="widgetBlock" cols="50" rows="5"><?php echo htmlspecialchars(stripslashes($options['widgetBlock'])); ?></textarea></td>
								</tr>
								<tr>
									<td><?php _e('Item', ON_THIS_DAY_DOMAIN); ?></td>
									<td><textarea name="widgetItem" id="widgetItem" cols="50" rows="5"><?php echo htmlspecialchars(stripslashes($options['widgetItem'])); ?></textarea></td>
								</tr>
								<tr>
									<td><?php _e('No result message for today', ON_THIS_DAY_DOMAIN); ?></td>
									<td><input name="widgetNoResult" type="text" id="widgetNoResult" value="<?php echo htmlspecialchars(stripslashes($options['widgetNoResult'])); ?>" size="50"/></td>
								</tr>
								<tr>
									<td><?php _e('No result message for single post', ON_THIS_DAY_DOMAIN); ?></td>
									<td><input name="widgetPostNoResult" type="text" id="widgetPostNoResult" value="<?php echo htmlspecialchars(stripslashes($options['widgetPostNoResult'])); ?>" size="50"/></td>
								</tr>
							</tbody></table>
							<div class="submit">
								<input name="updateWidgetOptions" type="hidden" value=""/>
								<input type="submit" name="updateWidgetOptions" value="<?php _e('Save', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
								<input type="submit" name="updateWidgetOptions" value="<?php _e('Reset', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
							</div>
							</form>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="dbx-b-ox-wrapper">
					<fieldset id="OTDsingleOptions"class="dbx-box">
						<div class="dbx-h-andle-wrapper">
							<h3 class="dbx-handle"><?php _e('Single post mode Options', ON_THIS_DAY_DOMAIN); ?></h3>
						</div>
						<div class="dbx-c-ontent-wrapper">
							<div class="dbx-content">
							<form method="post" action="">
							<table><tbody>
								<tr>
									<td><?php _e('Title', ON_THIS_DAY_DOMAIN); ?></td>
									<td><input name="singleTitle" type="text" id="singleTitle" value="<?php echo htmlspecialchars(stripslashes($options['singleTitle'])); ?>" size="50"/></td>
								</tr>
								<tr>
									<td><?php _e('Block', ON_THIS_DAY_DOMAIN); ?></td>
									<td><textarea name="singleBlock" id="singleBlock" cols="50" rows="5"><?php echo htmlspecialchars(stripslashes($options['singleBlock'])); ?></textarea></td>
								</tr>
								<tr>
									<td><?php _e('Item', ON_THIS_DAY_DOMAIN); ?></td>
									<td><textarea name="singleItem" id="singleItem" cols="50" rows="5"><?php echo htmlspecialchars(stripslashes($options['singleItem'])); ?></textarea></td>
								</tr>
								<tr>
									<td><?php _e('No result message', ON_THIS_DAY_DOMAIN); ?></td>
									<td><input name="singleNoResult" type="text" id="singleNoResult" value="<?php echo htmlspecialchars(stripslashes($options['singleNoResult'])); ?>" size="50"/></td>
								</tr>
							</tbody></table>
							<div class="submit">
								<input name="updateSingleOptions" type="hidden" value=""/>
								<input type="submit" name="updateSingleOptions" value="<?php _e('Save', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
								<input type="submit" name="updateSingleOptions" value="<?php _e('Reset', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
							</div>
							</form>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="dbx-b-ox-wrapper">
					<fieldset id="OTDmultiOptions"class="dbx-box">
						<div class="dbx-h-andle-wrapper">
							<h3 class="dbx-handle"><?php _e('Multi-posts mode Options', ON_THIS_DAY_DOMAIN); ?></h3>
						</div>
						<div class="dbx-c-ontent-wrapper">
							<div class="dbx-content">
							<form method="post" action="">
							<table><tbody>
								<tr>
									<td><?php _e('Title', ON_THIS_DAY_DOMAIN); ?></td>
									<td><input name="multiTitle" type="text" id="multiTitle" value="<?php echo htmlspecialchars(stripslashes($options['multiTitle'])); ?>" size="50"/></td>
								</tr>
								<tr>
									<td><?php _e('Block', ON_THIS_DAY_DOMAIN); ?></td>
									<td><textarea name="multiBlock" id="multiBlock" cols="50" rows="5"><?php echo htmlspecialchars(stripslashes($options['multiBlock'])); ?></textarea></td>
								</tr>
								<tr>
									<td><?php _e('Item', ON_THIS_DAY_DOMAIN); ?></td>
									<td><textarea name="multiItem" id="multiItem" cols="50" rows="5"><?php echo htmlspecialchars(stripslashes($options['multiItem'])); ?></textarea></td>
								</tr>
								<tr>
									<td><?php _e('No result message', ON_THIS_DAY_DOMAIN); ?></td>
									<td><input name="multiNoResult" type="text" id="multiNoResult" value="<?php echo htmlspecialchars(stripslashes($options['multiNoResult'])); ?>" size="50"/></td>
								</tr>
							</tbody></table>
							<div class="submit">
								<input name="updateMultiOptions" type="hidden" value=""/>
								<input type="submit" name="updateMultiOptions" value="<?php _e('Save', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
								<input type="submit" name="updateMultiOptions" value="<?php _e('Reset', ON_THIS_DAY_DOMAIN); ?>" style="font-weight:bold;"/>
							</div>
							</form>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
<?php
	}

function AddOTDOptionsStyle() {
	echo '<link rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/OnThisDay/OptionsPage.css" type="text/css"/>';
	}

// Add JS and stylesheet
add_action('admin_head-plugins_page_OnThisDay/OnThisDay', 'AddOTDOptionsStyle');
?>
