<?php
/*
    AU VIZIO manages Serbian online t-shirt store VIZIOshop.com affiliate program
    Copyright (C) 2009 Aleksandar Urošević <urke@users.sourceforge.net>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

Plugin Name: AU VIZIO
Plugin URI: http://blog.urosevic.net/wordpress/au-vizio/
Description: Upravljanje reklamama za VIZIOshop.com majice u vidžetu
Author: Aleksandar Urošević
Version: 0.5.0.3
Author URI: http://urosevic.net

*/

$auvs_version = '0.5.0.3';

add_action( 'plugins_loaded', 'init_auvs' );

function init_auvs() {
	register_sidebar_widget( 'VIZIOshop', 'auvs_widget' );
	register_widget_control( 'VIZIOshop', 'auvs_widget_control' );
	add_action( 'admin_menu', 'auvs_menu' );
}

if ( is_admin() ) {
	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'addAUVScfgLink' );
}

function addAUVScfgLink( $links ) { 
  $settings_link = '<a href="options-general.php?page=au-vizio/au-vizio.php">'.__('Settings').'</a>'; 
  array_unshift( $links, $settings_link );
  return $links; 
}

	load_plugin_textdomain('auvs', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/languages', dirname(plugin_basename(__FILE__)).'/languages');
	
function auvs_menu() {
	add_options_page(__('VIZIOshop Options', 'auvs'), __('VIZIOshop', 'auvs'), 8, __FILE__, 'auvs_options');
}

function auvs_options()
{
	global $auvs_version;
	$hidden_field_name = 'auvs-submit';

	// Čitam vrednosti promenljivih iz WP baze
	$auvs_opt     = get_option( 'auvs_opt' );
	$auvs_title   = get_option( 'auvs_title' );
	$auvs_keyword = get_option( 'auvs_keyword' );
	$auvs_postids = get_option( 'auvs_postids' );
	$auvs_majice  = get_option( 'auvs_majice' );

	if ( $auvs_opt['br'] < 1 ) $auvs_opt['br'] = 1;
	if ( $auvs_opt['afid'] < 1 ) $auvs_opt['afid'] = 230;
	if ( $auvs_opt['dzid'] == '' ) $auvs_opt['dzid'] = 'urkekg';
	if ( $auvs_opt['cena'] == '' ) $auvs_opt['cena'] = 859;

	// Proveri da li je korisnik poslao neke vrednosti
	// Ako jeste, skriveno polje će imati vrednost 'Y'
	if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
		// Čitaj poslate vrednosti
        $auvs_opt = array(
			'br'          => $_POST[ 'auvs_br' ],
			'afid'        => $_POST[ 'auvs_afid' ],
			'dzid'        => $_POST[ 'auvs_dzid' ],
			'cena'        => $_POST[ 'auvs_cena' ],
			'wtitle'      => $auvs_opt[ 'wtitle' ],
			'show_wtitle' => $auvs_opt[ 'show_wtitle' ]
		);

		// napravi nizove za sva polja
		$auvs_title       = serialize( $_POST[ 'auvs_title' ] );
		$auvs_keyword     = serialize( $_POST[ 'auvs_keyword' ] );
		$auvs_postids     = serialize( $_POST[ 'auvs_postids' ] );
		$auvs_majice      = serialize( $_POST[ 'auvs_majice' ] );

    	// Sacuvaj poslate vrednosti u bazu
		if ( $_POST['submit'] == __('Add') ) { $auvs_opt['br'] += 1;}
		if ( $_POST['submit'] == __('Remove') ) { $auvs_opt['br'] -= 1;}
    	update_option( 'auvs_opt',        $auvs_opt );
    	update_option( 'auvs_title',      $auvs_title );
		update_option( 'auvs_keyword',    $auvs_keyword );
		update_option( 'auvs_postids',    $auvs_postids );
		update_option( 'auvs_majice',     $auvs_majice );

    // Put an options updated message on the screen
?>
	<div id="message" class="updated fade">
  		<p><strong><?php _e('Options saved', 'auvs'); ?>.</strong></p>
	</div>
<?php	} ?>
	<div class="wrap">
	
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#auvs-help').hide();
	jQuery('a#auvs-helptoggle').click(function() {
		jQuery('#auvs-help').slideToggle(400);
		return false;
	});
	jQuery('#auvs-main').hide();
	jQuery('a#auvs-maintoggle').click(function() {
		jQuery('#auvs-main').slideToggle(400);
		return false;
	});
	jQuery('#auvs-konvert').hide();
	jQuery('a#auvs-konverttoggle').click(function() {
		jQuery('#auvs-konvert').slideToggle(400);
		return false;
	});
});
</script>

	<a href="http://vizioshop.com"><img src="<?php echo plugins_url('au-vizio/vizioshop_logo.png'); ?>" style="float: right; position: relative;" /></a>
	<h2>AU VIZIO</h2>
	<p><?php _e('Current version', 'auvs'); ?>: <strong><?php echo $auvs_version; ?></strong></p>
	<h3><a href="#" id="auvs-helptoggle"><?php _e('Users manual', 'auvs'); ?></a></h3>
	<div id="auvs-help">
	<ol>
		<li><strong><?php _e('Group title', 'auvs'); ?></strong>: <?php _e('Description of group that will explain idea of t-shirts in that group.', 'auvs'); ?></li>
		<li><strong><?php _e('Keywords', 'auvs'); ?></strong>: <?php _e('Keywords in blog post for which will be displayed t-shirts from appropriate group. Can be empty or contain one or more keywords. Multiple keywords are separated by pipe.', 'auvs'); ?></li>
		<li><strong><?php _e('Post ID`s', 'auvs'); ?></strong>: <?php _e('Blog post ID (or couple of posts) for which will be displayed t-shirt from appropriate group. You can get ID of <em>post</em> or <em>page</em> from post/page permalink.', 'auvs'); ?></li>
		<li><strong><?php _e('T-shirt list', 'auvs'); ?></strong>: <?php _e('Data about t-shirts that belong to current group, separated with pipe. Enter one t-shirt per line. You can get Data about t-shirts from URL to t-shirt page, for example:', 'auvs'); ?> http://www.vizioshop.com/majica_<span style="color: blue; font-weight: bold;" title="<?php _e('T-shirt ID', 'auvs'); ?>">2192</span>_<span style="color: red; font-weight: bold;" title="<?php _e('T-shirt name', 'auvs'); ?>">Vidimo-se-na-Facebook</span>/
			<ol>
				<li><strong><?php _e('T-shirt ID', 'auvs'); ?></strong>: <?php _e('unique t-shirt number (highlighted with blue color)', 'auvs'); ?></li>
				<li><strong><?php _e('T-shirt name', 'auvs'); ?></strong>: <?php _e('name of t-shirt, with spaces instead of dashes (highlighted with red color)', 'auvs'); ?></li>
				<li><em><strong><?php _e('T-shirt price', 'auvs'); ?></strong>: <?php printf(__('default price is %s RSD, but if you add t-shirt with different price, type it without decimals. This parameter is optional', 'auvs'), $auvs_opt['cena']); ?></em></li>
			</ol>
			<br />
			<?php _e('One line example in T-shirt list field with noticed data', 'auvs'); ?>:
			<strong>2192|Vidimo se na Facebook|<?php echo $auvs_opt['cena']; ?></strong> <?php _e("or without different price:", "auvs"); ?> <strong>2192|Vidimo se na Facebook</strong><br /><br />
			<?php _e("Use Converter below to convert T-shirts URL's to apropriate format.", 'auvs'); ?>
		</li>
	</ol>
	</div>
	
	<h3><a href="#" id="auvs-konverttoggle"><?php _e('Convertor', 'auvs'); ?></a></h3>
	<div id="auvs-konvert">
		<script type="text/javascript">
