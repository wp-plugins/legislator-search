<?php if($results == new stdClass()): ?>
	<p>
		<?php echo _e('No legislators found for ') ?> <?php echo $zip ?>
	</p>
<?php else: ?>
	<ul>
	<?php foreach($results as $result): ?>
		<li>
			<img class="photo" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/legislator-search/images/photos/40x50/<?php echo $result->legislator->bioguide_id ?>.jpg" />
			<span class="title"><a href="<?php echo $result->legislator->website ?>"><?php echo $result->legislator->title ?></span> <span class="first_name"><?php echo $result->legislator->firstname ?></span> <span class="last_name"><?php echo $result->legislator->lastname ?></span> <span class="party">(<?php echo $result->legislator->party ?>)</span></a>
			<br />
			P: <span class="phone"><?php echo $result->legislator->phone ?></span>
			<?php if($legislator->webform != ''): ?>
				<br />
				<span class="webform"><a href="<?php echo $legislator->webform ?>">Contact</a></span>
			<?php endif ?>
		</li>
	<?php endforeach ?>
	</ul>
<?php endif ?>