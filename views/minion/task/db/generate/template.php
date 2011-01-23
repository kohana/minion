

/**
 * <?php echo $description.PHP_EOL; ?>
 */
class <?php echo $class; ?> extends Kohana_Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		// $db->query(NULL, 'CREATE TABLE ... ');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		// $db->query(NULL, 'DROP TABLE ... ');
	}
}