function auvs_konvert() {
	var auvs_out = document.getElementById('auvs-rawform').value.replace(/http:..www.vizioshop.com.majica_([0-9]+)_(.*)./gi, "$1|$2");
	document.getElementById('auvs-outform').value = auvs_out.replace(/\-/gi, " ");
}
		</script>
		<form>
			<label><?php _e("<strong>Usage:</strong> Paste T-shirts URL's into <em>Raw</em> field, (one T-shirt's URL per line), press <em>Convert!</em> button and use converted data.", 'auvs'); ?></label><br />
			<label for="auvs-rawform"><?php _e('Raw', 'auvs'); ?></label>:<br /><textarea id="auvs-rawform" rows="6" cols="50"></textarea><br />
			<label for="auvs-outform"><?php _e('Converted', 'auvs'); ?></label>:<br /><textarea id="auvs-outform" rows="6" cols="50"></textarea><br />
			<input type="button" value="Convert!" onclick="auvs_konvert()" />
		</form>
	</div>
	
	<form name="auvs_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
  		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
  		
			<h3><a href="#" id="auvs-maintoggle"><?php _e('Main settings', 'auvs'); ?></a></h3>
			<div id="auvs-main">
			<table class="form-table" style="border-bottom: 1px solid #aaa">
			<tr valign="top">
				<th scope="row"><label><?php _e('Number of T-shirt groups', 'auvs'); ?>:</label></th>
				<td><input type="text" name="auvs_br" value="<?php echo $auvs_opt['br']; ?>" readonly="reaadonly" /> <small>(<?php _e('auto incremental number', 'auvs'); ?>)</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label><?php _e('Affiliate site ID', 'auvs'); ?>:</label></th>
				<td><input type="text" name="auvs_afid" value="<?php echo $auvs_opt['afid']; ?>" /> <small>(<?php printf(__('check ID for your blog in column <em>Banner</em> in <a href="%s">sites list</a> on VIZIOshop affiliate page', 'auvs'), "javascript: window.open('http://zarada.vizioshop.com/listasajtova.php','vizio'); void 0;"); ?>)</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label><?php _e('Designer username', 'auvs'); ?>:</label></th>
				<td><input type="text" name="auvs_dzid" value="<?php echo $auvs_opt['dzid']; ?>" /> <small>(<?php printf(__('get your username on top of page <a href="%s">designer control panel</a>', 'auvs'), "javascript: window.open('http://zarada.vizioshop.com/dizajn/index.php','vizio'); void 0;"); ?>)</small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label><?php _e('Default T-shirt price', 'auvs'); ?>:</label></th>
				<td><input type="text" name="auvs_cena" value="<?php echo $auvs_opt['cena']; ?>" /> <small>(<?php printf(__('default T-shirt price displayed on <a href="%s">T-shirt\'s page</a>', 'auvs'), "javascript: window.open('http://www.vizioshop.com/majica_104556_Nije-istina/','vizio'); void 0;"); ?>)</small></td>
			</tr>
			</table>
			</div>
		<h3><?php _e('Groups', 'auvs'); ?></h3>
		<ul>
