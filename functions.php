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

// Функция для получения данных из .json файла и их записи в  БД
function importData(string $fileName, PDO $db_conn)
{
    try {
        $json = file_get_contents($fileName); // получили содержимое файла .json в встроке
        if (!$json) {
            throw new Exception ('Проверьте файл' . "\n");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        return;
    }
    $decode = json_decode($json, true); // получаем ассоциативный массив из строки json
    /*результат выполнения функции json_decode(, true) - ассоциативный массив.
        В котором содержиться массивы со свойствами товаров.
        Свойства являются либо строками, либо массивами.
        Сначала обрабатываем свойства-строки, далее свойства-массивы.
        Исходим из того, что свойства-строки являются данными для таблицы items, а свойства-массивы
        являются данными для отдельных таблиц связанных с таблицей items посредствам FOREIGN KEY.
        Т.е. реализована связь один ко многоим между записями в таблице items и записями в 4 других таблицах.
        Т.е. реализована логика один товар может иметь много доп. опций из ADDITIONAL_SERVICES, может относиться к разным категориям и так далее.*/
    createTables($db_conn); // вызов функции для создания таблиц
    // Запуск цикла для прохода ассоциативному полученному массиву из файла .json
    foreach ($decode as $key => $item) {
        foreach ($item as $key => $itemOptions) {
            // записываем в переменные значения свойств строк
            $item_id = $itemOptions['ITEM_ID'];
            $itemActive = $itemOptions['ITEM_ACTIVE'];
            $itemName = $itemOptions['ITEM_NAME'];
            $itemPlatform = $itemOptions['ITEM_PLATFORM'];
            $itemType = $itemOptions['ITEM_TYPE'];
            $itemArticle = $itemOptions['ITEM_ARTICLE'];
            // Состаявлем sql запрос для записи в таблицу items
            $sqlInsertItem = "INSERT INTO items (IdItem, itemActive, itemName, itemPlatform, itemType, itemArticle) VALUES ('$item_id', '$itemActive', '$itemName', '$itemPlatform', '$itemType', '$itemArticle')";
            $db_conn->query($sqlInsertItem); // Выполянем запрос к таблице items
            $last_insert_id_item = $db_conn->lastInsertId(); // получаем id последнего sql запроса. Для внешних ключей.
            // обнуляем переменных для значений свойств-строк после записи в БД
            $item_id = "";
            $itemActive = "";
            $itemName = "";
            $itemPlatform = "";
            $itemType = "";
            $itemArticle = "";
// Обработка свойств-массивов
            $itemStorage = $itemOptions['ITEM_STORAGE']; // Получаем свойство массив ITEM_STORAGE
            foreach ($itemStorage as $key => $item) { // Обработка свойства массива ITEM_STORAGE
                // записываем в перменные значения свойства массива
                $storageGuid = $item['STORAGE_GUID'];
                $storageCount = $item['count'];
                $storagePrice = $item['price'];
                $storagePrice_final = $item['price_final'];
            }
            $itemSpecs = $itemOptions['ITEM_SPECIFICATION']; //Получаем свойство массив ITEM_SPECIFICATION
            foreach ($itemSpecs as $key => $item) { // Обработка свойства массива ITEM_SPECIFICATION
                foreach ($item as $index => $specsSubArr) { // Массив имеет два уровня вложенности
                    foreach ($specsSubArr as $index => $optValue) {
                        // записываем в перменные значения свойств массива, которые есть у всех подмассивов
                        $specsId = $optValue['id'];
                        $specsValue = $optValue['value'];
                        $specsName = $optValue['name'];
                        // записываем в перменные значения свойств массива, которые есть НЕ у всех подмассивов. Только у двух.
                        // т.к. значения существуют не для всех подмассивов реализуем проверку на наличие свойства
                        $specsTypeId = !empty($optValue['typeId']) ? $optValue['typeId'] : "";
                        $specsTypeValue = !empty($optValue['typeValue']) ? $optValue['typeValue'] : "";
                        // Определяем sql запрос INSERT ITEM_SPECIFICATION
                        $sqlInsertSpecs = "INSERT INTO ITEM_SPECIFICATION (SpecsId, SpecsValue, SpecsName, typeId, typeValue, itemId ) VALUES ('$specsId', '$specsValue', '$specsName', '$specsTypeId', '$specsTypeValue', '$last_insert_id_item')";
                        $db_conn->query($sqlInsertSpecs); // Выполняем sql запрос INSERT ITEM_SPECIFICATION
                        // Обнуляем значения переменных хранящих зхначения свойств после записи в БД
                        $specsId = "";
                        $specsValue = "";
                        $specsName = "";
                        $specsTypeId = "";
                        $specsTypeValue = "";
                    }
                }
            }
            // т.к. свойство массив ITEM_CATEGORY не является обязательным свойством для каждого товара
            // реализуем проверку на наличия массив ITEM_CATEGORY
            $itemCategory = !empty($itemOptions['ITEM_CATEGORY']) ? $itemOptions['ITEM_CATEGORY'] : "";
            if ($itemCategory != "") { // Если свойство массив в наличии, то запуск прохода по массиву для доступа к значениям.
                foreach ($itemCategory as $key => $value) {
                    // записываем в перменные значения свойств массива. Массив не является ассоциативным
                    $categoryOne = $itemCategory[0];
                    $categoryTwo = $itemCategory[1];
                    $categoryThree = $itemCategory[2];
                    $sqlInsertCategory = "INSERT INTO ITEM_CATEGORY (itemCategoryOne, itemCategoryTwo, itemCategoryThree, itemId) VALUES ('$categoryOne', '$categoryTwo', '$categoryThree', '$last_insert_id_item')";
                }
            }
            // т.к. свойство массив ADDITIONAL_SERVICES не является обязательным свойством для каждого товара
            // реализуем проверку на наличия массив ADDITIONAL_SERVICES
            $itemAddServices = !empty($itemOptions['ADDITIONAL_SERVICES']) ? $itemOptions['ADDITIONAL_SERVICES'] : "";
//        $itemAddServices = $itemOptions['ADDITIONAL_SERVICES']; // Работает с PHP Notice:  Undefined index: ADDITIONAL_SERVICES
            if ($itemAddServices != "") { // Если свойство массив в наличии, то запуск прохода по массиву для доступа к значениям.
                // записываем в перменные значения свойств массива. Массив не является ассоциативным, обращение идет через числовые индексы
                $addServicesOne = $itemAddServices[0];
                $addServicesTwo = $itemAddServices[1];
                $addServicesThree = $itemAddServices[2];
                $sqlInsertAddServices = "INSERT INTO ADDITIONAL_SERVICES (itemAddServiceOne, itemAddServiceTwo, itemAddServiceThree, itemId) VALUES ('$addServicesOne', '$addServicesTwo', '$addServicesThree', '$last_insert_id_item')";
            }
            // Определяем insert в ITEM_STORAGE
            $sqlInsertStorage = "INSERT INTO ITEM_STORAGE (STORAGE_GUID, StorageCount, price, price_final, itemId) VALUES ('$storageGuid', '$storageCount', '$storagePrice', '$storagePrice_final', '$last_insert_id_item')";
            $db_conn->query($sqlInsertStorage);  // выполняем insert в ITEM_STORAGE
            if ($itemCategory != "") $db_conn->query($sqlInsertCategory); // Выполняем insert ITEM_CATEGORY, в случае наличия свойства массив ITEM_CATEGORY
            if ($itemAddServices != "") $db_conn->query($sqlInsertAddServices); // Выполняем insert ADDITIONAL_SERVICES, в случае наличия свойства массив ADDITIONAL_SERVICES
        }
    }
}
