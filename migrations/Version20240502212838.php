<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502212838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE address_ip_id_seq CASCADE');
        $this->addSql('ALTER TABLE address_ip DROP CONSTRAINT fk_ef6d7b08a426d518');
        $this->addSql('ALTER TABLE address_ip DROP CONSTRAINT fk_ef6d7b08f92f3e70');
        $this->addSql('DROP TABLE address_ip');
        $this->addSql('ALTER TABLE AddressIp ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE AddressIp ALTER computer_id DROP NOT NULL');
        $this->addSql('ALTER TABLE Alert ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE Alert ADD CONSTRAINT FK_D63C69C5F92F3E70 FOREIGN KEY (country_id) REFERENCES "AuthorizedCountry" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D63C69C5F92F3E70 ON Alert (country_id)');
        $this->addSql('ALTER TABLE AuthorizedCountry ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE Computer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE Computer ALTER hostname DROP NOT NULL');
        $this->addSql('ALTER TABLE Computer ALTER all_locations_allow SET DEFAULT false');
        $this->addSql('ALTER TABLE ComputerAuthorizedCountry ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE LogsAlert ADD id_alert INT NOT NULL');
        $this->addSql('ALTER TABLE LogsAlert ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE LogsAlert ADD CONSTRAINT FK_4DDC576F97807F1 FOREIGN KEY (id_alert) REFERENCES "Alert" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE LogsAlert ADD CONSTRAINT FK_4DDC576F2434B33 FOREIGN KEY (id_computer) REFERENCES "Computer" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE LogsAlert ADD CONSTRAINT FK_4DDC576F92F3E70 FOREIGN KEY (country_id) REFERENCES "AuthorizedCountry" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4DDC576F97807F1 ON LogsAlert (id_alert)');
        $this->addSql('CREATE INDEX IDX_4DDC576F2434B33 ON LogsAlert (id_computer)');
        $this->addSql('CREATE INDEX IDX_4DDC576F92F3E70 ON LogsAlert (country_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE address_ip_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE address_ip (id INT NOT NULL, computer_id INT DEFAULT NULL, country_id INT NOT NULL, ip_address VARCHAR(90) NOT NULL, collection_datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_proxy BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_ef6d7b08f92f3e70 ON address_ip (country_id)');
        $this->addSql('CREATE INDEX idx_ef6d7b08a426d518 ON address_ip (computer_id)');
        $this->addSql('ALTER TABLE address_ip ADD CONSTRAINT fk_ef6d7b08a426d518 FOREIGN KEY (computer_id) REFERENCES "Computer" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE address_ip ADD CONSTRAINT fk_ef6d7b08f92f3e70 FOREIGN KEY (country_id) REFERENCES "AuthorizedCountry" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE SEQUENCE Computer_id_seq');
        $this->addSql('SELECT setval(\'Computer_id_seq\', (SELECT MAX(id) FROM "Computer"))');
        $this->addSql('ALTER TABLE "Computer" ALTER id SET DEFAULT nextval(\'Computer_id_seq\')');
        $this->addSql('ALTER TABLE "Computer" ALTER hostname SET NOT NULL');
        $this->addSql('ALTER TABLE "Computer" ALTER all_locations_allow DROP DEFAULT');
        $this->addSql('ALTER TABLE "LogsAlert" DROP CONSTRAINT FK_4DDC576F97807F1');
        $this->addSql('ALTER TABLE "LogsAlert" DROP CONSTRAINT FK_4DDC576F2434B33');
        $this->addSql('ALTER TABLE "LogsAlert" DROP CONSTRAINT FK_4DDC576F92F3E70');
        $this->addSql('DROP INDEX IDX_4DDC576F97807F1');
        $this->addSql('DROP INDEX IDX_4DDC576F2434B33');
        $this->addSql('DROP INDEX IDX_4DDC576F92F3E70');
        $this->addSql('ALTER TABLE "LogsAlert" DROP id_alert');
        $this->addSql('CREATE SEQUENCE LogsAlert_id_seq');
        $this->addSql('SELECT setval(\'LogsAlert_id_seq\', (SELECT MAX(id) FROM "LogsAlert"))');
        $this->addSql('ALTER TABLE "LogsAlert" ALTER id SET DEFAULT nextval(\'LogsAlert_id_seq\')');
        $this->addSql('CREATE SEQUENCE ComputerAuthorizedCountry_id_seq');
        $this->addSql('SELECT setval(\'ComputerAuthorizedCountry_id_seq\', (SELECT MAX(id) FROM "ComputerAuthorizedCountry"))');
        $this->addSql('ALTER TABLE "ComputerAuthorizedCountry" ALTER id SET DEFAULT nextval(\'ComputerAuthorizedCountry_id_seq\')');
        $this->addSql('CREATE SEQUENCE AddressIp_id_seq');
        $this->addSql('SELECT setval(\'AddressIp_id_seq\', (SELECT MAX(id) FROM "AddressIp"))');
        $this->addSql('ALTER TABLE "AddressIp" ALTER id SET DEFAULT nextval(\'AddressIp_id_seq\')');
        $this->addSql('ALTER TABLE "AddressIp" ALTER computer_id SET NOT NULL');
        $this->addSql('ALTER TABLE "Alert" DROP CONSTRAINT FK_D63C69C5F92F3E70');
        $this->addSql('DROP INDEX IDX_D63C69C5F92F3E70');
        $this->addSql('CREATE SEQUENCE Alert_id_seq');
        $this->addSql('SELECT setval(\'Alert_id_seq\', (SELECT MAX(id) FROM "Alert"))');
        $this->addSql('ALTER TABLE "Alert" ALTER id SET DEFAULT nextval(\'Alert_id_seq\')');
        $this->addSql('CREATE SEQUENCE AuthorizedCountry_id_seq');
        $this->addSql('SELECT setval(\'AuthorizedCountry_id_seq\', (SELECT MAX(id) FROM "AuthorizedCountry"))');
        $this->addSql('ALTER TABLE "AuthorizedCountry" ALTER id SET DEFAULT nextval(\'AuthorizedCountry_id_seq\')');
    }
}