<?php
	$opt_tmp['afid'] = $auvs_opt['afid'];
	$opt_tmp['dzid'] = $auvs_opt['dzid'];
	$opt_tmp['br']   = $auvs_opt['br'];
	$opt_tmp['cena'] = $auvs_opt['cena'];
	$opt_tmp['ti']   = unserialize($auvs_title);
	$opt_tmp['kw']   = unserialize($auvs_keyword);
	$opt_tmp['pi']   = unserialize($auvs_postids);
	$opt_tmp['ma']   = unserialize($auvs_majice);

// pravim spisak grupa
$auvs_br = $auvs_opt['br'];
for ( $i = 0; $i < $auvs_br; $i+=1 ) {
	printf ('<li style="display: inline;"><a href="#grupa%s">%s</a>, </li>', $opt_tmp['ti'][$i], $opt_tmp['ti'][$i]);
}
?>

		</ul>
			<table class="form-table" style="border-bottom: 1px solid #aaa">
			<?php for ( $i = 0; $i < $auvs_opt['br']; $i+=1 ) { ?>
			<tr valign="top" style="border-top: 1px solid #aaa" id="grupa<?php echo $opt_tmp['ti'][$i]; ?>">
				<th scope="row"><label><?php printf(__('Group %s title', 'auvs'), $i+1); ?>:</label></th>
				<td><input type="text" name="auvs_title[]" value="<?php echo $opt_tmp['ti'][$i]; ?>" /> <small>(<?php _e('short description', 'auvs'); ?>)</small></td>
			</tr>
				<th scope="row"><label><?php _e('Keywords', 'auvs'); ?>:</label></th>
				<td><input type="text" size="60" name="auvs_keyword[]" value="<?php echo $opt_tmp['kw'][$i]; ?>" />
				<br /><small>(<?php _e('split with pipe, w/o space', 'auvs'); ?>) - <strong>facebook|fejsbuk|фејсбук</strong></small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label><?php _e('Post ID`s', 'auvs'); ?>:</label></th>
				<td><input type="text" size="60" name="auvs_postids[]" value="<?php echo $opt_tmp['pi'][$i]; ?>" />
				<br /><small>(<?php _e('split with comma, w/o space', 'auvs'); ?>) - <strong>13,77,256</strong></small></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label><?php _e('T-shirt list', 'auvs'); ?>:</label></th>
				<td><textarea cols="50" rows="7" name="auvs_majice[]"><?php echo strip_tags(stripslashes($opt_tmp['ma'][$i])); ?></textarea>
				<br /><?php _e('one t-shirt per line, in format', 'auvs'); ?>: <strong>t-shirt_id|title|price</strong></td>
			</tr>
			<tr><td><a href="#wpwrap"><small><?php _e('[ top ]', 'auvs'); ?></small></a> | <a href="#footer"><small><?php _e('[ submit ]', 'auvs'); ?></small></a></td></tr>
			<?php } ?>
			</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			<input type="submit" name="submit" value="<?php _e('Add') ?>" />
			<input type="submit" name="submit" value="<?php _e('Remove') ?>" />
		</p>
	</form>
	<hr />
	<h3><?php _e("Donate", "auvs"); ?></h3>
	<p><?php printf( __('If you are satisfied with AU-VISIO extension, feel free to <a href="%s">rate it</a> on official WordPress Plugin page. If you wish to support further development <a href="%s">contact author or make some donation</a>.', 'auvs'), "http://wordpress.org/extend/plugins/au-vizio/", "http://urosevic.net/kontakt/"); ?></p>
