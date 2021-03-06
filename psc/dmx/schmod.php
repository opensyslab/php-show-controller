<?

/*
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Library General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 * schmod.php
 * mod schemas values
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
require("funct.php");
include("menu.php");

//delete schema (in schsum, and schema channels)
if ( isset($_GET['delsch']) )
{
    //remove all values of schema
    $sqlh="DELETE FROM dmx_schema WHERE id_schema='".$_GET['delsch']."'";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    //remove schema from schsum
    $sqlh="DELETE FROM dmx_schsum WHERE id='".$_GET['delsch']."'";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    echo'<i>'.TXT_SCHEMA_DELETED.'</i>';
}

//all
if ( isset($_POST['allenabled']) )
{
	$sqlg="UPDATE dmx_schsum SET disabled='0'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//all
if ( isset($_POST['alldisabled']) )
{
	$sqlg="UPDATE dmx_schsum SET disabled='1'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//affiche les schemas
echo'<div id="sequence"><table>';

	echo'<form action="schmod.php" method="post">';

	echo'<tr>';
		echo'<td><b>'.TXT_SCHEMAS.'</b></td>';
		echo'<td><b>'.TXT_CHANNELS.'</b></td>';
		echo'<td><b>'.TXT_ENABLED.'</b></td>';
	echo'</tr>';

	$sqlf="SELECT * FROM dmx_schsum ORDER BY id";
	$sqlf=mysql_query($sqlf);
	$testf=mysql_num_rows($sqlf);

	//chg values for each schema
	if ( isset($_POST['chgvalues']) )
	{
		//array values
		for ($j = 0; $j < $testf; $j++) {
			$sqlg="UPDATE dmx_schsum SET schema_name='".$_POST['schema_name'][$j]."',nb_channels='".$_POST['nb_channels'][$j]."' WHERE id='".$_POST['id'][$j]."'";
			$sqlg=mysql_query($sqlg) or die(mysql_error());
			//echo'ok_';
		}

		//all disabled first
		$sqlg="UPDATE dmx_schsum SET disabled='1'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());

		if ( isset($_POST['enabled']) ){
			//this array values
			foreach($_POST['enabled'] as $val)
			{
				//echo $val,'<br />';
				$sqlg="UPDATE dmx_schsum SET disabled='0' WHERE id='".$val."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}
	}



	//request again for refresh
	$sqlf="SELECT * FROM dmx_schsum ORDER BY id";
	$sqlf=mysql_query($sqlf);
	while ($dataf=mysql_fetch_array($sqlf)){
		echo'<tr>';
			echo'<td><input name="schema_name[]" value="'.$dataf[schema_name].'" size="20"></td>';
			echo'<td><input name="nb_channels[]" value="'.$dataf[nb_channels].'" size="4"></td>';
			echo'<input name="id[]" value="'.$dataf[id].'" type="hidden">';
			//echo'<td><input name="disabled[]" value="'.$dataf[disabled].'" size="3"></td>';
			echo'<td><center><input type="checkbox" name="enabled[]" value="'.$dataf[id].'"'; if ($dataf[disabled]=='0'){echo' checked';} echo'></center></td>';

		    echo'<td><a href="schmod.php?delsch='.$dataf[id].'"';
		    echo" onclick=\"javascript:if(!confirm('DELETE SCHEMA: ".$dataf[schema_name]." ?')) return false;\"";
		    echo'>'.TXT_DELETE.'</a></td>';
		echo'</tr>';
	}

echo'</table></div>';

echo''.TXT_SEP_VALUES.' <input type="submit" name="chgvalues" value="'.TXT_SAVE.'"><br><br>';

echo''.TXT_ALL.' : <input type="submit" name="allenabled" value="'.TXT_ENABLE.'">';
echo'<input type="submit" name="alldisabled" value="'.TXT_DISABLE.'"><br><br>';

echo'</form>';

//print_r($_POST);

?>

</body>

