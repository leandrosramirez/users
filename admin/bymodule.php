<?php
$ADMIN_SECTION = 'bymodule';
require_once(dirname(__FILE__).'/header.php');

if (array_key_exists('impersonate', $_POST)) {
	$impersonated_user= User::getUser($_POST['impersonate']);
	if ($impersonated_user !== null) {
		$impersonated_user->setSession(false); // always impersonate only for the browser session
		header('Location: '.UserConfig::$DEFAULTLOGINRETURN);
	}
	else
	{
		header('Location: #msg=cantimpersonate');
	}
}
?>
<script type='text/javascript' src='http://www.google.com/jsapi'></script>
<script type='text/javascript'>
google.load('visualization', '1', {'packages':['annotatedtimeline']});
google.setOnLoadCallback(function() {
	var data = new google.visualization.DataTable();
	data.addColumn('date', 'Date');
	<?php foreach (UserConfig::$modules as $module) { ?>
		data.addColumn('number', '<?php echo $module->getID()?>');
	<?php } ?>

	var daily = [
		<?php
		$dailyregs = User::getDailyRegistrationsByModule();

		$first = true;
		foreach ($dailyregs as $regdate => $day)
		{
			if (!$first) {
				?>, <?php
			}
			else
			{
				$first = false;
			}

			?>

		[new Date('<?php echo $regdate?>'), <?php
			$firstmodule = true;
			foreach (UserConfig::$modules as $module) {
				if (!$firstmodule) {
					?>, <?php
				}
				else
				{
					$firstmodule = false;
				}

				if (array_key_exists($module->getID(), $day)) {
					echo $day[$module->getID()];
				} else {
					echo 0;
				}
			}
			?>]<?php
		}
	?>

	];

	data.addRows(daily);

	var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
	chart.draw(data, {
		displayAnnotations: true
	});
});
</script>
<div id='chart_div' style='width: 100%; height: 240px; margin-bottom: 1em'></div>

<?php
require_once(dirname(__FILE__).'/footer.php');
