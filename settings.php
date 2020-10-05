<?php

// Ensure the configurations for this site are set
if ( $hassiteconfig ){

	// Create the new settings page
	// - in a local plugin this is not defined as standard, so normal $settings->methods will throw an error as
	// $settings will be NULL
	$settings = new admin_settingpage( 'local_bbzcal', 'BBZ Kalender' );

	// Create
	$ADMIN->add( 'localplugins', $settings );

	// Add a setting field to the settings for this page
	$settings->add( new admin_setting_configtext(

		// This is the reference you will use to your configuration
		'local_bbzcal/propertyname',

		// This is the friendly title for the config, which will be displayed
		'Property name',

		// This is helper text for this config field
		'The profile and course property to match users and courses',

		// This is the default value
		'canonicalclassnames',

		// This is the type of Parameter this config is
		PARAM_TEXT

	) );

}
