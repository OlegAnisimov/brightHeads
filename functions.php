<?php
// Функция для создания таблиц в БД. См. комментарий после строки: $decode = json_decode($json, true);
function createTables(PDO $db_conn)
{ // аргумент функции - экземпляр PDO
    // Таблиц items для хранения свойств-строк
    $sql_create_items = "CREATE TABLE items ( 
                             Id INT PRIMARY KEY AUTO_INCREMENT, 
                             IdItem VARCHAR(150) NOT NULL UNIQUE, 
                             itemActive VARCHAR(50) NOT NULL,
                             itemName VARCHAR(50) NOT NULL,
                             itemPlatform VARCHAR(50) NOT NULL,
                             itemType VARCHAR(50) NOT NULL,
                             itemArticle VARCHAR(50) NOT NULL
)";
    // Таблиц ITEM_CATEGORY для хранения значений свойства-массива 'ITEM_CATEGORY'
    $sql_create_categories = "CREATE TABLE ITEM_CATEGORY (
                                                Id INT PRIMARY KEY AUTO_INCREMENT,
                                               itemCategoryOne VARCHAR(50) NOT NULL,
                                               itemCategoryTwo VARCHAR(50) NOT NULL,
                                               itemCategoryThree VARCHAR(50) NOT NULL,
                                               itemId INT NOT NULL,
                                               FOREIGN KEY (itemId) REFERENCES items(Id)
                                                    )";
    // Таблиц ADDITIONAL_SERVICES для хранения значений свойства-массива 'ADDITIONAL_SERVICES'
    $sql_create_addServices = "CREATE TABLE ADDITIONAL_SERVICES (
                                                    Id INT PRIMARY KEY AUTO_INCREMENT,
                                                    itemAddServiceOne VARCHAR(50) NOT NULL,
                                                    itemAddServiceTwo VARCHAR(50) NOT NULL,
                                                    itemAddServiceThree VARCHAR(50) NOT NULL,
                                                    itemId INT NOT NULL,
                                                    FOREIGN KEY (itemId) REFERENCES items(Id)
)";
    // Таблиц ITEM_STORAGE для хранения значений свойства-массива 'ITEM_STORAGE'
    $sql_create_storage = "CREATE TABLE ITEM_STORAGE (
                                                        Id INT PRIMARY KEY AUTO_INCREMENT,
                                                        STORAGE_GUID VARCHAR(50) NOT NULL,
                                                        StorageCount VARCHAR(50) NOT NULL,
                                                        price VARCHAR(50) NOT NULL,
                                                        price_final VARCHAR(50) NOT NULL,
                                                        itemId INT NOT NULL,
                                                        FOREIGN KEY (itemId) REFERENCES items(Id))";
    // Таблиц ITEM_SPECIFICATION для хранения значений свойства-массива 'ITEM_SPECIFICATION'
    $sql_create_specs = "CREATE TABLE ITEM_SPECIFICATION (
                           Id INT PRIMARY KEY AUTO_INCREMENT,
                           SpecsId VARCHAR(50) NOT NULL,
                           SpecsValue   VARCHAR(20) NOT NULL,
                           SpecsName   VARCHAR(150) NOT NULL,
                           typeId   VARCHAR(70),                           
                           typeValue VARCHAR(150),
                           itemId INT NOT NULL,
                           FOREIGN KEY (itemId) REFERENCES items(Id)        
)";
    // выполняем sql запросы по созданию таблиц в БД
    try {
        $db_conn->query($sql_create_items);
        $db_conn->query($sql_create_categories);
        $db_conn->query($sql_create_addServices);
        $db_conn->query($sql_create_storage);
        $db_conn->query($sql_create_specs);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