<?php
}

function auvs_widget_control()
{
	// Configuration panel for VIZIOshop
	$options = get_option('auvs_opt');
	if (!is_array( $options ))
	{
		$options = array(
			'wtitle'      => 'Мајице за све',
			'show_wtitle' => '1',
			'br'          => $auvs_opt['br'],
			'afid'        => $auvs_opt['afid'],
			'dzid'        => $auvs_opt['dzid'],
			'cena'        => $auvs_opt['cena']
		);
	}

	if ($_POST['auvs-Submit'])
	{
		$options['wtitle'] = htmlspecialchars($_POST['auvs-wTitle']);
		$options['show_wtitle'] = htmlspecialchars($_POST['auvs-wShowTitle']);
		update_option('auvs_opt', $options);
	}

	// Form generator
	?>
	<p>
	<label for="auvs-wTitle"><?php _e('Title'); ?>: </label><br />
	<input type="text" id="auvs-wTitle" name="auvs-wTitle" value="<?php echo strip_tags(stripslashes($options['wtitle']));?>" /><br /><br />
	<input type="checkbox" id="auvs-wShowTitle" name="auvs-wShowTitle" value="1" <?php if ( $options['show_wtitle'] == "1" ) { echo 'checked="yes"'; } ?> /> <label for="auvs-wShowTitle"><?php _e('Show Title', 'auvs'); ?></label>
	<br /><br />
	<input type="hidden" id="auvs-Submit" name="auvs-Submit" value="1" />
	</p>
<?php
} // function auvs_widget_control

// Functions to print widget in sidebar
function auvs_widget($args)
{
	extract($args);
	global $auvs_version;
	
	$options = get_option('auvs_opt');
	if (!is_array( $options ))
	{
		$options = array(
		'wtitle'      => 'Мајице за све',
		'show_wtitle' => '1',
		'br'          => $auvs_opt['br'],
		'afid'        => $auvs_opt['afid'],
		'dzid'        => $auvs_opt['dzid'],
		'cena'        => $auvs_opt['cena']
		);
	}

	// da li terba prikazivati naslov?
	if ( $options['show_wtitle'] == '1' ) {
		$wnaslov = $before_title . strip_tags(stripslashes($options['wtitle'])) . $after_title;
	} else {
		$wnaslov = "";
	}
	
echo <<<EOF
<!-- star of AUVS v. $auvs_version -->
$before_widget

$wnaslov

<div style="text-align: center;">
EOF;

// štampanje majice
auvs_majica();

// štampanje kraja vidžeta
echo <<<EOF
</div>

$after_widget
<!-- end of AUVS v. $auvs_version -->
EOF;
}

