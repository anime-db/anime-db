<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130930180819_Init extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->createTableImage($schema);
        $this->createTableType($schema);
        $this->createTableName($schema);
        $this->createTableItemsGenres($schema);
        $this->createTableSource($schema);
        $this->createTableCountry($schema);
        $this->createTableGenre($schema);
        $this->createTableExtTranslations($schema);
        $this->createTableCountryTranslation($schema);
        $this->createTableTask($schema);
        $this->createTableNotice($schema);
        $this->createTableStorage($schema);
        $this->createTableItem($schema);

        // add index
        $this->addSql('
            CREATE INDEX image_item_idx ON image (item);
            CREATE INDEX name_item_idx ON name (item);
            CREATE INDEX item_genres_item_id_idx ON items_genres (item_id);
            CREATE INDEX item_genres_genre_id_idx ON items_genres (genre_id);
            CREATE INDEX source_item_idx ON source (item);
            CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key);
            CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key);
            CREATE INDEX country_translation_object_id_idx ON country_translation (object_id);
            CREATE UNIQUE INDEX country_translation_idx ON country_translation (locale, object_id, field);
            CREATE INDEX notice_show_idx ON notice (date_closed, date_created);
            CREATE INDEX item_manufacturer_idx ON item (manufacturer);
            CREATE INDEX item_storage_idx ON item (storage);
            CREATE INDEX item_type_idx ON item (type);
        ');
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('image');
        $schema->dropTable('type');
        $schema->dropTable('name');
        $schema->dropTable('items_genres');
        $schema->dropTable('source');
        $schema->dropTable('country');
        $schema->dropTable('genre');
        $schema->dropTable('ext_translations');
        $schema->dropTable('country_translation');
        $schema->dropTable('task');
        $schema->dropTable('notice');
        $schema->dropTable('storage');
        $schema->dropTable('item');
    }

    public function postUp(Schema $schema)
    {
        // clear sqlite sequence
        $this->addSql('DELETE FROM sqlite_sequence;');

        // add sqlite sequence
        $this->addSql('
            INSERT INTO "sqlite_sequence" VALUES("image",0);
            INSERT INTO "sqlite_sequence" VALUES("name",55);
            INSERT INTO "sqlite_sequence" VALUES("source",118);
            INSERT INTO "sqlite_sequence" VALUES("genre",64);
            INSERT INTO "sqlite_sequence" VALUES("storage",1);
            INSERT INTO "sqlite_sequence" VALUES("item",12);
        ');

        $this->addDataTypes();
        $this->addDataName();
        $this->addDataItemsGenres();
        $this->addDataSource();
        $this->addDataCountry();
        $this->addDataGenre();
        $this->addDataExtTranslations();
        $this->addDataCountryTranslation();
        $this->addDataStorage();
        $this->addDataItem();
    }

    public function postDown(Schema $schema)
    {
        // clear sqlite sequence
        $this->addSql('DELETE FROM sqlite_sequence;');
    }

    protected function createTableImage(Schema $schema)
    {
        $this->addSql('CREATE TABLE image (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            source VARCHAR(256) NOT NULL
        );');
    }

    protected function createTableType(Schema $schema)
    {
        $this->addSql('CREATE TABLE type (
            id VARCHAR(16) PRIMARY KEY NOT NULL,
            name VARCHAR(32) NOT NULL
        );');
    }

    protected function createTableName(Schema $schema)
    {
        $this->addSql('CREATE TABLE name (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL
        );');
    }

    protected function createTableItemsGenres(Schema $schema)
    {
        $this->addSql('CREATE TABLE items_genres (
            item_id INTEGER NOT NULL,
            genre_id INTEGER NOT NULL,
            PRIMARY KEY(item_id, genre_id)
        );');
    }

    protected function createTableSource(Schema $schema)
    {
        $this->addSql('CREATE TABLE source (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            url VARCHAR(256) NOT NULL
        );');
    }

    protected function createTableCountry(Schema $schema)
    {
        $this->addSql('CREATE TABLE country (
            id VARCHAR(2) PRIMARY KEY NOT NULL,
            name VARCHAR(16) NOT NULL
        );');
    }

    protected function createTableGenre(Schema $schema)
    {
        $this->addSql('CREATE TABLE genre (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(16) NOT NULL
        );');
    }

    protected function createTableExtTranslations(Schema $schema)
    {
        $this->addSql('CREATE TABLE ext_translations (
            id INTEGER NOT NULL,
            locale VARCHAR(8) NOT NULL,
            object_class VARCHAR(255) NOT NULL,
            field VARCHAR(32) NOT NULL,
            foreign_key VARCHAR(64) NOT NULL,
            content CLOB DEFAULT NULL,
            PRIMARY KEY(id)
        );');
    }

    protected function createTableCountryTranslation(Schema $schema)
    {
        $this->addSql('CREATE TABLE country_translation (
            id INTEGER NOT NULL,
            object_id VARCHAR(2) DEFAULT NULL,
            locale VARCHAR(8) NOT NULL,
            field VARCHAR(32) NOT NULL,
            content CLOB DEFAULT NULL,
            PRIMARY KEY(id)
        );');
    }

    protected function createTableTask(Schema $schema)
    {
        $this->addSql('CREATE TABLE `task` (
            `id` INTEGER NOT NULL,
            `command` VARCHAR(128) NOT NULL,
            `last_run` DATETIME DEFAULT NULL,
            `next_run` DATETIME NOT NULL,
            `modify` VARCHAR(128) DEFAULT NULL,
            `status` INTEGER NOT NULL,
            PRIMARY KEY(`id`)
        );');
    }

    protected function createTableNotice(Schema $schema)
    {
        $this->addSql('CREATE TABLE notice (
            id INTEGER NOT NULL,
            message CLOB NOT NULL,
            date_closed DATETIME DEFAULT NULL,
            date_created DATETIME NOT NULL,
            lifetime INTEGER NOT NULL,
            status INTEGER NOT NULL,
            PRIMARY KEY(id)
        );');
    }

    protected function createTableStorage(Schema $schema)
    {
        $this->addSql('CREATE TABLE "storage" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(128) NOT NULL,
            description CLOB NOT NULL,
            type VARCHAR(16) NOT NULL,
            path CLOB DEFAULT NULL
        );');
    }

    protected function createTableItem(Schema $schema)
    {
        $this->addSql('CREATE TABLE "item"  (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(16) DEFAULT NULL,
            manufacturer VARCHAR(2) DEFAULT NULL,
            storage INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL,
            date_start DATE NOT NULL,
            date_end DATE DEFAULT NULL,
            duration INTEGER DEFAULT NULL,
            summary CLOB DEFAULT NULL,
            path VARCHAR(256) DEFAULT NULL,
            episodes CLOB DEFAULT NULL,
            episodes_number VARCHAR(5) DEFAULT NULL,
            translate VARCHAR(256) DEFAULT NULL,
            file_info CLOB DEFAULT NULL,
            cover VARCHAR(256) DEFAULT NULL,
            date_add DATETIME NOT NULL,
            date_update DATETIME NOT NULL
        );');
    }

    protected function addDataTypes()
    {
        $this->addSql('
            INSERT INTO "type" VALUES("feature","Feature");
            INSERT INTO "type" VALUES("featurette","Featurette");
            INSERT INTO "type" VALUES("ona","ONA");
            INSERT INTO "type" VALUES("ova","OVA");
            INSERT INTO "type" VALUES("tv","TV");
            INSERT INTO "type" VALUES("special","TV-special");
            INSERT INTO "type" VALUES("music","Music video");
            INSERT INTO "type" VALUES("commercial","Commercial");
        ');
    }

    protected function addDataName()
    {
        $this->addSql('
            INSERT INTO "name" VALUES(1,1,"One Piece");
            INSERT INTO "name" VALUES(2,1,"Одним куском");
            INSERT INTO "name" VALUES(3,1,"ワンピース");
            INSERT INTO "name" VALUES(4,2,"Samurai Champloo");
            INSERT INTO "name" VALUES(5,2,"サムライチャンプルー");
            INSERT INTO "name" VALUES(6,3,"Fullmetal Alchemist");
            INSERT INTO "name" VALUES(7,3,"Hagane no Renkin Jutsushi");
            INSERT INTO "name" VALUES(8,3,"Hagane no Renkinjutsushi");
            INSERT INTO "name" VALUES(9,3,"Full Metal Alchemist");
            INSERT INTO "name" VALUES(10,3,"Hagaren");
            INSERT INTO "name" VALUES(11,3,"鋼の錬金術師");
            INSERT INTO "name" VALUES(12,4,"Spirited Away");
            INSERT INTO "name" VALUES(13,4,"Sen to Chihiro no Kamikakushi");
            INSERT INTO "name" VALUES(14,4,"千と千尋の神隠し");
            INSERT INTO "name" VALUES(15,5,"Great Teacher Onizuka");
            INSERT INTO "name" VALUES(16,5,"GTO");
            INSERT INTO "name" VALUES(17,5,"グレート・ティーチャー・オニヅカ");
            INSERT INTO "name" VALUES(18,6,"Beck: Mongolian Chop Squad");
            INSERT INTO "name" VALUES(19,6,"Beck - Mongorian Chop Squad");
            INSERT INTO "name" VALUES(20,6,"Beck Mongolian Chop Squad");
            INSERT INTO "name" VALUES(21,6,"Бек: Восточная Ударная Группа");
            INSERT INTO "name" VALUES(22,6,"BECK　ベック");
            INSERT INTO "name" VALUES(23,6,"ベック");
            INSERT INTO "name" VALUES(24,7,"Samurai X: Trust & Betrayal");
            INSERT INTO "name" VALUES(25,7,"Rurouni Kenshin: Meiji Kenkaku Romantan - Tsuioku Hen");
            INSERT INTO "name" VALUES(26,7,"Rurouni Kenshin: Meiji Kenkaku Romantan - Tsuiokuhen");
            INSERT INTO "name" VALUES(27,7,"Samurai X: Trust and Betrayal");
            INSERT INTO "name" VALUES(28,7,"Rurouni Kenshin: Tsuioku Hen");
            INSERT INTO "name" VALUES(29,7,"るろうに剣心 -明治剣客浪漫譚-　追憶編");
            INSERT INTO "name" VALUES(30,7,"るろうに剣心―明治剣客浪漫譚―追憶編");
            INSERT INTO "name" VALUES(31,7,"るろうに剣心 -明治剣客浪漫譚- 追憶編");
            INSERT INTO "name" VALUES(32,8,"My Neighbor Totoro");
            INSERT INTO "name" VALUES(33,8,"Tonari no Totoro");
            INSERT INTO "name" VALUES(34,8,"My Neighbour Totoro");
            INSERT INTO "name" VALUES(35,8,"Наш сосед Тоторо");
            INSERT INTO "name" VALUES(36,8,"となりのトトロ");
            INSERT INTO "name" VALUES(37,9,"Hellsing Ultimate");
            INSERT INTO "name" VALUES(38,9,"Hellsing OVA");
            INSERT INTO "name" VALUES(39,9,"Hellsing");
            INSERT INTO "name" VALUES(40,10,"Silver Soul");
            INSERT INTO "name" VALUES(41,10,"Gintama");
            INSERT INTO "name" VALUES(42,10,"銀魂[ぎんたま]");
            INSERT INTO "name" VALUES(43,10,"The Best of Gintama-san");
            INSERT INTO "name" VALUES(44,10,"Yorinuki Gintama-san");
            INSERT INTO "name" VALUES(45,10,"よりぬき銀魂さん");
            INSERT INTO "name" VALUES(46,11,"Bakuman.");
            INSERT INTO "name" VALUES(47,11,"バクマン。");
            INSERT INTO "name" VALUES(48,11,"バクマン.");
            INSERT INTO "name" VALUES(49,12,"Heavenly Breakthrough Gurren Lagann");
            INSERT INTO "name" VALUES(50,12,"Tengen Toppa Gurren-Lagann");
            INSERT INTO "name" VALUES(51,12,"Tengen Toppa Gurren Lagann");
            INSERT INTO "name" VALUES(52,12,"天元突破 グレンラガン");
            INSERT INTO "name" VALUES(53,12,"天元突破グレンラガン");
            INSERT INTO "name" VALUES(54,12,"Tengen Toppa Gurren Lagann: Ore no Gurren wa Pikka Pika!!");
            INSERT INTO "name" VALUES(55,12,"天元突破 グレンラガン 俺のグレンはピッカピカ!!");
        ');
    }

    protected function addDataItemsGenres()
    {
        $this->addSql('
            INSERT INTO "items_genres" VALUES(1,1);
            INSERT INTO "items_genres" VALUES(1,2);
            INSERT INTO "items_genres" VALUES(1,23);
            INSERT INTO "items_genres" VALUES(1,51);
            INSERT INTO "items_genres" VALUES(2,1);
            INSERT INTO "items_genres" VALUES(2,2);
            INSERT INTO "items_genres" VALUES(2,4);
            INSERT INTO "items_genres" VALUES(2,20);
            INSERT INTO "items_genres" VALUES(3,1);
            INSERT INTO "items_genres" VALUES(3,4);
            INSERT INTO "items_genres" VALUES(3,23);
            INSERT INTO "items_genres" VALUES(3,51);
            INSERT INTO "items_genres" VALUES(4,1);
            INSERT INTO "items_genres" VALUES(4,4);
            INSERT INTO "items_genres" VALUES(4,47);
            INSERT INTO "items_genres" VALUES(5,2);
            INSERT INTO "items_genres" VALUES(5,4);
            INSERT INTO "items_genres" VALUES(5,50);
            INSERT INTO "items_genres" VALUES(6,2);
            INSERT INTO "items_genres" VALUES(6,4);
            INSERT INTO "items_genres" VALUES(6,14);
            INSERT INTO "items_genres" VALUES(6,19);
            INSERT INTO "items_genres" VALUES(7,4);
            INSERT INTO "items_genres" VALUES(7,19);
            INSERT INTO "items_genres" VALUES(7,20);
            INSERT INTO "items_genres" VALUES(8,1);
            INSERT INTO "items_genres" VALUES(8,2);
            INSERT INTO "items_genres" VALUES(8,4);
            INSERT INTO "items_genres" VALUES(8,47);
            INSERT INTO "items_genres" VALUES(9,1);
            INSERT INTO "items_genres" VALUES(9,4);
            INSERT INTO "items_genres" VALUES(9,13);
            INSERT INTO "items_genres" VALUES(10,1);
            INSERT INTO "items_genres" VALUES(10,2);
            INSERT INTO "items_genres" VALUES(10,3);
            INSERT INTO "items_genres" VALUES(11,2);
            INSERT INTO "items_genres" VALUES(11,17);
            INSERT INTO "items_genres" VALUES(12,1);
            INSERT INTO "items_genres" VALUES(12,3);
            INSERT INTO "items_genres" VALUES(12,4);
            INSERT INTO "items_genres" VALUES(12,12);
        ');
    }

    protected function addDataSource()
    {
        $this->addSql('
            INSERT INTO "source" VALUES(1,1,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=836");
            INSERT INTO "source" VALUES(2,1,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=69");
            INSERT INTO "source" VALUES(3,1,"http://myanimelist.net/anime/21/");
            INSERT INTO "source" VALUES(4,1,"http://cal.syoboi.jp/tid/350/time");
            INSERT INTO "source" VALUES(5,1,"http://www.allcinema.net/prog/show_c.php?num_c=162790");
            INSERT INTO "source" VALUES(6,1,"http://en.wikipedia.org/wiki/One_Piece");
            INSERT INTO "source" VALUES(7,1,"http://ru.wikipedia.org/wiki/One_Piece");
            INSERT INTO "source" VALUES(8,1,"http://ja.wikipedia.org/wiki/ONE_PIECE_%28%E3%82%A2%E3%83%8B%E3%83%A1%29");
            INSERT INTO "source" VALUES(9,1,"http://www.fansubs.ru/base.php?id=731");
            INSERT INTO "source" VALUES(10,1,"http://www.world-art.ru/animation/animation.php?id=803");
            INSERT INTO "source" VALUES(11,2,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=2636");
            INSERT INTO "source" VALUES(12,2,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=1543");
            INSERT INTO "source" VALUES(13,2,"http://myanimelist.net/anime/205/");
            INSERT INTO "source" VALUES(14,2,"http://cal.syoboi.jp/tid/395/time");
            INSERT INTO "source" VALUES(15,2,"http://www.allcinema.net/prog/show_c.php?num_c=319278");
            INSERT INTO "source" VALUES(16,2,"http://wiki.livedoor.jp/radioi_34/d/%a5%b5%a5%e0%a5%e9%a5%a4%a5%c1%a5%e3%a5%f3%a5%d7%a5%eb%a1%bc");
            INSERT INTO "source" VALUES(17,2,"http://www1.vecceed.ne.jp/~m-satomi/SAMURAICHANPLOO.html");
            INSERT INTO "source" VALUES(18,2,"http://en.wikipedia.org/wiki/Samurai_Champloo");
            INSERT INTO "source" VALUES(19,2,"http://ru.wikipedia.org/wiki/%D0%A1%D0%B0%D0%BC%D1%83%D1%80%D0%B0%D0%B9_%D0%A7%D0%B0%D0%BC%D0%BF%D0%BB%D1%83");
            INSERT INTO "source" VALUES(20,2,"http://ja.wikipedia.org/wiki/%E3%82%B5%E3%83%A0%E3%83%A9%E3%82%A4%E3%83%81%E3%83%A3%E3%83%B3%E3%83%97%E3%83%AB%E3%83%BC");
            INSERT INTO "source" VALUES(21,2,"http://www.fansubs.ru/base.php?id=361");
            INSERT INTO "source" VALUES(22,2,"http://www.world-art.ru/animation/animation.php?id=2699");
            INSERT INTO "source" VALUES(23,3,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=2960");
            INSERT INTO "source" VALUES(24,3,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=979");
            INSERT INTO "source" VALUES(25,3,"http://cal.syoboi.jp/tid/134/time");
            INSERT INTO "source" VALUES(26,3,"http://www.allcinema.net/prog/show_c.php?num_c=241943");
            INSERT INTO "source" VALUES(27,3,"http://www1.vecceed.ne.jp/~m-satomi/FULLMETALALCHEMIST.html");
            INSERT INTO "source" VALUES(28,3,"http://en.wikipedia.org/wiki/Fullmetal_Alchemist");
            INSERT INTO "source" VALUES(29,3,"http://ru.wikipedia.org/wiki/Fullmetal_Alchemist");
            INSERT INTO "source" VALUES(30,3,"http://ja.wikipedia.org/wiki/%E9%8B%BC%E3%81%AE%E9%8C%AC%E9%87%91%E8%A1%93%E5%B8%AB_%28%E3%82%A2%E3%83%8B%E3%83%A1%29");
            INSERT INTO "source" VALUES(31,3,"http://oboi.kards.ru/?act=search&level=6&search_str=FullMetal%20Alchemist");
            INSERT INTO "source" VALUES(32,3,"http://www.fansubs.ru/base.php?id=124");
            INSERT INTO "source" VALUES(33,3,"http://www.world-art.ru/animation/animation.php?id=2368");
            INSERT INTO "source" VALUES(34,4,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=377");
            INSERT INTO "source" VALUES(35,4,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=112");
            INSERT INTO "source" VALUES(36,4,"http://www.allcinema.net/prog/show_c.php?num_c=163027");
            INSERT INTO "source" VALUES(37,4,"http://en.wikipedia.org/wiki/Spirited_Away");
            INSERT INTO "source" VALUES(38,4,"http://ru.wikipedia.org/wiki/%D0%A3%D0%BD%D0%B5%D1%81%D1%91%D0%BD%D0%BD%D1%8B%D0%B5_%D0%BF%D1%80%D0%B8%D0%B7%D1%80%D0%B0%D0%BA%D0%B0%D0%BC%D0%B8");
            INSERT INTO "source" VALUES(39,4,"http://ja.wikipedia.org/wiki/%E5%8D%83%E3%81%A8%E5%8D%83%E5%B0%8B%E3%81%AE%E7%A5%9E%E9%9A%A0%E3%81%97");
            INSERT INTO "source" VALUES(40,4,"http://oboi.kards.ru/?act=search&level=6&search_str=Spirited%20Away");
            INSERT INTO "source" VALUES(41,4,"http://www.fansubs.ru/base.php?id=368");
            INSERT INTO "source" VALUES(42,4,"http://uanime.org.ua/anime/38.html");
            INSERT INTO "source" VALUES(43,4,"http://www.world-art.ru/animation/animation.php?id=87");
            INSERT INTO "source" VALUES(44,5,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=153");
            INSERT INTO "source" VALUES(45,5,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=191");
            INSERT INTO "source" VALUES(46,5,"http://www.allcinema.net/prog/show_c.php?num_c=125613");
            INSERT INTO "source" VALUES(47,5,"http://en.wikipedia.org/wiki/Great_Teacher_Onizuka");
            INSERT INTO "source" VALUES(48,5,"http://ru.wikipedia.org/wiki/%D0%9A%D1%80%D1%83%D1%82%D0%BE%D0%B9_%D1%83%D1%87%D0%B8%D1%82%D0%B5%D0%BB%D1%8C_%D0%9E%D0%BD%D0%B8%D0%B4%D0%B7%D1%83%D0%BA%D0%B0");
            INSERT INTO "source" VALUES(49,5,"http://ja.wikipedia.org/wiki/GTO_(%E6%BC%AB%E7%94%BB)");
            INSERT INTO "source" VALUES(50,5,"http://www.fansubs.ru/base.php?id=147");
            INSERT INTO "source" VALUES(51,5,"http://www.world-art.ru/animation/animation.php?id=311");
            INSERT INTO "source" VALUES(52,6,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=4404");
            INSERT INTO "source" VALUES(53,6,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=2320");
            INSERT INTO "source" VALUES(54,6,"http://myanimelist.net/anime/57/");
            INSERT INTO "source" VALUES(55,6,"http://cal.syoboi.jp/tid/490/time");
            INSERT INTO "source" VALUES(56,6,"http://www.allcinema.net/prog/show_c.php?num_c=321252");
            INSERT INTO "source" VALUES(57,6,"http://en.wikipedia.org/wiki/BECK:_Mongolian_Chop_Squad");
            INSERT INTO "source" VALUES(58,6,"http://ru.wikipedia.org/wiki/BECK:_Mongolian_Chop_Squad");
            INSERT INTO "source" VALUES(59,6,"http://ja.wikipedia.org/wiki/BECK_%28%E6%BC%AB%E7%94%BB%29");
            INSERT INTO "source" VALUES(60,6,"http://www.fansubs.ru/base.php?id=725");
            INSERT INTO "source" VALUES(61,6,"http://www.world-art.ru/animation/animation.php?id=2671");
            INSERT INTO "source" VALUES(62,7,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=210");
            INSERT INTO "source" VALUES(63,7,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=73");
            INSERT INTO "source" VALUES(64,7,"http://myanimelist.net/anime/44/");
            INSERT INTO "source" VALUES(65,7,"http://www.allcinema.net/prog/show_c.php?num_c=88146");
            INSERT INTO "source" VALUES(66,7,"http://en.wikipedia.org/wiki/Rurouni_Kenshin");
            INSERT INTO "source" VALUES(67,7,"http://ru.wikipedia.org/wiki/%D0%A1%D0%B0%D0%BC%D1%83%D1%80%D0%B0%D0%B9_X");
            INSERT INTO "source" VALUES(68,7,"http://ja.wikipedia.org/wiki/%E3%82%8B%E3%82%8D%E3%81%86%E3%81%AB%E5%89%A3%E5%BF%83_-%E6%98%8E%E6%B2%BB%E5%89%A3%E5%AE%A2%E6%B5%AA%E6%BC%AB%E8%AD%9A-");
            INSERT INTO "source" VALUES(69,7,"http://www.fansubs.ru/base.php?id=870");
            INSERT INTO "source" VALUES(70,7,"http://www.world-art.ru/animation/animation.php?id=82");
            INSERT INTO "source" VALUES(71,8,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=534");
            INSERT INTO "source" VALUES(72,8,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=303");
            INSERT INTO "source" VALUES(73,8,"http://www.allcinema.net/prog/show_c.php?num_c=150435");
            INSERT INTO "source" VALUES(74,8,"http://en.wikipedia.org/wiki/My_Neighbor_Totoro");
            INSERT INTO "source" VALUES(75,8,"http://ru.wikipedia.org/wiki/%D0%9D%D0%B0%D1%88_%D1%81%D0%BE%D1%81%D0%B5%D0%B4_%D0%A2%D0%BE%D1%82%D0%BE%D1%80%D0%BE");
            INSERT INTO "source" VALUES(76,8,"http://ja.wikipedia.org/wiki/%E3%81%A8%E3%81%AA%E3%82%8A%E3%81%AE%E3%83%88%E3%83%88%E3%83%AD");
            INSERT INTO "source" VALUES(77,8,"http://www.fansubs.ru/base.php?id=266");
            INSERT INTO "source" VALUES(78,8,"http://uanime.org.ua/anime/145.html");
            INSERT INTO "source" VALUES(79,8,"http://www.world-art.ru/animation/animation.php?id=62");
            INSERT INTO "source" VALUES(80,9,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=5114");
            INSERT INTO "source" VALUES(81,9,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=3296");
            INSERT INTO "source" VALUES(82,9,"http://myanimelist.net/anime/777/");
            INSERT INTO "source" VALUES(83,9,"http://www.allcinema.net/prog/show_c.php?num_c=323337");
            INSERT INTO "source" VALUES(84,9,"http://en.wikipedia.org/wiki/Hellsing_%28manga%29");
            INSERT INTO "source" VALUES(85,9,"http://ru.wikipedia.org/wiki/%D0%A5%D0%B5%D0%BB%D0%BB%D1%81%D0%B8%D0%BD%D0%B3:_%D0%92%D0%BE%D0%B9%D0%BD%D0%B0_%D1%81_%D0%BD%D0%B5%D1%87%D0%B8%D1%81%D1%82%D1%8C%D1%8E");
            INSERT INTO "source" VALUES(86,9,"http://ja.wikipedia.org/wiki/HELLSING");
            INSERT INTO "source" VALUES(87,9,"http://www.fansubs.ru/base.php?id=988");
            INSERT INTO "source" VALUES(88,9,"http://uanime.org.ua/anime/63.html");
            INSERT INTO "source" VALUES(89,9,"http://www.world-art.ru/animation/animation.php?id=4340");
            INSERT INTO "source" VALUES(90,10,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=6236");
            INSERT INTO "source" VALUES(91,10,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=3468");
            INSERT INTO "source" VALUES(92,10,"http://myanimelist.net/anime/918/");
            INSERT INTO "source" VALUES(93,10,"http://cal.syoboi.jp/tid/853/time");
            INSERT INTO "source" VALUES(94,10,"http://www.allcinema.net/prog/show_c.php?num_c=324863");
            INSERT INTO "source" VALUES(95,10,"http://en.wikipedia.org/wiki/Gintama");
            INSERT INTO "source" VALUES(96,10,"http://ru.wikipedia.org/wiki/Gintama");
            INSERT INTO "source" VALUES(97,10,"http://ja.wikipedia.org/wiki/%E9%8A%80%E9%AD%82_%28%E3%82%A2%E3%83%8B%E3%83%A1%29");
            INSERT INTO "source" VALUES(98,10,"http://www.fansubs.ru/base.php?id=2022");
            INSERT INTO "source" VALUES(99,10,"http://www.world-art.ru/animation/animation.php?id=5013");
            INSERT INTO "source" VALUES(100,11,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=11197");
            INSERT INTO "source" VALUES(101,11,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=7251");
            INSERT INTO "source" VALUES(102,11,"http://myanimelist.net/anime/7674/");
            INSERT INTO "source" VALUES(103,11,"http://cal.syoboi.jp/tid/2037/time");
            INSERT INTO "source" VALUES(104,11,"http://www.allcinema.net/prog/show_c.php?num_c=335759");
            INSERT INTO "source" VALUES(105,11,"http://en.wikipedia.org/wiki/Bakuman");
            INSERT INTO "source" VALUES(106,11,"http://ru.wikipedia.org/wiki/Bakuman");
            INSERT INTO "source" VALUES(107,11,"http://ja.wikipedia.org/wiki/%E3%83%90%E3%82%AF%E3%83%9E%E3%83%B3%E3%80%82_%28%E3%82%A2%E3%83%8B%E3%83%A1%29");
            INSERT INTO "source" VALUES(108,11,"http://www.fansubs.ru/base.php?id=3109");
            INSERT INTO "source" VALUES(109,11,"http://www.world-art.ru/animation/animation.php?id=7740");
            INSERT INTO "source" VALUES(110,12,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=6698");
            INSERT INTO "source" VALUES(111,12,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=4575");
            INSERT INTO "source" VALUES(112,12,"http://cal.syoboi.jp/tid/1000/time");
            INSERT INTO "source" VALUES(113,12,"http://www.allcinema.net/prog/show_c.php?num_c=326669");
            INSERT INTO "source" VALUES(114,12,"http://en.wikipedia.org/wiki/Tengen_Toppa_Gurren_Lagann");
            INSERT INTO "source" VALUES(115,12,"http://ru.wikipedia.org/wiki/Tengen_Toppa_Gurren_Lagann");
            INSERT INTO "source" VALUES(116,12,"http://ja.wikipedia.org/wiki/%E5%A4%A9%E5%85%83%E7%AA%81%E7%A0%B4%E3%82%B0%E3%83%AC%E3%83%B3%E3%83%A9%E3%82%AC%E3%83%B3");
            INSERT INTO "source" VALUES(117,12,"http://www.fansubs.ru/base.php?id=1769");
            INSERT INTO "source" VALUES(118,12,"http://www.world-art.ru/animation/animation.php?id=5959");
        ');
    }

    protected function addDataCountry()
    {
        $this->addSql('
            INSERT INTO "country" VALUES("AF","Afghanistan");
            INSERT INTO "country" VALUES("AL","Albania");
            INSERT INTO "country" VALUES("DZ","Algeria");
            INSERT INTO "country" VALUES("AS","American Samoa");
            INSERT INTO "country" VALUES("AD","Andorra");
            INSERT INTO "country" VALUES("AO","Angola");
            INSERT INTO "country" VALUES("AI","Anguilla");
            INSERT INTO "country" VALUES("AQ","Antarctica");
            INSERT INTO "country" VALUES("AG","Antigua and Barbuda");
            INSERT INTO "country" VALUES("AR","Argentina");
            INSERT INTO "country" VALUES("AM","Armenia");
            INSERT INTO "country" VALUES("AW","Aruba");
            INSERT INTO "country" VALUES("AU","Australia");
            INSERT INTO "country" VALUES("AT","Austria");
            INSERT INTO "country" VALUES("AZ","Azerbaijan");
            INSERT INTO "country" VALUES("BS","Bahamas");
            INSERT INTO "country" VALUES("BH","Bahrain");
            INSERT INTO "country" VALUES("BD","Bangladesh");
            INSERT INTO "country" VALUES("BB","Barbados");
            INSERT INTO "country" VALUES("BY","Belarus");
            INSERT INTO "country" VALUES("BE","Belgium");
            INSERT INTO "country" VALUES("BZ","Belize");
            INSERT INTO "country" VALUES("BJ","Benin");
            INSERT INTO "country" VALUES("BM","Bermuda");
            INSERT INTO "country" VALUES("BT","Bhutan");
            INSERT INTO "country" VALUES("BO","Bolivia");
            INSERT INTO "country" VALUES("BA","Bosnia and Herzegovina");
            INSERT INTO "country" VALUES("BW","Botswana");
            INSERT INTO "country" VALUES("BV","Bouvet Island");
            INSERT INTO "country" VALUES("BR","Brazil");
            INSERT INTO "country" VALUES("BQ","British Antarctic Territory");
            INSERT INTO "country" VALUES("IO","British Indian Ocean Territory");
            INSERT INTO "country" VALUES("VG","British Virgin Islands");
            INSERT INTO "country" VALUES("BN","Brunei");
            INSERT INTO "country" VALUES("BG","Bulgaria");
            INSERT INTO "country" VALUES("BF","Burkina Faso");
            INSERT INTO "country" VALUES("BI","Burundi");
            INSERT INTO "country" VALUES("KH","Cambodia");
            INSERT INTO "country" VALUES("CM","Cameroon");
            INSERT INTO "country" VALUES("CA","Canada");
            INSERT INTO "country" VALUES("CT","Canton and Enderbury Islands");
            INSERT INTO "country" VALUES("CV","Cape Verde");
            INSERT INTO "country" VALUES("KY","Cayman Islands");
            INSERT INTO "country" VALUES("CF","Central African Republic");
            INSERT INTO "country" VALUES("TD","Chad");
            INSERT INTO "country" VALUES("CL","Chile");
            INSERT INTO "country" VALUES("CN","China");
            INSERT INTO "country" VALUES("CX","Christmas Island");
            INSERT INTO "country" VALUES("CC","Cocos (Keeling) Islands");
            INSERT INTO "country" VALUES("CO","Colombia");
            INSERT INTO "country" VALUES("KM","Comoros");
            INSERT INTO "country" VALUES("CG","Congo - Brazzaville");
            INSERT INTO "country" VALUES("CD","Congo - Kinshasa");
            INSERT INTO "country" VALUES("CK","Cook Islands");
            INSERT INTO "country" VALUES("CR","Costa Rica");
            INSERT INTO "country" VALUES("HR","Croatia");
            INSERT INTO "country" VALUES("CU","Cuba");
            INSERT INTO "country" VALUES("CY","Cyprus");
            INSERT INTO "country" VALUES("CZ","Czech Republic");
            INSERT INTO "country" VALUES("CI","Côte d’Ivoire");
            INSERT INTO "country" VALUES("DK","Denmark");
            INSERT INTO "country" VALUES("DJ","Djibouti");
            INSERT INTO "country" VALUES("DM","Dominica");
            INSERT INTO "country" VALUES("DO","Dominican Republic");
            INSERT INTO "country" VALUES("NQ","Dronning Maud Land");
            INSERT INTO "country" VALUES("DD","East Germany");
            INSERT INTO "country" VALUES("EC","Ecuador");
            INSERT INTO "country" VALUES("EG","Egypt");
            INSERT INTO "country" VALUES("SV","El Salvador");
            INSERT INTO "country" VALUES("GQ","Equatorial Guinea");
            INSERT INTO "country" VALUES("ER","Eritrea");
            INSERT INTO "country" VALUES("EE","Estonia");
            INSERT INTO "country" VALUES("ET","Ethiopia");
            INSERT INTO "country" VALUES("FK","Falkland Islands");
            INSERT INTO "country" VALUES("FO","Faroe Islands");
            INSERT INTO "country" VALUES("FJ","Fiji");
            INSERT INTO "country" VALUES("FI","Finland");
            INSERT INTO "country" VALUES("FR","France");
            INSERT INTO "country" VALUES("GF","French Guiana");
            INSERT INTO "country" VALUES("PF","French Polynesia");
            INSERT INTO "country" VALUES("TF","French Southern Territories");
            INSERT INTO "country" VALUES("FQ","French Southern and Antarctic Territories");
            INSERT INTO "country" VALUES("GA","Gabon");
            INSERT INTO "country" VALUES("GM","Gambia");
            INSERT INTO "country" VALUES("GE","Georgia");
            INSERT INTO "country" VALUES("DE","Germany");
            INSERT INTO "country" VALUES("GH","Ghana");
            INSERT INTO "country" VALUES("GI","Gibraltar");
            INSERT INTO "country" VALUES("GR","Greece");
            INSERT INTO "country" VALUES("GL","Greenland");
            INSERT INTO "country" VALUES("GD","Grenada");
            INSERT INTO "country" VALUES("GP","Guadeloupe");
            INSERT INTO "country" VALUES("GU","Guam");
            INSERT INTO "country" VALUES("GT","Guatemala");
            INSERT INTO "country" VALUES("GG","Guernsey");
            INSERT INTO "country" VALUES("GN","Guinea");
            INSERT INTO "country" VALUES("GW","Guinea-Bissau");
            INSERT INTO "country" VALUES("GY","Guyana");
            INSERT INTO "country" VALUES("HT","Haiti");
            INSERT INTO "country" VALUES("HM","Heard Island and McDonald Islands");
            INSERT INTO "country" VALUES("HN","Honduras");
            INSERT INTO "country" VALUES("HK","Hong Kong SAR China");
            INSERT INTO "country" VALUES("HU","Hungary");
            INSERT INTO "country" VALUES("IS","Iceland");
            INSERT INTO "country" VALUES("IN","India");
            INSERT INTO "country" VALUES("ID","Indonesia");
            INSERT INTO "country" VALUES("IR","Iran");
            INSERT INTO "country" VALUES("IQ","Iraq");
            INSERT INTO "country" VALUES("IE","Ireland");
            INSERT INTO "country" VALUES("IM","Isle of Man");
            INSERT INTO "country" VALUES("IL","Israel");
            INSERT INTO "country" VALUES("IT","Italy");
            INSERT INTO "country" VALUES("JM","Jamaica");
            INSERT INTO "country" VALUES("JP","Japan");
            INSERT INTO "country" VALUES("JE","Jersey");
            INSERT INTO "country" VALUES("JT","Johnston Island");
            INSERT INTO "country" VALUES("JO","Jordan");
            INSERT INTO "country" VALUES("KZ","Kazakhstan");
            INSERT INTO "country" VALUES("KE","Kenya");
            INSERT INTO "country" VALUES("KI","Kiribati");
            INSERT INTO "country" VALUES("KW","Kuwait");
            INSERT INTO "country" VALUES("KG","Kyrgyzstan");
            INSERT INTO "country" VALUES("LA","Laos");
            INSERT INTO "country" VALUES("LV","Latvia");
            INSERT INTO "country" VALUES("LB","Lebanon");
            INSERT INTO "country" VALUES("LS","Lesotho");
            INSERT INTO "country" VALUES("LR","Liberia");
            INSERT INTO "country" VALUES("LY","Libya");
            INSERT INTO "country" VALUES("LI","Liechtenstein");
            INSERT INTO "country" VALUES("LT","Lithuania");
            INSERT INTO "country" VALUES("LU","Luxembourg");
            INSERT INTO "country" VALUES("MO","Macau SAR China");
            INSERT INTO "country" VALUES("MK","Macedonia");
            INSERT INTO "country" VALUES("MG","Madagascar");
            INSERT INTO "country" VALUES("MW","Malawi");
            INSERT INTO "country" VALUES("MY","Malaysia");
            INSERT INTO "country" VALUES("MV","Maldives");
            INSERT INTO "country" VALUES("ML","Mali");
            INSERT INTO "country" VALUES("MT","Malta");
            INSERT INTO "country" VALUES("MH","Marshall Islands");
            INSERT INTO "country" VALUES("MQ","Martinique");
            INSERT INTO "country" VALUES("MR","Mauritania");
            INSERT INTO "country" VALUES("MU","Mauritius");
            INSERT INTO "country" VALUES("YT","Mayotte");
            INSERT INTO "country" VALUES("FX","Metropolitan France");
            INSERT INTO "country" VALUES("MX","Mexico");
            INSERT INTO "country" VALUES("FM","Micronesia");
            INSERT INTO "country" VALUES("MI","Midway Islands");
            INSERT INTO "country" VALUES("MD","Moldova");
            INSERT INTO "country" VALUES("MC","Monaco");
            INSERT INTO "country" VALUES("MN","Mongolia");
            INSERT INTO "country" VALUES("ME","Montenegro");
            INSERT INTO "country" VALUES("MS","Montserrat");
            INSERT INTO "country" VALUES("MA","Morocco");
            INSERT INTO "country" VALUES("MZ","Mozambique");
            INSERT INTO "country" VALUES("MM","Myanmar (Burma)");
            INSERT INTO "country" VALUES("NA","Namibia");
            INSERT INTO "country" VALUES("NR","Nauru");
            INSERT INTO "country" VALUES("NP","Nepal");
            INSERT INTO "country" VALUES("NL","Netherlands");
            INSERT INTO "country" VALUES("AN","Netherlands Antilles");
            INSERT INTO "country" VALUES("NT","Neutral Zone");
            INSERT INTO "country" VALUES("NC","New Caledonia");
            INSERT INTO "country" VALUES("NZ","New Zealand");
            INSERT INTO "country" VALUES("NI","Nicaragua");
            INSERT INTO "country" VALUES("NE","Niger");
            INSERT INTO "country" VALUES("NG","Nigeria");
            INSERT INTO "country" VALUES("NU","Niue");
            INSERT INTO "country" VALUES("NF","Norfolk Island");
            INSERT INTO "country" VALUES("KP","North Korea");
            INSERT INTO "country" VALUES("VD","North Vietnam");
            INSERT INTO "country" VALUES("MP","Northern Mariana Islands");
            INSERT INTO "country" VALUES("NO","Norway");
            INSERT INTO "country" VALUES("OM","Oman");
            INSERT INTO "country" VALUES("PC","Pacific Islands Trust Territory");
            INSERT INTO "country" VALUES("PK","Pakistan");
            INSERT INTO "country" VALUES("PW","Palau");
            INSERT INTO "country" VALUES("PS","Palestinian Territories");
            INSERT INTO "country" VALUES("PA","Panama");
            INSERT INTO "country" VALUES("PZ","Panama Canal Zone");
            INSERT INTO "country" VALUES("PG","Papua New Guinea");
            INSERT INTO "country" VALUES("PY","Paraguay");
            INSERT INTO "country" VALUES("YD","People`s Democratic Republic of Yemen");
            INSERT INTO "country" VALUES("PE","Peru");
            INSERT INTO "country" VALUES("PH","Philippines");
            INSERT INTO "country" VALUES("PN","Pitcairn Islands");
            INSERT INTO "country" VALUES("PL","Poland");
            INSERT INTO "country" VALUES("PT","Portugal");
            INSERT INTO "country" VALUES("PR","Puerto Rico");
            INSERT INTO "country" VALUES("QA","Qatar");
            INSERT INTO "country" VALUES("RO","Romania");
            INSERT INTO "country" VALUES("RU","Russia");
            INSERT INTO "country" VALUES("RW","Rwanda");
            INSERT INTO "country" VALUES("RE","Réunion");
            INSERT INTO "country" VALUES("BL","Saint Barthélemy");
            INSERT INTO "country" VALUES("SH","Saint Helena");
            INSERT INTO "country" VALUES("KN","Saint Kitts and Nevis");
            INSERT INTO "country" VALUES("LC","Saint Lucia");
            INSERT INTO "country" VALUES("MF","Saint Martin");
            INSERT INTO "country" VALUES("PM","Saint Pierre and Miquelon");
            INSERT INTO "country" VALUES("VC","Saint Vincent and the Grenadines");
            INSERT INTO "country" VALUES("WS","Samoa");
            INSERT INTO "country" VALUES("SM","San Marino");
            INSERT INTO "country" VALUES("SA","Saudi Arabia");
            INSERT INTO "country" VALUES("SN","Senegal");
            INSERT INTO "country" VALUES("RS","Serbia");
            INSERT INTO "country" VALUES("CS","Serbia and Montenegro");
            INSERT INTO "country" VALUES("SC","Seychelles");
            INSERT INTO "country" VALUES("SL","Sierra Leone");
            INSERT INTO "country" VALUES("SG","Singapore");
            INSERT INTO "country" VALUES("SK","Slovakia");
            INSERT INTO "country" VALUES("SI","Slovenia");
            INSERT INTO "country" VALUES("SB","Solomon Islands");
            INSERT INTO "country" VALUES("SO","Somalia");
            INSERT INTO "country" VALUES("ZA","South Africa");
            INSERT INTO "country" VALUES("GS","South Georgia and the South Sandwich Islands");
            INSERT INTO "country" VALUES("KR","South Korea");
            INSERT INTO "country" VALUES("ES","Spain");
            INSERT INTO "country" VALUES("LK","Sri Lanka");
            INSERT INTO "country" VALUES("SD","Sudan");
            INSERT INTO "country" VALUES("SR","Suriname");
            INSERT INTO "country" VALUES("SJ","Svalbard and Jan Mayen");
            INSERT INTO "country" VALUES("SZ","Swaziland");
            INSERT INTO "country" VALUES("SE","Sweden");
            INSERT INTO "country" VALUES("CH","Switzerland");
            INSERT INTO "country" VALUES("SY","Syria");
            INSERT INTO "country" VALUES("ST","São Tomé and Príncipe");
            INSERT INTO "country" VALUES("TW","Taiwan");
            INSERT INTO "country" VALUES("TJ","Tajikistan");
            INSERT INTO "country" VALUES("TZ","Tanzania");
            INSERT INTO "country" VALUES("TH","Thailand");
            INSERT INTO "country" VALUES("TL","Timor-Leste");
            INSERT INTO "country" VALUES("TG","Togo");
            INSERT INTO "country" VALUES("TK","Tokelau");
            INSERT INTO "country" VALUES("TO","Tonga");
            INSERT INTO "country" VALUES("TT","Trinidad and Tobago");
            INSERT INTO "country" VALUES("TN","Tunisia");
            INSERT INTO "country" VALUES("TR","Turkey");
            INSERT INTO "country" VALUES("TM","Turkmenistan");
            INSERT INTO "country" VALUES("TC","Turks and Caicos Islands");
            INSERT INTO "country" VALUES("TV","Tuvalu");
            INSERT INTO "country" VALUES("UM","U.S. Minor Outlying Islands");
            INSERT INTO "country" VALUES("PU","U.S. Miscellaneous Pacific Islands");
            INSERT INTO "country" VALUES("VI","U.S. Virgin Islands");
            INSERT INTO "country" VALUES("UG","Uganda");
            INSERT INTO "country" VALUES("UA","Ukraine");
            INSERT INTO "country" VALUES("SU","Union of Soviet Socialist Republics");
            INSERT INTO "country" VALUES("AE","United Arab Emirates");
            INSERT INTO "country" VALUES("GB","United Kingdom");
            INSERT INTO "country" VALUES("US","United States");
            INSERT INTO "country" VALUES("ZZ","Unknown or Invalid Region");
            INSERT INTO "country" VALUES("UY","Uruguay");
            INSERT INTO "country" VALUES("UZ","Uzbekistan");
            INSERT INTO "country" VALUES("VU","Vanuatu");
            INSERT INTO "country" VALUES("VA","Vatican City");
            INSERT INTO "country" VALUES("VE","Venezuela");
            INSERT INTO "country" VALUES("VN","Vietnam");
            INSERT INTO "country" VALUES("WK","Wake Island");
            INSERT INTO "country" VALUES("WF","Wallis and Futuna");
            INSERT INTO "country" VALUES("EH","Western Sahara");
            INSERT INTO "country" VALUES("YE","Yemen");
            INSERT INTO "country" VALUES("ZM","Zambia");
            INSERT INTO "country" VALUES("ZW","Zimbabwe");
            INSERT INTO "country" VALUES("AX","Åland Islands");
        ');
    }

    protected function addDataGenre()
    {
        $this->addSql('
            INSERT INTO "genre" VALUES(1,"Adventure");
            INSERT INTO "genre" VALUES(2,"Comedy");
            INSERT INTO "genre" VALUES(3,"Fantastic");
            INSERT INTO "genre" VALUES(4,"Drama");
            INSERT INTO "genre" VALUES(5,"Action");
            INSERT INTO "genre" VALUES(6,"Martial arts");
            INSERT INTO "genre" VALUES(7,"War");
            INSERT INTO "genre" VALUES(8,"Detective");
            INSERT INTO "genre" VALUES(9,"For children");
            INSERT INTO "genre" VALUES(10,"History");
            INSERT INTO "genre" VALUES(11,"Mahoe shoujo");
            INSERT INTO "genre" VALUES(12,"Meho");
            INSERT INTO "genre" VALUES(13,"Mysticism");
            INSERT INTO "genre" VALUES(14,"Musical");
            INSERT INTO "genre" VALUES(15,"Educational");
            INSERT INTO "genre" VALUES(16,"Parody");
            INSERT INTO "genre" VALUES(17,"Everyday");
            INSERT INTO "genre" VALUES(18,"Police");
            INSERT INTO "genre" VALUES(19,"Romance");
            INSERT INTO "genre" VALUES(20,"Samurai action");
            INSERT INTO "genre" VALUES(21,"Shoujo");
            INSERT INTO "genre" VALUES(22,"Shoujo-ai");
            INSERT INTO "genre" VALUES(23,"Senen");
            INSERT INTO "genre" VALUES(24,"Senen-ai");
            INSERT INTO "genre" VALUES(47,"Fable");
            INSERT INTO "genre" VALUES(48,"Sport");
            INSERT INTO "genre" VALUES(49,"Thriller");
            INSERT INTO "genre" VALUES(50,"School");
            INSERT INTO "genre" VALUES(51,"Fantasy");
            INSERT INTO "genre" VALUES(52,"Erotica");
            INSERT INTO "genre" VALUES(53,"Ettie");
            INSERT INTO "genre" VALUES(54,"Horror");
            INSERT INTO "genre" VALUES(55,"Hentai");
            INSERT INTO "genre" VALUES(56,"Urey");
            INSERT INTO "genre" VALUES(57,"Yaoi");
            INSERT INTO "genre" VALUES(58,"Psychology");
            INSERT INTO "genre" VALUES(59,"Apocalyptic fiction");
            INSERT INTO "genre" VALUES(60,"Steampunk");
            INSERT INTO "genre" VALUES(61,"Mystery play");
            INSERT INTO "genre" VALUES(62,"Josei");
            INSERT INTO "genre" VALUES(63,"Vampires");
            INSERT INTO "genre" VALUES(64,"Cyberpunk");
        ');
    }

    protected function addDataExtTranslations()
    {
        $this->addSql('
            INSERT INTO "ext_translations" VALUES(1,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Type","name","feature","Полнометражный фильм");
            INSERT INTO "ext_translations" VALUES(2,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Type","name","featurette","Короткометражный фильм");
            INSERT INTO "ext_translations" VALUES(3,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Type","name","ona","ONA");
            INSERT INTO "ext_translations" VALUES(4,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Type","name","ova","OVA");
            INSERT INTO "ext_translations" VALUES(5,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Type","name","tv","ТВ");
            INSERT INTO "ext_translations" VALUES(6,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Type","name","special","ТВ спецвыпуск");
            INSERT INTO "ext_translations" VALUES(7,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Type","name","music","Музыкальное видео");
            INSERT INTO "ext_translations" VALUES(8,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Type","name","commercial","Рекламный ролик");
            INSERT INTO "ext_translations" VALUES(9,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","1","Приключения");
            INSERT INTO "ext_translations" VALUES(10,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","2","Комедия");
            INSERT INTO "ext_translations" VALUES(11,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","3","Фантастика");
            INSERT INTO "ext_translations" VALUES(12,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","4","Драма");
            INSERT INTO "ext_translations" VALUES(13,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","5","Боевик");
            INSERT INTO "ext_translations" VALUES(14,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","6","Боевые искусства");
            INSERT INTO "ext_translations" VALUES(15,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","7","Война");
            INSERT INTO "ext_translations" VALUES(16,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","8","Детектив");
            INSERT INTO "ext_translations" VALUES(17,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","9","Для детей");
            INSERT INTO "ext_translations" VALUES(18,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","10","История");
            INSERT INTO "ext_translations" VALUES(19,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","11","Махо-сёдзё");
            INSERT INTO "ext_translations" VALUES(20,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","12","Меха");
            INSERT INTO "ext_translations" VALUES(21,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","13","Мистика");
            INSERT INTO "ext_translations" VALUES(22,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","14","Музыкальный");
            INSERT INTO "ext_translations" VALUES(23,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","15","Образовательный");
            INSERT INTO "ext_translations" VALUES(24,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","16","Пародия");
            INSERT INTO "ext_translations" VALUES(25,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","17","Повседневность");
            INSERT INTO "ext_translations" VALUES(26,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","18","Полиция");
            INSERT INTO "ext_translations" VALUES(27,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","19","Романтика");
            INSERT INTO "ext_translations" VALUES(28,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","20","Самурайский боевик");
            INSERT INTO "ext_translations" VALUES(29,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","21","Сёдзё");
            INSERT INTO "ext_translations" VALUES(30,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","22","Сёдзё-ай");
            INSERT INTO "ext_translations" VALUES(31,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","23","Сёнэн");
            INSERT INTO "ext_translations" VALUES(32,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","24","Сёнэн-ай");
            INSERT INTO "ext_translations" VALUES(33,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","47","Сказка");
            INSERT INTO "ext_translations" VALUES(34,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","48","Спорт");
            INSERT INTO "ext_translations" VALUES(35,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","49","Триллер");
            INSERT INTO "ext_translations" VALUES(36,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","50","Школа");
            INSERT INTO "ext_translations" VALUES(37,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","51","Фэнтези");
            INSERT INTO "ext_translations" VALUES(38,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","52","Эротика");
            INSERT INTO "ext_translations" VALUES(39,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","53","Этти");
            INSERT INTO "ext_translations" VALUES(40,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","54","Ужасы");
            INSERT INTO "ext_translations" VALUES(41,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","55","Хентай");
            INSERT INTO "ext_translations" VALUES(42,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","56","Юри");
            INSERT INTO "ext_translations" VALUES(43,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","57","Яой");
            INSERT INTO "ext_translations" VALUES(44,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","58","Психология");
            INSERT INTO "ext_translations" VALUES(45,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","59","Постапокалиптика");
            INSERT INTO "ext_translations" VALUES(46,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","60","Стимпанк");
            INSERT INTO "ext_translations" VALUES(47,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","61","Мистерия");
            INSERT INTO "ext_translations" VALUES(48,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","62","Дзёсэй");
            INSERT INTO "ext_translations" VALUES(49,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","63","Вампиры");
            INSERT INTO "ext_translations" VALUES(50,"ru","AnimeDB\Bundle\CatalogBundle\Entity\Genre","name","64","Киберпанк");
        ');
    }

    protected function addDataCountryTranslation()
    {
        $this->addSql('
            INSERT INTO "country_translation" VALUES(1,"AF","en","name","Afghanistan");
            INSERT INTO "country_translation" VALUES(2,"AF","ru","name","Афганистан");
            INSERT INTO "country_translation" VALUES(3,"AL","en","name","Albania");
            INSERT INTO "country_translation" VALUES(4,"AL","ru","name","Албания");
            INSERT INTO "country_translation" VALUES(5,"DZ","en","name","Algeria");
            INSERT INTO "country_translation" VALUES(6,"DZ","ru","name","Алжир");
            INSERT INTO "country_translation" VALUES(7,"AS","en","name","American Samoa");
            INSERT INTO "country_translation" VALUES(8,"AS","ru","name","Американское Самоа");
            INSERT INTO "country_translation" VALUES(9,"AD","en","name","Andorra");
            INSERT INTO "country_translation" VALUES(10,"AD","ru","name","Андорра");
            INSERT INTO "country_translation" VALUES(11,"AO","en","name","Angola");
            INSERT INTO "country_translation" VALUES(12,"AO","ru","name","Ангола");
            INSERT INTO "country_translation" VALUES(13,"AI","en","name","Anguilla");
            INSERT INTO "country_translation" VALUES(14,"AI","ru","name","Ангуилла");
            INSERT INTO "country_translation" VALUES(15,"AQ","en","name","Antarctica");
            INSERT INTO "country_translation" VALUES(16,"AQ","ru","name","Антарктика");
            INSERT INTO "country_translation" VALUES(17,"AG","en","name","Antigua and Barbuda");
            INSERT INTO "country_translation" VALUES(18,"AG","ru","name","Антигуа и Барбуда");
            INSERT INTO "country_translation" VALUES(19,"AR","en","name","Argentina");
            INSERT INTO "country_translation" VALUES(20,"AR","ru","name","Аргентина");
            INSERT INTO "country_translation" VALUES(21,"AM","en","name","Armenia");
            INSERT INTO "country_translation" VALUES(22,"AM","ru","name","Армения");
            INSERT INTO "country_translation" VALUES(23,"AW","en","name","Aruba");
            INSERT INTO "country_translation" VALUES(24,"AW","ru","name","Аруба");
            INSERT INTO "country_translation" VALUES(25,"AU","en","name","Australia");
            INSERT INTO "country_translation" VALUES(26,"AU","ru","name","Австралия");
            INSERT INTO "country_translation" VALUES(27,"AT","en","name","Austria");
            INSERT INTO "country_translation" VALUES(28,"AT","ru","name","Австрия");
            INSERT INTO "country_translation" VALUES(29,"AZ","en","name","Azerbaijan");
            INSERT INTO "country_translation" VALUES(30,"AZ","ru","name","Азербайджан");
            INSERT INTO "country_translation" VALUES(31,"BS","en","name","Bahamas");
            INSERT INTO "country_translation" VALUES(32,"BS","ru","name","Багамские острова");
            INSERT INTO "country_translation" VALUES(33,"BH","en","name","Bahrain");
            INSERT INTO "country_translation" VALUES(34,"BH","ru","name","Бахрейн");
            INSERT INTO "country_translation" VALUES(35,"BD","en","name","Bangladesh");
            INSERT INTO "country_translation" VALUES(36,"BD","ru","name","Бангладеш");
            INSERT INTO "country_translation" VALUES(37,"BB","en","name","Barbados");
            INSERT INTO "country_translation" VALUES(38,"BB","ru","name","Барбадос");
            INSERT INTO "country_translation" VALUES(39,"BY","en","name","Belarus");
            INSERT INTO "country_translation" VALUES(40,"BY","ru","name","Беларусь");
            INSERT INTO "country_translation" VALUES(41,"BE","en","name","Belgium");
            INSERT INTO "country_translation" VALUES(42,"BE","ru","name","Бельгия");
            INSERT INTO "country_translation" VALUES(43,"BZ","en","name","Belize");
            INSERT INTO "country_translation" VALUES(44,"BZ","ru","name","Белиз");
            INSERT INTO "country_translation" VALUES(45,"BJ","en","name","Benin");
            INSERT INTO "country_translation" VALUES(46,"BJ","ru","name","Бенин");
            INSERT INTO "country_translation" VALUES(47,"BM","en","name","Bermuda");
            INSERT INTO "country_translation" VALUES(48,"BM","ru","name","Бермудские Острова");
            INSERT INTO "country_translation" VALUES(49,"BT","en","name","Bhutan");
            INSERT INTO "country_translation" VALUES(50,"BT","ru","name","Бутан");
            INSERT INTO "country_translation" VALUES(51,"BO","en","name","Bolivia");
            INSERT INTO "country_translation" VALUES(52,"BO","ru","name","Боливия");
            INSERT INTO "country_translation" VALUES(53,"BA","en","name","Bosnia and Herzegovina");
            INSERT INTO "country_translation" VALUES(54,"BA","ru","name","Босния и Герцеговина");
            INSERT INTO "country_translation" VALUES(55,"BW","en","name","Botswana");
            INSERT INTO "country_translation" VALUES(56,"BW","ru","name","Ботсвана");
            INSERT INTO "country_translation" VALUES(57,"BV","en","name","Bouvet Island");
            INSERT INTO "country_translation" VALUES(58,"BV","ru","name","Остров Буве");
            INSERT INTO "country_translation" VALUES(59,"BR","en","name","Brazil");
            INSERT INTO "country_translation" VALUES(60,"BR","ru","name","Бразилия");
            INSERT INTO "country_translation" VALUES(61,"BQ","en","name","British Antarctic Territory");
            INSERT INTO "country_translation" VALUES(62,"BQ","ru","name","Британская антарктическая территория");
            INSERT INTO "country_translation" VALUES(63,"IO","en","name","British Indian Ocean Territory");
            INSERT INTO "country_translation" VALUES(64,"IO","ru","name","Британская территория в Индийском океане");
            INSERT INTO "country_translation" VALUES(65,"VG","en","name","British Virgin Islands");
            INSERT INTO "country_translation" VALUES(66,"VG","ru","name","Британские Виргинские Острова");
            INSERT INTO "country_translation" VALUES(67,"BN","en","name","Brunei");
            INSERT INTO "country_translation" VALUES(68,"BN","ru","name","Бруней Даруссалам");
            INSERT INTO "country_translation" VALUES(69,"BG","en","name","Bulgaria");
            INSERT INTO "country_translation" VALUES(70,"BG","ru","name","Болгария");
            INSERT INTO "country_translation" VALUES(71,"BF","en","name","Burkina Faso");
            INSERT INTO "country_translation" VALUES(72,"BF","ru","name","Буркина Фасо");
            INSERT INTO "country_translation" VALUES(73,"BI","en","name","Burundi");
            INSERT INTO "country_translation" VALUES(74,"BI","ru","name","Бурунди");
            INSERT INTO "country_translation" VALUES(75,"KH","en","name","Cambodia");
            INSERT INTO "country_translation" VALUES(76,"KH","ru","name","Камбоджа");
            INSERT INTO "country_translation" VALUES(77,"CM","en","name","Cameroon");
            INSERT INTO "country_translation" VALUES(78,"CM","ru","name","Камерун");
            INSERT INTO "country_translation" VALUES(79,"CA","en","name","Canada");
            INSERT INTO "country_translation" VALUES(80,"CA","ru","name","Канада");
            INSERT INTO "country_translation" VALUES(81,"CT","en","name","Canton and Enderbury Islands");
            INSERT INTO "country_translation" VALUES(82,"CT","ru","name","Кантон и Эндербери");
            INSERT INTO "country_translation" VALUES(83,"CV","en","name","Cape Verde");
            INSERT INTO "country_translation" VALUES(84,"CV","ru","name","Острова Зеленого Мыса");
            INSERT INTO "country_translation" VALUES(85,"KY","en","name","Cayman Islands");
            INSERT INTO "country_translation" VALUES(86,"KY","ru","name","Каймановы острова");
            INSERT INTO "country_translation" VALUES(87,"CF","en","name","Central African Republic");
            INSERT INTO "country_translation" VALUES(88,"CF","ru","name","Центрально-Африканская Республика");
            INSERT INTO "country_translation" VALUES(89,"TD","en","name","Chad");
            INSERT INTO "country_translation" VALUES(90,"TD","ru","name","Чад");
            INSERT INTO "country_translation" VALUES(91,"CL","en","name","Chile");
            INSERT INTO "country_translation" VALUES(92,"CL","ru","name","Чили");
            INSERT INTO "country_translation" VALUES(93,"CN","en","name","China");
            INSERT INTO "country_translation" VALUES(94,"CN","ru","name","Китай");
            INSERT INTO "country_translation" VALUES(95,"CX","en","name","Christmas Island");
            INSERT INTO "country_translation" VALUES(96,"CX","ru","name","Остров Рождества");
            INSERT INTO "country_translation" VALUES(97,"CC","en","name","Cocos (Keeling) Islands");
            INSERT INTO "country_translation" VALUES(98,"CC","ru","name","Кокосовые острова");
            INSERT INTO "country_translation" VALUES(99,"CO","en","name","Colombia");
            INSERT INTO "country_translation" VALUES(100,"CO","ru","name","Колумбия");
            INSERT INTO "country_translation" VALUES(101,"KM","en","name","Comoros");
            INSERT INTO "country_translation" VALUES(102,"KM","ru","name","Коморские Острова");
            INSERT INTO "country_translation" VALUES(103,"CG","en","name","Congo - Brazzaville");
            INSERT INTO "country_translation" VALUES(104,"CG","ru","name","Конго");
            INSERT INTO "country_translation" VALUES(105,"CD","en","name","Congo - Kinshasa");
            INSERT INTO "country_translation" VALUES(106,"CD","ru","name","Демократическая Республика Конго");
            INSERT INTO "country_translation" VALUES(107,"CK","en","name","Cook Islands");
            INSERT INTO "country_translation" VALUES(108,"CK","ru","name","Острова Кука");
            INSERT INTO "country_translation" VALUES(109,"CR","en","name","Costa Rica");
            INSERT INTO "country_translation" VALUES(110,"CR","ru","name","Коста-Рика");
            INSERT INTO "country_translation" VALUES(111,"HR","en","name","Croatia");
            INSERT INTO "country_translation" VALUES(112,"HR","ru","name","Хорватия");
            INSERT INTO "country_translation" VALUES(113,"CU","en","name","Cuba");
            INSERT INTO "country_translation" VALUES(114,"CU","ru","name","Куба");
            INSERT INTO "country_translation" VALUES(115,"CY","en","name","Cyprus");
            INSERT INTO "country_translation" VALUES(116,"CY","ru","name","Кипр");
            INSERT INTO "country_translation" VALUES(117,"CZ","en","name","Czech Republic");
            INSERT INTO "country_translation" VALUES(118,"CZ","ru","name","Чешская республика");
            INSERT INTO "country_translation" VALUES(119,"CI","en","name","Côte d’Ivoire");
            INSERT INTO "country_translation" VALUES(120,"CI","ru","name","Кот д’Ивуар");
            INSERT INTO "country_translation" VALUES(121,"DK","en","name","Denmark");
            INSERT INTO "country_translation" VALUES(122,"DK","ru","name","Дания");
            INSERT INTO "country_translation" VALUES(123,"DJ","en","name","Djibouti");
            INSERT INTO "country_translation" VALUES(124,"DJ","ru","name","Джибути");
            INSERT INTO "country_translation" VALUES(125,"DM","en","name","Dominica");
            INSERT INTO "country_translation" VALUES(126,"DM","ru","name","Остров Доминика");
            INSERT INTO "country_translation" VALUES(127,"DO","en","name","Dominican Republic");
            INSERT INTO "country_translation" VALUES(128,"DO","ru","name","Доминиканская Республика");
            INSERT INTO "country_translation" VALUES(129,"NQ","en","name","Dronning Maud Land");
            INSERT INTO "country_translation" VALUES(130,"NQ","ru","name","Земля Королевы Мод");
            INSERT INTO "country_translation" VALUES(131,"DD","en","name","East Germany");
            INSERT INTO "country_translation" VALUES(132,"DD","ru","name","Германская Демократическая Республика");
            INSERT INTO "country_translation" VALUES(133,"EC","en","name","Ecuador");
            INSERT INTO "country_translation" VALUES(134,"EC","ru","name","Эквадор");
            INSERT INTO "country_translation" VALUES(135,"EG","en","name","Egypt");
            INSERT INTO "country_translation" VALUES(136,"EG","ru","name","Египет");
            INSERT INTO "country_translation" VALUES(137,"SV","en","name","El Salvador");
            INSERT INTO "country_translation" VALUES(138,"SV","ru","name","Сальвадор");
            INSERT INTO "country_translation" VALUES(139,"GQ","en","name","Equatorial Guinea");
            INSERT INTO "country_translation" VALUES(140,"GQ","ru","name","Экваториальная Гвинея");
            INSERT INTO "country_translation" VALUES(141,"ER","en","name","Eritrea");
            INSERT INTO "country_translation" VALUES(142,"ER","ru","name","Эритрея");
            INSERT INTO "country_translation" VALUES(143,"EE","en","name","Estonia");
            INSERT INTO "country_translation" VALUES(144,"EE","ru","name","Эстония");
            INSERT INTO "country_translation" VALUES(145,"ET","en","name","Ethiopia");
            INSERT INTO "country_translation" VALUES(146,"ET","ru","name","Эфиопия");
            INSERT INTO "country_translation" VALUES(147,"FK","en","name","Falkland Islands");
            INSERT INTO "country_translation" VALUES(148,"FK","ru","name","Фолклендские острова");
            INSERT INTO "country_translation" VALUES(149,"FO","en","name","Faroe Islands");
            INSERT INTO "country_translation" VALUES(150,"FO","ru","name","Фарерские острова");
            INSERT INTO "country_translation" VALUES(151,"FJ","en","name","Fiji");
            INSERT INTO "country_translation" VALUES(152,"FJ","ru","name","Фиджи");
            INSERT INTO "country_translation" VALUES(153,"FI","en","name","Finland");
            INSERT INTO "country_translation" VALUES(154,"FI","ru","name","Финляндия");
            INSERT INTO "country_translation" VALUES(155,"FR","en","name","France");
            INSERT INTO "country_translation" VALUES(156,"FR","ru","name","Франция");
            INSERT INTO "country_translation" VALUES(157,"GF","en","name","French Guiana");
            INSERT INTO "country_translation" VALUES(158,"GF","ru","name","Французская Гвиана");
            INSERT INTO "country_translation" VALUES(159,"PF","en","name","French Polynesia");
            INSERT INTO "country_translation" VALUES(160,"PF","ru","name","Французская Полинезия");
            INSERT INTO "country_translation" VALUES(161,"TF","en","name","French Southern Territories");
            INSERT INTO "country_translation" VALUES(162,"TF","ru","name","Французские Южные Территории");
            INSERT INTO "country_translation" VALUES(163,"FQ","en","name","French Southern and Antarctic Territories");
            INSERT INTO "country_translation" VALUES(164,"FQ","ru","name","Французские Южные и Антарктические территории");
            INSERT INTO "country_translation" VALUES(165,"GA","en","name","Gabon");
            INSERT INTO "country_translation" VALUES(166,"GA","ru","name","Габон");
            INSERT INTO "country_translation" VALUES(167,"GM","en","name","Gambia");
            INSERT INTO "country_translation" VALUES(168,"GM","ru","name","Гамбия");
            INSERT INTO "country_translation" VALUES(169,"GE","en","name","Georgia");
            INSERT INTO "country_translation" VALUES(170,"GE","ru","name","Грузия");
            INSERT INTO "country_translation" VALUES(171,"DE","en","name","Germany");
            INSERT INTO "country_translation" VALUES(172,"DE","ru","name","Германия");
            INSERT INTO "country_translation" VALUES(173,"GH","en","name","Ghana");
            INSERT INTO "country_translation" VALUES(174,"GH","ru","name","Гана");
            INSERT INTO "country_translation" VALUES(175,"GI","en","name","Gibraltar");
            INSERT INTO "country_translation" VALUES(176,"GI","ru","name","Гибралтар");
            INSERT INTO "country_translation" VALUES(177,"GR","en","name","Greece");
            INSERT INTO "country_translation" VALUES(178,"GR","ru","name","Греция");
            INSERT INTO "country_translation" VALUES(179,"GL","en","name","Greenland");
            INSERT INTO "country_translation" VALUES(180,"GL","ru","name","Гренландия");
            INSERT INTO "country_translation" VALUES(181,"GD","en","name","Grenada");
            INSERT INTO "country_translation" VALUES(182,"GD","ru","name","Гренада");
            INSERT INTO "country_translation" VALUES(183,"GP","en","name","Guadeloupe");
            INSERT INTO "country_translation" VALUES(184,"GP","ru","name","Гваделупа");
            INSERT INTO "country_translation" VALUES(185,"GU","en","name","Guam");
            INSERT INTO "country_translation" VALUES(186,"GU","ru","name","Гуам");
            INSERT INTO "country_translation" VALUES(187,"GT","en","name","Guatemala");
            INSERT INTO "country_translation" VALUES(188,"GT","ru","name","Гватемала");
            INSERT INTO "country_translation" VALUES(189,"GG","en","name","Guernsey");
            INSERT INTO "country_translation" VALUES(190,"GG","ru","name","Гернси");
            INSERT INTO "country_translation" VALUES(191,"GN","en","name","Guinea");
            INSERT INTO "country_translation" VALUES(192,"GN","ru","name","Гвинея");
            INSERT INTO "country_translation" VALUES(193,"GW","en","name","Guinea-Bissau");
            INSERT INTO "country_translation" VALUES(194,"GW","ru","name","Гвинея-Биссау");
            INSERT INTO "country_translation" VALUES(195,"GY","en","name","Guyana");
            INSERT INTO "country_translation" VALUES(196,"GY","ru","name","Гайана");
            INSERT INTO "country_translation" VALUES(197,"HT","en","name","Haiti");
            INSERT INTO "country_translation" VALUES(198,"HT","ru","name","Гаити");
            INSERT INTO "country_translation" VALUES(199,"HM","en","name","Heard Island and McDonald Islands");
            INSERT INTO "country_translation" VALUES(200,"HM","ru","name","Острова Херд и Макдональд");
            INSERT INTO "country_translation" VALUES(201,"HN","en","name","Honduras");
            INSERT INTO "country_translation" VALUES(202,"HN","ru","name","Гондурас");
            INSERT INTO "country_translation" VALUES(203,"HK","en","name","Hong Kong SAR China");
            INSERT INTO "country_translation" VALUES(204,"HK","ru","name","Гонконг, Особый Административный Район Китая");
            INSERT INTO "country_translation" VALUES(205,"HU","en","name","Hungary");
            INSERT INTO "country_translation" VALUES(206,"HU","ru","name","Венгрия");
            INSERT INTO "country_translation" VALUES(207,"IS","en","name","Iceland");
            INSERT INTO "country_translation" VALUES(208,"IS","ru","name","Исландия");
            INSERT INTO "country_translation" VALUES(209,"IN","en","name","India");
            INSERT INTO "country_translation" VALUES(210,"IN","ru","name","Индия");
            INSERT INTO "country_translation" VALUES(211,"ID","en","name","Indonesia");
            INSERT INTO "country_translation" VALUES(212,"ID","ru","name","Индонезия");
            INSERT INTO "country_translation" VALUES(213,"IR","en","name","Iran");
            INSERT INTO "country_translation" VALUES(214,"IR","ru","name","Иран");
            INSERT INTO "country_translation" VALUES(215,"IQ","en","name","Iraq");
            INSERT INTO "country_translation" VALUES(216,"IQ","ru","name","Ирак");
            INSERT INTO "country_translation" VALUES(217,"IE","en","name","Ireland");
            INSERT INTO "country_translation" VALUES(218,"IE","ru","name","Ирландия");
            INSERT INTO "country_translation" VALUES(219,"IM","en","name","Isle of Man");
            INSERT INTO "country_translation" VALUES(220,"IM","ru","name","Остров Мэн");
            INSERT INTO "country_translation" VALUES(221,"IL","en","name","Israel");
            INSERT INTO "country_translation" VALUES(222,"IL","ru","name","Израиль");
            INSERT INTO "country_translation" VALUES(223,"IT","en","name","Italy");
            INSERT INTO "country_translation" VALUES(224,"IT","ru","name","Италия");
            INSERT INTO "country_translation" VALUES(225,"JM","en","name","Jamaica");
            INSERT INTO "country_translation" VALUES(226,"JM","ru","name","Ямайка");
            INSERT INTO "country_translation" VALUES(227,"JP","en","name","Japan");
            INSERT INTO "country_translation" VALUES(228,"JP","ru","name","Япония");
            INSERT INTO "country_translation" VALUES(229,"JE","en","name","Jersey");
            INSERT INTO "country_translation" VALUES(230,"JE","ru","name","Джерси");
            INSERT INTO "country_translation" VALUES(231,"JT","en","name","Johnston Island");
            INSERT INTO "country_translation" VALUES(232,"JT","ru","name","Джонстон");
            INSERT INTO "country_translation" VALUES(233,"JO","en","name","Jordan");
            INSERT INTO "country_translation" VALUES(234,"JO","ru","name","Иордания");
            INSERT INTO "country_translation" VALUES(235,"KZ","en","name","Kazakhstan");
            INSERT INTO "country_translation" VALUES(236,"KZ","ru","name","Казахстан");
            INSERT INTO "country_translation" VALUES(237,"KE","en","name","Kenya");
            INSERT INTO "country_translation" VALUES(238,"KE","ru","name","Кения");
            INSERT INTO "country_translation" VALUES(239,"KI","en","name","Kiribati");
            INSERT INTO "country_translation" VALUES(240,"KI","ru","name","Кирибати");
            INSERT INTO "country_translation" VALUES(241,"KW","en","name","Kuwait");
            INSERT INTO "country_translation" VALUES(242,"KW","ru","name","Кувейт");
            INSERT INTO "country_translation" VALUES(243,"KG","en","name","Kyrgyzstan");
            INSERT INTO "country_translation" VALUES(244,"KG","ru","name","Кыргызстан");
            INSERT INTO "country_translation" VALUES(245,"LA","en","name","Laos");
            INSERT INTO "country_translation" VALUES(246,"LA","ru","name","Лаос");
            INSERT INTO "country_translation" VALUES(247,"LV","en","name","Latvia");
            INSERT INTO "country_translation" VALUES(248,"LV","ru","name","Латвия");
            INSERT INTO "country_translation" VALUES(249,"LB","en","name","Lebanon");
            INSERT INTO "country_translation" VALUES(250,"LB","ru","name","Ливан");
            INSERT INTO "country_translation" VALUES(251,"LS","en","name","Lesotho");
            INSERT INTO "country_translation" VALUES(252,"LS","ru","name","Лесото");
            INSERT INTO "country_translation" VALUES(253,"LR","en","name","Liberia");
            INSERT INTO "country_translation" VALUES(254,"LR","ru","name","Либерия");
            INSERT INTO "country_translation" VALUES(255,"LY","en","name","Libya");
            INSERT INTO "country_translation" VALUES(256,"LY","ru","name","Ливия");
            INSERT INTO "country_translation" VALUES(257,"LI","en","name","Liechtenstein");
            INSERT INTO "country_translation" VALUES(258,"LI","ru","name","Лихтенштейн");
            INSERT INTO "country_translation" VALUES(259,"LT","en","name","Lithuania");
            INSERT INTO "country_translation" VALUES(260,"LT","ru","name","Литва");
            INSERT INTO "country_translation" VALUES(261,"LU","en","name","Luxembourg");
            INSERT INTO "country_translation" VALUES(262,"LU","ru","name","Люксембург");
            INSERT INTO "country_translation" VALUES(263,"MO","en","name","Macau SAR China");
            INSERT INTO "country_translation" VALUES(264,"MO","ru","name","Макао (особый административный район КНР)");
            INSERT INTO "country_translation" VALUES(265,"MK","en","name","Macedonia");
            INSERT INTO "country_translation" VALUES(266,"MK","ru","name","Македония");
            INSERT INTO "country_translation" VALUES(267,"MG","en","name","Madagascar");
            INSERT INTO "country_translation" VALUES(268,"MG","ru","name","Мадагаскар");
            INSERT INTO "country_translation" VALUES(269,"MW","en","name","Malawi");
            INSERT INTO "country_translation" VALUES(270,"MW","ru","name","Малави");
            INSERT INTO "country_translation" VALUES(271,"MY","en","name","Malaysia");
            INSERT INTO "country_translation" VALUES(272,"MY","ru","name","Малайзия");
            INSERT INTO "country_translation" VALUES(273,"MV","en","name","Maldives");
            INSERT INTO "country_translation" VALUES(274,"MV","ru","name","Мальдивы");
            INSERT INTO "country_translation" VALUES(275,"ML","en","name","Mali");
            INSERT INTO "country_translation" VALUES(276,"ML","ru","name","Мали");
            INSERT INTO "country_translation" VALUES(277,"MT","en","name","Malta");
            INSERT INTO "country_translation" VALUES(278,"MT","ru","name","Мальта");
            INSERT INTO "country_translation" VALUES(279,"MH","en","name","Marshall Islands");
            INSERT INTO "country_translation" VALUES(280,"MH","ru","name","Маршалловы Острова");
            INSERT INTO "country_translation" VALUES(281,"MQ","en","name","Martinique");
            INSERT INTO "country_translation" VALUES(282,"MQ","ru","name","Мартиник");
            INSERT INTO "country_translation" VALUES(283,"MR","en","name","Mauritania");
            INSERT INTO "country_translation" VALUES(284,"MR","ru","name","Мавритания");
            INSERT INTO "country_translation" VALUES(285,"MU","en","name","Mauritius");
            INSERT INTO "country_translation" VALUES(286,"MU","ru","name","Маврикий");
            INSERT INTO "country_translation" VALUES(287,"YT","en","name","Mayotte");
            INSERT INTO "country_translation" VALUES(288,"YT","ru","name","Майотта");
            INSERT INTO "country_translation" VALUES(289,"FX","en","name","Metropolitan France");
            INSERT INTO "country_translation" VALUES(290,"FX","ru","name","Метрополия Франции");
            INSERT INTO "country_translation" VALUES(291,"MX","en","name","Mexico");
            INSERT INTO "country_translation" VALUES(292,"MX","ru","name","Мексика");
            INSERT INTO "country_translation" VALUES(293,"FM","en","name","Micronesia");
            INSERT INTO "country_translation" VALUES(294,"FM","ru","name","Федеративные Штаты Микронезии");
            INSERT INTO "country_translation" VALUES(295,"MI","en","name","Midway Islands");
            INSERT INTO "country_translation" VALUES(296,"MI","ru","name","Мидуэй");
            INSERT INTO "country_translation" VALUES(297,"MD","en","name","Moldova");
            INSERT INTO "country_translation" VALUES(298,"MD","ru","name","Молдова");
            INSERT INTO "country_translation" VALUES(299,"MC","en","name","Monaco");
            INSERT INTO "country_translation" VALUES(300,"MC","ru","name","Монако");
            INSERT INTO "country_translation" VALUES(301,"MN","en","name","Mongolia");
            INSERT INTO "country_translation" VALUES(302,"MN","ru","name","Монголия");
            INSERT INTO "country_translation" VALUES(303,"ME","en","name","Montenegro");
            INSERT INTO "country_translation" VALUES(304,"ME","ru","name","Черногория");
            INSERT INTO "country_translation" VALUES(305,"MS","en","name","Montserrat");
            INSERT INTO "country_translation" VALUES(306,"MS","ru","name","Монсеррат");
            INSERT INTO "country_translation" VALUES(307,"MA","en","name","Morocco");
            INSERT INTO "country_translation" VALUES(308,"MA","ru","name","Марокко");
            INSERT INTO "country_translation" VALUES(309,"MZ","en","name","Mozambique");
            INSERT INTO "country_translation" VALUES(310,"MZ","ru","name","Мозамбик");
            INSERT INTO "country_translation" VALUES(311,"MM","en","name","Myanmar (Burma)");
            INSERT INTO "country_translation" VALUES(312,"MM","ru","name","Мьянма");
            INSERT INTO "country_translation" VALUES(313,"NA","en","name","Namibia");
            INSERT INTO "country_translation" VALUES(314,"NA","ru","name","Намибия");
            INSERT INTO "country_translation" VALUES(315,"NR","en","name","Nauru");
            INSERT INTO "country_translation" VALUES(316,"NR","ru","name","Науру");
            INSERT INTO "country_translation" VALUES(317,"NP","en","name","Nepal");
            INSERT INTO "country_translation" VALUES(318,"NP","ru","name","Непал");
            INSERT INTO "country_translation" VALUES(319,"NL","en","name","Netherlands");
            INSERT INTO "country_translation" VALUES(320,"NL","ru","name","Нидерланды");
            INSERT INTO "country_translation" VALUES(321,"AN","en","name","Netherlands Antilles");
            INSERT INTO "country_translation" VALUES(322,"AN","ru","name","Нидерландские Антильские острова");
            INSERT INTO "country_translation" VALUES(323,"NT","en","name","Neutral Zone");
            INSERT INTO "country_translation" VALUES(324,"NT","ru","name","Нейтральная зона (саудовско-иракская)");
            INSERT INTO "country_translation" VALUES(325,"NC","en","name","New Caledonia");
            INSERT INTO "country_translation" VALUES(326,"NC","ru","name","Новая Каледония");
            INSERT INTO "country_translation" VALUES(327,"NZ","en","name","New Zealand");
            INSERT INTO "country_translation" VALUES(328,"NZ","ru","name","Новая Зеландия");
            INSERT INTO "country_translation" VALUES(329,"NI","en","name","Nicaragua");
            INSERT INTO "country_translation" VALUES(330,"NI","ru","name","Никарагуа");
            INSERT INTO "country_translation" VALUES(331,"NE","en","name","Niger");
            INSERT INTO "country_translation" VALUES(332,"NE","ru","name","Нигер");
            INSERT INTO "country_translation" VALUES(333,"NG","en","name","Nigeria");
            INSERT INTO "country_translation" VALUES(334,"NG","ru","name","Нигерия");
            INSERT INTO "country_translation" VALUES(335,"NU","en","name","Niue");
            INSERT INTO "country_translation" VALUES(336,"NU","ru","name","Ниуе");
            INSERT INTO "country_translation" VALUES(337,"NF","en","name","Norfolk Island");
            INSERT INTO "country_translation" VALUES(338,"NF","ru","name","Остров Норфолк");
            INSERT INTO "country_translation" VALUES(339,"KP","en","name","North Korea");
            INSERT INTO "country_translation" VALUES(340,"KP","ru","name","Корейская Народно-Демократическая Республика");
            INSERT INTO "country_translation" VALUES(341,"VD","en","name","North Vietnam");
            INSERT INTO "country_translation" VALUES(342,"VD","ru","name","Демократическая Республика Вьетнам");
            INSERT INTO "country_translation" VALUES(343,"MP","en","name","Northern Mariana Islands");
            INSERT INTO "country_translation" VALUES(344,"MP","ru","name","Северные Марианские Острова");
            INSERT INTO "country_translation" VALUES(345,"NO","en","name","Norway");
            INSERT INTO "country_translation" VALUES(346,"NO","ru","name","Норвегия");
            INSERT INTO "country_translation" VALUES(347,"OM","en","name","Oman");
            INSERT INTO "country_translation" VALUES(348,"OM","ru","name","Оман");
            INSERT INTO "country_translation" VALUES(349,"PC","en","name","Pacific Islands Trust Territory");
            INSERT INTO "country_translation" VALUES(350,"PC","ru","name","Подопечная территория Тихоокеанские острова");
            INSERT INTO "country_translation" VALUES(351,"PK","en","name","Pakistan");
            INSERT INTO "country_translation" VALUES(352,"PK","ru","name","Пакистан");
            INSERT INTO "country_translation" VALUES(353,"PW","en","name","Palau");
            INSERT INTO "country_translation" VALUES(354,"PW","ru","name","Палау");
            INSERT INTO "country_translation" VALUES(355,"PS","en","name","Palestinian Territories");
            INSERT INTO "country_translation" VALUES(356,"PS","ru","name","Палестинская автономия");
            INSERT INTO "country_translation" VALUES(357,"PA","en","name","Panama");
            INSERT INTO "country_translation" VALUES(358,"PA","ru","name","Панама");
            INSERT INTO "country_translation" VALUES(359,"PZ","en","name","Panama Canal Zone");
            INSERT INTO "country_translation" VALUES(360,"PZ","ru","name","Зона Панамского канала");
            INSERT INTO "country_translation" VALUES(361,"PG","en","name","Papua New Guinea");
            INSERT INTO "country_translation" VALUES(362,"PG","ru","name","Папуа-Новая Гвинея");
            INSERT INTO "country_translation" VALUES(363,"PY","en","name","Paraguay");
            INSERT INTO "country_translation" VALUES(364,"PY","ru","name","Парагвай");
            INSERT INTO "country_translation" VALUES(365,"YD","en","name","People`s Democratic Republic of Yemen");
            INSERT INTO "country_translation" VALUES(366,"YD","ru","name","Народная Демократическая Республика Йемен");
            INSERT INTO "country_translation" VALUES(367,"PE","en","name","Peru");
            INSERT INTO "country_translation" VALUES(368,"PE","ru","name","Перу");
            INSERT INTO "country_translation" VALUES(369,"PH","en","name","Philippines");
            INSERT INTO "country_translation" VALUES(370,"PH","ru","name","Филиппины");
            INSERT INTO "country_translation" VALUES(371,"PN","en","name","Pitcairn Islands");
            INSERT INTO "country_translation" VALUES(372,"PN","ru","name","Питкерн");
            INSERT INTO "country_translation" VALUES(373,"PL","en","name","Poland");
            INSERT INTO "country_translation" VALUES(374,"PL","ru","name","Польша");
            INSERT INTO "country_translation" VALUES(375,"PT","en","name","Portugal");
            INSERT INTO "country_translation" VALUES(376,"PT","ru","name","Португалия");
            INSERT INTO "country_translation" VALUES(377,"PR","en","name","Puerto Rico");
            INSERT INTO "country_translation" VALUES(378,"PR","ru","name","Пуэрто-Рико");
            INSERT INTO "country_translation" VALUES(379,"QA","en","name","Qatar");
            INSERT INTO "country_translation" VALUES(380,"QA","ru","name","Катар");
            INSERT INTO "country_translation" VALUES(381,"RO","en","name","Romania");
            INSERT INTO "country_translation" VALUES(382,"RO","ru","name","Румыния");
            INSERT INTO "country_translation" VALUES(383,"RU","en","name","Russia");
            INSERT INTO "country_translation" VALUES(384,"RU","ru","name","Россия");
            INSERT INTO "country_translation" VALUES(385,"RW","en","name","Rwanda");
            INSERT INTO "country_translation" VALUES(386,"RW","ru","name","Руанда");
            INSERT INTO "country_translation" VALUES(387,"RE","en","name","Réunion");
            INSERT INTO "country_translation" VALUES(388,"RE","ru","name","Реюньон");
            INSERT INTO "country_translation" VALUES(389,"BL","en","name","Saint Barthélemy");
            INSERT INTO "country_translation" VALUES(390,"BL","ru","name","Остров Святого Бартоломея");
            INSERT INTO "country_translation" VALUES(391,"SH","en","name","Saint Helena");
            INSERT INTO "country_translation" VALUES(392,"SH","ru","name","Остров Святой Елены");
            INSERT INTO "country_translation" VALUES(393,"KN","en","name","Saint Kitts and Nevis");
            INSERT INTO "country_translation" VALUES(394,"KN","ru","name","Сент-Киттс и Невис");
            INSERT INTO "country_translation" VALUES(395,"LC","en","name","Saint Lucia");
            INSERT INTO "country_translation" VALUES(396,"LC","ru","name","Сент-Люсия");
            INSERT INTO "country_translation" VALUES(397,"MF","en","name","Saint Martin");
            INSERT INTO "country_translation" VALUES(398,"MF","ru","name","Остров Святого Мартина");
            INSERT INTO "country_translation" VALUES(399,"PM","en","name","Saint Pierre and Miquelon");
            INSERT INTO "country_translation" VALUES(400,"PM","ru","name","Сен-Пьер и Микелон");
            INSERT INTO "country_translation" VALUES(401,"VC","en","name","Saint Vincent and the Grenadines");
            INSERT INTO "country_translation" VALUES(402,"VC","ru","name","Сент-Винсент и Гренадины");
            INSERT INTO "country_translation" VALUES(403,"WS","en","name","Samoa");
            INSERT INTO "country_translation" VALUES(404,"WS","ru","name","Самоа");
            INSERT INTO "country_translation" VALUES(405,"SM","en","name","San Marino");
            INSERT INTO "country_translation" VALUES(406,"SM","ru","name","Сан-Марино");
            INSERT INTO "country_translation" VALUES(407,"SA","en","name","Saudi Arabia");
            INSERT INTO "country_translation" VALUES(408,"SA","ru","name","Саудовская Аравия");
            INSERT INTO "country_translation" VALUES(409,"SN","en","name","Senegal");
            INSERT INTO "country_translation" VALUES(410,"SN","ru","name","Сенегал");
            INSERT INTO "country_translation" VALUES(411,"RS","en","name","Serbia");
            INSERT INTO "country_translation" VALUES(412,"RS","ru","name","Сербия");
            INSERT INTO "country_translation" VALUES(413,"CS","en","name","Serbia and Montenegro");
            INSERT INTO "country_translation" VALUES(414,"CS","ru","name","Сербия и Черногория");
            INSERT INTO "country_translation" VALUES(415,"SC","en","name","Seychelles");
            INSERT INTO "country_translation" VALUES(416,"SC","ru","name","Сейшельские Острова");
            INSERT INTO "country_translation" VALUES(417,"SL","en","name","Sierra Leone");
            INSERT INTO "country_translation" VALUES(418,"SL","ru","name","Сьерра-Леоне");
            INSERT INTO "country_translation" VALUES(419,"SG","en","name","Singapore");
            INSERT INTO "country_translation" VALUES(420,"SG","ru","name","Сингапур");
            INSERT INTO "country_translation" VALUES(421,"SK","en","name","Slovakia");
            INSERT INTO "country_translation" VALUES(422,"SK","ru","name","Словакия");
            INSERT INTO "country_translation" VALUES(423,"SI","en","name","Slovenia");
            INSERT INTO "country_translation" VALUES(424,"SI","ru","name","Словения");
            INSERT INTO "country_translation" VALUES(425,"SB","en","name","Solomon Islands");
            INSERT INTO "country_translation" VALUES(426,"SB","ru","name","Соломоновы Острова");
            INSERT INTO "country_translation" VALUES(427,"SO","en","name","Somalia");
            INSERT INTO "country_translation" VALUES(428,"SO","ru","name","Сомали");
            INSERT INTO "country_translation" VALUES(429,"ZA","en","name","South Africa");
            INSERT INTO "country_translation" VALUES(430,"ZA","ru","name","Южная Африка");
            INSERT INTO "country_translation" VALUES(431,"GS","en","name","South Georgia and the South Sandwich Islands");
            INSERT INTO "country_translation" VALUES(432,"GS","ru","name","Южная Джорджия и Южные Сандвичевы Острова");
            INSERT INTO "country_translation" VALUES(433,"KR","en","name","South Korea");
            INSERT INTO "country_translation" VALUES(434,"KR","ru","name","Республика Корея");
            INSERT INTO "country_translation" VALUES(435,"ES","en","name","Spain");
            INSERT INTO "country_translation" VALUES(436,"ES","ru","name","Испания");
            INSERT INTO "country_translation" VALUES(437,"LK","en","name","Sri Lanka");
            INSERT INTO "country_translation" VALUES(438,"LK","ru","name","Шри-Ланка");
            INSERT INTO "country_translation" VALUES(439,"SD","en","name","Sudan");
            INSERT INTO "country_translation" VALUES(440,"SD","ru","name","Судан");
            INSERT INTO "country_translation" VALUES(441,"SR","en","name","Suriname");
            INSERT INTO "country_translation" VALUES(442,"SR","ru","name","Суринам");
            INSERT INTO "country_translation" VALUES(443,"SJ","en","name","Svalbard and Jan Mayen");
            INSERT INTO "country_translation" VALUES(444,"SJ","ru","name","Свальбард и Ян-Майен");
            INSERT INTO "country_translation" VALUES(445,"SZ","en","name","Swaziland");
            INSERT INTO "country_translation" VALUES(446,"SZ","ru","name","Свазиленд");
            INSERT INTO "country_translation" VALUES(447,"SE","en","name","Sweden");
            INSERT INTO "country_translation" VALUES(448,"SE","ru","name","Швеция");
            INSERT INTO "country_translation" VALUES(449,"CH","en","name","Switzerland");
            INSERT INTO "country_translation" VALUES(450,"CH","ru","name","Швейцария");
            INSERT INTO "country_translation" VALUES(451,"SY","en","name","Syria");
            INSERT INTO "country_translation" VALUES(452,"SY","ru","name","Сирийская Арабская Республика");
            INSERT INTO "country_translation" VALUES(453,"ST","en","name","São Tomé and Príncipe");
            INSERT INTO "country_translation" VALUES(454,"ST","ru","name","Сан-Томе и Принсипи");
            INSERT INTO "country_translation" VALUES(455,"TW","en","name","Taiwan");
            INSERT INTO "country_translation" VALUES(456,"TW","ru","name","Тайвань");
            INSERT INTO "country_translation" VALUES(457,"TJ","en","name","Tajikistan");
            INSERT INTO "country_translation" VALUES(458,"TJ","ru","name","Таджикистан");
            INSERT INTO "country_translation" VALUES(459,"TZ","en","name","Tanzania");
            INSERT INTO "country_translation" VALUES(460,"TZ","ru","name","Танзания");
            INSERT INTO "country_translation" VALUES(461,"TH","en","name","Thailand");
            INSERT INTO "country_translation" VALUES(462,"TH","ru","name","Таиланд");
            INSERT INTO "country_translation" VALUES(463,"TL","en","name","Timor-Leste");
            INSERT INTO "country_translation" VALUES(464,"TL","ru","name","Восточный Тимор");
            INSERT INTO "country_translation" VALUES(465,"TG","en","name","Togo");
            INSERT INTO "country_translation" VALUES(466,"TG","ru","name","Того");
            INSERT INTO "country_translation" VALUES(467,"TK","en","name","Tokelau");
            INSERT INTO "country_translation" VALUES(468,"TK","ru","name","Токелау");
            INSERT INTO "country_translation" VALUES(469,"TO","en","name","Tonga");
            INSERT INTO "country_translation" VALUES(470,"TO","ru","name","Тонга");
            INSERT INTO "country_translation" VALUES(471,"TT","en","name","Trinidad and Tobago");
            INSERT INTO "country_translation" VALUES(472,"TT","ru","name","Тринидад и Тобаго");
            INSERT INTO "country_translation" VALUES(473,"TN","en","name","Tunisia");
            INSERT INTO "country_translation" VALUES(474,"TN","ru","name","Тунис");
            INSERT INTO "country_translation" VALUES(475,"TR","en","name","Turkey");
            INSERT INTO "country_translation" VALUES(476,"TR","ru","name","Турция");
            INSERT INTO "country_translation" VALUES(477,"TM","en","name","Turkmenistan");
            INSERT INTO "country_translation" VALUES(478,"TM","ru","name","Туркменистан");
            INSERT INTO "country_translation" VALUES(479,"TC","en","name","Turks and Caicos Islands");
            INSERT INTO "country_translation" VALUES(480,"TC","ru","name","Острова Тёркс и Кайкос");
            INSERT INTO "country_translation" VALUES(481,"TV","en","name","Tuvalu");
            INSERT INTO "country_translation" VALUES(482,"TV","ru","name","Тувалу");
            INSERT INTO "country_translation" VALUES(483,"UM","en","name","U.S. Minor Outlying Islands");
            INSERT INTO "country_translation" VALUES(484,"UM","ru","name","Внешние малые острова (США)");
            INSERT INTO "country_translation" VALUES(485,"PU","en","name","U.S. Miscellaneous Pacific Islands");
            INSERT INTO "country_translation" VALUES(486,"PU","ru","name","Малые отдаленные острова Соединенных Штатов");
            INSERT INTO "country_translation" VALUES(487,"VI","en","name","U.S. Virgin Islands");
            INSERT INTO "country_translation" VALUES(488,"VI","ru","name","Американские Виргинские острова");
            INSERT INTO "country_translation" VALUES(489,"UG","en","name","Uganda");
            INSERT INTO "country_translation" VALUES(490,"UG","ru","name","Уганда");
            INSERT INTO "country_translation" VALUES(491,"UA","en","name","Ukraine");
            INSERT INTO "country_translation" VALUES(492,"UA","ru","name","Украина");
            INSERT INTO "country_translation" VALUES(493,"SU","en","name","Union of Soviet Socialist Republics");
            INSERT INTO "country_translation" VALUES(494,"SU","ru","name","СССР");
            INSERT INTO "country_translation" VALUES(495,"AE","en","name","United Arab Emirates");
            INSERT INTO "country_translation" VALUES(496,"AE","ru","name","Объединенные Арабские Эмираты");
            INSERT INTO "country_translation" VALUES(497,"GB","en","name","United Kingdom");
            INSERT INTO "country_translation" VALUES(498,"GB","ru","name","Великобритания");
            INSERT INTO "country_translation" VALUES(499,"US","en","name","United States");
            INSERT INTO "country_translation" VALUES(500,"US","ru","name","США");
            INSERT INTO "country_translation" VALUES(501,"ZZ","en","name","Unknown or Invalid Region");
            INSERT INTO "country_translation" VALUES(502,"ZZ","ru","name","Неизвестный или недействительный регион");
            INSERT INTO "country_translation" VALUES(503,"UY","en","name","Uruguay");
            INSERT INTO "country_translation" VALUES(504,"UY","ru","name","Уругвай");
            INSERT INTO "country_translation" VALUES(505,"UZ","en","name","Uzbekistan");
            INSERT INTO "country_translation" VALUES(506,"UZ","ru","name","Узбекистан");
            INSERT INTO "country_translation" VALUES(507,"VU","en","name","Vanuatu");
            INSERT INTO "country_translation" VALUES(508,"VU","ru","name","Вануату");
            INSERT INTO "country_translation" VALUES(509,"VA","en","name","Vatican City");
            INSERT INTO "country_translation" VALUES(510,"VA","ru","name","Ватикан");
            INSERT INTO "country_translation" VALUES(511,"VE","en","name","Venezuela");
            INSERT INTO "country_translation" VALUES(512,"VE","ru","name","Венесуэла");
            INSERT INTO "country_translation" VALUES(513,"VN","en","name","Vietnam");
            INSERT INTO "country_translation" VALUES(514,"VN","ru","name","Вьетнам");
            INSERT INTO "country_translation" VALUES(515,"WK","en","name","Wake Island");
            INSERT INTO "country_translation" VALUES(516,"WK","ru","name","Уэйк");
            INSERT INTO "country_translation" VALUES(517,"WF","en","name","Wallis and Futuna");
            INSERT INTO "country_translation" VALUES(518,"WF","ru","name","Уоллис и Футуна");
            INSERT INTO "country_translation" VALUES(519,"EH","en","name","Western Sahara");
            INSERT INTO "country_translation" VALUES(520,"EH","ru","name","Западная Сахара");
            INSERT INTO "country_translation" VALUES(521,"YE","en","name","Yemen");
            INSERT INTO "country_translation" VALUES(522,"YE","ru","name","Йемен");
            INSERT INTO "country_translation" VALUES(523,"ZM","en","name","Zambia");
            INSERT INTO "country_translation" VALUES(524,"ZM","ru","name","Замбия");
            INSERT INTO "country_translation" VALUES(525,"ZW","en","name","Zimbabwe");
            INSERT INTO "country_translation" VALUES(526,"ZW","ru","name","Зимбабве");
            INSERT INTO "country_translation" VALUES(527,"AX","en","name","Åland Islands");
            INSERT INTO "country_translation" VALUES(528,"AX","ru","name","Аландские острова");
        ');
    }

    protected function addDataStorage()
    {
        $this->addSql('
            INSERT INTO "storage" VALUES(1,"Local","Storage on local computer","folder","/home/user/");
        ');
    }

    protected function addDataItem()
    {
        $this->addSql('
            INSERT INTO "item" VALUES(1,"tv","JP",1,"Ван-Пис","1999-10-20",NULL,25,"Последние слова, произнесенные Королем Пиратов перед казнью, вдохновили многих: «Мои сокровища? Коли хотите, забирайте. Ищите – я их все оставил там!». Легендарная фраза Золотого Роджера ознаменовала начало Великой Эры Пиратов – тысячи людей в погоне за своими мечтами отправились на Гранд Лайн, самое опасное место в мире, желая стать обладателями мифических сокровищ... Но с каждым годом романтиков становилось все меньше, их постепенно вытесняли прагматичные пираты-разбойники, которым награбленное добро было куда ближе, чем какие-то «никчемные мечты». Но вот, одним прекрасным днем, семнадцатилетний Монки Д. Луффи исполнил заветную мечту детства - отправился в море. Его цель - ни много, ни мало стать новым Королем Пиратов. За достаточно короткий срок юному капитану удается собрать команду, состоящую из не менее амбициозных искателей приключений. И пусть ими движут совершенно разные устремления, главное, этим ребятам важны не столько деньги и слава, сколько куда более ценное – принципы и верность друзьям. И еще – служение Мечте. Что ж, пока по Гранд Лайн плавают такие люди, Великая Эра Пиратов всегда будет с нами!","/home/user/Video/One Piece (2011) [TV]","","602+",NULL,"+ 6 спэшлов","example/one-piece.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(2,"tv","JP",1,"Самурай Чамплу","2004-05-20","2005-03-19",25,"Потеряв маму, юная Фуу год проработала в чайной, а потом решила отправиться на поиски человека, который, кажется, виновен во всех её несчастьях. У Фуу была надёжная примета: это самурай, пахнущий подсолнухами. Но как выжить в Японии эпохи Эдо, когда за каждым поворотом – бандиты, которые могут тебя похитить и продать в бордель, а единственный друг – ручная белка-летяга? Фуу повезло: она встретила двух юных и при этом весьма сноровистых бойцов – бывшего пирата Мугэна и ронина Дзина. Заручившись их поддержкой, девушка отправилась в путь через всю страну. Не важно, что в животе всё время бурчит, и нет ни денег, ни документов – зато есть несравненные способности ввязываться в неприятности! При первой встрече Мугэн и Дзин попытались выяснить, кто из них круче – и они готовы продолжить дуэль при первой возможности, однако главная проблема в том, что у каждого из путешественников своё прошлое и опасные враги, о которых они даже не подозревают. И неизвестно ещё, у кого этих врагов и старых грехов больше – у пирата, грабившего корабли, у ронина, убившего своего учителя, или у девушки-сиротки?
Автор знаменитого Cowboy Bebop Синъитиро Ватанабэ смешал стильный коктейль из катан и хип-хопа. В его сериале прошлое сталкивается с будущим, Восток – с Западом, герои классического кино – с реальными историческими персонажами. Но все эти забористые ингредиенты лишь оттеняют историю о трёх разных людях, которых свела и сроднила долгая дорога...","/home/user/Video/Samurai Champloo (2004) [TV]","1. Tempestuous Temperaments (20.05.2004, 25 мин.)
2. Redeye Reprisal (03.06.2004, 25 мин.)
3. Hellhounds for Hire (Part 1) (10.06.2004, 25 мин.)
4. Hellhounds for Hire (Part 2) (17.06.2004, 25 мин.)
5. Artistic Anarchy (24.06.2004, 25 мин.)
6. Stranger Searching (01.07.2004, 25 мин.)
7. A Risky Racket (08.07.2004, 25 мин.)
8. The Art of Altercation (15.07.2004, 25 мин.)
9. Beatbox Bandits (22.07.2004, 25 мин.)
10. Lethal Lunacy (29.07.2004, 25 мин.)
11. Gamblers and Gallantry (05.08.2004, 25 мин.)
12. The Disorder Diaries (12.08.2004, 25 мин.)
13. Misguided Miscreants (Part 1) (26.08.2004, 25 мин.)
14. Misguided Miscreants (Part 2) (02.09.2004, 25 мин.)
15. Bogus Booty (09.09.2004, 25 мин.)
16. Lullabies of The Lost (Verse 1) (16.09.2004, 25 мин.)
17. Lullabies of The Lost (Verse 2) (23.09.2004, 25 мин.)
18. War of The Words (22.01.2005, 25 мин.)
19. Unholy Union (29.01.2005, 25 мин.)
20. Elegy of Entrapment (Verse 1) (05.02.2005, 25 мин.)
21. Elegy of Entrapment (Verse 2) (12.02.2005, 25 мин.)
22. Cosmic Collisions (19.02.2005, 25 мин.)
23. Baseball Blues (26.02.2005, 25 мин.)
24. Evanescent Encounter (Part 1) (05.03.2005, 25 мин.)
25. Evanescent Encounter (Part 2) (12.03.2005, 25 мин.)
26. Evanescent Encounter (Part 3) (19.03.2005, 25 мин.)","26",NULL,NULL,"example/samurai-champloo.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(3,"tv","JP",1,"Стальной алхимик [ТВ-1]","2003-10-04","2004-10-02",25,"Они нарушили основной закон алхимии и жестоко за это поплатились. И теперь два брата странствуют по миру в поисках загадочного философского камня, который поможет им исправить содеянное… Это мир, в котором вместо науки властвует магия, в котором люди способны управлять стихиями. Но у магии тоже есть законы, которым нужно следовать. В противном случае расплата будет жестокой и страшной. Два брата - Эдвард и Альфонс Элрики - пытаются совершить запретное: воскресить умершую мать. Однако закон равноценного обмена гласит: чтобы что-то получить, ты должен отдать нечто равноценное…","/home/user/Video/Fullmetal Alchemist (2003) [TV]","1. To Challenge the Sun (04.10.2003, 25 мин.)
2. Body of the Sanctioned (11.10.2003, 25 мин.)
3. Mother (18.10.2003, 25 мин.)
4. A Forger`s Love (25.10.2003, 25 мин.)
5. The Man with the Mechanical Arm (01.11.2003, 25 мин.)
6. The Alchemy Exam (08.11.2003, 25 мин.)
7. Night of the Chimera`s Cry (15.11.2003, 25 мин.)
8. The Philosopher`s Stone (22.11.2003, 25 мин.)
9. Be Thou for the People (29.11.2003, 25 мин.)
10. The Phantom Thief (06.12.2003, 25 мин.)
11. The Other Brothers Elric, Part 1 (13.12.2003, 25 мин.)
12. The Other Brothers Elric, Part 2 (20.12.2003, 25 мин.)
13. Fullmetal vs. Flame (27.12.2003, 25 мин.)
14. Destruction`s Right Hand (10.01.2004, 25 мин.)
15. The Ishbal Massacre (17.01.2004, 25 мин.)
16. That Which Is Lost (24.01.2004, 25 мин.)
17. House of the Waiting Family (31.01.2004, 25 мин.)
18. Marcoh`s Notes (07.02.2004, 25 мин.)
19. The Truth Behind Truths (14.02.2004, 25 мин.)
20. Soul of the Guardian (21.02.2004, 25 мин.)
21. The Red Glow (28.02.2004, 25 мин.)
22. Created Human (06.03.2004, 25 мин.)
23. Fullmetal Heart (13.03.2004, 25 мин.)
24. Bonding Memories (20.03.2004, 25 мин.)
25. Words of Farewell (27.03.2004, 25 мин.)
26. Her Reason (03.04.2004, 25 мин.)
27. Teacher (10.04.2004, 25 мин.)
28. All is One, One is All (17.04.2004, 25 мин.)
29. The Untainted Child (24.04.2004, 25 мин.)
30. Assault on South Headquarters (01.05.2004, 25 мин.)
31. Sin (08.05.2004, 25 мин.)
32. Dante of the Deep Forest (15.05.2004, 25 мин.)
33. Al, Captured (29.05.2004, 25 мин.)
34. Theory of Avarice (05.06.2004, 25 мин.)
35. Reunion of the Fallen (12.06.2004, 25 мин.)
36. The Sinner Within (19.06.2004, 25 мин.)
37. The Flame Alchemist, the Bachelor Lieutenant and the Mystery of Warehouse 13 (26.06.2004, 25 мин.)
38. With the River`s Flow (03.07.2004, 25 мин.)
39. Secret of Ishbal (10.07.2004, 25 мин.)
40. The Scar (17.07.2004, 25 мин.)
41. Holy Mother (24.07.2004, 25 мин.)
42. His Name is Unknown (24.07.2004, 25 мин.)
43. The Stray Dog (31.07.2004, 25 мин.)
44. Hohenheim of Light (07.08.2004, 25 мин.)
45. A Rotted Heart (21.08.2004, 25 мин.)
46. Human Transmutation (28.08.2004, 25 мин.)
47. Sealing the Homunculus (04.09.2004, 25 мин.)
48. Goodbye (11.09.2004, 25 мин.)
49. The Other Side of the Gate (18.09.2004, 25 мин.)
50. Death (25.09.2004, 25 мин.)
51. Laws and Promises (02.10.2004, 25 мин.)","51",NULL,"+ спэшл","example/fullmetal-alchemist.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(4,"feature","JP",1,"Унесённые призраками","2001-07-20",NULL,125,"Маленькая Тихиро вместе с мамой и папой переезжают в новый дом. Заблудившись по дороге, они оказываются в странном пустынном городе, где их ждет великолепный пир. Родители с жадностью набрасываются на еду и к ужасу девочки превращаются в свиней, став пленниками злой колдуньи Юбабы, властительницы таинственного мира древних богов и могущественных духов. Теперь, оказавшись одна среди магических существ и загадочных видений, отважная Тихиро должна придумать, как избавить своих родителей от чар коварной старухи и спастись из пугающего царства призраков...","/home/user/Video/Spirited Away (2001)",NULL,"1",NULL,NULL,"example/spirited-away.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(5,"tv","JP",1,"Крутой учитель Онидзука","1999-06-30","2000-09-17",25,"Онидзука Эйкити («22 года, холост», - как он сам любит представляться) - настоящий ужас на двух колесах, член нагоняющей ужас на горожан банды мотоциклистов, решает переквалифицироваться в… школьного учителя. Ведь в любом учебном заведении полным-полно аппетитных старшеклассниц в коротеньких юбочках! Но чем глубже примеривший необычную роль хулиган окунается в перипетии общего образования, тем сильнее он пытается переиначить место работы на свой манер - одерживая одну за другой победы над царящими в школе косностью, лицемерием, показухой и безразличием.","/home/user/Video/GTO (1999) [TV]","1. GTO - The Legend Begins (30.06.1999, 45 мин.)
2. Enter Uchiyamada (07.07.1999, 25 мин.)
3. Late Night Roof Diving (21.07.1999, 25 мин.)
4. The Secret Life of Onizuka (11.08.1999, 25 мин.)
5. GTO - An Eye for an Eye, a Butt for a Butt (18.08.1999, 25 мин.)
6. Conspiracies All Around (25.08.1999, 25 мин.)
7. The Mother of All Crushes (01.09.1999, 25 мин.)
8. Bungee Jumping Made Easy (08.09.1999, 25 мин.)
9. Onizuka and The Art of War (22.09.1999, 25 мин.)
10. Outside Looking In (22.09.1999, 25 мин.)
11. To Be Idolized by a Nation (17.10.1999, 25 мин.)
12. The Formula for Treachery (31.10.1999, 25 мин.)
13. Only the Best Will Do (21.11.1999, 25 мин.)
14. Between a Rock and a Hard Place (05.12.1999, 25 мин.)
15. The Great Sacrifice (12.12.1999, 25 мин.)
16. Beauty + Brains = A Dangerous Mix (19.12.1999, 25 мин.)
17. Falling for The Great Onizuka (16.01.2000, 25 мин.)
18. How to Dine and Dash (23.01.2000, 25 мин.)
19. Private Investigations (30.01.2000, 25 мин.)
20. Love Letters (06.02.2000, 25 мин.)
21. Revolution Everywhere (13.02.2000, 25 мин.)
22. The Art of Demolition (20.02.2000, 25 мин.)
23. Superstition (27.02.2000, 25 мин.)
24. Compromising Positions (05.03.2000, 25 мин.)
25. Playing Doctor - GTO Style (12.03.2000, 25 мин.)
26. Onizuka Meets His Match (19.03.2000, 25 мин.)
27. GTO - Agent to the Stars (02.04.2000, 25 мин.)
28. Whatever Can Go Wrong, Will Go Wrong (16.04.2000, 25 мин.)
29. Studies in High Finance (23.04.2000, 25 мин.)
30. Money Talks, GTO Walks (30.04.2000, 25 мин.)
31. Destination: Okinawa (07.05.2000, 25 мин.)
32. The Law of Probability (14.05.2000, 25 мин.)
33. Search and Rescue (28.05.2000, 25 мин.)
34. Good Cop / Bad Cop (04.06.2000, 25 мин.)
35. Wedding Bell Blues (11.06.2000, 25 мин.)
36. Self-Improvement: Fuyutsuki`s Transformation (18.06.2000, 25 мин.)
37. Living Together (16.07.2000, 25 мин.)
38. Great Treasure Onizuka (30.07.2000, 25 мин.)
39. Alone in the Dark (13.08.2000, 25 мин.)
40. Matters of the Heart (20.08.2000, 25 мин.)
41. Confessions (27.08.2000, 25 мин.)
42. Old Wounds Revisited (10.09.2000, 25 мин.)
43. Onizuka`s Final Battle (17.09.2000, 25 мин.)","43",NULL,"+ 2 эп.-коллажа","example/gto.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(6,"tv","JP",1,"Бек","2004-10-07","2005-03-31",25,"В начале была Песня – так верят многие народы, и не зря музыка все так же объединяет нас спустя тысячелетия после начала писаной и неписаной истории. Бек – это аниме про молодых людей, ищущих свой жизненный путь, и про уже состоявшихся людей, которым музыка помогла и помогает в жизни. Бек – это аниме про универсальный язык, на котором могут разговаривать разные поколения. А еще это аниме про современное общество, в котором всплески таланта и искренние порывы души рано или поздно становятся частью глобальной индустрии развлечений. Можно спорить – хорошо это или плохо, но таков мир, в котором мы живем.
А вообще-то, Бек – это рассказ о простом японском парне, 14-летнем Юкио Танаке, который волею судьбы встретился с молодым гитаристом Рюскэ Минами и, благодаря таланту, силе духа, простому и открытому характеру, нашел свое место в жизни, обрел друзей и встретил любовь. Это рассказ о поиске путей самовыражения, на которых искренность и честность приносят радость, а злоба и лицемерие заводят в тупик. А еще это рассказ о встрече непростых людей, которые сумели создать и сохранить рок-группу, то самое целое, которое куда больше суммы слагаемых. Именно так и рождается настоящая музыка. Именно так вышло одно из лучших музыкальных аниме всех времен!","/home/user/Video/Beck (2004) [TV]","1. The View at Fourteen (07.10.2004, 25 мин.)
2. Live House (14.10.2004, 25 мин.)
3. Moon on the Water (21.10.2004, 25 мин.)
4. Strum the Guitar (28.10.2004, 25 мин.)
5. Beck (04.11.2004, 25 мин.)
6. Hyodo and the Jaguar (11.11.2004, 25 мин.)
7. Prudence (18.11.2004, 25 мин.)
8. Broadcast in the School (25.11.2004, 25 мин.)
9. The Night Before Live (02.12.2004, 25 мин.)
10. Face (09.12.2004, 25 мин.)
11. Summer Holiday (16.12.2004, 25 мин.)
12. Secret Live (23.12.2004, 25 мин.)
13. Ciel Bleu (30.12.2004, 25 мин.)
14. Dream (06.01.2005, 25 мин.)
15. Back to School (13.01.2005, 25 мин.)
16. Indies (20.01.2005, 25 мин.)
17. Three Days (27.01.2005, 25 мин.)
18. Leon Sykes (03.02.2005, 25 мин.)
19. Blues (10.02.2005, 25 мин.)
20. Greatful Sound (17.02.2005, 25 мин.)
21. Write Music (24.02.2005, 25 мин.)
22. Night Before the Festival (03.03.2005, 25 мин.)
23. Festival (10.03.2005, 25 мин.)
24. Third Stage (17.03.2005, 25 мин.)
25. Slip Out (24.03.2005, 25 мин.)
26. America (31.03.2005, 25 мин.)","26",NULL,NULL,"example/beck.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(7,"ova","JP",1,"Бродяга Кэнсин","1999-02-20","1999-09-22",30,"XIX век, Японию раздирает клановая вражда. Маленький Синта в детстве был продан работорговцам и попал вместе с другими в засаду - всех спутников мальчика на его глазах закололи, его же спас случайно проходивший мимо воин, мастерски владеющий мечом. Синта поступает к нему в ученики и становится мастером меча по имени Кэнсин. Парень выбирает жизненный путь убийцы экстра-класса. В одной из операций он встречает таинственную девушку Томоэ, которая видит Кэнсина в действии. Привыкший не оставлять свидетелей, самурай не убивает девушку, а забирает её с собой. Что-то дрогнуло у него в душе при виде Томоэ, возможно, она смягчит этого смелого, но холодного человека?","/home/user/Video/Samurai X - Trust Betrayal (1999) [OVA]","1. The Man of the Slashing Sword (20.02.1999, 30 мин.)
2. The Lost Cat (21.04.1999, 30 мин.)
3. The Previous Night at the Mountain Home (19.06.1999, 30 мин.)
4. The Cross-Shaped Wound (22.09.1999, 30 мин.)","4",NULL,NULL,"example/samurai-x-trust-betrayal.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(8,"feature","JP",1,"Мой сосед Тоторо","1988-04-16",NULL,88,"Япония, пятидесятые годы прошлого века. Переехав в деревню, две маленькие сестры Сацуки (старшая) и Мэй (младшая) глубоко внутри дерева обнаружили необыкновенный, чудесный мир, населённый Тоторо, очаровательными пушистыми созданиями, с которыми у девочек сразу же завязалась дружба. Одни из них большие, другие совсем крохотные, но у всех у них огромное, доброе сердце и магические способности совершать необыкновенные вещи, наподобие полётов над горами или взращивания огромного дерева за одну ночь! Но увидеть этих существ могут лишь дети, которые им приглянутся... Подружившись с сёстрами, Тоторо не только устраивают им воздушную экскурсию по своим владениям, но и помогают Мэй повидаться с лежащей в больнице мамой.","/home/user/Video/Tonari no Totoro (1988)",NULL,"1",NULL,NULL,"example/tonari-no-totoro.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(9,"ova","JP",1,"Хеллсинг","2006-02-10","2012-12-26",50,"На каждое действие найдётся противодействие – для борьбы с кровожадной нечистью в Великобритании был создан Королевский Орден Протестантских Рыцарей, которому служит древнейший вампир Алукард. Согласно заключённому договору, он подчиняется главе тайной организации «Хеллсинг».
У Ватикана свой козырь – особый Тринадцатый Отдел, организация «Искариот», в составе которой неубиваемый отец Александр. Для них Алукард ничем не отличается от остальных монстров.
Однако всем им придётся на время забыть о дрязгах между католической и англиканской церквями, когда на сцену выйдет могущественный враг из прошлого – загадочный Майор во главе секретной нацистской организации «Миллениум».
Но пока не началась битва за Англию, Алукард занят воспитанием новообращённой вампирши: Виктория Серас раньше служила в полиции, а теперь ей приходится привыкать к жизни в старинном особняке, к своим новым способностям и новым обязанностям. Даже хозяйка Алукарда, леди Интегра, не знает, зачем он обратил эту упрямую девушку...
Вторая экранизация манги Хирано Кота дотошно следует оригиналу, и потому заметно отличается от сериала, ведь именно чёрный юмор, реки крови, харизматичные враги и закрученный конфликт сделали «Хеллсинга» всемирно популярным.","/home/user/Video/Hellsing (2006) [OVA]","1. Hellsing I (10.02.2006, 50 мин.)
2. Hellsing II (25.08.2006, 45 мин.)
3. Hellsing III (04.04.2007, 50 мин.)
4. Hellsing IV (22.02.2008, 55 мин.)
5. Hellsing V (21.11.2008, 40 мин.)
6. Hellsing VI (24.07.2009, 40 мин.)
7. Hellsing VII (23.12.2009, 45 мин.)
8. Hellsing VIII (27.07.2011, 50 мин.)
9. Hellsing IX (15.02.2012, 45 мин.)
10. Hellsing X (26.12.2012, 65 мин.)","10",NULL,"+ 4 спэшла","example/hellsing.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(10,"tv","JP",1,"Гинтама","2006-04-04","2010-03-25",25,"Абсурдистская фантастическо-самурайская пародийная комедия о приключениях фрилансеров в псевдо-средневековом Эдо. Захватив Землю, пришельцы Аманто запретили ношение мечей, единственный, в ком ещё жив подлинно японский дух – самоуверенный сластёна Гинтоки Саката. Неуклюжий очкарик Симпати нанялся к нему в ученики. Третьим в их команде стала прелестная Кагура из сильнейшей во вселенной семьи Ятудзоку, а с ней её питомец Садахару – пёсик размером с бегемота, обладающий забавной привычкой грызть головы всем, кто под морду подвернётся. Они называют себя «мастерами на все руки» и выполняют любые заказы – главное, чтобы заплатили.
Кроме инопланетян с ушками, бандитов со шрамами, самураев с бокэнами, девушек-ниндзя с натто и странных существ, в «Гинтаме» встречаются также Синсэнгуми, состоящие из придурковатых юношей в европейской одежде. Высмеиванию подвергается множество штампов, пародируется «Блич», «Ковбой Бибоп» и многие другие известные сериалы. Юмор колеблется от «сортирного» до «тонкой иронии», в целом это весьма «зубастая» комедия, лишённая каких-либо рамок и ограничений.","/home/user/Video/Gintama (2006) [TV-1]","","201",NULL,NULL,"example/gintama.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(11,"tv","JP",1,"Бакуман.","2010-10-02","2011-04-02",25,"Хорошие школьные оценки – престижный вуз – крупная корпорация: вот жизненный план большинства японских юношей и девушек. Но в каждом поколении находятся упрямцы, готовые отринуть синицу в руках ради возможности сохранить индивидуальность и заняться любимым делом. Таковы юный художник Моритака Масиро и начинающий писатель Акито Такаги, которые пока оканчивают среднюю школу, но уже приняли непростое решение – посвятить жизнь созданию манги, уникального феномена японской культуры.
Герои сериала - фанаты манги, лауреаты юношеских конкурсов и знакомы с реалиями «взрослого» шоу-бизнеса, где наверх пробиваются единицы. Но когда еще рисковать, как не в 16 лет?! А тут Моритака, склонный к рефлексии, внезапно узнает, что его любимая и одноклассница, Михо Адзуки, хочет быть актрисой-сэйю, то есть работать по «смежной специальности». Будучи во власти эйфории, парень тут же предлагает девушке две вещи: сыграть когда-нибудь в аниме по их манге и… выйти за него замуж. Самое интересное, что Адзуки соглашается на то и другое – но в этой же строгой последовательности. Теперь творческому дуэту придется поставить на карту все – тяжкий труд, талант, потенциальную карьеру – и крепко верить в себя и свою удачу. Не попробуешь – не узнаешь, Драгонболл тоже не сразу строился!","/home/user/Video/Bakuman (2010) [TV-1]","1. Dream and Reality (02.10.2010, 25 мин.)
2. Stupid and Clever (09.10.2010, 25 мин.)
3. Parent and Child (16.10.2010, 25 мин.)
4. Time and Key (23.10.2010, 25 мин.)
5. Summer and Storyboard (30.10.2010, 25 мин.)
6. Carrot and Stick (06.11.2010, 25 мин.)
7. Tears and Tears (13.11.2010, 25 мин.)
8. Anxiety and Anticipation (20.11.2010, 25 мин.)
9. Regret and Consent (27.11.2010, 25 мин.)
10. 10 and 2 (04.12.2010, 25 мин.)
11. Chocolate and Next! (11.12.2010, 25 мин.)
12. Feast and Graduation (18.12.2010, 25 мин.)
13. Early Results And The Real Deal (25.12.2010, 25 мин.)
14. Battles and Copying (08.01.2011, 25 мин.)
15. Debut and Hectic (15.01.2011, 25 мин.)
16. Wall and Kiss (22.01.2011, 25 мин.)
17. Braggart and Kindness (29.01.2011, 25 мин.)
18. Jealousy and Love (05.02.2011, 25 мин.)
19. Two and One (12.02.2011, 25 мин.)
20. Cooperation and Conditions (19.02.2011, 25 мин.)
21. Literature and Music (26.02.2011, 25 мин.)
22. Solidarity and Breakdown (05.03.2011, 25 мин.)
23. Tuesday and Friday (19.03.2011, 25 мин.)
24. Call and Eve (26.03.2011, 25 мин.)
25. Yes and No (02.04.2011, 25 мин.)","25",NULL,NULL,"example/bakuman.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
            INSERT INTO "item" VALUES(12,"tv","JP",1,"Гуррен-Лаганн","2007-04-01","2007-09-30",25,"Сотни лет люди живут в глубоких пещерах, в постоянном страхе перед землетрясениями и обвалами. В одной из таких подземных деревень живет мальчик Симон и его «духовный наставник» — молодой парень Камина. Камина верит, что наверху есть другой мир, без стен и потолков; его мечта — попасть туда. Но его мечты остаются пустыми фантазиями, пока в один прекрасный день Симон случайно не находит дрель... вернее, ключ от странного железного лица в толще земли. В этот же день происходит землетрясение, и потолок пещеры рушится — так начинается поистине эпическое приключение Симона, Камины и их компаньонов в новом для них мире: мире под открытым небом огромной Вселенной.","/home/user/Video/Tengen Toppa Gurren Lagann (2007) [TV]","1. Pierce the Heavens with Your Drill! (01.04.2007, 25 мин.)
2. I Said I`d Ride It (08.04.2007, 25 мин.)
3. You Two-Faced Son of a Bitch! (15.04.2007, 25 мин.)
4. Does Having So Many Faces Make You Great? (22.04.2007, 25 мин.)
5. I Don`t Understand It At All! (29.04.2007, 25 мин.)
6. All of You Bastards Put Us In Hot Water! (06.05.2007, 25 мин.)
7. You`ll Be the One To Do That! (13.05.2007, 25 мин.)
8. Farewell Comrades! (20.05.2007, 25 мин.)
9. Just What Exactly Is a Human? (27.05.2007, 25 мин.)
10. Who Really Was Your Big Brother? (03.06.2007, 25 мин.)
11. Simon, Please Remove Your Hand (10.06.2007, 25 мин.)
12. Youko-san, I Have Something to Ask of You (17.06.2007, 25 мин.)
13. Everybody, Eat to Your Heart`s Content (24.06.2007, 25 мин.)
14. How Are You, Everyone? (01.07.2007, 25 мин.)
15. I`ll Head Towards Tomorrow (08.07.2007, 25 мин.)
16. Summary Episode (15.07.2007, 25 мин.)
17. You Understand Nothing (22.07.2007, 25 мин.)
18. I`ll Make You Tell the Truth of the World (29.07.2007, 25 мин.)
19. We Must Survive. No Matter What it Takes! (05.08.2007, 25 мин.)
20. Oh God, To How Far Will You Test Us? (12.08.2007, 25 мин.)
21. You Must Survive (19.08.2007, 25 мин.)
22. And to Space (26.08.2007, 25 мин.)
23. Let`s Go, The Final Battle (02.09.2007, 25 мин.)
24. We Will Never Forget, This Minute and Second (09.09.2007, 25 мин.)
25. I Accept Your Dying Wish! (16.09.2007, 25 мин.)
26. Let`s Go, Comrades! (23.09.2007, 25 мин.)
27. All the Lights in the Sky are Stars (30.09.2007, 25 мин.)","27",NULL,"+ 2 спэшла","example/tengen-toppa-gurren-lagann.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00");
        ');
    }
}