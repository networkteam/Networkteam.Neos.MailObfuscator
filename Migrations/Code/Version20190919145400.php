<?php
namespace Neos\Flow\Core\Migrations;

/**
 * Move class names in namespace String because string is a reserved keyword
 */
class Version20190919145400 extends \Neos\Flow\Core\Migrations\AbstractMigration
{
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return 'Networkteam.Neos.MailObfuscator-20190919145400';
    }

    public function up()
    {
        $this->searchAndReplace('Networkteam\Neos\MailObfuscator\String\Converter', 'Networkteam\Neos\MailObfuscator\Converter', ['yaml', 'php', 'fusion']);
    }

    public function down()
    {
        $this->searchAndReplace('Networkteam\Neos\MailObfuscator\Converter', 'Networkteam\Neos\MailObfuscator\String\Converter', ['yaml', 'php', 'fusion']);
    }
}