function auvs_majica() {
	global $post;

	// Čitam vrednosti promenljivih iz WP baze
	$auvs_opt     = get_option( 'auvs_opt' );
	$auvs_title   = unserialize( get_option( 'auvs_title' ) );
	$auvs_keyword = unserialize( get_option( 'auvs_keyword' ) );
	$auvs_postids = unserialize( get_option( 'auvs_postids' ) );
	$auvs_majice  = unserialize( get_option( 'auvs_majice' ) );

	if ( $auvs_opt['br'] < 1) $auvs_opt['br'] = 1;
	( $auvs_opt['afid'] < 1) ? $afid = 230 : $afid = $auvs_opt['afid'];
	( $auvs_opt['dzid'] == '') ? $dzid = 'urkekg' : $dzid = $auvs_opt['dzid'];
	( $auvs_opt['cena'] == '') ? $dcena = 859 : $dcena = $auvs_opt['cena'];

	// izvlačim ID i sadržaj članka
	$postID  = $post->ID;
	$postTXT = $post->post_content;
	$majice = 'default';
	$auvs_br = $auvs_opt['br'];
	
	for ( $i = 0; $i < $auvs_br; $i+=1 ) {
		// proverim da li je popunjena grupa
		if ( !is_null($auvs_majice[$i]) ) {
			// pretvaram string u niz sa ID-ovima članaka
			$auvs_pi =  explode(',', $auvs_postids[$i]);
			// pravim pattern za parsovanje ključnih reči
			$auvs_pt = '/' . $auvs_keyword[$i] . '/i';
			// ako u nizu postoji ID članaka, ili se poklapa neka ključna reč
			if ( is_array($auvs_pi) && in_array($postID, $auvs_pi) ) { $tapid = true; } else { $tapid = false; }
			if ( !is_null($auvs_pt) && preg_match($auvs_pt, $postTXT) ) { $tapt = true; } else { $tapt = false; }
			if ( $tapid || $tapt ) {
				// punim spisak majici u niz iz trenutne grupe
				$majice = preg_split('/\n|\r\n|\r/', $auvs_majice[$i]);
			}
		}
	}

	if ( is_array($majice) ) { // ako je niz, procesiraj
		// uzmi nasumičnu majicu i parsuj podatke za nju
		$br = rand(0, sizeof($majice)-1);
		list($m_id, $title, $cena) = split('\|', strip_tags(stripslashes($majice[$br])), 3);
		if ( $cena == "" ) { $cena = $dcena; }
		$title = str_replace("&", "&amp;", $title);
		$t_url = str_replace("&", "&amp;", $t_url);

		// definisanje URLova
		$t_url = strtr($title, ' \/!?%,.', '-');		
		$m_url = "http://www.vizioshop.com/majica_${m_id}_${t_url}/";
		$ms_dir = substr($m_id, 0, (strlen($m_id) - 3) ) . "000";
		$i_url = "http://www.vizioshop.com/proizvodi/$ms_dir/$m_id.jpg";

echo <<<EOF
	<a href="http://www.vizioshop.com/?sajt=$afid&amp;redir=$m_url"><img src="$i_url" alt="VIZIOshop - majica $title" /></a><br />
	<a href="http://www.vizioshop.com/?sajt=$afid&amp;redir=$m_url" title="VIZIOshop - majica $title"><span style="color:#0033FF">$title</span></a><br />
	od $cena,00 din.<br />
	<span style="font-size:22px"><a href="http://www.vizioshop.com/?sajt=$afid&amp;redir=$m_url">VIZIOshop.com</a></span>
EOF;
	} else {
echo <<<EOF
<a href="http://www.vizioshop.com/?sajt=$afid&amp;redir=http://vizioshop.com/majice/$dzid" title="VIZIOshop internet prodavnica majica"><img src="http://zarada.vizioshop.com/baneri/plaza01.jpg" alt="VizioShop.com - prodavnica majica" style="border: none;" /></a>
EOF;
	}
}

?>
