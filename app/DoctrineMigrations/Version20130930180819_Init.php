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
        // create tables
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

        // clear sqlite sequence
        $this->addSql('DELETE FROM sqlite_sequence WHERE name IN ("image", "name", "source", "genre", "storage", "item")');
        // add sequence for image
        $this->addSql('INSERT INTO "sqlite_sequence" VALUES("image",0)');

        // add data
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

    public function down(Schema $schema)
    {
        // drop tables
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
        // clear sqlite sequence
        $this->addSql('DELETE FROM sqlite_sequence WHERE name IN ("image", "name", "source", "genre", "storage", "item")');
    }

    protected function createTableImage(Schema $schema)
    {
        $this->addSql('CREATE TABLE image (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            source VARCHAR(256) NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX image_item_idx ON image (item)');
    }

    protected function createTableType(Schema $schema)
    {
        $this->addSql('CREATE TABLE type (
            id VARCHAR(16) PRIMARY KEY NOT NULL,
            name VARCHAR(32) NOT NULL
        )');
    }

    protected function createTableName(Schema $schema)
    {
        $this->addSql('CREATE TABLE name (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            name VARCHAR(256) NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX name_item_idx ON name (item)');
    }

    protected function createTableItemsGenres(Schema $schema)
    {
        $this->addSql('CREATE TABLE items_genres (
            item_id INTEGER NOT NULL,
            genre_id INTEGER NOT NULL,
            PRIMARY KEY(item_id, genre_id)
        )');
        // add index
        $this->addSql('CREATE INDEX item_genres_item_id_idx ON items_genres (item_id)');
        $this->addSql('CREATE INDEX item_genres_genre_id_idx ON items_genres (genre_id)');
    }

    protected function createTableSource(Schema $schema)
    {
        $this->addSql('CREATE TABLE source (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            item INTEGER DEFAULT NULL,
            url VARCHAR(256) NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX source_item_idx ON source (item)');
    }

    protected function createTableCountry(Schema $schema)
    {
        $this->addSql('CREATE TABLE country (
            id VARCHAR(2) PRIMARY KEY NOT NULL,
            name VARCHAR(16) NOT NULL
        )');
    }

    protected function createTableGenre(Schema $schema)
    {
        $this->addSql('CREATE TABLE genre (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(16) NOT NULL
        )');
    }

    protected function createTableExtTranslations(Schema $schema)
    {
        $this->addSql('CREATE TABLE ext_translations (
            id INTEGER NOT NULL,
            locale VARCHAR(8) NOT NULL,
            object_class VARCHAR(255) NOT NULL,
            field VARCHAR(32) NOT NULL,
            foreign_key VARCHAR(64) NOT NULL,
            content TEXT DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        // add index
        $this->addSql('CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key)');
        $this->addSql('CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key)');
    }

    protected function createTableCountryTranslation(Schema $schema)
    {
        $this->addSql('CREATE TABLE country_translation (
            id INTEGER NOT NULL,
            object_id VARCHAR(2) DEFAULT NULL,
            locale VARCHAR(8) NOT NULL,
            field VARCHAR(32) NOT NULL,
            content TEXT DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        // add index
        $this->addSql('CREATE INDEX country_translation_object_id_idx ON country_translation (object_id)');
        $this->addSql('CREATE UNIQUE INDEX country_translation_idx ON country_translation (locale, object_id, field)');
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
        )');
    }

    protected function createTableNotice(Schema $schema)
    {
        $this->addSql('CREATE TABLE notice (
            id INTEGER NOT NULL,
            message TEXT NOT NULL,
            date_closed DATETIME DEFAULT NULL,
            date_created DATETIME NOT NULL,
            lifetime INTEGER NOT NULL,
            status INTEGER NOT NULL,
            PRIMARY KEY(id)
        )');
        // add index
        $this->addSql('CREATE INDEX notice_show_idx ON notice (date_closed, date_created)');
    }

    protected function createTableStorage(Schema $schema)
    {
        $this->addSql('CREATE TABLE "storage" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(128) NOT NULL,
            description TEXT NOT NULL,
            type VARCHAR(16) NOT NULL,
            path TEXT DEFAULT NULL
        )');
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
            summary TEXT DEFAULT NULL,
            path VARCHAR(256) DEFAULT NULL,
            episodes TEXT DEFAULT NULL,
            episodes_number VARCHAR(5) DEFAULT NULL,
            translate VARCHAR(256) DEFAULT NULL,
            file_info TEXT DEFAULT NULL,
            cover VARCHAR(256) DEFAULT NULL,
            date_add DATETIME NOT NULL,
            date_update DATETIME NOT NULL
        )');
        // add index
        $this->addSql('CREATE INDEX item_manufacturer_idx ON item (manufacturer)');
        $this->addSql('CREATE INDEX item_storage_idx ON item (storage)');
        $this->addSql('CREATE INDEX item_type_idx ON item (type)');
    }

    protected function addDataTypes()
    {
        $this->addSql('INSERT INTO "type" VALUES("feature","Feature")');
        $this->addSql('INSERT INTO "type" VALUES("featurette","Featurette")');
        $this->addSql('INSERT INTO "type" VALUES("ona","ONA")');
        $this->addSql('INSERT INTO "type" VALUES("ova","OVA")');
        $this->addSql('INSERT INTO "type" VALUES("tv","TV")');
        $this->addSql('INSERT INTO "type" VALUES("special","TV-special")');
        $this->addSql('INSERT INTO "type" VALUES("music","Music video")');
        $this->addSql('INSERT INTO "type" VALUES("commercial","Commercial")');
    }

    protected function addDataName()
    {
        $this->addSql('INSERT INTO "name" VALUES(1,1,"One Piece")');
        $this->addSql('INSERT INTO "name" VALUES(2,1,"Одним куском")');
        $this->addSql('INSERT INTO "name" VALUES(3,1,"ワンピース")');
        $this->addSql('INSERT INTO "name" VALUES(4,2,"Samurai Champloo")');
        $this->addSql('INSERT INTO "name" VALUES(5,2,"サムライチャンプルー")');
        $this->addSql('INSERT INTO "name" VALUES(6,3,"Fullmetal Alchemist")');
        $this->addSql('INSERT INTO "name" VALUES(7,3,"Hagane no Renkin Jutsushi")');
        $this->addSql('INSERT INTO "name" VALUES(8,3,"Hagane no Renkinjutsushi")');
        $this->addSql('INSERT INTO "name" VALUES(9,3,"Full Metal Alchemist")');
        $this->addSql('INSERT INTO "name" VALUES(10,3,"Hagaren")');
        $this->addSql('INSERT INTO "name" VALUES(11,3,"鋼の錬金術師")');
        $this->addSql('INSERT INTO "name" VALUES(12,4,"Spirited Away")');
        $this->addSql('INSERT INTO "name" VALUES(13,4,"Sen to Chihiro no Kamikakushi")');
        $this->addSql('INSERT INTO "name" VALUES(14,4,"千と千尋の神隠し")');
        $this->addSql('INSERT INTO "name" VALUES(15,5,"Great Teacher Onizuka")');
        $this->addSql('INSERT INTO "name" VALUES(16,5,"GTO")');
        $this->addSql('INSERT INTO "name" VALUES(17,5,"グレート・ティーチャー・オニヅカ")');
        $this->addSql('INSERT INTO "name" VALUES(18,6,"Beck: Mongolian Chop Squad")');
        $this->addSql('INSERT INTO "name" VALUES(19,6,"Beck - Mongorian Chop Squad")');
        $this->addSql('INSERT INTO "name" VALUES(20,6,"Beck Mongolian Chop Squad")');
        $this->addSql('INSERT INTO "name" VALUES(21,6,"Бек: Восточная Ударная Группа")');
        $this->addSql('INSERT INTO "name" VALUES(22,6,"BECK　ベック")');
        $this->addSql('INSERT INTO "name" VALUES(23,6,"ベック")');
        $this->addSql('INSERT INTO "name" VALUES(24,7,"Samurai X: Trust & Betrayal")');
        $this->addSql('INSERT INTO "name" VALUES(25,7,"Rurouni Kenshin: Meiji Kenkaku Romantan - Tsuioku Hen")');
        $this->addSql('INSERT INTO "name" VALUES(26,7,"Rurouni Kenshin: Meiji Kenkaku Romantan - Tsuiokuhen")');
        $this->addSql('INSERT INTO "name" VALUES(27,7,"Samurai X: Trust and Betrayal")');
        $this->addSql('INSERT INTO "name" VALUES(28,7,"Rurouni Kenshin: Tsuioku Hen")');
        $this->addSql('INSERT INTO "name" VALUES(29,7,"るろうに剣心 -明治剣客浪漫譚-　追憶編")');
        $this->addSql('INSERT INTO "name" VALUES(30,7,"るろうに剣心―明治剣客浪漫譚―追憶編")');
        $this->addSql('INSERT INTO "name" VALUES(31,7,"るろうに剣心 -明治剣客浪漫譚- 追憶編")');
        $this->addSql('INSERT INTO "name" VALUES(32,8,"My Neighbor Totoro")');
        $this->addSql('INSERT INTO "name" VALUES(33,8,"Tonari no Totoro")');
        $this->addSql('INSERT INTO "name" VALUES(34,8,"My Neighbour Totoro")');
        $this->addSql('INSERT INTO "name" VALUES(35,8,"Наш сосед Тоторо")');
        $this->addSql('INSERT INTO "name" VALUES(36,8,"となりのトトロ")');
        $this->addSql('INSERT INTO "name" VALUES(37,9,"Hellsing Ultimate")');
        $this->addSql('INSERT INTO "name" VALUES(38,9,"Hellsing OVA")');
        $this->addSql('INSERT INTO "name" VALUES(39,9,"Hellsing")');
        $this->addSql('INSERT INTO "name" VALUES(40,10,"Silver Soul")');
        $this->addSql('INSERT INTO "name" VALUES(41,10,"Gintama")');
        $this->addSql('INSERT INTO "name" VALUES(42,10,"銀魂[ぎんたま]")');
        $this->addSql('INSERT INTO "name" VALUES(43,10,"The Best of Gintama-san")');
        $this->addSql('INSERT INTO "name" VALUES(44,10,"Yorinuki Gintama-san")');
        $this->addSql('INSERT INTO "name" VALUES(45,10,"よりぬき銀魂さん")');
        $this->addSql('INSERT INTO "name" VALUES(46,11,"Bakuman.")');
        $this->addSql('INSERT INTO "name" VALUES(47,11,"バクマン。")');
        $this->addSql('INSERT INTO "name" VALUES(48,11,"バクマン.")');
        $this->addSql('INSERT INTO "name" VALUES(49,12,"Heavenly Breakthrough Gurren Lagann")');
        $this->addSql('INSERT INTO "name" VALUES(50,12,"Tengen Toppa Gurren-Lagann")');
        $this->addSql('INSERT INTO "name" VALUES(51,12,"Tengen Toppa Gurren Lagann")');
        $this->addSql('INSERT INTO "name" VALUES(52,12,"天元突破 グレンラガン")');
        $this->addSql('INSERT INTO "name" VALUES(53,12,"天元突破グレンラガン")');
        $this->addSql('INSERT INTO "name" VALUES(54,12,"Tengen Toppa Gurren Lagann: Ore no Gurren wa Pikka Pika!!")');
        $this->addSql('INSERT INTO "name" VALUES(55,12,"天元突破 グレンラガン 俺のグレンはピッカピカ!!")');
        // add sequence
        $this->addSql('INSERT INTO "sqlite_sequence" VALUES("name",55)');
    }

    protected function addDataItemsGenres()
    {
        $this->addSql('INSERT INTO "items_genres" VALUES(1,1)');
        $this->addSql('INSERT INTO "items_genres" VALUES(1,2)');
        $this->addSql('INSERT INTO "items_genres" VALUES(1,23)');
        $this->addSql('INSERT INTO "items_genres" VALUES(1,51)');
        $this->addSql('INSERT INTO "items_genres" VALUES(2,1)');
        $this->addSql('INSERT INTO "items_genres" VALUES(2,2)');
        $this->addSql('INSERT INTO "items_genres" VALUES(2,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(2,20)');
        $this->addSql('INSERT INTO "items_genres" VALUES(3,1)');
        $this->addSql('INSERT INTO "items_genres" VALUES(3,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(3,23)');
        $this->addSql('INSERT INTO "items_genres" VALUES(3,51)');
        $this->addSql('INSERT INTO "items_genres" VALUES(4,1)');
        $this->addSql('INSERT INTO "items_genres" VALUES(4,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(4,47)');
        $this->addSql('INSERT INTO "items_genres" VALUES(5,2)');
        $this->addSql('INSERT INTO "items_genres" VALUES(5,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(5,50)');
        $this->addSql('INSERT INTO "items_genres" VALUES(6,2)');
        $this->addSql('INSERT INTO "items_genres" VALUES(6,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(6,14)');
        $this->addSql('INSERT INTO "items_genres" VALUES(6,19)');
        $this->addSql('INSERT INTO "items_genres" VALUES(7,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(7,19)');
        $this->addSql('INSERT INTO "items_genres" VALUES(7,20)');
        $this->addSql('INSERT INTO "items_genres" VALUES(8,1)');
        $this->addSql('INSERT INTO "items_genres" VALUES(8,2)');
        $this->addSql('INSERT INTO "items_genres" VALUES(8,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(8,47)');
        $this->addSql('INSERT INTO "items_genres" VALUES(9,1)');
        $this->addSql('INSERT INTO "items_genres" VALUES(9,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(9,13)');
        $this->addSql('INSERT INTO "items_genres" VALUES(10,1)');
        $this->addSql('INSERT INTO "items_genres" VALUES(10,2)');
        $this->addSql('INSERT INTO "items_genres" VALUES(10,3)');
        $this->addSql('INSERT INTO "items_genres" VALUES(11,2)');
        $this->addSql('INSERT INTO "items_genres" VALUES(11,17)');
        $this->addSql('INSERT INTO "items_genres" VALUES(12,1)');
        $this->addSql('INSERT INTO "items_genres" VALUES(12,3)');
        $this->addSql('INSERT INTO "items_genres" VALUES(12,4)');
        $this->addSql('INSERT INTO "items_genres" VALUES(12,12)');
    }

    protected function addDataSource()
    {
        $this->addSql('INSERT INTO "source" VALUES(1,1,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=836")');
        $this->addSql('INSERT INTO "source" VALUES(2,1,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=69")');
        $this->addSql('INSERT INTO "source" VALUES(3,1,"http://myanimelist.net/anime/21/")');
        $this->addSql('INSERT INTO "source" VALUES(4,1,"http://cal.syoboi.jp/tid/350/time")');
        $this->addSql('INSERT INTO "source" VALUES(5,1,"http://www.allcinema.net/prog/show_c.php?num_c=162790")');
        $this->addSql('INSERT INTO "source" VALUES(6,1,"http://en.wikipedia.org/wiki/One_Piece")');
        $this->addSql('INSERT INTO "source" VALUES(7,1,"http://ru.wikipedia.org/wiki/One_Piece")');
        $this->addSql('INSERT INTO "source" VALUES(8,1,"http://ja.wikipedia.org/wiki/ONE_PIECE_%28%E3%82%A2%E3%83%8B%E3%83%A1%29")');
        $this->addSql('INSERT INTO "source" VALUES(9,1,"http://www.fansubs.ru/base.php?id=731")');
        $this->addSql('INSERT INTO "source" VALUES(10,1,"http://www.world-art.ru/animation/animation.php?id=803")');
        $this->addSql('INSERT INTO "source" VALUES(11,2,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=2636")');
        $this->addSql('INSERT INTO "source" VALUES(12,2,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=1543")');
        $this->addSql('INSERT INTO "source" VALUES(13,2,"http://myanimelist.net/anime/205/")');
        $this->addSql('INSERT INTO "source" VALUES(14,2,"http://cal.syoboi.jp/tid/395/time")');
        $this->addSql('INSERT INTO "source" VALUES(15,2,"http://www.allcinema.net/prog/show_c.php?num_c=319278")');
        $this->addSql('INSERT INTO "source" VALUES(16,2,"http://wiki.livedoor.jp/radioi_34/d/%a5%b5%a5%e0%a5%e9%a5%a4%a5%c1%a5%e3%a5%f3%a5%d7%a5%eb%a1%bc")');
        $this->addSql('INSERT INTO "source" VALUES(17,2,"http://www1.vecceed.ne.jp/~m-satomi/SAMURAICHANPLOO.html")');
        $this->addSql('INSERT INTO "source" VALUES(18,2,"http://en.wikipedia.org/wiki/Samurai_Champloo")');
        $this->addSql('INSERT INTO "source" VALUES(19,2,"http://ru.wikipedia.org/wiki/%D0%A1%D0%B0%D0%BC%D1%83%D1%80%D0%B0%D0%B9_%D0%A7%D0%B0%D0%BC%D0%BF%D0%BB%D1%83")');
        $this->addSql('INSERT INTO "source" VALUES(20,2,"http://ja.wikipedia.org/wiki/%E3%82%B5%E3%83%A0%E3%83%A9%E3%82%A4%E3%83%81%E3%83%A3%E3%83%B3%E3%83%97%E3%83%AB%E3%83%BC")');
        $this->addSql('INSERT INTO "source" VALUES(21,2,"http://www.fansubs.ru/base.php?id=361")');
        $this->addSql('INSERT INTO "source" VALUES(22,2,"http://www.world-art.ru/animation/animation.php?id=2699")');
        $this->addSql('INSERT INTO "source" VALUES(23,3,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=2960")');
        $this->addSql('INSERT INTO "source" VALUES(24,3,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=979")');
        $this->addSql('INSERT INTO "source" VALUES(25,3,"http://cal.syoboi.jp/tid/134/time")');
        $this->addSql('INSERT INTO "source" VALUES(26,3,"http://www.allcinema.net/prog/show_c.php?num_c=241943")');
        $this->addSql('INSERT INTO "source" VALUES(27,3,"http://www1.vecceed.ne.jp/~m-satomi/FULLMETALALCHEMIST.html")');
        $this->addSql('INSERT INTO "source" VALUES(28,3,"http://en.wikipedia.org/wiki/Fullmetal_Alchemist")');
        $this->addSql('INSERT INTO "source" VALUES(29,3,"http://ru.wikipedia.org/wiki/Fullmetal_Alchemist")');
        $this->addSql('INSERT INTO "source" VALUES(30,3,"http://ja.wikipedia.org/wiki/%E9%8B%BC%E3%81%AE%E9%8C%AC%E9%87%91%E8%A1%93%E5%B8%AB_%28%E3%82%A2%E3%83%8B%E3%83%A1%29")');
        $this->addSql('INSERT INTO "source" VALUES(31,3,"http://oboi.kards.ru/?act=search&level=6&search_str=FullMetal%20Alchemist")');
        $this->addSql('INSERT INTO "source" VALUES(32,3,"http://www.fansubs.ru/base.php?id=124")');
        $this->addSql('INSERT INTO "source" VALUES(33,3,"http://www.world-art.ru/animation/animation.php?id=2368")');
        $this->addSql('INSERT INTO "source" VALUES(34,4,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=377")');
        $this->addSql('INSERT INTO "source" VALUES(35,4,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=112")');
        $this->addSql('INSERT INTO "source" VALUES(36,4,"http://www.allcinema.net/prog/show_c.php?num_c=163027")');
        $this->addSql('INSERT INTO "source" VALUES(37,4,"http://en.wikipedia.org/wiki/Spirited_Away")');
        $this->addSql('INSERT INTO "source" VALUES(38,4,"http://ru.wikipedia.org/wiki/%D0%A3%D0%BD%D0%B5%D1%81%D1%91%D0%BD%D0%BD%D1%8B%D0%B5_%D0%BF%D1%80%D0%B8%D0%B7%D1%80%D0%B0%D0%BA%D0%B0%D0%BC%D0%B8")');
        $this->addSql('INSERT INTO "source" VALUES(39,4,"http://ja.wikipedia.org/wiki/%E5%8D%83%E3%81%A8%E5%8D%83%E5%B0%8B%E3%81%AE%E7%A5%9E%E9%9A%A0%E3%81%97")');
        $this->addSql('INSERT INTO "source" VALUES(40,4,"http://oboi.kards.ru/?act=search&level=6&search_str=Spirited%20Away")');
        $this->addSql('INSERT INTO "source" VALUES(41,4,"http://www.fansubs.ru/base.php?id=368")');
        $this->addSql('INSERT INTO "source" VALUES(42,4,"http://uanime.org.ua/anime/38.html")');
        $this->addSql('INSERT INTO "source" VALUES(43,4,"http://www.world-art.ru/animation/animation.php?id=87")');
        $this->addSql('INSERT INTO "source" VALUES(44,5,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=153")');
        $this->addSql('INSERT INTO "source" VALUES(45,5,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=191")');
        $this->addSql('INSERT INTO "source" VALUES(46,5,"http://www.allcinema.net/prog/show_c.php?num_c=125613")');
        $this->addSql('INSERT INTO "source" VALUES(47,5,"http://en.wikipedia.org/wiki/Great_Teacher_Onizuka")');
        $this->addSql('INSERT INTO "source" VALUES(48,5,"http://ru.wikipedia.org/wiki/%D0%9A%D1%80%D1%83%D1%82%D0%BE%D0%B9_%D1%83%D1%87%D0%B8%D1%82%D0%B5%D0%BB%D1%8C_%D0%9E%D0%BD%D0%B8%D0%B4%D0%B7%D1%83%D0%BA%D0%B0")');
        $this->addSql('INSERT INTO "source" VALUES(49,5,"http://ja.wikipedia.org/wiki/GTO_(%E6%BC%AB%E7%94%BB)")');
        $this->addSql('INSERT INTO "source" VALUES(50,5,"http://www.fansubs.ru/base.php?id=147")');
        $this->addSql('INSERT INTO "source" VALUES(51,5,"http://www.world-art.ru/animation/animation.php?id=311")');
        $this->addSql('INSERT INTO "source" VALUES(52,6,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=4404")');
        $this->addSql('INSERT INTO "source" VALUES(53,6,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=2320")');
        $this->addSql('INSERT INTO "source" VALUES(54,6,"http://myanimelist.net/anime/57/")');
        $this->addSql('INSERT INTO "source" VALUES(55,6,"http://cal.syoboi.jp/tid/490/time")');
        $this->addSql('INSERT INTO "source" VALUES(56,6,"http://www.allcinema.net/prog/show_c.php?num_c=321252")');
        $this->addSql('INSERT INTO "source" VALUES(57,6,"http://en.wikipedia.org/wiki/BECK:_Mongolian_Chop_Squad")');
        $this->addSql('INSERT INTO "source" VALUES(58,6,"http://ru.wikipedia.org/wiki/BECK:_Mongolian_Chop_Squad")');
        $this->addSql('INSERT INTO "source" VALUES(59,6,"http://ja.wikipedia.org/wiki/BECK_%28%E6%BC%AB%E7%94%BB%29")');
        $this->addSql('INSERT INTO "source" VALUES(60,6,"http://www.fansubs.ru/base.php?id=725")');
        $this->addSql('INSERT INTO "source" VALUES(61,6,"http://www.world-art.ru/animation/animation.php?id=2671")');
        $this->addSql('INSERT INTO "source" VALUES(62,7,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=210")');
        $this->addSql('INSERT INTO "source" VALUES(63,7,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=73")');
        $this->addSql('INSERT INTO "source" VALUES(64,7,"http://myanimelist.net/anime/44/")');
        $this->addSql('INSERT INTO "source" VALUES(65,7,"http://www.allcinema.net/prog/show_c.php?num_c=88146")');
        $this->addSql('INSERT INTO "source" VALUES(66,7,"http://en.wikipedia.org/wiki/Rurouni_Kenshin")');
        $this->addSql('INSERT INTO "source" VALUES(67,7,"http://ru.wikipedia.org/wiki/%D0%A1%D0%B0%D0%BC%D1%83%D1%80%D0%B0%D0%B9_X")');
        $this->addSql('INSERT INTO "source" VALUES(68,7,"http://ja.wikipedia.org/wiki/%E3%82%8B%E3%82%8D%E3%81%86%E3%81%AB%E5%89%A3%E5%BF%83_-%E6%98%8E%E6%B2%BB%E5%89%A3%E5%AE%A2%E6%B5%AA%E6%BC%AB%E8%AD%9A-")');
        $this->addSql('INSERT INTO "source" VALUES(69,7,"http://www.fansubs.ru/base.php?id=870")');
        $this->addSql('INSERT INTO "source" VALUES(70,7,"http://www.world-art.ru/animation/animation.php?id=82")');
        $this->addSql('INSERT INTO "source" VALUES(71,8,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=534")');
        $this->addSql('INSERT INTO "source" VALUES(72,8,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=303")');
        $this->addSql('INSERT INTO "source" VALUES(73,8,"http://www.allcinema.net/prog/show_c.php?num_c=150435")');
        $this->addSql('INSERT INTO "source" VALUES(74,8,"http://en.wikipedia.org/wiki/My_Neighbor_Totoro")');
        $this->addSql('INSERT INTO "source" VALUES(75,8,"http://ru.wikipedia.org/wiki/%D0%9D%D0%B0%D1%88_%D1%81%D0%BE%D1%81%D0%B5%D0%B4_%D0%A2%D0%BE%D1%82%D0%BE%D1%80%D0%BE")');
        $this->addSql('INSERT INTO "source" VALUES(76,8,"http://ja.wikipedia.org/wiki/%E3%81%A8%E3%81%AA%E3%82%8A%E3%81%AE%E3%83%88%E3%83%88%E3%83%AD")');
        $this->addSql('INSERT INTO "source" VALUES(77,8,"http://www.fansubs.ru/base.php?id=266")');
        $this->addSql('INSERT INTO "source" VALUES(78,8,"http://uanime.org.ua/anime/145.html")');
        $this->addSql('INSERT INTO "source" VALUES(79,8,"http://www.world-art.ru/animation/animation.php?id=62")');
        $this->addSql('INSERT INTO "source" VALUES(80,9,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=5114")');
        $this->addSql('INSERT INTO "source" VALUES(81,9,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=3296")');
        $this->addSql('INSERT INTO "source" VALUES(82,9,"http://myanimelist.net/anime/777/")');
        $this->addSql('INSERT INTO "source" VALUES(83,9,"http://www.allcinema.net/prog/show_c.php?num_c=323337")');
        $this->addSql('INSERT INTO "source" VALUES(84,9,"http://en.wikipedia.org/wiki/Hellsing_%28manga%29")');
        $this->addSql('INSERT INTO "source" VALUES(85,9,"http://ru.wikipedia.org/wiki/%D0%A5%D0%B5%D0%BB%D0%BB%D1%81%D0%B8%D0%BD%D0%B3:_%D0%92%D0%BE%D0%B9%D0%BD%D0%B0_%D1%81_%D0%BD%D0%B5%D1%87%D0%B8%D1%81%D1%82%D1%8C%D1%8E")');
        $this->addSql('INSERT INTO "source" VALUES(86,9,"http://ja.wikipedia.org/wiki/HELLSING")');
        $this->addSql('INSERT INTO "source" VALUES(87,9,"http://www.fansubs.ru/base.php?id=988")');
        $this->addSql('INSERT INTO "source" VALUES(88,9,"http://uanime.org.ua/anime/63.html")');
        $this->addSql('INSERT INTO "source" VALUES(89,9,"http://www.world-art.ru/animation/animation.php?id=4340")');
        $this->addSql('INSERT INTO "source" VALUES(90,10,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=6236")');
        $this->addSql('INSERT INTO "source" VALUES(91,10,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=3468")');
        $this->addSql('INSERT INTO "source" VALUES(92,10,"http://myanimelist.net/anime/918/")');
        $this->addSql('INSERT INTO "source" VALUES(93,10,"http://cal.syoboi.jp/tid/853/time")');
        $this->addSql('INSERT INTO "source" VALUES(94,10,"http://www.allcinema.net/prog/show_c.php?num_c=324863")');
        $this->addSql('INSERT INTO "source" VALUES(95,10,"http://en.wikipedia.org/wiki/Gintama")');
        $this->addSql('INSERT INTO "source" VALUES(96,10,"http://ru.wikipedia.org/wiki/Gintama")');
        $this->addSql('INSERT INTO "source" VALUES(97,10,"http://ja.wikipedia.org/wiki/%E9%8A%80%E9%AD%82_%28%E3%82%A2%E3%83%8B%E3%83%A1%29")');
        $this->addSql('INSERT INTO "source" VALUES(98,10,"http://www.fansubs.ru/base.php?id=2022")');
        $this->addSql('INSERT INTO "source" VALUES(99,10,"http://www.world-art.ru/animation/animation.php?id=5013")');
        $this->addSql('INSERT INTO "source" VALUES(100,11,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=11197")');
        $this->addSql('INSERT INTO "source" VALUES(101,11,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=7251")');
        $this->addSql('INSERT INTO "source" VALUES(102,11,"http://myanimelist.net/anime/7674/")');
        $this->addSql('INSERT INTO "source" VALUES(103,11,"http://cal.syoboi.jp/tid/2037/time")');
        $this->addSql('INSERT INTO "source" VALUES(104,11,"http://www.allcinema.net/prog/show_c.php?num_c=335759")');
        $this->addSql('INSERT INTO "source" VALUES(105,11,"http://en.wikipedia.org/wiki/Bakuman")');
        $this->addSql('INSERT INTO "source" VALUES(106,11,"http://ru.wikipedia.org/wiki/Bakuman")');
        $this->addSql('INSERT INTO "source" VALUES(107,11,"http://ja.wikipedia.org/wiki/%E3%83%90%E3%82%AF%E3%83%9E%E3%83%B3%E3%80%82_%28%E3%82%A2%E3%83%8B%E3%83%A1%29")');
        $this->addSql('INSERT INTO "source" VALUES(108,11,"http://www.fansubs.ru/base.php?id=3109")');
        $this->addSql('INSERT INTO "source" VALUES(109,11,"http://www.world-art.ru/animation/animation.php?id=7740")');
        $this->addSql('INSERT INTO "source" VALUES(110,12,"http://www.animenewsnetwork.com/encyclopedia/anime.php?id=6698")');
        $this->addSql('INSERT INTO "source" VALUES(111,12,"http://anidb.net/perl-bin/animedb.pl?show=anime&aid=4575")');
        $this->addSql('INSERT INTO "source" VALUES(112,12,"http://cal.syoboi.jp/tid/1000/time")');
        $this->addSql('INSERT INTO "source" VALUES(113,12,"http://www.allcinema.net/prog/show_c.php?num_c=326669")');
        $this->addSql('INSERT INTO "source" VALUES(114,12,"http://en.wikipedia.org/wiki/Tengen_Toppa_Gurren_Lagann")');
        $this->addSql('INSERT INTO "source" VALUES(115,12,"http://ru.wikipedia.org/wiki/Tengen_Toppa_Gurren_Lagann")');
        $this->addSql('INSERT INTO "source" VALUES(116,12,"http://ja.wikipedia.org/wiki/%E5%A4%A9%E5%85%83%E7%AA%81%E7%A0%B4%E3%82%B0%E3%83%AC%E3%83%B3%E3%83%A9%E3%82%AC%E3%83%B3")');
        $this->addSql('INSERT INTO "source" VALUES(117,12,"http://www.fansubs.ru/base.php?id=1769")');
        $this->addSql('INSERT INTO "source" VALUES(118,12,"http://www.world-art.ru/animation/animation.php?id=5959")');
        // add sequence
        $this->addSql('INSERT INTO "sqlite_sequence" VALUES("source",118)');
    }

    protected function addDataCountry()
    {
        $this->addSql('INSERT INTO "country" VALUES("AF","Afghanistan")');
        $this->addSql('INSERT INTO "country" VALUES("AL","Albania")');
        $this->addSql('INSERT INTO "country" VALUES("DZ","Algeria")');
        $this->addSql('INSERT INTO "country" VALUES("AS","American Samoa")');
        $this->addSql('INSERT INTO "country" VALUES("AD","Andorra")');
        $this->addSql('INSERT INTO "country" VALUES("AO","Angola")');
        $this->addSql('INSERT INTO "country" VALUES("AI","Anguilla")');
        $this->addSql('INSERT INTO "country" VALUES("AQ","Antarctica")');
        $this->addSql('INSERT INTO "country" VALUES("AG","Antigua and Barbuda")');
        $this->addSql('INSERT INTO "country" VALUES("AR","Argentina")');
        $this->addSql('INSERT INTO "country" VALUES("AM","Armenia")');
        $this->addSql('INSERT INTO "country" VALUES("AW","Aruba")');
        $this->addSql('INSERT INTO "country" VALUES("AU","Australia")');
        $this->addSql('INSERT INTO "country" VALUES("AT","Austria")');
        $this->addSql('INSERT INTO "country" VALUES("AZ","Azerbaijan")');
        $this->addSql('INSERT INTO "country" VALUES("BS","Bahamas")');
        $this->addSql('INSERT INTO "country" VALUES("BH","Bahrain")');
        $this->addSql('INSERT INTO "country" VALUES("BD","Bangladesh")');
        $this->addSql('INSERT INTO "country" VALUES("BB","Barbados")');
        $this->addSql('INSERT INTO "country" VALUES("BY","Belarus")');
        $this->addSql('INSERT INTO "country" VALUES("BE","Belgium")');
        $this->addSql('INSERT INTO "country" VALUES("BZ","Belize")');
        $this->addSql('INSERT INTO "country" VALUES("BJ","Benin")');
        $this->addSql('INSERT INTO "country" VALUES("BM","Bermuda")');
        $this->addSql('INSERT INTO "country" VALUES("BT","Bhutan")');
        $this->addSql('INSERT INTO "country" VALUES("BO","Bolivia")');
        $this->addSql('INSERT INTO "country" VALUES("BA","Bosnia and Herzegovina")');
        $this->addSql('INSERT INTO "country" VALUES("BW","Botswana")');
        $this->addSql('INSERT INTO "country" VALUES("BV","Bouvet Island")');
        $this->addSql('INSERT INTO "country" VALUES("BR","Brazil")');
        $this->addSql('INSERT INTO "country" VALUES("BQ","British Antarctic Territory")');
        $this->addSql('INSERT INTO "country" VALUES("IO","British Indian Ocean Territory")');
        $this->addSql('INSERT INTO "country" VALUES("VG","British Virgin Islands")');
        $this->addSql('INSERT INTO "country" VALUES("BN","Brunei")');
        $this->addSql('INSERT INTO "country" VALUES("BG","Bulgaria")');
        $this->addSql('INSERT INTO "country" VALUES("BF","Burkina Faso")');
        $this->addSql('INSERT INTO "country" VALUES("BI","Burundi")');
        $this->addSql('INSERT INTO "country" VALUES("KH","Cambodia")');
        $this->addSql('INSERT INTO "country" VALUES("CM","Cameroon")');
        $this->addSql('INSERT INTO "country" VALUES("CA","Canada")');
        $this->addSql('INSERT INTO "country" VALUES("CT","Canton and Enderbury Islands")');
        $this->addSql('INSERT INTO "country" VALUES("CV","Cape Verde")');
        $this->addSql('INSERT INTO "country" VALUES("KY","Cayman Islands")');
        $this->addSql('INSERT INTO "country" VALUES("CF","Central African Republic")');
        $this->addSql('INSERT INTO "country" VALUES("TD","Chad")');
        $this->addSql('INSERT INTO "country" VALUES("CL","Chile")');
        $this->addSql('INSERT INTO "country" VALUES("CN","China")');
        $this->addSql('INSERT INTO "country" VALUES("CX","Christmas Island")');
        $this->addSql('INSERT INTO "country" VALUES("CC","Cocos (Keeling) Islands")');
        $this->addSql('INSERT INTO "country" VALUES("CO","Colombia")');
        $this->addSql('INSERT INTO "country" VALUES("KM","Comoros")');
        $this->addSql('INSERT INTO "country" VALUES("CG","Congo - Brazzaville")');
        $this->addSql('INSERT INTO "country" VALUES("CD","Congo - Kinshasa")');
        $this->addSql('INSERT INTO "country" VALUES("CK","Cook Islands")');
        $this->addSql('INSERT INTO "country" VALUES("CR","Costa Rica")');
        $this->addSql('INSERT INTO "country" VALUES("HR","Croatia")');
        $this->addSql('INSERT INTO "country" VALUES("CU","Cuba")');
        $this->addSql('INSERT INTO "country" VALUES("CY","Cyprus")');
        $this->addSql('INSERT INTO "country" VALUES("CZ","Czech Republic")');
        $this->addSql('INSERT INTO "country" VALUES("CI","Côte d’Ivoire")');
        $this->addSql('INSERT INTO "country" VALUES("DK","Denmark")');
        $this->addSql('INSERT INTO "country" VALUES("DJ","Djibouti")');
        $this->addSql('INSERT INTO "country" VALUES("DM","Dominica")');
        $this->addSql('INSERT INTO "country" VALUES("DO","Dominican Republic")');
        $this->addSql('INSERT INTO "country" VALUES("NQ","Dronning Maud Land")');
        $this->addSql('INSERT INTO "country" VALUES("DD","East Germany")');
        $this->addSql('INSERT INTO "country" VALUES("EC","Ecuador")');
        $this->addSql('INSERT INTO "country" VALUES("EG","Egypt")');
        $this->addSql('INSERT INTO "country" VALUES("SV","El Salvador")');
        $this->addSql('INSERT INTO "country" VALUES("GQ","Equatorial Guinea")');
        $this->addSql('INSERT INTO "country" VALUES("ER","Eritrea")');
        $this->addSql('INSERT INTO "country" VALUES("EE","Estonia")');
        $this->addSql('INSERT INTO "country" VALUES("ET","Ethiopia")');
        $this->addSql('INSERT INTO "country" VALUES("FK","Falkland Islands")');
        $this->addSql('INSERT INTO "country" VALUES("FO","Faroe Islands")');
        $this->addSql('INSERT INTO "country" VALUES("FJ","Fiji")');
        $this->addSql('INSERT INTO "country" VALUES("FI","Finland")');
        $this->addSql('INSERT INTO "country" VALUES("FR","France")');
        $this->addSql('INSERT INTO "country" VALUES("GF","French Guiana")');
        $this->addSql('INSERT INTO "country" VALUES("PF","French Polynesia")');
        $this->addSql('INSERT INTO "country" VALUES("TF","French Southern Territories")');
        $this->addSql('INSERT INTO "country" VALUES("FQ","French Southern and Antarctic Territories")');
        $this->addSql('INSERT INTO "country" VALUES("GA","Gabon")');
        $this->addSql('INSERT INTO "country" VALUES("GM","Gambia")');
        $this->addSql('INSERT INTO "country" VALUES("GE","Georgia")');
        $this->addSql('INSERT INTO "country" VALUES("DE","Germany")');
        $this->addSql('INSERT INTO "country" VALUES("GH","Ghana")');
        $this->addSql('INSERT INTO "country" VALUES("GI","Gibraltar")');
        $this->addSql('INSERT INTO "country" VALUES("GR","Greece")');
        $this->addSql('INSERT INTO "country" VALUES("GL","Greenland")');
        $this->addSql('INSERT INTO "country" VALUES("GD","Grenada")');
        $this->addSql('INSERT INTO "country" VALUES("GP","Guadeloupe")');
        $this->addSql('INSERT INTO "country" VALUES("GU","Guam")');
        $this->addSql('INSERT INTO "country" VALUES("GT","Guatemala")');
        $this->addSql('INSERT INTO "country" VALUES("GG","Guernsey")');
        $this->addSql('INSERT INTO "country" VALUES("GN","Guinea")');
        $this->addSql('INSERT INTO "country" VALUES("GW","Guinea-Bissau")');
        $this->addSql('INSERT INTO "country" VALUES("GY","Guyana")');
        $this->addSql('INSERT INTO "country" VALUES("HT","Haiti")');
        $this->addSql('INSERT INTO "country" VALUES("HM","Heard Island and McDonald Islands")');
        $this->addSql('INSERT INTO "country" VALUES("HN","Honduras")');
        $this->addSql('INSERT INTO "country" VALUES("HK","Hong Kong SAR China")');
        $this->addSql('INSERT INTO "country" VALUES("HU","Hungary")');
        $this->addSql('INSERT INTO "country" VALUES("IS","Iceland")');
        $this->addSql('INSERT INTO "country" VALUES("IN","India")');
        $this->addSql('INSERT INTO "country" VALUES("ID","Indonesia")');
        $this->addSql('INSERT INTO "country" VALUES("IR","Iran")');
        $this->addSql('INSERT INTO "country" VALUES("IQ","Iraq")');
        $this->addSql('INSERT INTO "country" VALUES("IE","Ireland")');
        $this->addSql('INSERT INTO "country" VALUES("IM","Isle of Man")');
        $this->addSql('INSERT INTO "country" VALUES("IL","Israel")');
        $this->addSql('INSERT INTO "country" VALUES("IT","Italy")');
        $this->addSql('INSERT INTO "country" VALUES("JM","Jamaica")');
        $this->addSql('INSERT INTO "country" VALUES("JP","Japan")');
        $this->addSql('INSERT INTO "country" VALUES("JE","Jersey")');
        $this->addSql('INSERT INTO "country" VALUES("JT","Johnston Island")');
        $this->addSql('INSERT INTO "country" VALUES("JO","Jordan")');
        $this->addSql('INSERT INTO "country" VALUES("KZ","Kazakhstan")');
        $this->addSql('INSERT INTO "country" VALUES("KE","Kenya")');
        $this->addSql('INSERT INTO "country" VALUES("KI","Kiribati")');
        $this->addSql('INSERT INTO "country" VALUES("KW","Kuwait")');
        $this->addSql('INSERT INTO "country" VALUES("KG","Kyrgyzstan")');
        $this->addSql('INSERT INTO "country" VALUES("LA","Laos")');
        $this->addSql('INSERT INTO "country" VALUES("LV","Latvia")');
        $this->addSql('INSERT INTO "country" VALUES("LB","Lebanon")');
        $this->addSql('INSERT INTO "country" VALUES("LS","Lesotho")');
        $this->addSql('INSERT INTO "country" VALUES("LR","Liberia")');
        $this->addSql('INSERT INTO "country" VALUES("LY","Libya")');
        $this->addSql('INSERT INTO "country" VALUES("LI","Liechtenstein")');
        $this->addSql('INSERT INTO "country" VALUES("LT","Lithuania")');
        $this->addSql('INSERT INTO "country" VALUES("LU","Luxembourg")');
        $this->addSql('INSERT INTO "country" VALUES("MO","Macau SAR China")');
        $this->addSql('INSERT INTO "country" VALUES("MK","Macedonia")');
        $this->addSql('INSERT INTO "country" VALUES("MG","Madagascar")');
        $this->addSql('INSERT INTO "country" VALUES("MW","Malawi")');
        $this->addSql('INSERT INTO "country" VALUES("MY","Malaysia")');
        $this->addSql('INSERT INTO "country" VALUES("MV","Maldives")');
        $this->addSql('INSERT INTO "country" VALUES("ML","Mali")');
        $this->addSql('INSERT INTO "country" VALUES("MT","Malta")');
        $this->addSql('INSERT INTO "country" VALUES("MH","Marshall Islands")');
        $this->addSql('INSERT INTO "country" VALUES("MQ","Martinique")');
        $this->addSql('INSERT INTO "country" VALUES("MR","Mauritania")');
        $this->addSql('INSERT INTO "country" VALUES("MU","Mauritius")');
        $this->addSql('INSERT INTO "country" VALUES("YT","Mayotte")');
        $this->addSql('INSERT INTO "country" VALUES("FX","Metropolitan France")');
        $this->addSql('INSERT INTO "country" VALUES("MX","Mexico")');
        $this->addSql('INSERT INTO "country" VALUES("FM","Micronesia")');
        $this->addSql('INSERT INTO "country" VALUES("MI","Midway Islands")');
        $this->addSql('INSERT INTO "country" VALUES("MD","Moldova")');
        $this->addSql('INSERT INTO "country" VALUES("MC","Monaco")');
        $this->addSql('INSERT INTO "country" VALUES("MN","Mongolia")');
        $this->addSql('INSERT INTO "country" VALUES("ME","Montenegro")');
        $this->addSql('INSERT INTO "country" VALUES("MS","Montserrat")');
        $this->addSql('INSERT INTO "country" VALUES("MA","Morocco")');
        $this->addSql('INSERT INTO "country" VALUES("MZ","Mozambique")');
        $this->addSql('INSERT INTO "country" VALUES("MM","Myanmar (Burma)")');
        $this->addSql('INSERT INTO "country" VALUES("NA","Namibia")');
        $this->addSql('INSERT INTO "country" VALUES("NR","Nauru")');
        $this->addSql('INSERT INTO "country" VALUES("NP","Nepal")');
        $this->addSql('INSERT INTO "country" VALUES("NL","Netherlands")');
        $this->addSql('INSERT INTO "country" VALUES("AN","Netherlands Antilles")');
        $this->addSql('INSERT INTO "country" VALUES("NT","Neutral Zone")');
        $this->addSql('INSERT INTO "country" VALUES("NC","New Caledonia")');
        $this->addSql('INSERT INTO "country" VALUES("NZ","New Zealand")');
        $this->addSql('INSERT INTO "country" VALUES("NI","Nicaragua")');
        $this->addSql('INSERT INTO "country" VALUES("NE","Niger")');
        $this->addSql('INSERT INTO "country" VALUES("NG","Nigeria")');
        $this->addSql('INSERT INTO "country" VALUES("NU","Niue")');
        $this->addSql('INSERT INTO "country" VALUES("NF","Norfolk Island")');
        $this->addSql('INSERT INTO "country" VALUES("KP","North Korea")');
        $this->addSql('INSERT INTO "country" VALUES("VD","North Vietnam")');
        $this->addSql('INSERT INTO "country" VALUES("MP","Northern Mariana Islands")');
        $this->addSql('INSERT INTO "country" VALUES("NO","Norway")');
        $this->addSql('INSERT INTO "country" VALUES("OM","Oman")');
        $this->addSql('INSERT INTO "country" VALUES("PC","Pacific Islands Trust Territory")');
        $this->addSql('INSERT INTO "country" VALUES("PK","Pakistan")');
        $this->addSql('INSERT INTO "country" VALUES("PW","Palau")');
        $this->addSql('INSERT INTO "country" VALUES("PS","Palestinian Territories")');
        $this->addSql('INSERT INTO "country" VALUES("PA","Panama")');
        $this->addSql('INSERT INTO "country" VALUES("PZ","Panama Canal Zone")');
        $this->addSql('INSERT INTO "country" VALUES("PG","Papua New Guinea")');
        $this->addSql('INSERT INTO "country" VALUES("PY","Paraguay")');
        $this->addSql('INSERT INTO "country" VALUES("YD","People`s Democratic Republic of Yemen")');
        $this->addSql('INSERT INTO "country" VALUES("PE","Peru")');
        $this->addSql('INSERT INTO "country" VALUES("PH","Philippines")');
        $this->addSql('INSERT INTO "country" VALUES("PN","Pitcairn Islands")');
        $this->addSql('INSERT INTO "country" VALUES("PL","Poland")');
        $this->addSql('INSERT INTO "country" VALUES("PT","Portugal")');
        $this->addSql('INSERT INTO "country" VALUES("PR","Puerto Rico")');
        $this->addSql('INSERT INTO "country" VALUES("QA","Qatar")');
        $this->addSql('INSERT INTO "country" VALUES("RO","Romania")');
        $this->addSql('INSERT INTO "country" VALUES("RU","Russia")');
        $this->addSql('INSERT INTO "country" VALUES("RW","Rwanda")');
        $this->addSql('INSERT INTO "country" VALUES("RE","Réunion")');
        $this->addSql('INSERT INTO "country" VALUES("BL","Saint Barthélemy")');
        $this->addSql('INSERT INTO "country" VALUES("SH","Saint Helena")');
        $this->addSql('INSERT INTO "country" VALUES("KN","Saint Kitts and Nevis")');
        $this->addSql('INSERT INTO "country" VALUES("LC","Saint Lucia")');
        $this->addSql('INSERT INTO "country" VALUES("MF","Saint Martin")');
        $this->addSql('INSERT INTO "country" VALUES("PM","Saint Pierre and Miquelon")');
        $this->addSql('INSERT INTO "country" VALUES("VC","Saint Vincent and the Grenadines")');
        $this->addSql('INSERT INTO "country" VALUES("WS","Samoa")');
        $this->addSql('INSERT INTO "country" VALUES("SM","San Marino")');
        $this->addSql('INSERT INTO "country" VALUES("SA","Saudi Arabia")');
        $this->addSql('INSERT INTO "country" VALUES("SN","Senegal")');
        $this->addSql('INSERT INTO "country" VALUES("RS","Serbia")');
        $this->addSql('INSERT INTO "country" VALUES("CS","Serbia and Montenegro")');
        $this->addSql('INSERT INTO "country" VALUES("SC","Seychelles")');
        $this->addSql('INSERT INTO "country" VALUES("SL","Sierra Leone")');
        $this->addSql('INSERT INTO "country" VALUES("SG","Singapore")');
        $this->addSql('INSERT INTO "country" VALUES("SK","Slovakia")');
        $this->addSql('INSERT INTO "country" VALUES("SI","Slovenia")');
        $this->addSql('INSERT INTO "country" VALUES("SB","Solomon Islands")');
        $this->addSql('INSERT INTO "country" VALUES("SO","Somalia")');
        $this->addSql('INSERT INTO "country" VALUES("ZA","South Africa")');
        $this->addSql('INSERT INTO "country" VALUES("GS","South Georgia and the South Sandwich Islands")');
        $this->addSql('INSERT INTO "country" VALUES("KR","South Korea")');
        $this->addSql('INSERT INTO "country" VALUES("ES","Spain")');
        $this->addSql('INSERT INTO "country" VALUES("LK","Sri Lanka")');
        $this->addSql('INSERT INTO "country" VALUES("SD","Sudan")');
        $this->addSql('INSERT INTO "country" VALUES("SR","Suriname")');
        $this->addSql('INSERT INTO "country" VALUES("SJ","Svalbard and Jan Mayen")');
        $this->addSql('INSERT INTO "country" VALUES("SZ","Swaziland")');
        $this->addSql('INSERT INTO "country" VALUES("SE","Sweden")');
        $this->addSql('INSERT INTO "country" VALUES("CH","Switzerland")');
        $this->addSql('INSERT INTO "country" VALUES("SY","Syria")');
        $this->addSql('INSERT INTO "country" VALUES("ST","São Tomé and Príncipe")');
        $this->addSql('INSERT INTO "country" VALUES("TW","Taiwan")');
        $this->addSql('INSERT INTO "country" VALUES("TJ","Tajikistan")');
        $this->addSql('INSERT INTO "country" VALUES("TZ","Tanzania")');
        $this->addSql('INSERT INTO "country" VALUES("TH","Thailand")');
        $this->addSql('INSERT INTO "country" VALUES("TL","Timor-Leste")');
        $this->addSql('INSERT INTO "country" VALUES("TG","Togo")');
        $this->addSql('INSERT INTO "country" VALUES("TK","Tokelau")');
        $this->addSql('INSERT INTO "country" VALUES("TO","Tonga")');
        $this->addSql('INSERT INTO "country" VALUES("TT","Trinidad and Tobago")');
        $this->addSql('INSERT INTO "country" VALUES("TN","Tunisia")');
        $this->addSql('INSERT INTO "country" VALUES("TR","Turkey")');
        $this->addSql('INSERT INTO "country" VALUES("TM","Turkmenistan")');
        $this->addSql('INSERT INTO "country" VALUES("TC","Turks and Caicos Islands")');
        $this->addSql('INSERT INTO "country" VALUES("TV","Tuvalu")');
        $this->addSql('INSERT INTO "country" VALUES("UM","U.S. Minor Outlying Islands")');
        $this->addSql('INSERT INTO "country" VALUES("PU","U.S. Miscellaneous Pacific Islands")');
        $this->addSql('INSERT INTO "country" VALUES("VI","U.S. Virgin Islands")');
        $this->addSql('INSERT INTO "country" VALUES("UG","Uganda")');
        $this->addSql('INSERT INTO "country" VALUES("UA","Ukraine")');
        $this->addSql('INSERT INTO "country" VALUES("SU","Union of Soviet Socialist Republics")');
        $this->addSql('INSERT INTO "country" VALUES("AE","United Arab Emirates")');
        $this->addSql('INSERT INTO "country" VALUES("GB","United Kingdom")');
        $this->addSql('INSERT INTO "country" VALUES("US","United States")');
        $this->addSql('INSERT INTO "country" VALUES("ZZ","Unknown or Invalid Region")');
        $this->addSql('INSERT INTO "country" VALUES("UY","Uruguay")');
        $this->addSql('INSERT INTO "country" VALUES("UZ","Uzbekistan")');
        $this->addSql('INSERT INTO "country" VALUES("VU","Vanuatu")');
        $this->addSql('INSERT INTO "country" VALUES("VA","Vatican City")');
        $this->addSql('INSERT INTO "country" VALUES("VE","Venezuela")');
        $this->addSql('INSERT INTO "country" VALUES("VN","Vietnam")');
        $this->addSql('INSERT INTO "country" VALUES("WK","Wake Island")');
        $this->addSql('INSERT INTO "country" VALUES("WF","Wallis and Futuna")');
        $this->addSql('INSERT INTO "country" VALUES("EH","Western Sahara")');
        $this->addSql('INSERT INTO "country" VALUES("YE","Yemen")');
        $this->addSql('INSERT INTO "country" VALUES("ZM","Zambia")');
        $this->addSql('INSERT INTO "country" VALUES("ZW","Zimbabwe")');
        $this->addSql('INSERT INTO "country" VALUES("AX","Åland Islands")');
    }

    protected function addDataGenre()
    {
        $this->addSql('INSERT INTO "genre" VALUES(1,"Adventure")');
        $this->addSql('INSERT INTO "genre" VALUES(2,"Comedy")');
        $this->addSql('INSERT INTO "genre" VALUES(3,"Fantastic")');
        $this->addSql('INSERT INTO "genre" VALUES(4,"Drama")');
        $this->addSql('INSERT INTO "genre" VALUES(5,"Action")');
        $this->addSql('INSERT INTO "genre" VALUES(6,"Martial arts")');
        $this->addSql('INSERT INTO "genre" VALUES(7,"War")');
        $this->addSql('INSERT INTO "genre" VALUES(8,"Detective")');
        $this->addSql('INSERT INTO "genre" VALUES(9,"For children")');
        $this->addSql('INSERT INTO "genre" VALUES(10,"History")');
        $this->addSql('INSERT INTO "genre" VALUES(11,"Mahoe shoujo")');
        $this->addSql('INSERT INTO "genre" VALUES(12,"Meho")');
        $this->addSql('INSERT INTO "genre" VALUES(13,"Mysticism")');
        $this->addSql('INSERT INTO "genre" VALUES(14,"Musical")');
        $this->addSql('INSERT INTO "genre" VALUES(15,"Educational")');
        $this->addSql('INSERT INTO "genre" VALUES(16,"Parody")');
        $this->addSql('INSERT INTO "genre" VALUES(17,"Everyday")');
        $this->addSql('INSERT INTO "genre" VALUES(18,"Police")');
        $this->addSql('INSERT INTO "genre" VALUES(19,"Romance")');
        $this->addSql('INSERT INTO "genre" VALUES(20,"Samurai action")');
        $this->addSql('INSERT INTO "genre" VALUES(21,"Shoujo")');
        $this->addSql('INSERT INTO "genre" VALUES(22,"Shoujo-ai")');
        $this->addSql('INSERT INTO "genre" VALUES(23,"Senen")');
        $this->addSql('INSERT INTO "genre" VALUES(24,"Senen-ai")');
        $this->addSql('INSERT INTO "genre" VALUES(47,"Fable")');
        $this->addSql('INSERT INTO "genre" VALUES(48,"Sport")');
        $this->addSql('INSERT INTO "genre" VALUES(49,"Thriller")');
        $this->addSql('INSERT INTO "genre" VALUES(50,"School")');
        $this->addSql('INSERT INTO "genre" VALUES(51,"Fantasy")');
        $this->addSql('INSERT INTO "genre" VALUES(52,"Erotica")');
        $this->addSql('INSERT INTO "genre" VALUES(53,"Ettie")');
        $this->addSql('INSERT INTO "genre" VALUES(54,"Horror")');
        $this->addSql('INSERT INTO "genre" VALUES(55,"Hentai")');
        $this->addSql('INSERT INTO "genre" VALUES(56,"Urey")');
        $this->addSql('INSERT INTO "genre" VALUES(57,"Yaoi")');
        $this->addSql('INSERT INTO "genre" VALUES(58,"Psychology")');
        $this->addSql('INSERT INTO "genre" VALUES(59,"Apocalyptic fiction")');
        $this->addSql('INSERT INTO "genre" VALUES(60,"Steampunk")');
        $this->addSql('INSERT INTO "genre" VALUES(61,"Mystery play")');
        $this->addSql('INSERT INTO "genre" VALUES(62,"Josei")');
        $this->addSql('INSERT INTO "genre" VALUES(63,"Vampires")');
        $this->addSql('INSERT INTO "genre" VALUES(64,"Cyberpunk")');
        // add sequence
        $this->addSql('INSERT INTO "sqlite_sequence" VALUES("genre",64)');
    }

    protected function addDataExtTranslations()
    {
        $this->addSql('INSERT INTO "ext_translations" VALUES(1,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","feature","Полнометражный фильм")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(2,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","featurette","Короткометражный фильм")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(3,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","ona","ONA")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(4,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","ova","OVA")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(5,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","tv","ТВ")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(6,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","special","ТВ спецвыпуск")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(7,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","music","Музыкальное видео")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(8,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Type","name","commercial","Рекламный ролик")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(9,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","1","Приключения")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(10,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","2","Комедия")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(11,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","3","Фантастика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(12,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","4","Драма")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(13,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","5","Боевик")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(14,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","6","Боевые искусства")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(15,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","7","Война")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(16,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","8","Детектив")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(17,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","9","Для детей")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(18,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","10","История")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(19,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","11","Махо-сёдзё")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(20,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","12","Меха")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(21,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","13","Мистика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(22,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","14","Музыкальный")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(23,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","15","Образовательный")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(24,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","16","Пародия")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(25,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","17","Повседневность")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(26,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","18","Полиция")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(27,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","19","Романтика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(28,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","20","Самурайский боевик")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(29,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","21","Сёдзё")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(30,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","22","Сёдзё-ай")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(31,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","23","Сёнэн")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(32,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","24","Сёнэн-ай")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(33,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","47","Сказка")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(34,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","48","Спорт")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(35,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","49","Триллер")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(36,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","50","Школа")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(37,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","51","Фэнтези")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(38,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","52","Эротика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(39,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","53","Этти")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(40,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","54","Ужасы")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(41,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","55","Хентай")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(42,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","56","Юри")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(43,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","57","Яой")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(44,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","58","Психология")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(45,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","59","Постапокалиптика")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(46,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","60","Стимпанк")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(47,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","61","Мистерия")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(48,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","62","Дзёсэй")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(49,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","63","Вампиры")');
        $this->addSql('INSERT INTO "ext_translations" VALUES(50,"ru","AnimeDb\Bundle\CatalogBundle\Entity\Genre","name","64","Киберпанк")');
    }

    protected function addDataCountryTranslation()
    {
            $this->addSql('INSERT INTO "country_translation" VALUES(1,"AF","en","name","Afghanistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(2,"AF","ru","name","Афганистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(3,"AL","en","name","Albania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(4,"AL","ru","name","Албания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(5,"DZ","en","name","Algeria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(6,"DZ","ru","name","Алжир")');
            $this->addSql('INSERT INTO "country_translation" VALUES(7,"AS","en","name","American Samoa")');
            $this->addSql('INSERT INTO "country_translation" VALUES(8,"AS","ru","name","Американское Самоа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(9,"AD","en","name","Andorra")');
            $this->addSql('INSERT INTO "country_translation" VALUES(10,"AD","ru","name","Андорра")');
            $this->addSql('INSERT INTO "country_translation" VALUES(11,"AO","en","name","Angola")');
            $this->addSql('INSERT INTO "country_translation" VALUES(12,"AO","ru","name","Ангола")');
            $this->addSql('INSERT INTO "country_translation" VALUES(13,"AI","en","name","Anguilla")');
            $this->addSql('INSERT INTO "country_translation" VALUES(14,"AI","ru","name","Ангуилла")');
            $this->addSql('INSERT INTO "country_translation" VALUES(15,"AQ","en","name","Antarctica")');
            $this->addSql('INSERT INTO "country_translation" VALUES(16,"AQ","ru","name","Антарктика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(17,"AG","en","name","Antigua and Barbuda")');
            $this->addSql('INSERT INTO "country_translation" VALUES(18,"AG","ru","name","Антигуа и Барбуда")');
            $this->addSql('INSERT INTO "country_translation" VALUES(19,"AR","en","name","Argentina")');
            $this->addSql('INSERT INTO "country_translation" VALUES(20,"AR","ru","name","Аргентина")');
            $this->addSql('INSERT INTO "country_translation" VALUES(21,"AM","en","name","Armenia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(22,"AM","ru","name","Армения")');
            $this->addSql('INSERT INTO "country_translation" VALUES(23,"AW","en","name","Aruba")');
            $this->addSql('INSERT INTO "country_translation" VALUES(24,"AW","ru","name","Аруба")');
            $this->addSql('INSERT INTO "country_translation" VALUES(25,"AU","en","name","Australia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(26,"AU","ru","name","Австралия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(27,"AT","en","name","Austria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(28,"AT","ru","name","Австрия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(29,"AZ","en","name","Azerbaijan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(30,"AZ","ru","name","Азербайджан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(31,"BS","en","name","Bahamas")');
            $this->addSql('INSERT INTO "country_translation" VALUES(32,"BS","ru","name","Багамские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(33,"BH","en","name","Bahrain")');
            $this->addSql('INSERT INTO "country_translation" VALUES(34,"BH","ru","name","Бахрейн")');
            $this->addSql('INSERT INTO "country_translation" VALUES(35,"BD","en","name","Bangladesh")');
            $this->addSql('INSERT INTO "country_translation" VALUES(36,"BD","ru","name","Бангладеш")');
            $this->addSql('INSERT INTO "country_translation" VALUES(37,"BB","en","name","Barbados")');
            $this->addSql('INSERT INTO "country_translation" VALUES(38,"BB","ru","name","Барбадос")');
            $this->addSql('INSERT INTO "country_translation" VALUES(39,"BY","en","name","Belarus")');
            $this->addSql('INSERT INTO "country_translation" VALUES(40,"BY","ru","name","Беларусь")');
            $this->addSql('INSERT INTO "country_translation" VALUES(41,"BE","en","name","Belgium")');
            $this->addSql('INSERT INTO "country_translation" VALUES(42,"BE","ru","name","Бельгия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(43,"BZ","en","name","Belize")');
            $this->addSql('INSERT INTO "country_translation" VALUES(44,"BZ","ru","name","Белиз")');
            $this->addSql('INSERT INTO "country_translation" VALUES(45,"BJ","en","name","Benin")');
            $this->addSql('INSERT INTO "country_translation" VALUES(46,"BJ","ru","name","Бенин")');
            $this->addSql('INSERT INTO "country_translation" VALUES(47,"BM","en","name","Bermuda")');
            $this->addSql('INSERT INTO "country_translation" VALUES(48,"BM","ru","name","Бермудские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(49,"BT","en","name","Bhutan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(50,"BT","ru","name","Бутан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(51,"BO","en","name","Bolivia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(52,"BO","ru","name","Боливия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(53,"BA","en","name","Bosnia and Herzegovina")');
            $this->addSql('INSERT INTO "country_translation" VALUES(54,"BA","ru","name","Босния и Герцеговина")');
            $this->addSql('INSERT INTO "country_translation" VALUES(55,"BW","en","name","Botswana")');
            $this->addSql('INSERT INTO "country_translation" VALUES(56,"BW","ru","name","Ботсвана")');
            $this->addSql('INSERT INTO "country_translation" VALUES(57,"BV","en","name","Bouvet Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(58,"BV","ru","name","Остров Буве")');
            $this->addSql('INSERT INTO "country_translation" VALUES(59,"BR","en","name","Brazil")');
            $this->addSql('INSERT INTO "country_translation" VALUES(60,"BR","ru","name","Бразилия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(61,"BQ","en","name","British Antarctic Territory")');
            $this->addSql('INSERT INTO "country_translation" VALUES(62,"BQ","ru","name","Британская антарктическая территория")');
            $this->addSql('INSERT INTO "country_translation" VALUES(63,"IO","en","name","British Indian Ocean Territory")');
            $this->addSql('INSERT INTO "country_translation" VALUES(64,"IO","ru","name","Британская территория в Индийском океане")');
            $this->addSql('INSERT INTO "country_translation" VALUES(65,"VG","en","name","British Virgin Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(66,"VG","ru","name","Британские Виргинские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(67,"BN","en","name","Brunei")');
            $this->addSql('INSERT INTO "country_translation" VALUES(68,"BN","ru","name","Бруней Даруссалам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(69,"BG","en","name","Bulgaria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(70,"BG","ru","name","Болгария")');
            $this->addSql('INSERT INTO "country_translation" VALUES(71,"BF","en","name","Burkina Faso")');
            $this->addSql('INSERT INTO "country_translation" VALUES(72,"BF","ru","name","Буркина Фасо")');
            $this->addSql('INSERT INTO "country_translation" VALUES(73,"BI","en","name","Burundi")');
            $this->addSql('INSERT INTO "country_translation" VALUES(74,"BI","ru","name","Бурунди")');
            $this->addSql('INSERT INTO "country_translation" VALUES(75,"KH","en","name","Cambodia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(76,"KH","ru","name","Камбоджа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(77,"CM","en","name","Cameroon")');
            $this->addSql('INSERT INTO "country_translation" VALUES(78,"CM","ru","name","Камерун")');
            $this->addSql('INSERT INTO "country_translation" VALUES(79,"CA","en","name","Canada")');
            $this->addSql('INSERT INTO "country_translation" VALUES(80,"CA","ru","name","Канада")');
            $this->addSql('INSERT INTO "country_translation" VALUES(81,"CT","en","name","Canton and Enderbury Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(82,"CT","ru","name","Кантон и Эндербери")');
            $this->addSql('INSERT INTO "country_translation" VALUES(83,"CV","en","name","Cape Verde")');
            $this->addSql('INSERT INTO "country_translation" VALUES(84,"CV","ru","name","Острова Зеленого Мыса")');
            $this->addSql('INSERT INTO "country_translation" VALUES(85,"KY","en","name","Cayman Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(86,"KY","ru","name","Каймановы острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(87,"CF","en","name","Central African Republic")');
            $this->addSql('INSERT INTO "country_translation" VALUES(88,"CF","ru","name","Центрально-Африканская Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(89,"TD","en","name","Chad")');
            $this->addSql('INSERT INTO "country_translation" VALUES(90,"TD","ru","name","Чад")');
            $this->addSql('INSERT INTO "country_translation" VALUES(91,"CL","en","name","Chile")');
            $this->addSql('INSERT INTO "country_translation" VALUES(92,"CL","ru","name","Чили")');
            $this->addSql('INSERT INTO "country_translation" VALUES(93,"CN","en","name","China")');
            $this->addSql('INSERT INTO "country_translation" VALUES(94,"CN","ru","name","Китай")');
            $this->addSql('INSERT INTO "country_translation" VALUES(95,"CX","en","name","Christmas Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(96,"CX","ru","name","Остров Рождества")');
            $this->addSql('INSERT INTO "country_translation" VALUES(97,"CC","en","name","Cocos (Keeling) Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(98,"CC","ru","name","Кокосовые острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(99,"CO","en","name","Colombia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(100,"CO","ru","name","Колумбия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(101,"KM","en","name","Comoros")');
            $this->addSql('INSERT INTO "country_translation" VALUES(102,"KM","ru","name","Коморские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(103,"CG","en","name","Congo - Brazzaville")');
            $this->addSql('INSERT INTO "country_translation" VALUES(104,"CG","ru","name","Конго")');
            $this->addSql('INSERT INTO "country_translation" VALUES(105,"CD","en","name","Congo - Kinshasa")');
            $this->addSql('INSERT INTO "country_translation" VALUES(106,"CD","ru","name","Демократическая Республика Конго")');
            $this->addSql('INSERT INTO "country_translation" VALUES(107,"CK","en","name","Cook Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(108,"CK","ru","name","Острова Кука")');
            $this->addSql('INSERT INTO "country_translation" VALUES(109,"CR","en","name","Costa Rica")');
            $this->addSql('INSERT INTO "country_translation" VALUES(110,"CR","ru","name","Коста-Рика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(111,"HR","en","name","Croatia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(112,"HR","ru","name","Хорватия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(113,"CU","en","name","Cuba")');
            $this->addSql('INSERT INTO "country_translation" VALUES(114,"CU","ru","name","Куба")');
            $this->addSql('INSERT INTO "country_translation" VALUES(115,"CY","en","name","Cyprus")');
            $this->addSql('INSERT INTO "country_translation" VALUES(116,"CY","ru","name","Кипр")');
            $this->addSql('INSERT INTO "country_translation" VALUES(117,"CZ","en","name","Czech Republic")');
            $this->addSql('INSERT INTO "country_translation" VALUES(118,"CZ","ru","name","Чешская республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(119,"CI","en","name","Côte d’Ivoire")');
            $this->addSql('INSERT INTO "country_translation" VALUES(120,"CI","ru","name","Кот д’Ивуар")');
            $this->addSql('INSERT INTO "country_translation" VALUES(121,"DK","en","name","Denmark")');
            $this->addSql('INSERT INTO "country_translation" VALUES(122,"DK","ru","name","Дания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(123,"DJ","en","name","Djibouti")');
            $this->addSql('INSERT INTO "country_translation" VALUES(124,"DJ","ru","name","Джибути")');
            $this->addSql('INSERT INTO "country_translation" VALUES(125,"DM","en","name","Dominica")');
            $this->addSql('INSERT INTO "country_translation" VALUES(126,"DM","ru","name","Остров Доминика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(127,"DO","en","name","Dominican Republic")');
            $this->addSql('INSERT INTO "country_translation" VALUES(128,"DO","ru","name","Доминиканская Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(129,"NQ","en","name","Dronning Maud Land")');
            $this->addSql('INSERT INTO "country_translation" VALUES(130,"NQ","ru","name","Земля Королевы Мод")');
            $this->addSql('INSERT INTO "country_translation" VALUES(131,"DD","en","name","East Germany")');
            $this->addSql('INSERT INTO "country_translation" VALUES(132,"DD","ru","name","Германская Демократическая Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(133,"EC","en","name","Ecuador")');
            $this->addSql('INSERT INTO "country_translation" VALUES(134,"EC","ru","name","Эквадор")');
            $this->addSql('INSERT INTO "country_translation" VALUES(135,"EG","en","name","Egypt")');
            $this->addSql('INSERT INTO "country_translation" VALUES(136,"EG","ru","name","Египет")');
            $this->addSql('INSERT INTO "country_translation" VALUES(137,"SV","en","name","El Salvador")');
            $this->addSql('INSERT INTO "country_translation" VALUES(138,"SV","ru","name","Сальвадор")');
            $this->addSql('INSERT INTO "country_translation" VALUES(139,"GQ","en","name","Equatorial Guinea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(140,"GQ","ru","name","Экваториальная Гвинея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(141,"ER","en","name","Eritrea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(142,"ER","ru","name","Эритрея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(143,"EE","en","name","Estonia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(144,"EE","ru","name","Эстония")');
            $this->addSql('INSERT INTO "country_translation" VALUES(145,"ET","en","name","Ethiopia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(146,"ET","ru","name","Эфиопия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(147,"FK","en","name","Falkland Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(148,"FK","ru","name","Фолклендские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(149,"FO","en","name","Faroe Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(150,"FO","ru","name","Фарерские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(151,"FJ","en","name","Fiji")');
            $this->addSql('INSERT INTO "country_translation" VALUES(152,"FJ","ru","name","Фиджи")');
            $this->addSql('INSERT INTO "country_translation" VALUES(153,"FI","en","name","Finland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(154,"FI","ru","name","Финляндия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(155,"FR","en","name","France")');
            $this->addSql('INSERT INTO "country_translation" VALUES(156,"FR","ru","name","Франция")');
            $this->addSql('INSERT INTO "country_translation" VALUES(157,"GF","en","name","French Guiana")');
            $this->addSql('INSERT INTO "country_translation" VALUES(158,"GF","ru","name","Французская Гвиана")');
            $this->addSql('INSERT INTO "country_translation" VALUES(159,"PF","en","name","French Polynesia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(160,"PF","ru","name","Французская Полинезия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(161,"TF","en","name","French Southern Territories")');
            $this->addSql('INSERT INTO "country_translation" VALUES(162,"TF","ru","name","Французские Южные Территории")');
            $this->addSql('INSERT INTO "country_translation" VALUES(163,"FQ","en","name","French Southern and Antarctic Territories")');
            $this->addSql('INSERT INTO "country_translation" VALUES(164,"FQ","ru","name","Французские Южные и Антарктические территории")');
            $this->addSql('INSERT INTO "country_translation" VALUES(165,"GA","en","name","Gabon")');
            $this->addSql('INSERT INTO "country_translation" VALUES(166,"GA","ru","name","Габон")');
            $this->addSql('INSERT INTO "country_translation" VALUES(167,"GM","en","name","Gambia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(168,"GM","ru","name","Гамбия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(169,"GE","en","name","Georgia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(170,"GE","ru","name","Грузия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(171,"DE","en","name","Germany")');
            $this->addSql('INSERT INTO "country_translation" VALUES(172,"DE","ru","name","Германия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(173,"GH","en","name","Ghana")');
            $this->addSql('INSERT INTO "country_translation" VALUES(174,"GH","ru","name","Гана")');
            $this->addSql('INSERT INTO "country_translation" VALUES(175,"GI","en","name","Gibraltar")');
            $this->addSql('INSERT INTO "country_translation" VALUES(176,"GI","ru","name","Гибралтар")');
            $this->addSql('INSERT INTO "country_translation" VALUES(177,"GR","en","name","Greece")');
            $this->addSql('INSERT INTO "country_translation" VALUES(178,"GR","ru","name","Греция")');
            $this->addSql('INSERT INTO "country_translation" VALUES(179,"GL","en","name","Greenland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(180,"GL","ru","name","Гренландия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(181,"GD","en","name","Grenada")');
            $this->addSql('INSERT INTO "country_translation" VALUES(182,"GD","ru","name","Гренада")');
            $this->addSql('INSERT INTO "country_translation" VALUES(183,"GP","en","name","Guadeloupe")');
            $this->addSql('INSERT INTO "country_translation" VALUES(184,"GP","ru","name","Гваделупа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(185,"GU","en","name","Guam")');
            $this->addSql('INSERT INTO "country_translation" VALUES(186,"GU","ru","name","Гуам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(187,"GT","en","name","Guatemala")');
            $this->addSql('INSERT INTO "country_translation" VALUES(188,"GT","ru","name","Гватемала")');
            $this->addSql('INSERT INTO "country_translation" VALUES(189,"GG","en","name","Guernsey")');
            $this->addSql('INSERT INTO "country_translation" VALUES(190,"GG","ru","name","Гернси")');
            $this->addSql('INSERT INTO "country_translation" VALUES(191,"GN","en","name","Guinea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(192,"GN","ru","name","Гвинея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(193,"GW","en","name","Guinea-Bissau")');
            $this->addSql('INSERT INTO "country_translation" VALUES(194,"GW","ru","name","Гвинея-Биссау")');
            $this->addSql('INSERT INTO "country_translation" VALUES(195,"GY","en","name","Guyana")');
            $this->addSql('INSERT INTO "country_translation" VALUES(196,"GY","ru","name","Гайана")');
            $this->addSql('INSERT INTO "country_translation" VALUES(197,"HT","en","name","Haiti")');
            $this->addSql('INSERT INTO "country_translation" VALUES(198,"HT","ru","name","Гаити")');
            $this->addSql('INSERT INTO "country_translation" VALUES(199,"HM","en","name","Heard Island and McDonald Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(200,"HM","ru","name","Острова Херд и Макдональд")');
            $this->addSql('INSERT INTO "country_translation" VALUES(201,"HN","en","name","Honduras")');
            $this->addSql('INSERT INTO "country_translation" VALUES(202,"HN","ru","name","Гондурас")');
            $this->addSql('INSERT INTO "country_translation" VALUES(203,"HK","en","name","Hong Kong SAR China")');
            $this->addSql('INSERT INTO "country_translation" VALUES(204,"HK","ru","name","Гонконг, Особый Административный Район Китая")');
            $this->addSql('INSERT INTO "country_translation" VALUES(205,"HU","en","name","Hungary")');
            $this->addSql('INSERT INTO "country_translation" VALUES(206,"HU","ru","name","Венгрия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(207,"IS","en","name","Iceland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(208,"IS","ru","name","Исландия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(209,"IN","en","name","India")');
            $this->addSql('INSERT INTO "country_translation" VALUES(210,"IN","ru","name","Индия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(211,"ID","en","name","Indonesia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(212,"ID","ru","name","Индонезия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(213,"IR","en","name","Iran")');
            $this->addSql('INSERT INTO "country_translation" VALUES(214,"IR","ru","name","Иран")');
            $this->addSql('INSERT INTO "country_translation" VALUES(215,"IQ","en","name","Iraq")');
            $this->addSql('INSERT INTO "country_translation" VALUES(216,"IQ","ru","name","Ирак")');
            $this->addSql('INSERT INTO "country_translation" VALUES(217,"IE","en","name","Ireland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(218,"IE","ru","name","Ирландия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(219,"IM","en","name","Isle of Man")');
            $this->addSql('INSERT INTO "country_translation" VALUES(220,"IM","ru","name","Остров Мэн")');
            $this->addSql('INSERT INTO "country_translation" VALUES(221,"IL","en","name","Israel")');
            $this->addSql('INSERT INTO "country_translation" VALUES(222,"IL","ru","name","Израиль")');
            $this->addSql('INSERT INTO "country_translation" VALUES(223,"IT","en","name","Italy")');
            $this->addSql('INSERT INTO "country_translation" VALUES(224,"IT","ru","name","Италия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(225,"JM","en","name","Jamaica")');
            $this->addSql('INSERT INTO "country_translation" VALUES(226,"JM","ru","name","Ямайка")');
            $this->addSql('INSERT INTO "country_translation" VALUES(227,"JP","en","name","Japan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(228,"JP","ru","name","Япония")');
            $this->addSql('INSERT INTO "country_translation" VALUES(229,"JE","en","name","Jersey")');
            $this->addSql('INSERT INTO "country_translation" VALUES(230,"JE","ru","name","Джерси")');
            $this->addSql('INSERT INTO "country_translation" VALUES(231,"JT","en","name","Johnston Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(232,"JT","ru","name","Джонстон")');
            $this->addSql('INSERT INTO "country_translation" VALUES(233,"JO","en","name","Jordan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(234,"JO","ru","name","Иордания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(235,"KZ","en","name","Kazakhstan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(236,"KZ","ru","name","Казахстан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(237,"KE","en","name","Kenya")');
            $this->addSql('INSERT INTO "country_translation" VALUES(238,"KE","ru","name","Кения")');
            $this->addSql('INSERT INTO "country_translation" VALUES(239,"KI","en","name","Kiribati")');
            $this->addSql('INSERT INTO "country_translation" VALUES(240,"KI","ru","name","Кирибати")');
            $this->addSql('INSERT INTO "country_translation" VALUES(241,"KW","en","name","Kuwait")');
            $this->addSql('INSERT INTO "country_translation" VALUES(242,"KW","ru","name","Кувейт")');
            $this->addSql('INSERT INTO "country_translation" VALUES(243,"KG","en","name","Kyrgyzstan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(244,"KG","ru","name","Кыргызстан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(245,"LA","en","name","Laos")');
            $this->addSql('INSERT INTO "country_translation" VALUES(246,"LA","ru","name","Лаос")');
            $this->addSql('INSERT INTO "country_translation" VALUES(247,"LV","en","name","Latvia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(248,"LV","ru","name","Латвия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(249,"LB","en","name","Lebanon")');
            $this->addSql('INSERT INTO "country_translation" VALUES(250,"LB","ru","name","Ливан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(251,"LS","en","name","Lesotho")');
            $this->addSql('INSERT INTO "country_translation" VALUES(252,"LS","ru","name","Лесото")');
            $this->addSql('INSERT INTO "country_translation" VALUES(253,"LR","en","name","Liberia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(254,"LR","ru","name","Либерия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(255,"LY","en","name","Libya")');
            $this->addSql('INSERT INTO "country_translation" VALUES(256,"LY","ru","name","Ливия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(257,"LI","en","name","Liechtenstein")');
            $this->addSql('INSERT INTO "country_translation" VALUES(258,"LI","ru","name","Лихтенштейн")');
            $this->addSql('INSERT INTO "country_translation" VALUES(259,"LT","en","name","Lithuania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(260,"LT","ru","name","Литва")');
            $this->addSql('INSERT INTO "country_translation" VALUES(261,"LU","en","name","Luxembourg")');
            $this->addSql('INSERT INTO "country_translation" VALUES(262,"LU","ru","name","Люксембург")');
            $this->addSql('INSERT INTO "country_translation" VALUES(263,"MO","en","name","Macau SAR China")');
            $this->addSql('INSERT INTO "country_translation" VALUES(264,"MO","ru","name","Макао (особый административный район КНР)")');
            $this->addSql('INSERT INTO "country_translation" VALUES(265,"MK","en","name","Macedonia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(266,"MK","ru","name","Македония")');
            $this->addSql('INSERT INTO "country_translation" VALUES(267,"MG","en","name","Madagascar")');
            $this->addSql('INSERT INTO "country_translation" VALUES(268,"MG","ru","name","Мадагаскар")');
            $this->addSql('INSERT INTO "country_translation" VALUES(269,"MW","en","name","Malawi")');
            $this->addSql('INSERT INTO "country_translation" VALUES(270,"MW","ru","name","Малави")');
            $this->addSql('INSERT INTO "country_translation" VALUES(271,"MY","en","name","Malaysia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(272,"MY","ru","name","Малайзия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(273,"MV","en","name","Maldives")');
            $this->addSql('INSERT INTO "country_translation" VALUES(274,"MV","ru","name","Мальдивы")');
            $this->addSql('INSERT INTO "country_translation" VALUES(275,"ML","en","name","Mali")');
            $this->addSql('INSERT INTO "country_translation" VALUES(276,"ML","ru","name","Мали")');
            $this->addSql('INSERT INTO "country_translation" VALUES(277,"MT","en","name","Malta")');
            $this->addSql('INSERT INTO "country_translation" VALUES(278,"MT","ru","name","Мальта")');
            $this->addSql('INSERT INTO "country_translation" VALUES(279,"MH","en","name","Marshall Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(280,"MH","ru","name","Маршалловы Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(281,"MQ","en","name","Martinique")');
            $this->addSql('INSERT INTO "country_translation" VALUES(282,"MQ","ru","name","Мартиник")');
            $this->addSql('INSERT INTO "country_translation" VALUES(283,"MR","en","name","Mauritania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(284,"MR","ru","name","Мавритания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(285,"MU","en","name","Mauritius")');
            $this->addSql('INSERT INTO "country_translation" VALUES(286,"MU","ru","name","Маврикий")');
            $this->addSql('INSERT INTO "country_translation" VALUES(287,"YT","en","name","Mayotte")');
            $this->addSql('INSERT INTO "country_translation" VALUES(288,"YT","ru","name","Майотта")');
            $this->addSql('INSERT INTO "country_translation" VALUES(289,"FX","en","name","Metropolitan France")');
            $this->addSql('INSERT INTO "country_translation" VALUES(290,"FX","ru","name","Метрополия Франции")');
            $this->addSql('INSERT INTO "country_translation" VALUES(291,"MX","en","name","Mexico")');
            $this->addSql('INSERT INTO "country_translation" VALUES(292,"MX","ru","name","Мексика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(293,"FM","en","name","Micronesia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(294,"FM","ru","name","Федеративные Штаты Микронезии")');
            $this->addSql('INSERT INTO "country_translation" VALUES(295,"MI","en","name","Midway Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(296,"MI","ru","name","Мидуэй")');
            $this->addSql('INSERT INTO "country_translation" VALUES(297,"MD","en","name","Moldova")');
            $this->addSql('INSERT INTO "country_translation" VALUES(298,"MD","ru","name","Молдова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(299,"MC","en","name","Monaco")');
            $this->addSql('INSERT INTO "country_translation" VALUES(300,"MC","ru","name","Монако")');
            $this->addSql('INSERT INTO "country_translation" VALUES(301,"MN","en","name","Mongolia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(302,"MN","ru","name","Монголия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(303,"ME","en","name","Montenegro")');
            $this->addSql('INSERT INTO "country_translation" VALUES(304,"ME","ru","name","Черногория")');
            $this->addSql('INSERT INTO "country_translation" VALUES(305,"MS","en","name","Montserrat")');
            $this->addSql('INSERT INTO "country_translation" VALUES(306,"MS","ru","name","Монсеррат")');
            $this->addSql('INSERT INTO "country_translation" VALUES(307,"MA","en","name","Morocco")');
            $this->addSql('INSERT INTO "country_translation" VALUES(308,"MA","ru","name","Марокко")');
            $this->addSql('INSERT INTO "country_translation" VALUES(309,"MZ","en","name","Mozambique")');
            $this->addSql('INSERT INTO "country_translation" VALUES(310,"MZ","ru","name","Мозамбик")');
            $this->addSql('INSERT INTO "country_translation" VALUES(311,"MM","en","name","Myanmar (Burma)")');
            $this->addSql('INSERT INTO "country_translation" VALUES(312,"MM","ru","name","Мьянма")');
            $this->addSql('INSERT INTO "country_translation" VALUES(313,"NA","en","name","Namibia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(314,"NA","ru","name","Намибия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(315,"NR","en","name","Nauru")');
            $this->addSql('INSERT INTO "country_translation" VALUES(316,"NR","ru","name","Науру")');
            $this->addSql('INSERT INTO "country_translation" VALUES(317,"NP","en","name","Nepal")');
            $this->addSql('INSERT INTO "country_translation" VALUES(318,"NP","ru","name","Непал")');
            $this->addSql('INSERT INTO "country_translation" VALUES(319,"NL","en","name","Netherlands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(320,"NL","ru","name","Нидерланды")');
            $this->addSql('INSERT INTO "country_translation" VALUES(321,"AN","en","name","Netherlands Antilles")');
            $this->addSql('INSERT INTO "country_translation" VALUES(322,"AN","ru","name","Нидерландские Антильские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(323,"NT","en","name","Neutral Zone")');
            $this->addSql('INSERT INTO "country_translation" VALUES(324,"NT","ru","name","Нейтральная зона (саудовско-иракская)")');
            $this->addSql('INSERT INTO "country_translation" VALUES(325,"NC","en","name","New Caledonia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(326,"NC","ru","name","Новая Каледония")');
            $this->addSql('INSERT INTO "country_translation" VALUES(327,"NZ","en","name","New Zealand")');
            $this->addSql('INSERT INTO "country_translation" VALUES(328,"NZ","ru","name","Новая Зеландия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(329,"NI","en","name","Nicaragua")');
            $this->addSql('INSERT INTO "country_translation" VALUES(330,"NI","ru","name","Никарагуа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(331,"NE","en","name","Niger")');
            $this->addSql('INSERT INTO "country_translation" VALUES(332,"NE","ru","name","Нигер")');
            $this->addSql('INSERT INTO "country_translation" VALUES(333,"NG","en","name","Nigeria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(334,"NG","ru","name","Нигерия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(335,"NU","en","name","Niue")');
            $this->addSql('INSERT INTO "country_translation" VALUES(336,"NU","ru","name","Ниуе")');
            $this->addSql('INSERT INTO "country_translation" VALUES(337,"NF","en","name","Norfolk Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(338,"NF","ru","name","Остров Норфолк")');
            $this->addSql('INSERT INTO "country_translation" VALUES(339,"KP","en","name","North Korea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(340,"KP","ru","name","Корейская Народно-Демократическая Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(341,"VD","en","name","North Vietnam")');
            $this->addSql('INSERT INTO "country_translation" VALUES(342,"VD","ru","name","Демократическая Республика Вьетнам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(343,"MP","en","name","Northern Mariana Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(344,"MP","ru","name","Северные Марианские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(345,"NO","en","name","Norway")');
            $this->addSql('INSERT INTO "country_translation" VALUES(346,"NO","ru","name","Норвегия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(347,"OM","en","name","Oman")');
            $this->addSql('INSERT INTO "country_translation" VALUES(348,"OM","ru","name","Оман")');
            $this->addSql('INSERT INTO "country_translation" VALUES(349,"PC","en","name","Pacific Islands Trust Territory")');
            $this->addSql('INSERT INTO "country_translation" VALUES(350,"PC","ru","name","Подопечная территория Тихоокеанские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(351,"PK","en","name","Pakistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(352,"PK","ru","name","Пакистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(353,"PW","en","name","Palau")');
            $this->addSql('INSERT INTO "country_translation" VALUES(354,"PW","ru","name","Палау")');
            $this->addSql('INSERT INTO "country_translation" VALUES(355,"PS","en","name","Palestinian Territories")');
            $this->addSql('INSERT INTO "country_translation" VALUES(356,"PS","ru","name","Палестинская автономия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(357,"PA","en","name","Panama")');
            $this->addSql('INSERT INTO "country_translation" VALUES(358,"PA","ru","name","Панама")');
            $this->addSql('INSERT INTO "country_translation" VALUES(359,"PZ","en","name","Panama Canal Zone")');
            $this->addSql('INSERT INTO "country_translation" VALUES(360,"PZ","ru","name","Зона Панамского канала")');
            $this->addSql('INSERT INTO "country_translation" VALUES(361,"PG","en","name","Papua New Guinea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(362,"PG","ru","name","Папуа-Новая Гвинея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(363,"PY","en","name","Paraguay")');
            $this->addSql('INSERT INTO "country_translation" VALUES(364,"PY","ru","name","Парагвай")');
            $this->addSql('INSERT INTO "country_translation" VALUES(365,"YD","en","name","People`s Democratic Republic of Yemen")');
            $this->addSql('INSERT INTO "country_translation" VALUES(366,"YD","ru","name","Народная Демократическая Республика Йемен")');
            $this->addSql('INSERT INTO "country_translation" VALUES(367,"PE","en","name","Peru")');
            $this->addSql('INSERT INTO "country_translation" VALUES(368,"PE","ru","name","Перу")');
            $this->addSql('INSERT INTO "country_translation" VALUES(369,"PH","en","name","Philippines")');
            $this->addSql('INSERT INTO "country_translation" VALUES(370,"PH","ru","name","Филиппины")');
            $this->addSql('INSERT INTO "country_translation" VALUES(371,"PN","en","name","Pitcairn Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(372,"PN","ru","name","Питкерн")');
            $this->addSql('INSERT INTO "country_translation" VALUES(373,"PL","en","name","Poland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(374,"PL","ru","name","Польша")');
            $this->addSql('INSERT INTO "country_translation" VALUES(375,"PT","en","name","Portugal")');
            $this->addSql('INSERT INTO "country_translation" VALUES(376,"PT","ru","name","Португалия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(377,"PR","en","name","Puerto Rico")');
            $this->addSql('INSERT INTO "country_translation" VALUES(378,"PR","ru","name","Пуэрто-Рико")');
            $this->addSql('INSERT INTO "country_translation" VALUES(379,"QA","en","name","Qatar")');
            $this->addSql('INSERT INTO "country_translation" VALUES(380,"QA","ru","name","Катар")');
            $this->addSql('INSERT INTO "country_translation" VALUES(381,"RO","en","name","Romania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(382,"RO","ru","name","Румыния")');
            $this->addSql('INSERT INTO "country_translation" VALUES(383,"RU","en","name","Russia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(384,"RU","ru","name","Россия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(385,"RW","en","name","Rwanda")');
            $this->addSql('INSERT INTO "country_translation" VALUES(386,"RW","ru","name","Руанда")');
            $this->addSql('INSERT INTO "country_translation" VALUES(387,"RE","en","name","Réunion")');
            $this->addSql('INSERT INTO "country_translation" VALUES(388,"RE","ru","name","Реюньон")');
            $this->addSql('INSERT INTO "country_translation" VALUES(389,"BL","en","name","Saint Barthélemy")');
            $this->addSql('INSERT INTO "country_translation" VALUES(390,"BL","ru","name","Остров Святого Бартоломея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(391,"SH","en","name","Saint Helena")');
            $this->addSql('INSERT INTO "country_translation" VALUES(392,"SH","ru","name","Остров Святой Елены")');
            $this->addSql('INSERT INTO "country_translation" VALUES(393,"KN","en","name","Saint Kitts and Nevis")');
            $this->addSql('INSERT INTO "country_translation" VALUES(394,"KN","ru","name","Сент-Киттс и Невис")');
            $this->addSql('INSERT INTO "country_translation" VALUES(395,"LC","en","name","Saint Lucia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(396,"LC","ru","name","Сент-Люсия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(397,"MF","en","name","Saint Martin")');
            $this->addSql('INSERT INTO "country_translation" VALUES(398,"MF","ru","name","Остров Святого Мартина")');
            $this->addSql('INSERT INTO "country_translation" VALUES(399,"PM","en","name","Saint Pierre and Miquelon")');
            $this->addSql('INSERT INTO "country_translation" VALUES(400,"PM","ru","name","Сен-Пьер и Микелон")');
            $this->addSql('INSERT INTO "country_translation" VALUES(401,"VC","en","name","Saint Vincent and the Grenadines")');
            $this->addSql('INSERT INTO "country_translation" VALUES(402,"VC","ru","name","Сент-Винсент и Гренадины")');
            $this->addSql('INSERT INTO "country_translation" VALUES(403,"WS","en","name","Samoa")');
            $this->addSql('INSERT INTO "country_translation" VALUES(404,"WS","ru","name","Самоа")');
            $this->addSql('INSERT INTO "country_translation" VALUES(405,"SM","en","name","San Marino")');
            $this->addSql('INSERT INTO "country_translation" VALUES(406,"SM","ru","name","Сан-Марино")');
            $this->addSql('INSERT INTO "country_translation" VALUES(407,"SA","en","name","Saudi Arabia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(408,"SA","ru","name","Саудовская Аравия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(409,"SN","en","name","Senegal")');
            $this->addSql('INSERT INTO "country_translation" VALUES(410,"SN","ru","name","Сенегал")');
            $this->addSql('INSERT INTO "country_translation" VALUES(411,"RS","en","name","Serbia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(412,"RS","ru","name","Сербия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(413,"CS","en","name","Serbia and Montenegro")');
            $this->addSql('INSERT INTO "country_translation" VALUES(414,"CS","ru","name","Сербия и Черногория")');
            $this->addSql('INSERT INTO "country_translation" VALUES(415,"SC","en","name","Seychelles")');
            $this->addSql('INSERT INTO "country_translation" VALUES(416,"SC","ru","name","Сейшельские Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(417,"SL","en","name","Sierra Leone")');
            $this->addSql('INSERT INTO "country_translation" VALUES(418,"SL","ru","name","Сьерра-Леоне")');
            $this->addSql('INSERT INTO "country_translation" VALUES(419,"SG","en","name","Singapore")');
            $this->addSql('INSERT INTO "country_translation" VALUES(420,"SG","ru","name","Сингапур")');
            $this->addSql('INSERT INTO "country_translation" VALUES(421,"SK","en","name","Slovakia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(422,"SK","ru","name","Словакия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(423,"SI","en","name","Slovenia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(424,"SI","ru","name","Словения")');
            $this->addSql('INSERT INTO "country_translation" VALUES(425,"SB","en","name","Solomon Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(426,"SB","ru","name","Соломоновы Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(427,"SO","en","name","Somalia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(428,"SO","ru","name","Сомали")');
            $this->addSql('INSERT INTO "country_translation" VALUES(429,"ZA","en","name","South Africa")');
            $this->addSql('INSERT INTO "country_translation" VALUES(430,"ZA","ru","name","Южная Африка")');
            $this->addSql('INSERT INTO "country_translation" VALUES(431,"GS","en","name","South Georgia and the South Sandwich Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(432,"GS","ru","name","Южная Джорджия и Южные Сандвичевы Острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(433,"KR","en","name","South Korea")');
            $this->addSql('INSERT INTO "country_translation" VALUES(434,"KR","ru","name","Республика Корея")');
            $this->addSql('INSERT INTO "country_translation" VALUES(435,"ES","en","name","Spain")');
            $this->addSql('INSERT INTO "country_translation" VALUES(436,"ES","ru","name","Испания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(437,"LK","en","name","Sri Lanka")');
            $this->addSql('INSERT INTO "country_translation" VALUES(438,"LK","ru","name","Шри-Ланка")');
            $this->addSql('INSERT INTO "country_translation" VALUES(439,"SD","en","name","Sudan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(440,"SD","ru","name","Судан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(441,"SR","en","name","Suriname")');
            $this->addSql('INSERT INTO "country_translation" VALUES(442,"SR","ru","name","Суринам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(443,"SJ","en","name","Svalbard and Jan Mayen")');
            $this->addSql('INSERT INTO "country_translation" VALUES(444,"SJ","ru","name","Свальбард и Ян-Майен")');
            $this->addSql('INSERT INTO "country_translation" VALUES(445,"SZ","en","name","Swaziland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(446,"SZ","ru","name","Свазиленд")');
            $this->addSql('INSERT INTO "country_translation" VALUES(447,"SE","en","name","Sweden")');
            $this->addSql('INSERT INTO "country_translation" VALUES(448,"SE","ru","name","Швеция")');
            $this->addSql('INSERT INTO "country_translation" VALUES(449,"CH","en","name","Switzerland")');
            $this->addSql('INSERT INTO "country_translation" VALUES(450,"CH","ru","name","Швейцария")');
            $this->addSql('INSERT INTO "country_translation" VALUES(451,"SY","en","name","Syria")');
            $this->addSql('INSERT INTO "country_translation" VALUES(452,"SY","ru","name","Сирийская Арабская Республика")');
            $this->addSql('INSERT INTO "country_translation" VALUES(453,"ST","en","name","São Tomé and Príncipe")');
            $this->addSql('INSERT INTO "country_translation" VALUES(454,"ST","ru","name","Сан-Томе и Принсипи")');
            $this->addSql('INSERT INTO "country_translation" VALUES(455,"TW","en","name","Taiwan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(456,"TW","ru","name","Тайвань")');
            $this->addSql('INSERT INTO "country_translation" VALUES(457,"TJ","en","name","Tajikistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(458,"TJ","ru","name","Таджикистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(459,"TZ","en","name","Tanzania")');
            $this->addSql('INSERT INTO "country_translation" VALUES(460,"TZ","ru","name","Танзания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(461,"TH","en","name","Thailand")');
            $this->addSql('INSERT INTO "country_translation" VALUES(462,"TH","ru","name","Таиланд")');
            $this->addSql('INSERT INTO "country_translation" VALUES(463,"TL","en","name","Timor-Leste")');
            $this->addSql('INSERT INTO "country_translation" VALUES(464,"TL","ru","name","Восточный Тимор")');
            $this->addSql('INSERT INTO "country_translation" VALUES(465,"TG","en","name","Togo")');
            $this->addSql('INSERT INTO "country_translation" VALUES(466,"TG","ru","name","Того")');
            $this->addSql('INSERT INTO "country_translation" VALUES(467,"TK","en","name","Tokelau")');
            $this->addSql('INSERT INTO "country_translation" VALUES(468,"TK","ru","name","Токелау")');
            $this->addSql('INSERT INTO "country_translation" VALUES(469,"TO","en","name","Tonga")');
            $this->addSql('INSERT INTO "country_translation" VALUES(470,"TO","ru","name","Тонга")');
            $this->addSql('INSERT INTO "country_translation" VALUES(471,"TT","en","name","Trinidad and Tobago")');
            $this->addSql('INSERT INTO "country_translation" VALUES(472,"TT","ru","name","Тринидад и Тобаго")');
            $this->addSql('INSERT INTO "country_translation" VALUES(473,"TN","en","name","Tunisia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(474,"TN","ru","name","Тунис")');
            $this->addSql('INSERT INTO "country_translation" VALUES(475,"TR","en","name","Turkey")');
            $this->addSql('INSERT INTO "country_translation" VALUES(476,"TR","ru","name","Турция")');
            $this->addSql('INSERT INTO "country_translation" VALUES(477,"TM","en","name","Turkmenistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(478,"TM","ru","name","Туркменистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(479,"TC","en","name","Turks and Caicos Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(480,"TC","ru","name","Острова Тёркс и Кайкос")');
            $this->addSql('INSERT INTO "country_translation" VALUES(481,"TV","en","name","Tuvalu")');
            $this->addSql('INSERT INTO "country_translation" VALUES(482,"TV","ru","name","Тувалу")');
            $this->addSql('INSERT INTO "country_translation" VALUES(483,"UM","en","name","U.S. Minor Outlying Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(484,"UM","ru","name","Внешние малые острова (США)")');
            $this->addSql('INSERT INTO "country_translation" VALUES(485,"PU","en","name","U.S. Miscellaneous Pacific Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(486,"PU","ru","name","Малые отдаленные острова Соединенных Штатов")');
            $this->addSql('INSERT INTO "country_translation" VALUES(487,"VI","en","name","U.S. Virgin Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(488,"VI","ru","name","Американские Виргинские острова")');
            $this->addSql('INSERT INTO "country_translation" VALUES(489,"UG","en","name","Uganda")');
            $this->addSql('INSERT INTO "country_translation" VALUES(490,"UG","ru","name","Уганда")');
            $this->addSql('INSERT INTO "country_translation" VALUES(491,"UA","en","name","Ukraine")');
            $this->addSql('INSERT INTO "country_translation" VALUES(492,"UA","ru","name","Украина")');
            $this->addSql('INSERT INTO "country_translation" VALUES(493,"SU","en","name","Union of Soviet Socialist Republics")');
            $this->addSql('INSERT INTO "country_translation" VALUES(494,"SU","ru","name","СССР")');
            $this->addSql('INSERT INTO "country_translation" VALUES(495,"AE","en","name","United Arab Emirates")');
            $this->addSql('INSERT INTO "country_translation" VALUES(496,"AE","ru","name","Объединенные Арабские Эмираты")');
            $this->addSql('INSERT INTO "country_translation" VALUES(497,"GB","en","name","United Kingdom")');
            $this->addSql('INSERT INTO "country_translation" VALUES(498,"GB","ru","name","Великобритания")');
            $this->addSql('INSERT INTO "country_translation" VALUES(499,"US","en","name","United States")');
            $this->addSql('INSERT INTO "country_translation" VALUES(500,"US","ru","name","США")');
            $this->addSql('INSERT INTO "country_translation" VALUES(501,"ZZ","en","name","Unknown or Invalid Region")');
            $this->addSql('INSERT INTO "country_translation" VALUES(502,"ZZ","ru","name","Неизвестный или недействительный регион")');
            $this->addSql('INSERT INTO "country_translation" VALUES(503,"UY","en","name","Uruguay")');
            $this->addSql('INSERT INTO "country_translation" VALUES(504,"UY","ru","name","Уругвай")');
            $this->addSql('INSERT INTO "country_translation" VALUES(505,"UZ","en","name","Uzbekistan")');
            $this->addSql('INSERT INTO "country_translation" VALUES(506,"UZ","ru","name","Узбекистан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(507,"VU","en","name","Vanuatu")');
            $this->addSql('INSERT INTO "country_translation" VALUES(508,"VU","ru","name","Вануату")');
            $this->addSql('INSERT INTO "country_translation" VALUES(509,"VA","en","name","Vatican City")');
            $this->addSql('INSERT INTO "country_translation" VALUES(510,"VA","ru","name","Ватикан")');
            $this->addSql('INSERT INTO "country_translation" VALUES(511,"VE","en","name","Venezuela")');
            $this->addSql('INSERT INTO "country_translation" VALUES(512,"VE","ru","name","Венесуэла")');
            $this->addSql('INSERT INTO "country_translation" VALUES(513,"VN","en","name","Vietnam")');
            $this->addSql('INSERT INTO "country_translation" VALUES(514,"VN","ru","name","Вьетнам")');
            $this->addSql('INSERT INTO "country_translation" VALUES(515,"WK","en","name","Wake Island")');
            $this->addSql('INSERT INTO "country_translation" VALUES(516,"WK","ru","name","Уэйк")');
            $this->addSql('INSERT INTO "country_translation" VALUES(517,"WF","en","name","Wallis and Futuna")');
            $this->addSql('INSERT INTO "country_translation" VALUES(518,"WF","ru","name","Уоллис и Футуна")');
            $this->addSql('INSERT INTO "country_translation" VALUES(519,"EH","en","name","Western Sahara")');
            $this->addSql('INSERT INTO "country_translation" VALUES(520,"EH","ru","name","Западная Сахара")');
            $this->addSql('INSERT INTO "country_translation" VALUES(521,"YE","en","name","Yemen")');
            $this->addSql('INSERT INTO "country_translation" VALUES(522,"YE","ru","name","Йемен")');
            $this->addSql('INSERT INTO "country_translation" VALUES(523,"ZM","en","name","Zambia")');
            $this->addSql('INSERT INTO "country_translation" VALUES(524,"ZM","ru","name","Замбия")');
            $this->addSql('INSERT INTO "country_translation" VALUES(525,"ZW","en","name","Zimbabwe")');
            $this->addSql('INSERT INTO "country_translation" VALUES(526,"ZW","ru","name","Зимбабве")');
            $this->addSql('INSERT INTO "country_translation" VALUES(527,"AX","en","name","Åland Islands")');
            $this->addSql('INSERT INTO "country_translation" VALUES(528,"AX","ru","name","Аландские острова")');
    }

    protected function addDataStorage()
    {
        $this->addSql('INSERT INTO "storage" VALUES(1,"Local","Storage on local computer","folder","'.$this->getUserHomeDir().'Videos'.DIRECTORY_SEPARATOR.'")');
        // add sequence
        $this->addSql('INSERT INTO "sqlite_sequence" VALUES("storage",1)');
    }

    protected function addDataItem()
    {
        // default path for store items
        $path = $this->getUserHomeDir().'Videos'.DIRECTORY_SEPARATOR;

        $this->addSql('INSERT INTO "item" VALUES(1,"tv","JP",1,"Ван-Пис","1999-10-20",NULL,25,"Последние слова, произнесенные Королем Пиратов перед казнью, вдохновили многих: «Мои сокровища? Коли хотите, забирайте. Ищите – я их все оставил там!». Легендарная фраза Золотого Роджера ознаменовала начало Великой Эры Пиратов – тысячи людей в погоне за своими мечтами отправились на Гранд Лайн, самое опасное место в мире, желая стать обладателями мифических сокровищ... Но с каждым годом романтиков становилось все меньше, их постепенно вытесняли прагматичные пираты-разбойники, которым награбленное добро было куда ближе, чем какие-то «никчемные мечты». Но вот, одним прекрасным днем, семнадцатилетний Монки Д. Луффи исполнил заветную мечту детства - отправился в море. Его цель - ни много, ни мало стать новым Королем Пиратов. За достаточно короткий срок юному капитану удается собрать команду, состоящую из не менее амбициозных искателей приключений. И пусть ими движут совершенно разные устремления, главное, этим ребятам важны не столько деньги и слава, сколько куда более ценное – принципы и верность друзьям. И еще – служение Мечте. Что ж, пока по Гранд Лайн плавают такие люди, Великая Эра Пиратов всегда будет с нами!","'.$path.'One Piece (2011) [TV]",\'Первый сезон (эп. 1-61)
Второй сезон (эп. 62-77)
Третий сезон (эп. 78-92)
Четвёртый сезон (эп. 93-130)
Пятый сезон (эп. 131-143)
Шестой сезон (эп. 144-195)
Седьмой сезон (эп. 196-228)
Восьмой сезон (эп. 229-263)
Девятый сезон (эп. 264-336)
Десятый сезон (эп. 337-381)
382. Noro Noro Menace - Return of Foxy the Silver Fox
383. The Great Treasure Contest! Collapse! The Spyland
384. Brooks Hard Struggle - The Difficult Path to Becoming a True Nakama
385. Arriving at Halfway Through the Grand Line! The Red Line
386. Hatred of the Straw Hat Crew - Enter Iron Mask Duval
387. The Fated Reunion! Save the Imprisoned Fishman
388. Tragedy! The Truth of the Unmasked Duval
389. Explosion! The Sunnys Super Secret Weapon: Gaon Cannon
390. Landing to Get to Fishman Island - The Sabaody Archipelago
391. Tyranny! The Rulers of Sabaody, the Celestial Dragons
392. New Rivals Gather! The 11 Supernovas
393. The Target is Caimie!! The Kidnappers Evil Draws Near
394. Rescue Caimie - The Dark History of the Archipelago
395. Time Limit - The Human Auction Begins
396. The Exploding Fist! Destroy the Auction
397. Huge Panic! Struggle in the Auction Hall
398. Admiral Kizaru Moves! The Sabaody Archipelago in Chaos
399. Break Through the Encirclement! Marines vs. Three Captains
400. Roger and Rayleigh - The Pirate King and His Right Hand
401. Impossible to Avoid!? Admiral Kizarus Speed of Light Kick
402. Overwhelming! The Marine Combat Weapon Pacifista
403. Another Strong Enemy Appears! Broadaxe-Wielding Sentomaru
404. Admiral Kizarus Fierce Attack. The Straw Hat Crews Desperate Situation!
405. Disappearing Crewmates - The Final Day of the Straw Hat Crew
406. Special Historical Arc - Boss Luffy Appears Again
407. Special Historical Arc - Destroy! Thriller Companys Trap
408. Landing! Young Men Forbidden Island Amazon Lily
409. Hurry! Back to the Crew - Adventure on the Isle of Women
410. Everyones in Love! Pirate Empress Hancock
411. The Secret Hidden on Their Backs - Luffy Encounters the Snake Princess
412. The Heartless Judgment! Margaret Turned to Stone!
413. The Power of the Snake Sisters Haki!!
414. Battle with Full-Powered Abilities! Gomu Gomu vs. Hebi Hebi
415. Hancocks Confession - The Sisters Disgusting Past
416. Rescue Ace! The New Destination is the Great Prison
417. Love is a Hurricane! Hancock Madly in Love
418. The Nakamas whereabouts: Weather Science and Karakuri Island!
419. The Nakamas whereabouts: The Island of huge Birds and the pink Garden!
420. The Nakamas whereabouts: The island-connecting Bridge and man-eating Plants!
421. The Nakamas whereabouts: The Negative Princess and the Demon King
422. A Life-threatening Break-in! Breaking Into the Underwater Prison Impel Down!
423. Reunion in Hell!? The User of the Bara Bara Fruit!
424. Break Through! Crimson Hell - Buggys Big Flashy Plan
425. The Strongest Man in the Prison! Enter Poison Man Magellan
426. Special Linked to the Movie - The Golden Lions Ambitions Start to Move
427. Special Linked to the Movie - Little East Blue Targeted
428. Special Linked to the Movie - Fierce Attack of the Amigo Pirates
429. Special Linked to the Movie - Decisive Battle! Luffy vs. Ralgo
430. The Imprisoned Shichibukai! Jimbei, Boss of the Sea
431. The Trap of Jailer Saldeath - Level 3 Starvation Hell
432. The Liberated Swan! Reunion! Bon Clay
433. Chief Warden Magellan Moves - The Net to Trap Straw Hat Is Complete!
434. Preparations for War! A Decisive Battle in Level 4 - Inferno Hell
435. Magellans Strength! Bon Clay Flees Before His Enemy
436. A Friend Decision! Luffys Final Life-Risking Attack
437. Because Hes Our Friend - Bon Clays Do-or-Die Rescue
438. A Paradise in Hell! Impel Down - Level 5.5!
439. Luffys Treatment Begins! Ivans Miraculous Ability
440. Believe in Miracles! Bon Clay Cheers From His Heart
441. Luffy Revives! Ivans Jailbreak Plan Begins!!
442. Aces Convoy Begins - The Defenses of the Lowest Level, Level 6!
443. The Strongest Team is Formed - Shake Impel Down to Its Core!
444. Even More Chaos! Blackbeard Teach Invades!
445. A Dangerous Meeting! Blackbeard and Shiryu of the Rain
446. His Spirit Won`t Break! Hannyabal Goes All Out
447. The Jet Pistol of Rage - Luffy vs. Blackbeard
448. Stop Magellan! Ivan-san Unleashes His Secret Attack
449. Magellans Scheme! The Jailbreak is Obstructed
450. A Hopeless Situation for the Escapees - Forbidden Technique "Venom Demon"
451. Produce One Last Miracle - Break through the Gates of Justice
452. Destination Navy HQ - The Voyage to Rescue Ace!
453. The Crews Whereabouts - Weatheria Report and the Cyborg Animal
454. The Crews Whereabouts - Giant Bird Chicks and a Pink Showdown
455. The Crews Whereabouts - The Revolutionary Army and the Trap of the Forest of Gluttony!
456. The Crews Whereabouts - The Giant Gravestone and Panties of Gratitude
457. Flashback Special Before Marineford - The Brothers Oath!
458. A Special Retrospective Before Marineford! Assemble! The Three Admirals
459. The Time of the Decisive Battle Draws Near! The Navys Strongest Battle Formation Is Ready!
460. An Enormous Fleet Appears - The Whitebeard Pirates Invade!
461. The Beginning of the War! Ace and Whitebeards Past
462. The Power to Destroy the World! The Ability of the Quake Quake Fruit
463. An All-Consuming Inferno!! Admiral Akainus Power!
464. Descendant of the Devil! Little Oars Jr. Rushes!
465. The Winner Will be Justice - Sengokus Strategy Is Put Into Action!
466. The Straw Hat Team Arrives - The Battlefield Grows More Intense
467. Ill Save You Even If I Die - The Battle Between Luffy and the Navy Begins
468. Consecutive Battles! An Army of Devil Fruit Users vs. An Army of Devil Fruit Users
469. The Change That Occurred in Kuma - Ivan-sans Angry Attack
470. Master Swordsman Mihawk - The Black Swords Slash Draws Near Luffy
471. Annihilation Strategy Starts - Power of Pacifista Corps
472. Akainus Stratagem! - Framed Whitebead
473. Siege Strategy Woks! - Whitebeard Pirates in Crisis!!
474. Execution Order Issued - Break Through the Siege!
475. Rush Into the Final Phase! Whitebeards Maneuver to Turn the Tides
476. Luffys Brute Force! All-Out War in the Oris Plaza!!
477. Power that Reduces Ones Life - Tension Hormones Return
478. For a Promise!! Luffy and Coby Clash!
479. The Execution Platform Within Reach! The Path To Ace Is Opened!!
480. The Different Paths They Chose - Luffy vs Garp
481. Ace Rescued! Whitebeards Final Order!
482. A Power That Burns Even Fire - Akainus Fierce Assault
483. Searching for an Answer - Fire Fist Ace Dies on the Battlefield
484. Marine Headquarters Crumbles! Whitebeards Silent Rage!
485. Settling the Score - Whitebeard vs. The Blackbeard Pirates
486. The Start of the Show - Blackbeards Plot Revealed
487. The Insatiable Akainu! Lava Fists Pummel Luffy!
488. A Desperate Cry - Seconds of Valor that Change Destiny
489. Enter Shanks! The Ultimate War Ends at Last
490. Powerful Independent Rivals! The Beginning of the "New Era"!
491. Arrival at the Island of Women - Cruel Reality Tortures Luffy
492. The Strongest Tag-Team! Luffy and Torikos Hard Struggle!
493. Luffy and Ace! The Story of How the Brothers Met!
494. Enter Sabo! The Boy From the Grey Terminal
495. I Won Run - Aces Do-or-Die Rescue Operation
496. One Day Well Go Out to Sea! The Oath Cups of the Three Brats!
497. Leaving the Dadan Family!? The Secret Base is Complete!
498. Apprentice Luffy!? The Man who Fought the Pirate King!
499. The Battle Against the Big Tiger! Who Will Be the Captain?!
500. Stolen Freedom! The Nobles Trap Draws Near the Three Brothers
501. The Flames Are Lit - The Gray Terminals Crisis
502. Where is Freedom? The Sad Departure of a Boy on a Ship
503. Im Counting On You! A Letter From a Brother!
504. To Fulfill the Promise - Separate Departures!
505. I Want to See Them! Luffy`s Tearful Scream
506. The Straw Hat Crew Shocked! The Bad News are Received
507. Reunion with Dark King Rayleigh - Luffy`s Decision
508. Back to Our Captain! A Jail Break at the Sky Island and the Incident on the Winter Island!
509. Encounter! The Great Swordsman Mihawk! Zoro`s Self-Willed Deadly Struggle!
510. Sanji`s Suffering - The Queen Returns to His Kingdom!
511. Unexpected Relanding! Luffy, to Marineford!
512. With Hopes It Will Reach My Friends! Big News Spreading Fast!
513. Pirates Get on the Move! Astounding New World!
514. Live Through Hell - Sanji`s Fight with Men at Stake
515. I Will Get Much, Much Stronger! Zoro`s Pledge to His Captain!
516. Luffy`s Training Begins! To the Place We Promised in 2 Years!
517. A New Chapter Begins - The Straw Hat Crew Reunites!
518. An Explosive Situation! Luffy vs. Fake Luffy!
519. The Navy Has Set Out! The Straw Hats in Danger!
520. Big Guns Assembled! The Danger of the Fake Straw Hats!
521. The Battle Begins! Showing the Results of Training!
522. All Aboard Luffy Sets Sail for the New World
523. A Shocking Revelation - The Man Who Protected the Sunny
524. Undersea Struggle, The Demon of the Deep Appears!
525. Disaster in the Deep Sea, The Straw Hat Crew Gets Lost!
526. Undersea Volcanic Eruption! Sailing To Fishman Island
527. Landing at Fishman Island - The Lovely Mermaids
528. Eruption of Excitement! Sanji`s Crisis of Life!
529. Fishman Island Collapses!? Shirley`s Prediction
530. The King of Fishman Island, Sea God Neptune!
531. Ryugu Palace! Led By The Shark That Was Saved!
532. The Timid Crybaby! The Mermaid Princess of the Hard Shell Tower!
533. State of Emergency! Ryugu Palace Taken Over!
534. The Ryugu Palace Shakeup! Shirahoshi`s Abduction Incident!
535. Hordy`s Invasion! The Beginning of the Plan for Revenge!
536. The Ryugu Palace Fight! Zoro vs. Hordy!
537. Keep Shirahoshi Safe! Decken Close Behind!
538. The Straw Hats Defeated?! Hordy Gains Control of the Ryugu Palace!
539. The Haunting Ties! Nami and the Fish-man Pirates!
540. A Hero Who Freed the Slaves! An Adventurer Tiger!
541. Kizaru Appears! A Trap to Catch Tiger!
542. Team Formation! Rescue Chopper
543. The Death of the Hero! A Shocking Truth of Tiger!
544. The Sun Pirates Split! Jimbei vs. Arlong!
545. Shaking Fish-man Island! A Celestial Dragon Drifts in
546. A Sudden Tragedy! A Gunshot Shuts Down the Future!
547. To the Present Once More! Hody Begins to Move
548. The Kingdom is Violently Shaken! Neptune`s Execution is Ordered
549. A Crack is Caused! Luffy Vs. Jinbe
550. Hody`s Accident. The True Power of the Evil Drug!
551. The Decisive Battle Begins at Gyoncorde Plaza!
552. Shock Confession. The Truth of Otohime`s Assassination
553. Shirahoshi`s Tears! Luffy Finally Shows Up!
554. Big Clash! Straw Hat Crew VS 100,000 Enemies
555. Explosive Move! Zoro and Sanji`s Sortie!
556. Premiere! The Sunny`s Secret Weapon!
557. Iron Pirate! Entry of Franky Shogun
558. Noah Approaching! The Crisis of Fishman Island`s Destruction!
559. Hurry Up, Luffy! Shirahoshi`s Desperate Situation
560. The Battle Begins! Luffy Vs. Hody!
561. A Massive Confused Fight! The Straw Hats vs. The New Fish-Man Pirates!
562. Luffy Loses the Fight?! Hordy`s Long Awaited Revenge!
563. A Shocking Fact! The True Identity of Hordy!
564. Back to Zero! Ernest Wishes for Luffy!
565. Luffy`s All-out Attack! Red Hawk Blasts!
566. Coming to an End! The Final Decisive Battle against Hordy
567. Stop, Noah! Desperate Elephant Gantling!
568. To the Future! The Path to the Sun!
569. The Secret Revealed! The Truth About the Ancient Weapon!
570. The Straw Hats Stunned! The New Fleet Admiral of the Navy!
571. She Loves Sweets! Big Mam of the Four Emperors!
572. Many Problems Lie Ahead! A Trap Awaiting in the New World!
573. Finally Time to Go! Goodbye, Fish-Man Island!
574. To the New World! Heading for the Ultimate Sea
575. Z`s Ambition! Lily the Little Giant!
576. Z`s Ambition! A Dark and Powerful Army!
577. Z`s Ambition! A Great and Desperate Escape Plan!
578. Z`s Ambition! Luffy vs. Shuzo!
Спэшлы:
01. Luffy`s Fall! The Unexplored Region - Grand Adventure in the Ocean`s Navel (50 мин, 20.12.2000)
02. Open Upon The Great Sea! - A Father`s huge, Huge Dream! (45 мин, 06.04.2003)
03. Protect! The Last Great Performance (45 мин, 14.12.2003)
04. The Detective Memoirs of Boss Straw Hat Luffy (40 мин, 18.12.2005)
05. Episode of Nami: Tears of a Navigator and the Bonds of Friends (110 мин, 25.08.2012)
06. Episode of Luffy: The Hand Island Adventure (130 мин, 15.12.2012)\',"602+",NULL,"+ 6 спэшлов","example/one-piece.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(2,"tv","JP",1,"Самурай Чамплу","2004-05-20","2005-03-19",25,"Потеряв маму, юная Фуу год проработала в чайной, а потом решила отправиться на поиски человека, который, кажется, виновен во всех её несчастьях. У Фуу была надёжная примета: это самурай, пахнущий подсолнухами. Но как выжить в Японии эпохи Эдо, когда за каждым поворотом – бандиты, которые могут тебя похитить и продать в бордель, а единственный друг – ручная белка-летяга? Фуу повезло: она встретила двух юных и при этом весьма сноровистых бойцов – бывшего пирата Мугэна и ронина Дзина. Заручившись их поддержкой, девушка отправилась в путь через всю страну. Не важно, что в животе всё время бурчит, и нет ни денег, ни документов – зато есть несравненные способности ввязываться в неприятности! При первой встрече Мугэн и Дзин попытались выяснить, кто из них круче – и они готовы продолжить дуэль при первой возможности, однако главная проблема в том, что у каждого из путешественников своё прошлое и опасные враги, о которых они даже не подозревают. И неизвестно ещё, у кого этих врагов и старых грехов больше – у пирата, грабившего корабли, у ронина, убившего своего учителя, или у девушки-сиротки?
Автор знаменитого Cowboy Bebop Синъитиро Ватанабэ смешал стильный коктейль из катан и хип-хопа. В его сериале прошлое сталкивается с будущим, Восток – с Западом, герои классического кино – с реальными историческими персонажами. Но все эти забористые ингредиенты лишь оттеняют историю о трёх разных людях, которых свела и сроднила долгая дорога...","'.$path.'Samurai Champloo (2004) [TV]","1. Tempestuous Temperaments (20.05.2004, 25 мин.)
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
26. Evanescent Encounter (Part 3) (19.03.2005, 25 мин.)","26",NULL,NULL,"example/samurai-champloo.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(3,"tv","JP",1,"Стальной алхимик","2003-10-04","2004-10-02",25,"Они нарушили основной закон алхимии и жестоко за это поплатились. И теперь два брата странствуют по миру в поисках загадочного философского камня, который поможет им исправить содеянное… Это мир, в котором вместо науки властвует магия, в котором люди способны управлять стихиями. Но у магии тоже есть законы, которым нужно следовать. В противном случае расплата будет жестокой и страшной. Два брата - Эдвард и Альфонс Элрики - пытаются совершить запретное: воскресить умершую мать. Однако закон равноценного обмена гласит: чтобы что-то получить, ты должен отдать нечто равноценное…","'.$path.'Fullmetal Alchemist (2003) [TV]","1. To Challenge the Sun (04.10.2003, 25 мин.)
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
51. Laws and Promises (02.10.2004, 25 мин.)","51",NULL,"+ спэшл","example/fullmetal-alchemist.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(4,"feature","JP",1,"Унесённые призраками","2001-07-20",NULL,125,"Маленькая Тихиро вместе с мамой и папой переезжают в новый дом. Заблудившись по дороге, они оказываются в странном пустынном городе, где их ждет великолепный пир. Родители с жадностью набрасываются на еду и к ужасу девочки превращаются в свиней, став пленниками злой колдуньи Юбабы, властительницы таинственного мира древних богов и могущественных духов. Теперь, оказавшись одна среди магических существ и загадочных видений, отважная Тихиро должна придумать, как избавить своих родителей от чар коварной старухи и спастись из пугающего царства призраков...","'.$path.'Spirited Away (2001)",NULL,"1",NULL,NULL,"example/spirited-away.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(5,"tv","JP",1,"Крутой учитель Онидзука","1999-06-30","2000-09-17",25,"Онидзука Эйкити («22 года, холост», - как он сам любит представляться) - настоящий ужас на двух колесах, член нагоняющей ужас на горожан банды мотоциклистов, решает переквалифицироваться в… школьного учителя. Ведь в любом учебном заведении полным-полно аппетитных старшеклассниц в коротеньких юбочках! Но чем глубже примеривший необычную роль хулиган окунается в перипетии общего образования, тем сильнее он пытается переиначить место работы на свой манер - одерживая одну за другой победы над царящими в школе косностью, лицемерием, показухой и безразличием.","'.$path.'GTO (1999) [TV]","1. GTO - The Legend Begins (30.06.1999, 45 мин.)
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
43. Onizuka`s Final Battle (17.09.2000, 25 мин.)","43",NULL,"+ 2 эп.-коллажа","example/gto.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(6,"tv","JP",1,"Бек","2004-10-07","2005-03-31",25,"В начале была Песня – так верят многие народы, и не зря музыка все так же объединяет нас спустя тысячелетия после начала писаной и неписаной истории. Бек – это аниме про молодых людей, ищущих свой жизненный путь, и про уже состоявшихся людей, которым музыка помогла и помогает в жизни. Бек – это аниме про универсальный язык, на котором могут разговаривать разные поколения. А еще это аниме про современное общество, в котором всплески таланта и искренние порывы души рано или поздно становятся частью глобальной индустрии развлечений. Можно спорить – хорошо это или плохо, но таков мир, в котором мы живем.
А вообще-то, Бек – это рассказ о простом японском парне, 14-летнем Юкио Танаке, который волею судьбы встретился с молодым гитаристом Рюскэ Минами и, благодаря таланту, силе духа, простому и открытому характеру, нашел свое место в жизни, обрел друзей и встретил любовь. Это рассказ о поиске путей самовыражения, на которых искренность и честность приносят радость, а злоба и лицемерие заводят в тупик. А еще это рассказ о встрече непростых людей, которые сумели создать и сохранить рок-группу, то самое целое, которое куда больше суммы слагаемых. Именно так и рождается настоящая музыка. Именно так вышло одно из лучших музыкальных аниме всех времен!","'.$path.'Beck (2004) [TV]","1. The View at Fourteen (07.10.2004, 25 мин.)
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
26. America (31.03.2005, 25 мин.)","26",NULL,NULL,"example/beck.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(7,"ova","JP",1,"Бродяга Кэнсин","1999-02-20","1999-09-22",30,"XIX век, Японию раздирает клановая вражда. Маленький Синта в детстве был продан работорговцам и попал вместе с другими в засаду - всех спутников мальчика на его глазах закололи, его же спас случайно проходивший мимо воин, мастерски владеющий мечом. Синта поступает к нему в ученики и становится мастером меча по имени Кэнсин. Парень выбирает жизненный путь убийцы экстра-класса. В одной из операций он встречает таинственную девушку Томоэ, которая видит Кэнсина в действии. Привыкший не оставлять свидетелей, самурай не убивает девушку, а забирает её с собой. Что-то дрогнуло у него в душе при виде Томоэ, возможно, она смягчит этого смелого, но холодного человека?","'.$path.'Samurai X - Trust Betrayal (1999) [OVA]","1. The Man of the Slashing Sword (20.02.1999, 30 мин.)
2. The Lost Cat (21.04.1999, 30 мин.)
3. The Previous Night at the Mountain Home (19.06.1999, 30 мин.)
4. The Cross-Shaped Wound (22.09.1999, 30 мин.)","4",NULL,NULL,"example/samurai-x-trust-betrayal.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(8,"feature","JP",1,"Мой сосед Тоторо","1988-04-16",NULL,88,"Япония, пятидесятые годы прошлого века. Переехав в деревню, две маленькие сестры Сацуки (старшая) и Мэй (младшая) глубоко внутри дерева обнаружили необыкновенный, чудесный мир, населённый Тоторо, очаровательными пушистыми созданиями, с которыми у девочек сразу же завязалась дружба. Одни из них большие, другие совсем крохотные, но у всех у них огромное, доброе сердце и магические способности совершать необыкновенные вещи, наподобие полётов над горами или взращивания огромного дерева за одну ночь! Но увидеть этих существ могут лишь дети, которые им приглянутся... Подружившись с сёстрами, Тоторо не только устраивают им воздушную экскурсию по своим владениям, но и помогают Мэй повидаться с лежащей в больнице мамой.","'.$path.'Tonari no Totoro (1988)",NULL,"1",NULL,NULL,"example/tonari-no-totoro.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(9,"ova","JP",1,"Хеллсинг","2006-02-10","2012-12-26",50,"На каждое действие найдётся противодействие – для борьбы с кровожадной нечистью в Великобритании был создан Королевский Орден Протестантских Рыцарей, которому служит древнейший вампир Алукард. Согласно заключённому договору, он подчиняется главе тайной организации «Хеллсинг».
У Ватикана свой козырь – особый Тринадцатый Отдел, организация «Искариот», в составе которой неубиваемый отец Александр. Для них Алукард ничем не отличается от остальных монстров.
Однако всем им придётся на время забыть о дрязгах между католической и англиканской церквями, когда на сцену выйдет могущественный враг из прошлого – загадочный Майор во главе секретной нацистской организации «Миллениум».
Но пока не началась битва за Англию, Алукард занят воспитанием новообращённой вампирши: Виктория Серас раньше служила в полиции, а теперь ей приходится привыкать к жизни в старинном особняке, к своим новым способностям и новым обязанностям. Даже хозяйка Алукарда, леди Интегра, не знает, зачем он обратил эту упрямую девушку...
Вторая экранизация манги Хирано Кота дотошно следует оригиналу, и потому заметно отличается от сериала, ведь именно чёрный юмор, реки крови, харизматичные враги и закрученный конфликт сделали «Хеллсинга» всемирно популярным.","'.$path.'Hellsing (2006) [OVA]","1. Hellsing I (10.02.2006, 50 мин.)
2. Hellsing II (25.08.2006, 45 мин.)
3. Hellsing III (04.04.2007, 50 мин.)
4. Hellsing IV (22.02.2008, 55 мин.)
5. Hellsing V (21.11.2008, 40 мин.)
6. Hellsing VI (24.07.2009, 40 мин.)
7. Hellsing VII (23.12.2009, 45 мин.)
8. Hellsing VIII (27.07.2011, 50 мин.)
9. Hellsing IX (15.02.2012, 45 мин.)
10. Hellsing X (26.12.2012, 65 мин.)","10",NULL,"+ 4 спэшла","example/hellsing.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(10,"tv","JP",1,"Гинтама","2006-04-04","2010-03-25",25,"Абсурдистская фантастическо-самурайская пародийная комедия о приключениях фрилансеров в псевдо-средневековом Эдо. Захватив Землю, пришельцы Аманто запретили ношение мечей, единственный, в ком ещё жив подлинно японский дух – самоуверенный сластёна Гинтоки Саката. Неуклюжий очкарик Симпати нанялся к нему в ученики. Третьим в их команде стала прелестная Кагура из сильнейшей во вселенной семьи Ятудзоку, а с ней её питомец Садахару – пёсик размером с бегемота, обладающий забавной привычкой грызть головы всем, кто под морду подвернётся. Они называют себя «мастерами на все руки» и выполняют любые заказы – главное, чтобы заплатили.
Кроме инопланетян с ушками, бандитов со шрамами, самураев с бокэнами, девушек-ниндзя с натто и странных существ, в «Гинтаме» встречаются также Синсэнгуми, состоящие из придурковатых юношей в европейской одежде. Высмеиванию подвергается множество штампов, пародируется «Блич», «Ковбой Бибоп» и многие другие известные сериалы. Юмор колеблется от «сортирного» до «тонкой иронии», в целом это весьма «зубастая» комедия, лишённая каких-либо рамок и ограничений.","'.$path.'Gintama (2006) [TV-1]",\'001. When seal is stamped, contents are confirmed
002. What is extraterritoriality law?
003. Natural perms are detestable things
004. This magazine appears on Saturday depending on the mood
005. Becoming an old man, making friends and calling them by nicknames
006. A promise to die and protect
007. A pet is the responsibility of the owner to look after until the end
008. Not making a good first impression...
009. There is a paper-thin difference between toughness and vengefulness
010. It`s a sour thing when you`re tired
011. Gooey messy sweet dumpling... What...? It`s not a dumpling...? That bastard...!
012. Making a bad first impression...
013. If cosplaying, cosplay to adorn the heart
014. There`s a weird rule guys have... That touching a frog means coming of age
015. An owner and his pet are alike
016. Life gets longer when Mr Otae becomes longer! Scary!!
017. Father And Son Always Share Their Negative Qualities
018. Ah! Home`s where the heart is
019. Why is the sea salty? Probably because you city folks use it as a toliet!!
020. Beware of the conveyor belt!
021. If you`re a guy at least Kajiki! / If you keep the fan on while you`re sleeping, you`ll get a stomachache, so watch out!
022. Marriage means one continues raising misunderstanding
023. When being troubled, burst out laughing
024. Something is hiding by all means in this cute face
025. The pot is the reduced drawing of life
026. Don`t be shy, raise your hand and speak up
027. There are some swords which cannot be cut
028. Comparatively, a bad thing that happens is that good things doensn`t happen continuously
029. You are flurried! Cool off! / Unless you properly see the television or the newspaper, it`s useless
030. Even Idols Do Pretty Much The Same Things You Guys Do
031. Even you do not easily forget how to be good
032. Life flows like the conveyer
033. It is impolite to mistake a person`s name
034. There is no need for a manual on love
035. You should not judge a person by his appearance
036. For a person who has an injury in the shank, you can really talk
037. Those fellows who complain that there is no Santa believe in his existence / A bell cannot make troubles disappear, you must do it yourself
038. Only kids get excited when it snows / it`s very strange to rub against the ice wich is eaten in the winter
039. Ramen shops which offer lots of menus are not usually popular
040. The plan is to make children
041. I do not know whether the movie is interesting just by the titles
042. Earthworms swell when you urinate on them
043. For the character, I will draw and divide the distinction that is attached to the reader with just the silhoutte
044. Please stop your complaints about the dinner menu because my mother is busy
045. My pet dog`s stroll is done at a moderate speed
046. You become 20 years old after you play in a cabaret
047. Cherry things become cherry trees?
048. The two who look alike are quarreling / Why do you use that useless negativity?
049. Life without gambles is like sushi without wasabi
050. Undecided is undecided and it`s not a decision
051. Milk should be in the temperature like human skin
052. Make an appointment first before meeting with someone
053. Stress will causes baldness but when paying too much attention on prevent stress the stress will builds up again in the end there is nothing that we can do to it
054. Mothers are the same no matter where they are
055. Do not make that “kucha kucha” sound when eating
056. Take note of the one-day director
057. You must go back to the day of action when you are looking for lost items
058. Korokke bread is always popular in shops
059. Do not forget where you left your umbrella
060. The sun will rise
061. Insects at night gather in the light
062. What you want is not what you get
063. The preview of the next edition of Jump is unreliable
064. Eating corn is unexpectedly very filling
065. The boy learns the value of life through the beetle
066. Substance over form
067. This life that continues to run
068. The cross-over world is full of idiots
069. Please separate your garbage
070. Cute things become disgusting when there are too many
071. There is data that cannot be deletable
072. Let`s drive there / Doggy meatballs have a fragrant smell
073. The mushrooms are delicious
074. The draft is ready
075. Work should not be split at home
076. Be quiet at this time
077. Yesterday`s enemy is also today`s enemy
078. A person who`s picky with food is also picky with humans
079. If there`s four people, it is a lot of knowledge
080. A person that wears glasses suddenly takes the glasses off, it feels like somethign is missing. Like the part of the person is missing
081. A woman`s best make up is the smile
082. It`s not like I line up for Ramen. I line up for satisfy myself
083. Luck and identity have no relationship
084. A Man`s heart is like a hard-boiled egg
085. Hard boiled egg won`t get crashed
086. There are many instances where you cannot get to sleep even after counting sheep
087. Use German surplexes on women who like to ask which is important, me or work
088. The start is always the happiest in joint parties
089. When there`s two, there will be three
090. It`s scary to eat the wrong food when it`s so delicious
091. If you want to slim down, go and exercise, do not eat
092. Become a person who is able to find a person`s merits rather than his weak points
093. Even heroes have their own problems
094. When you are sitting on the train, both hands must be strapped
095. Men are Madao
096. If you are a man, do not give up
097. Herioc stories of the past are exaggerated by three folds / Boys are weak towards girls from a flower shop or a cake shop
098. Game is an hour per day
099. Life as well as games are only bugs
100. Those who are disliked are always adorable
101. Law exists to be violated
102. Otakus are talkative
103. The differnce between stengths and weaknesses is only a thin line
104. Important things are hard to see
105. Anything depends on the beat and the timing
106. Most people I love sudden death
107. Parents, not knowing the child
108. Better leave it unsaid
109. Life is a test
110. Everyone is an escapee of a prison called `myself`
111. Definitely Do Not Let Your Girlfriend See the Things You Use for Crossdressing / There`s Almost a 100% Chance of Forgetting One`s Vinyl Umbrella and Then Hating Oneself
112. A birthday in your twenties has no deep meaning / You`re lucky if you can stay up late to work
113. The Act of Polishing a Urinal is Like the Act of Polishing One`s Heart / Subtitle Undecided
114. When sweet and spicy things are switched... / They say that adding soy sauce to pudding gives the taste of sea urchin, but really, adding soy sauce to pudding only gives the taste of soy sauce and pudding
115. The time just before the summer holidays start is the most fun
116. The Older, The Wiser
117. Beauty is like a summer fruit
118. Be as straightforward as your back is bent
119. Those who smoke one or two cigarettes are putting the smell of horse dung in them
120. The taste of overseas Japanese restaurants is generally at the same level as a school cafeteria / Once you`ve chosen a dish, you can`t give it back
121. Only a slotted and a crosshead screwdriver is needed for an amateur
122. Imagination should be developed in grade
123. There is a screwdriver in the every one`s hearts
124. Too much badgering can become a threatening one
125. Into the Final Chapter!
126. There are things that cannot be expressed in words
127. There are things you can`t understand unless you meet them
128. There are things you can`t understand even when you meet them
129. Watch out for your pet eating off the floor
130. Cat-lovers and dog-lovers never keep peace
131. Quarrels usually happen at the trip destination
132. Tight underpants will unavoidably get soiled
133. Gin and His Excellency`s Good For Nothing
134. Be Very Careful When Using Ghost Stories
135. Before Thinking About the Earth, Think About the More Endangered Gintaman`s Future!
136. It`s Your House, You Build It
137. 99% Of Men Are Not Confident In Confessing Their Love
138. Why Not Talk About the Old Days for a Change?
139. Don`t Put Your Wallet In Your Back Pocket
140. Beware of Those Who Use an Umbrella on a Sunny Day
141. Butting Into a Fight is Dangerous
142. Life is a Series of Choices
143. Those Who Stand On Four Legs Are Beasts. Those Who Stand on Two Legs, Guts, and Glory, Are Men
144. Don`t Trust Bedtime Stories
145. The Color for Each Person`s Bond Comes in Various Colors
146. The taste of drinking under broad daylight is something special
147. Every adult is every children`s instructor
148. Zippers should be undone slowly
149. When your half eaten popsicle starts sliding down the stick, that`s when jerks come around hoping for a share
150. Become bound by long things
151. A conversation with a barber during a haircut is the most pointless thing in the world
152. The heavens created Chomage above man instead of another man
153. Sleep Helps a Child Grow
154. That person looks different from usual during a birthday party
155. The other side of the other side of the other side would be the other side
156. It takes a bit of courage to enter a street vendor`s stand
157. Any place with a bunch of men gathered around will turn into a battlefield
158. If a friend gets injured, take him to the hospital
159. If one orange in the box is rotten, the rest of them will become rotten before you know it!
160. From a foreigner`s perspective, you`re the foreigner
161. Laputa`s still good after seeing it so many times
162. Love Asks For Nothing In Return
163. The black ships even make a scene when they sink!
164. That matsutake soup stuff tastes better than the real deal, plus one / Man cannot come back to life once dead
165. If It Works Once, It`ll Work Over And Over Again
166. Two Is Better Than One. Two People Are Better Than One
167. Smooth Polygons Smooth Men`s Hearts Too
168. A Human Body Is Like a Little Universe
169. The Chosen Idiots
170. And into the Legend...
171. If You Keep Copying, They Will Retaliate / A Loss Opens Your Eyes to the Love You Have
172. It All Depends on How You Use the "Carrot and Stick" Method
173. It`s What`s On the Inside that Counts / It`s What`s On the Inside that Counts, But Only to a Certain Extent
174. When a Person Is Trapped, Their Inner Door Opens
175. People of All Ages Hate The Dentist
176. Beginning the Countdown
177. A Spider at Night is a Bad Omen
178. Once a Spider`s Thred Has Entangled Something it`s Hard to Get it Off Again
179. It`s the Irresponsible One Who`s Scary When Pissed
180. The More Precious the Burden, the Heavier and More Difficult it is to Shoulder It
181. Watch Out For A Set of Women and A Drink
182. Screw Popularity Polls
183. Popularity Polls Can Burn in Hell
184. Popularity Polls Can...
185. Hometowns and Boobs are Best Thought From Afar / The Whole Peeing on a Bee Sting Is a Myth. You`ll Get Germs, So Don`t Do It!!
186. Beware of Foreshadows
187. It`s Goodbye Once a Flag is Set
188. An Observation Journal Should Be Seen Right Through To The Very End
189. Mobile Suit Gundam 30th Anniversary Special Extra #2 / Radio Exercises are Socials for Boys and Girls
190. When Looking For Something, Try Using its Perspective
191. Freedom Means to Live True to Yourself, not Without Law!
192. Kabukicho Stray Cat Blues
193. Cooking is About Guts
194. Whenever I hear Leviathan, I think of Sazae-san. Stupid me!!
195. Not Losing to the Rain!
196. Not Losing to the Wind
197. Not Losing to the Storm
198. Never Losing that Smile
199. That`s How I Wish to Be, Beautiful and Strong
200. Santa Claus Red is Blood Red!
201. Everybody`s a Santa!\',"201",NULL,NULL,"example/gintama.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(11,"tv","JP",1,"Бакуман.","2010-10-02","2011-04-02",25,"Хорошие школьные оценки – престижный вуз – крупная корпорация: вот жизненный план большинства японских юношей и девушек. Но в каждом поколении находятся упрямцы, готовые отринуть синицу в руках ради возможности сохранить индивидуальность и заняться любимым делом. Таковы юный художник Моритака Масиро и начинающий писатель Акито Такаги, которые пока оканчивают среднюю школу, но уже приняли непростое решение – посвятить жизнь созданию манги, уникального феномена японской культуры.
Герои сериала - фанаты манги, лауреаты юношеских конкурсов и знакомы с реалиями «взрослого» шоу-бизнеса, где наверх пробиваются единицы. Но когда еще рисковать, как не в 16 лет?! А тут Моритака, склонный к рефлексии, внезапно узнает, что его любимая и одноклассница, Михо Адзуки, хочет быть актрисой-сэйю, то есть работать по «смежной специальности». Будучи во власти эйфории, парень тут же предлагает девушке две вещи: сыграть когда-нибудь в аниме по их манге и… выйти за него замуж. Самое интересное, что Адзуки соглашается на то и другое – но в этой же строгой последовательности. Теперь творческому дуэту придется поставить на карту все – тяжкий труд, талант, потенциальную карьеру – и крепко верить в себя и свою удачу. Не попробуешь – не узнаешь, Драгонболл тоже не сразу строился!","'.$path.'Bakuman (2010) [TV-1]","1. Dream and Reality (02.10.2010, 25 мин.)
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
25. Yes and No (02.04.2011, 25 мин.)","25",NULL,NULL,"example/bakuman.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        $this->addSql('INSERT INTO "item" VALUES(12,"tv","JP",1,"Гуррен-Лаганн","2007-04-01","2007-09-30",25,"Сотни лет люди живут в глубоких пещерах, в постоянном страхе перед землетрясениями и обвалами. В одной из таких подземных деревень живет мальчик Симон и его «духовный наставник» — молодой парень Камина. Камина верит, что наверху есть другой мир, без стен и потолков; его мечта — попасть туда. Но его мечты остаются пустыми фантазиями, пока в один прекрасный день Симон случайно не находит дрель... вернее, ключ от странного железного лица в толще земли. В этот же день происходит землетрясение, и потолок пещеры рушится — так начинается поистине эпическое приключение Симона, Камины и их компаньонов в новом для них мире: мире под открытым небом огромной Вселенной.","'.$path.'Tengen Toppa Gurren Lagann (2007) [TV]","1. Pierce the Heavens with Your Drill! (01.04.2007, 25 мин.)
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
27. All the Lights in the Sky are Stars (30.09.2007, 25 мин.)","27",NULL,"+ 2 спэшла","example/tengen-toppa-gurren-lagann.jpg","2013-07-24 00:00:00","2013-07-24 00:00:00")');
        // add sequence
        $this->addSql('INSERT INTO "sqlite_sequence" VALUES("item",12)');
    }

    protected function getUserHomeDir() {
        if ($home = getenv('HOME')) {
            $last = substr($home, strlen($home), 1);
            if ($last == '/' || $last == '\\') {
                return $home;
            } else {
                return $home.DIRECTORY_SEPARATOR;
            }
        } elseif (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            return '/home/'.get_current_user().'/';
        } elseif (is_dir($win7path = 'C:\Users\\'.get_current_user().'\\')) { // is Windows 7 or Vista
            return $win7path;
        } else {
            return 'C:\Documents and Settings\\'.get_current_user().'\\';
        }
    }
}