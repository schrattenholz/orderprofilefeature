<?php

namespace Schrattenholz\OrderProfileFeature;

use Page;

class OrderProfileFeature_Profile extends Page{
	private static $table_name="OrderProfileFeature_Profile";
	private static $singular_name = "Profil_Hauptseite";
	private static $plural_name = "Profil_Hauptseiten";
	private static $allowed_children = array("Schrattenholz\OrderProfileFeature\OrderProfileFeature_Profile_ProfileData","Schrattenholz\OrderProfileFeature\OrderProfileFeature_Profile_Orders","Schrattenholz\OrderProfileFeature\OrderProfileFeature_Profile_SavedOrders");
}