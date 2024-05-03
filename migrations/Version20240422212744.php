<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422212744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE AuthorizedCountry_id_seq1 CASCADE');
        $this->addSql('DROP SEQUENCE Computer_id_seq1 CASCADE');
        $this->addSql('DROP SEQUENCE AddressIp_idAddressip_seq CASCADE');
        $this->addSql('ALTER TABLE AddressIp DROP CONSTRAINT "AddressIp_computer_id_fkey"');
        $this->addSql('ALTER TABLE AddressIp ADD ip_address VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE AddressIp ADD is_proxy BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE AddressIp DROP ipAddress');
        $this->addSql('ALTER TABLE AddressIp DROP isproxy');
        $this->addSql('ALTER TABLE AddressIp ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE AddressIp ALTER verified DROP DEFAULT');
        $this->addSql('ALTER TABLE AddressIp ALTER verified SET NOT NULL');
        $this->addSql('ALTER TABLE AddressIp RENAME COLUMN collectiondate TO collection_date');
        $this->addSql('ALTER TABLE AddressIp RENAME COLUMN collectiontime TO collection_time');
        $this->addSql('ALTER TABLE AddressIp ADD CONSTRAINT FK_DAA53BE4A426D518 FOREIGN KEY (computer_id) REFERENCES "computer" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_b6577d10a426d518 RENAME TO IDX_DAA53BE4A426D518');
        $this->addSql('ALTER TABLE AuthorizedCountry ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE Computer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE ComputerAuthorizedCountry DROP CONSTRAINT "ComputerAuthorizedCountry_country_id_fkey"');
        $this->addSql('ALTER TABLE ComputerAuthorizedCountry DROP CONSTRAINT "ComputerAuthorizedCountry_computer_id_fkey"');
        $this->addSql('ALTER TABLE ComputerAuthorizedCountry ADD CONSTRAINT FK_E3482C73A426D518 FOREIGN KEY (computer_id) REFERENCES "computer" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ComputerAuthorizedCountry ADD CONSTRAINT FK_E3482C73F92F3E70 FOREIGN KEY (country_id) REFERENCES "authorizedcountry" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_b27869c9a426d518 RENAME TO IDX_E3482C73A426D518');
        $this->addSql('ALTER INDEX idx_b27869c9f92f3e70 RENAME TO IDX_E3482C73F92F3E70');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE AuthorizedCountry_id_seq1 INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE Computer_id_seq1 INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE AddressIp_idAddressip_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE "computerauthorizedcountry" DROP CONSTRAINT FK_E3482C73A426D518');
        $this->addSql('ALTER TABLE "computerauthorizedcountry" DROP CONSTRAINT FK_E3482C73F92F3E70');
        $this->addSql('ALTER TABLE "computerauthorizedcountry" ADD CONSTRAINT "ComputerAuthorizedCountry_country_id_fkey" FOREIGN KEY (country_id) REFERENCES "AuthorizedCountry" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "computerauthorizedcountry" ADD CONSTRAINT "ComputerAuthorizedCountry_computer_id_fkey" FOREIGN KEY (computer_id) REFERENCES "Computer" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_e3482c73f92f3e70 RENAME TO IDX_B27869C9F92F3E70');
        $this->addSql('ALTER INDEX idx_e3482c73a426d518 RENAME TO IDX_B27869C9A426D518');
        $this->addSql('ALTER TABLE "Addressip" DROP CONSTRAINT FK_DAA53BE4A426D518');
        $this->addSql('ALTER TABLE "Addressip" ADD ipAddress VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE "Addressip" ADD isproxy BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE "Addressip" DROP ip_address');
        $this->addSql('ALTER TABLE "Addressip" DROP is_proxy');
        $this->addSql('CREATE SEQUENCE Addressip_id_seq');
        $this->addSql('SELECT setval(\'Addressip_id_seq\', (SELECT MAX(id) FROM "Addressip"))');
        $this->addSql('ALTER TABLE "Addressip" ALTER id SET DEFAULT nextval(\'Addressip_id_seq\')');
        $this->addSql('ALTER TABLE "Addressip" ALTER verified SET DEFAULT false');
        $this->addSql('ALTER TABLE "Addressip" ALTER verified DROP NOT NULL');
        $this->addSql('ALTER TABLE "Addressip" RENAME COLUMN collection_date TO collectiondate');
        $this->addSql('ALTER TABLE "Addressip" RENAME COLUMN collection_time TO collectiontime');
        $this->addSql('ALTER TABLE "Addressip" ADD CONSTRAINT "AddressIp_computer_id_fkey" FOREIGN KEY (computer_id) REFERENCES "Computer" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_daa53be4a426d518 RENAME TO IDX_B6577D10A426D518');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE SEQUENCE computer_id_seq');
        $this->addSql('SELECT setval(\'computer_id_seq\', (SELECT MAX(id) FROM "computer"))');
        $this->addSql('ALTER TABLE "computer" ALTER id SET DEFAULT nextval(\'computer_id_seq\')');
        $this->addSql('CREATE SEQUENCE authorizedcountry_id_seq');
        $this->addSql('SELECT setval(\'authorizedcountry_id_seq\', (SELECT MAX(id) FROM "authorizedcountry"))');
        $this->addSql('ALTER TABLE "authorizedcountry" ALTER id SET DEFAULT nextval(\'authorizedcountry_id_seq\')');
    }
}
