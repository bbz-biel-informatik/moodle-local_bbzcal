<?php

function xmldb_local_bbzcal_upgrade($oldversion) {
  global $DB;

  $dbman = $DB->get_manager();

  if ($oldversion < 2020100501) {

    // Define table local_bbzcal to be created.
    $table = new xmldb_table('local_bbzcal');

    // Adding fields to table local_bbzcal.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('date', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);

    // Adding keys to table local_bbzcal.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Adding indexes to table local_bbzcal.
    $table->add_index('course_id-date', XMLDB_INDEX_NOTUNIQUE, ['course_id', 'date']);

    // Conditionally launch create table for local_bbzcal.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Bbzcal savepoint reached.
    upgrade_plugin_savepoint(true, 2020100501, 'local', 'bbzcal');
  }

  if ($oldversion < 2020100502) {

    // Define table local_bbzcal to be created.
    $table = new xmldb_table('local_bbzcal');

    // Adding keys to table local_bbzcal.
    $table->add_key('course_id', XMLDB_KEY_FOREIGN, ['course_id'], 'course', ['id']);

    // Conditionally launch create table for local_bbzcal.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Bbzcal savepoint reached.
    upgrade_plugin_savepoint(true, 2020100502, 'local', 'bbzcal');
  }


  return true;
}